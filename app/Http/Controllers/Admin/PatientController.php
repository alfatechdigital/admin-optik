<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Exports\PatientExport;
use App\Imports\PatientImport;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(15)->withQueryString();

        return view('admin.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('admin.patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:100',
            'tanggal_lahir'    => 'nullable|date|before:today',
            'jenis_kelamin'    => 'nullable|in:L,P',
            'no_hp'            => 'nullable|string|max:20',
            'no_bpjs'          => 'nullable|string',
            'tipe_bpjs'        => 'nullable|in:1,2,3',
            'nik'              => 'nullable|string',
            'email'            => 'nullable|email|max:100',
            'alamat'           => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
        ]);

        $validated['no_rm'] = Patient::generateNoRM();

        DB::transaction(function () use ($validated) {
            Patient::create($validated);
        });

        return redirect()->route('patients.index')
            ->with('success', "Pasien {$validated['nama']} berhasil ditambahkan dengan No. RM {$validated['no_rm']}.");
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'latestRecord.oldGlasses',
            'latestRecord.refraction',
            'latestRecord.prescription',
            'transactions.items.product',
            'transactions.kasir',
        ]);

        return view('admin.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('admin.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'nama'             => 'required|string|max:100',
            'tanggal_lahir'    => 'nullable|date|before:today',
            'jenis_kelamin'    => 'nullable|in:L,P',
            'no_hp'            => 'nullable|string|max:20',
            'no_bpjs'          => 'nullable|string',
            'tipe_bpjs'        => 'nullable|in:1,2,3',
            'nik'              => 'nullable|string',
            'email'            => 'nullable|email|max:100',
            'alamat'           => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Data pasien berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new PatientExport, 'patients_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $file = $request->file('file');

        $headerError = $this->validateImportHeaders($file, PatientImport::expectedHeadings());
        if ($headerError) {
            return redirect()->route('patients.index')
                ->with('error', $headerError);
        }

        $import = new PatientImport;

        try {
            DB::beginTransaction();

            Excel::import($import, $file);

            $errors = $import->getErrors();
            if (!empty($errors)) {
                DB::rollBack();

                $message = 'Import gagal: data tidak valid pada beberapa baris. ';
                $message .= implode(' | ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= ' (+' . (count($errors) - 5) . ' lainnya)';
                }

                return redirect()->route('patients.index')
                    ->with('error', $message);
            }

            DB::commit();

            $successCount = $import->getSuccessCount();
            $message = "Import selesai. {$successCount} data berhasil diimpor.";

            return redirect()->route('patients.index')
                ->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('patients.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    private function validateImportHeaders($file, array $expectedHeaders): ?string
    {
        $rows = (new HeadingRowImport)->toArray($file);
        $headers = $rows[0][0] ?? [];

        if (!is_array($headers) || empty($headers)) {
            return 'Format file tidak valid: tidak dapat membaca baris header.';
        }

        $normalize = fn ($value) => Str::of($value)
            ->trim()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', '_')
            ->replaceMatches('/_+/', '_')
            ->trim('_')
            ->__toString();

        $normalized = array_map($normalize, $headers);
        $expectedNormalized = array_map($normalize, $expectedHeaders);

        $missing = array_diff($expectedNormalized, $normalized);
        $extra = array_diff($normalized, $expectedNormalized);

        if (!empty($missing) || !empty($extra)) {
            $msgParts = [];
            if (!empty($missing)) {
                $msgParts[] = 'Kolom hilang: ' . implode(', ', $missing);
            }
            if (!empty($extra)) {
                $msgParts[] = 'Kolom tidak diharapkan: ' . implode(', ', $extra);
            }
            return 'Template file tidak sesuai. ' . implode(' ', $msgParts) . ' Pastikan menggunakan template import yang benar.';
        }

        return null;
    }

    // AJAX — search pasien untuk POS
    public function search(Request $request)
    {
        $patients = Patient::where('nama', 'like', "%{$request->q}%")
            ->orWhere('no_rm', 'like', "%{$request->q}%")
            ->orWhere('no_bpjs', 'like', "%{$request->q}%")
            ->limit(10)
            ->get(['id', 'no_rm', 'nama', 'no_hp']);

        return response()->json($patients);
    }

    
    public function latestRefraction(Patient $patient)
    {
        $patient->load([
            'medicalRecords' => function ($query) {
                $query->latest('visit_date');
            },
            'medicalRecords.refraction',
            'medicalRecords.oldGlasses',
            'medicalRecords.prescription',
            'medicalRecords.transaction',
        ]);

        $latest = $patient->medicalRecords->first();
        $latestRefraction = $latest?->refraction;

        $history = $patient->medicalRecords->map(function ($rm) {
            $refraction = $rm->refraction;
            $oldGlasses = $rm->oldGlasses;
            $prescription = $rm->prescription;
            $transaction = $rm->transaction;

            $isBpjs = $transaction && $transaction->typefaktur == 2;
            $isUmum = $transaction && $transaction->typefaktur == 1;

            return [
                'id' => $rm->id,
                'tanggal_kunjungan' => $rm->visit_date->format('d M Y'),
                'dokter' => $refraction?->doctor_name ?? $prescription?->doctor_name ?? '-',
                'keluhan' => $rm->complaint,
                'is_bpjs' => $isBpjs,
                'is_umum' => $isUmum,

                // Old Glasses (sub-object)
                'old_glasses' => $oldGlasses ? [
                    'od_sph' => $oldGlasses->od_sph,
                    'od_cyl' => $oldGlasses->od_cyl,
                    'od_axis' => $oldGlasses->od_axis,
                    'od_add' => $oldGlasses->od_add,
                    'od_pd' => $oldGlasses->od_pd,
                    'od_prism' => $oldGlasses->od_prism,
                    'os_sph' => $oldGlasses->os_sph,
                    'os_cyl' => $oldGlasses->os_cyl,
                    'os_axis' => $oldGlasses->os_axis,
                    'os_add' => $oldGlasses->os_add,
                    'os_pd' => $oldGlasses->os_pd,
                    'os_prism' => $oldGlasses->os_prism,
                ] : null,

                // Refraction (sub-object)
                'refraction' => $refraction ? [
                    'doctor_name' => $refraction->doctor_name,
                    'diagnosis' => $refraction->diagnosis,
                    'od_sc' => $refraction->od_sc,
                    'od_sph' => $refraction->od_sph,
                    'od_cyl' => $refraction->od_cyl,
                    'od_axis' => $refraction->od_axis,
                    'od_add' => $refraction->od_add,
                    'od_pd' => $refraction->od_pd,
                    'od_prism' => $refraction->od_prism,
                    'od_cc' => $refraction->od_cc,
                    'os_sc' => $refraction->os_sc,
                    'os_sph' => $refraction->os_sph,
                    'os_cyl' => $refraction->os_cyl,
                    'os_axis' => $refraction->os_axis,
                    'os_add' => $refraction->os_add,
                    'os_pd' => $refraction->os_pd,
                    'os_prism' => $refraction->os_prism,
                    'os_cc' => $refraction->os_cc,
                ] : null,

                // Prescription (sub-object)
                'prescription' => $prescription ? [
                    'doctor_name' => $prescription->doctor_name,
                    'diagnosis' => $prescription->diagnosis,
                    'od_sph' => $prescription->od_sph,
                    'od_cyl' => $prescription->od_cyl,
                    'od_axis' => $prescription->od_axis,
                    'od_add' => $prescription->od_add,
                    'od_mpd' => $prescription->od_mpd,
                    'od_prism' => $prescription->od_prism,
                    'od_cc' => $prescription->od_cc,
                    'os_sph' => $prescription->os_sph,
                    'os_cyl' => $prescription->os_cyl,
                    'os_axis' => $prescription->os_axis,
                    'os_add' => $prescription->os_add,
                    'os_pd' => $prescription->os_pd,
                    'os_prism' => $prescription->os_prism,
                    'os_cc' => $prescription->os_cc,
                ] : null,
            ];
        });

        return response()->json([
            // Backward compatibility: refraksi terakhir (auto-fill)
            'od_sph' => $latestRefraction?->od_sph,
            'od_cyl' => $latestRefraction?->od_cyl,
            'od_axis' => $latestRefraction?->od_axis,
            'od_add' => $latestRefraction?->od_add,
            'od_mpd' => $latestRefraction?->od_pd,
            'od_prism' => $latestRefraction?->od_prism,
            'os_sph' => $latestRefraction?->os_sph,
            'os_cyl' => $latestRefraction?->os_cyl,
            'os_axis' => $latestRefraction?->os_axis,
            'os_add' => $latestRefraction?->os_add,
            'os_mpd' => $latestRefraction?->os_pd,
            'os_prism' => $latestRefraction?->os_prism,
            // Histori lengkap (terstruktur)
            'history' => $history,
        ]);
    }
}

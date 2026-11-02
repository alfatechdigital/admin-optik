<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Diagnosis, MedicalRecord, Patient, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $latestIds = MedicalRecord::selectRaw('MAX(id) as id')
            ->groupBy('patient_id');

        $query = MedicalRecord::whereIn('id', $latestIds)
            ->with(['patient', 'createdBy', 'refraction', 'prescription']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas(
                'patient',
                fn($q) =>
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_rm', 'like', "%{$search}%")
            );
        }

        if ($request->filled('from')) {
            $query->whereDate('visit_date', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('visit_date', '<=', $request->to);
        }

        $records = $query->latest('visit_date')->latest('id')->paginate(15)->withQueryString();

        return view('admin.medical-records.index', compact('records'));
    }

    public function create(Request $request)
    {
        $patients = Patient::orderBy('nama')->get(['id', 'no_rm', 'nama']);
        $dokters = User::role(['super_admin', 'admin'])->orderBy('name')->get(['id', 'name']);
        $selectedPatient = $request->patient_id ? Patient::find($request->patient_id) : null;
        $selectedDiagnosis = $request->diagnosis_id ? Diagnosis::find($request->diagnosis_id) : null;
        $diagnoses = Diagnosis::orderBy('name')->get();

        return view('admin.medical-records.create', compact('patients', 'dokters', 'selectedPatient', 'selectedDiagnosis', 'diagnoses'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // 1. VALIDASI 
            $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'tanggal_kunjungan' => 'required|date',
                'nama_dokter' => 'required|string|max:255',
                'keluhan' => 'nullable|string|max:255',
            ]);

            // 2. CREATE NEW MEDICAL RECORD
            $record = MedicalRecord::create([
                'patient_id' => $request->patient_id,
                'user_id' => Auth::id(),
                'visit_date' => $request->tanggal_kunjungan,
                'complaint' => $request->keluhan,
            ]);

            // 3. SPLIT DATA BERDASARKAN PREFIX
            $oldData = $this->extractData($request->all(), 'og_');
            $refData = $this->extractData($request->all(), 'ref_');
            $rxData = $this->extractData($request->all(), 'presc_');

            // Handle root level nama_dokter for sub-tables
            if ($request->filled('nama_dokter')) {
                $refData['doctor_name'] = $request->nama_dokter;
                $rxData['doctor_name'] = $request->nama_dokter;
            }
            // dd(compact('oldData', 'refData', 'rxData'));

            // 4. SIMPAN JIKA ADA ISI MENGGUNAKAN updateOrCreate

            if ($this->hasValue($oldData)) {
                $record->oldGlasses()->updateOrCreate(
                    ['medical_record_id' => $record->id],
                    $oldData
                );
            }

            if ($this->hasValue($refData)) {
                $record->refraction()->updateOrCreate(
                    ['medical_record_id' => $record->id],
                    $refData
                );
            }

            if ($this->hasValue($rxData)) {
                $record->prescription()->updateOrCreate(
                    ['medical_record_id' => $record->id],
                    $rxData
                );
            }

            DB::commit();

            return redirect()->route('medical-records.index')
                ->with('success', 'Rekam medis berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['patient', 'createdBy', 'transaction.items.product', 'refraction', 'prescription', 'oldGlasses']);

        $patientRecords = $medicalRecord->patient
            ? MedicalRecord::where('patient_id', $medicalRecord->patient_id)
                ->with(['refraction', 'prescription'])
                ->latest('visit_date')
                ->get()
            : collect([$medicalRecord]);

        return view('admin.medical-records.show', compact('medicalRecord', 'patientRecords'));
    }

    public function edit(MedicalRecord $medicalRecord, Request $request)
    {
        $dokters = User::role(['super_admin', 'admin'])
            ->orderBy('name')
            ->get(['id', 'name']);

        // Selected patient
        $selectedPatient = $request->patient_id
            ? Patient::select('id', 'nama', 'no_rm')->find($request->patient_id)
            : $medicalRecord->patient;

        // Selected diagnosis
        $selectedDiagnosis = $request->diagnosis_id
            ? Diagnosis::find($request->diagnosis_id)
            : $medicalRecord->diagnosis ?? null;

        $diagnoses = Diagnosis::orderBy('name')->get();

        // dd(compact('medicalRecord', 'dokters', 'selectedPatient', 'selectedDiagnosis', 'diagnoses'));
        return view('admin.medical-records.edit', compact(
            'medicalRecord',
            'dokters',
            'selectedPatient',
            'selectedDiagnosis',
            'diagnoses'
        ));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        // dd($request->all());
        DB::beginTransaction();

        try {
            // 1. VALIDASI BASIC
            $request->validate([
                'patient_id' => 'required|exists:patients,id',
                'tanggal_kunjungan' => 'required|date',
                'nama_dokter' => 'required|string|max:255',
                'keluhan' => 'nullable|string|max:255',
            ]);

            // 2. UPDATE MEDICAL RECORD
            $medicalRecord->update([
                'patient_id' => $request->patient_id,
                'visit_date' => $request->tanggal_kunjungan,
                'complaint' => $request->keluhan,
            ]);

            // 3. SPLIT DATA
            $oldData = $this->extractData($request->all(), 'og_');
            $refData = $this->extractData($request->all(), 'ref_');
            $rxData = $this->extractData($request->all(), 'presc_');

            // Handle root level nama_dokter for sub-tables
            if ($request->filled('nama_dokter')) {
                $refData['doctor_name'] = $request->nama_dokter;
                $rxData['doctor_name'] = $request->nama_dokter;
            }

            // 4. UPDATE / CREATE RELASI

            // OLD GLASSES
            if ($this->hasValue($oldData)) {
                $medicalRecord->oldGlasses()->updateOrCreate(
                    ['medical_record_id' => $medicalRecord->id],
                    $oldData
                );
            }

            // REFRACTION
            if ($this->hasValue($refData)) {
                $medicalRecord->refraction()->updateOrCreate(
                    ['medical_record_id' => $medicalRecord->id],
                    $refData
                );
            }

            // PRESCRIPTION
            if ($this->hasValue($rxData)) {
                $medicalRecord->prescription()->updateOrCreate(
                    ['medical_record_id' => $medicalRecord->id],
                    $rxData
                );
            }

            DB::commit();

            return redirect()->route('medical-records.index')
                ->with('success', 'Rekam medis berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();
        return redirect()->route('medical-records.index')
            ->with('success', 'Rekam medis berhasil dihapus.');
    }

    public function getDetailJson(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['oldGlasses', 'refraction', 'prescription']);

        $data = [];

        // Helper to flatten
        $flatten = function ($obj, $prefix) use (&$data) {
            $fields = ($prefix === 'ref')
                ? ['sc', 'sph', 'cyl', 'axis', 'prism', 'add', 'mpd', 'cc']
                : ['sph', 'cyl', 'axis', 'prism', 'add', 'mpd', 'cc'];
            foreach (['od', 'os'] as $eye) {
                foreach ($fields as $f) {
                    $key = "{$eye}_{$f}";
                    $data["{$prefix}_{$key}"] = $obj ? ($obj->$key ?? '') : '';
                }
            }
        };

        $flatten($medicalRecord->oldGlasses, 'og');
        $flatten($medicalRecord->refraction, 'ref');
        $flatten($medicalRecord->prescription, 'presc');

        $data['doctor_name'] = $medicalRecord->refraction?->doctor_name
            ?? $medicalRecord->prescription?->doctor_name
            ?? '';

        return response()->json($data);
    }

    public function getByPatient($patient_id)
    {
        $medicalRecord = MedicalRecord::where('patient_id', $patient_id)->with(['oldGlasses', 'refraction', 'prescription'])->latest()->first();

        if (!$medicalRecord) {
            return response()->json([]);
        }

        $data = [
            'tanggal_kunjungan' => $medicalRecord->visit_date,
            'keluhan' => $medicalRecord->complaint,
        ];

        // Helper to flatten
        $flatten = function ($obj, $prefix) use (&$data) {
            $fields = ($prefix === 'ref')
                ? ['sc', 'sph', 'cyl', 'axis', 'prism', 'add', 'mpd', 'cc', 'diagnosis', 'doctor_name', 'exam_date', 'notes']
                : ['sph', 'cyl', 'axis', 'prism', 'add', 'mpd', 'cc', 'diagnosis', 'doctor_name', 'exam_date', 'notes'];
            foreach (['od', 'os'] as $eye) {
                foreach ($fields as $f) {
                    if (in_array($f, ['diagnosis', 'doctor_name', 'exam_date', 'notes'])) {
                        // These are typically at the table level, not per eye. We handle them separately below.
                        continue;
                    }
                    $key = "{$eye}_{$f}";
                    $data["{$prefix}_{$key}"] = $obj ? ($obj->$key ?? '') : '';
                }
            }
            // non-eye specific fields
            $data["{$prefix}_diagnosis"] = $obj ? ($obj->diagnosis ?? '') : '';
            $data["{$prefix}_doctor_name"] = $obj ? ($obj->doctor_name ?? '') : '';
            $data["{$prefix}_exam_date"] = $obj ? ($obj->exam_date ?? '') : '';
            $data["{$prefix}_notes"] = $obj ? ($obj->notes ?? '') : '';
        };

        $flatten($medicalRecord->oldGlasses, 'og');
        $flatten($medicalRecord->refraction, 'ref');
        $flatten($medicalRecord->prescription, 'presc');

        // Overwrite or append global rx values just in case if needed
        return response()->json($data);
    }

    // --- HELPER METHODS ---
    private function extractData($data, $prefix)
    {
        return collect($data)
            ->filter(fn($v, $k) => str_starts_with($k, $prefix))
            ->mapWithKeys(function ($value, $key) use ($prefix) {
                return [str_replace($prefix, '', $key) => $value];
            })
            ->toArray();

        // Handle PD/MPD mapping for clinical tables (PD is legacy, MPD is preferred)
        if (isset($extracted['od_mpd']) && !isset($extracted['od_pd'])) $extracted['od_pd'] = $extracted['od_mpd'];
        if (isset($extracted['os_mpd']) && !isset($extracted['os_pd'])) $extracted['os_pd'] = $extracted['os_mpd'];
        if (isset($extracted['od_pd']) && !isset($extracted['od_mpd'])) $extracted['od_mpd'] = $extracted['od_pd'];
        if (isset($extracted['os_pd']) && !isset($extracted['os_mpd'])) $extracted['os_mpd'] = $extracted['os_pd'];

        return $extracted;
    }

    public function doctorAutocomplete(Request $request)
    {
        $q = $request->q;
        // Search users with 'ro' or 'admin' or 'super_admin' role
        $doctors = User::role(['ro', 'admin', 'super_admin'])
            ->where('name', 'like', "%{$q}%")
            ->take(10)
            ->get(['id', 'name']);

        return response()->json($doctors);
    }

    private function hasValue($data)
    {
        return collect($data)->filter(fn($v) => !is_null($v) && $v !== '')->isNotEmpty();
    }
}

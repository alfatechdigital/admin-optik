<?php

namespace App\Services;

use App\Models\MedicalRecord;
use App\Models\Refraction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MedicalRecordService
{
    public function __construct()
    {
        //
    }

    /**
     * Create a medical record with all sub-tables if requested or if data differs.
     */
    public function saveFromTransaction($patientId, array $data)
    {
        $medicalRecordId = $data['history_dropdown'] ?? $data['medical_record_id'] ?? null;
        $saveAsNew = isset($data['save_as_new_medical']) ? ($data['save_as_new_medical'] == "1") : true;

        $medicalRecord = null;
        if (!$saveAsNew && $medicalRecordId) {
            $medicalRecord = MedicalRecord::find($medicalRecordId);
        }

        $visitDate = $data['tgl_resep'] ?? $data['tgl_faktur'] ?? $data['tanggal_kunjungan'] ?? now()->toDateString();
        $complaint = $data['catatan_resep'] ?? $data['keluhan'] ?? null;

        if (!$medicalRecord) {
            $medicalRecord = MedicalRecord::create([
                'patient_id' => $patientId,
                'user_id' => Auth::id(),
                'visit_date' => $visitDate,
                'complaint' => $complaint,
            ]);
        } else {
            $medicalRecord->update([
                'user_id' => Auth::id(),
                'visit_date' => $visitDate,
                'complaint' => $complaint ?? $medicalRecord->complaint,
            ]);
        }

        // 1. Old Glasses (og_)
        $ogData = $this->extractByPrefix($data, 'og_');
        if ($this->hasClinicalData($ogData)) {
            $medicalRecord->oldGlasses()->updateOrCreate(
                ['medical_record_id' => $medicalRecord->id],
                $ogData
            );
        }

        // 2. Prescription (presc_)
        $prescData = $this->extractByPrefix($data, 'presc_');
        if ($this->hasClinicalData($prescData)) {
            $medicalRecord->prescription()->updateOrCreate(
                ['medical_record_id' => $medicalRecord->id],
                $prescData
            );
        }

        // 3. Refraction (ref_ or standard names)
        // From transaction, we use the standard names (od_sph, etc.)
        $refData = [
            'doctor_name' => $data['nama_dokter'] ?? null,
            'od_sc' => $data['od_sc'] ?? null,
            'od_sph' => $data['od_sph'] ?? null,
            'od_cyl' => $data['od_cyl'] ?? null,
            'od_axis' => $data['od_axis'] ?? null,
            'od_add' => $data['od_add'] ?? null,
            'od_pd' => $data['od_mpd'] ?? null,
            'od_mpd' => $data['od_mpd'] ?? null,
            'od_prism' => $data['od_prism'] ?? null,
            'od_cc' => $data['od_cc'] ?? null,
            'os_sc' => $data['os_sc'] ?? null,
            'os_sph' => $data['os_sph'] ?? null,
            'os_cyl' => $data['os_cyl'] ?? null,
            'os_axis' => $data['os_axis'] ?? null,
            'os_add' => $data['os_add'] ?? null,
            'os_pd' => $data['os_mpd'] ?? null,
            'os_mpd' => $data['os_mpd'] ?? null,
            'os_prism' => $data['os_prism'] ?? null,
            'os_cc' => $data['os_cc'] ?? null,
        ];
        if ($this->hasClinicalData($refData)) {
            $medicalRecord->refraction()->updateOrCreate(
                ['medical_record_id' => $medicalRecord->id],
                $refData
            );
        }

        return $medicalRecord;
    }

    private function extractByPrefix($data, $prefix)
    {
        $extracted = collect($data)
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

    private function hasClinicalData($data)
    {
        return collect($data)->filter(fn($v) => !is_null($v) && $v !== '')->isNotEmpty();
    }

    private function normalize(array $data)
    {
        foreach ($data as $key => $value) {
            if ($value instanceof Carbon) {
                $data[$key] = $value->format('Y-m-d');
            } elseif (is_numeric($value)) {
                $data[$key] = (float) $value;
            } elseif ($value === null || $value === '') {
                $data[$key] = null;
            }
        }

        return $data;
    }
}

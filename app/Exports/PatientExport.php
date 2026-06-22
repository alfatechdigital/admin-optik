<?php

namespace App\Exports;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PatientExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Patient::all()->map(function ($patient) {
            return [
                'no_rm' => $patient->no_rm,
                'nama' => $patient->nama,
                'tanggal_lahir' => $patient->tanggal_lahir?->format('Y-m-d'),
                'jenis_kelamin' => $patient->jenis_kelamin,
                'no_hp' => $patient->no_hp,
                'no_bpjs' => $patient->no_bpjs,
                'email' => $patient->email,
                'alamat' => $patient->alamat,
                'riwayat_penyakit' => $patient->riwayat_penyakit,
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No. RM',
            'Nama',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'No. HP',
            'No. BPJS',
            'Email',
            'Alamat',
            'Riwayat Penyakit',
        ];
    }
}
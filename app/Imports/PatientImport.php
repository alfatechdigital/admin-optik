<?php

namespace App\Imports;

use App\Models\Patient;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class PatientImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    private array $errors = [];
    private int $successCount = 0;

    public static function expectedHeadings(): array
    {
        return [
            // 'no_rm',
            'nama',
            'tanggal_lahir',
            'jenis_kelamin',
            'no_hp',
            'no_bpjs',
            'email',
            'alamat',
            'riwayat_penyakit',
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $patient = new Patient([
            'no_rm' => $row['no_rm'] ?? Patient::generateNoRM(),
            'nama' => $row['nama'],
            'tanggal_lahir' => $row['tanggal_lahir'] ? Carbon::parse($row['tanggal_lahir']) : null,
            'jenis_kelamin' => $row['jenis_kelamin'],
            'no_hp' => $row['no_hp'],
            'no_bpjs' => $row['no_bpjs'],
            'email' => $row['email'],
            'alamat' => $row['alamat'],
            'riwayat_penyakit' => $row['riwayat_penyakit'],
        ]);

        $patient->save();
        $this->successCount++;

        return $patient;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'tanggal_lahir' => 'nullable|date|before:today',
            'jenis_kelamin' => 'nullable|in:L,P',
            'no_hp' => 'nullable|string|max:20',
            'no_bpjs' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'alamat' => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
        ];
    }

    /**
     * @param Throwable $e
     */
    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    /**
     * Get the errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the success count
     *
     * @return int
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
}
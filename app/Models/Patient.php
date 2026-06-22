<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'no_rm',
        'nama',
        'tanggal_lahir',
        'jenis_kelamin',
        'no_hp',
        'no_bpjs',
        'tipe_bpjs',
        'nik',
        'email',
        'alamat',
        'riwayat_penyakit',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Auto-generate Nomor Rekam Medis
    public static function generateNoRM(): string
    {
        $last = static::withTrashed()->latest('id')->first();
        $num  = $last ? ((int) substr($last->no_rm, 2)) + 1 : 1;
        return 'RM' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }

    // Accessor umur
    public function getUmurAttribute(): ?int
    {
        return $this->tanggal_lahir
            ? $this->tanggal_lahir->age
            : null;
    }

    // Relasi
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class)->latest();
    }

    public function latestRecord()
    {
        return $this->hasOne(MedicalRecord::class)->latestOfMany();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->latest();
    }
}

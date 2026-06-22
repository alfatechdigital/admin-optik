<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicalRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'user_id',
        'visit_date',
        'complaint',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    // Format nilai resep dengan tanda + atau -
    public function formatResep(?float $val): string
    {
        if (is_null($val)) return '-';
        return ($val >= 0 ? '+' : '') . number_format($val, 2);
    }

    // Relasi
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function refraction()
    {
        return $this->hasOne(Refraction::class);
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }

    public function oldGlasses()
    {
        return $this->hasOne(OldGlasses::class);
    }
}

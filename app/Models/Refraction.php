<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refraction extends Model
{
    protected $fillable = [
        'medical_record_id',
        'doctor_name',
        'diagnosis',
        'exam_date',
        'notes',
        // OD
        'od_sc', 'od_sph', 'od_cyl', 'od_axis', 'od_add', 'od_pd', 'od_mpd', 'od_prism', 'od_cc',
        // OS
        'os_sc', 'os_sph', 'os_cyl', 'os_axis', 'os_add', 'os_pd', 'os_mpd', 'os_prism', 'os_cc',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class);
    }
}

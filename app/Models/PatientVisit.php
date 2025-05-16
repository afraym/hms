<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientVisit extends Model
{
    protected $fillable = [
        'patient_id', 'type', 'visit_at', 'notes'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'type',
        'visit_at',
        'notes',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Define the bed relationship
    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }
}

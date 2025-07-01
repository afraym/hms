<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'type', 'visit_at', 'notes', 'department_id', 'bed_id',
        'companion_name', 'companion_relation', 'companion_phone', 'companion_national_id',
    ];

    protected $casts = [
        'visit_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function bed()
    {
        return $this->belongsTo(Bed::class);
    }
    
    public function attachments()
    {
        return $this->hasMany(\App\Models\Attachment::class);
    }
}

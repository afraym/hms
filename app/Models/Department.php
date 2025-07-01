<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    public function patientVisits()
    {
        return $this->hasMany(PatientVisit::class);
    }
}

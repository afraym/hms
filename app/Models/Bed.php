<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    protected $fillable = [
        'bed_number',
        'room_number',
        'status',
        'department',
        'patient_name',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
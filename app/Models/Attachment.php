<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'patient_id',
        'file',
        'original_name',
        'type',
        'description',
        'title'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file);
    }
}

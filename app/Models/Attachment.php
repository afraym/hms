<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'patient_id',
        'file',
        'original_name',
        'filename',
        'file_path',
        'file_size',
        'mime_type',
        'type',
        'description',
        'uploaded_by'
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

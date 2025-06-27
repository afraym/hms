<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use App\Models\Attachment;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',  // Replace individual name fields
        'email', 'phone', 'companion_phone', 'national_id', 'date_of_birth', 'gender',
        'status', 'bed_id', 'medical_id', 'uhi_number', 'address', 'governorate',
        'companion_name', 'companion_relation', 'companion_national_id',
        'department_id', 'created_by', 'created_at' // أضف created_at
    ];

    // أضف هذا لتحويل الحقل تلقائياً إلى كائن DateTime
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
        'date_of_birth' => 'date'
    ];

    // Add this method to automatically convert times to Egyptian timezone
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->timezone('Africa/Cairo');
    }

    // العلاقة مع المستخدم الذي أنشأ السجل
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Define the visits relationship
    public function visits()
    {
        return $this->hasMany(PatientVisit::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    // Add this helper method to get the storage path for attachments
    public function getAttachmentPath()
    {
        $date = $this->created_at ?? now();
        return "attachments/{$date->format('Y/m/d')}/{$this->medical_id}";
    }
}

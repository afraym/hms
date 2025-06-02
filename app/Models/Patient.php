<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'second_name', 'third_name', 'fourth_name',
        'email', 'phone', 'phone2', 'national_id', 'date_of_birth', 'gender'
    ];

    // Encrypt phone before saving
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = Crypt::encryptString($value);
    }

    // Decrypt phone when retrieving
    public function getPhoneAttribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }

      // Encrypt phone2 before saving
    public function setPhone2Attribute($value)
    {
        $this->attributes['phone2'] = Crypt::encryptString($value);
    }

    // Decrypt phone2 when retrieving
    public function getPhone2Attribute($value)
    {
        return $value ? Crypt::decryptString($value) : null;
    }
    // Encrypt national_id before saving
    // public function setNationalIdAttribute($value)
    // {
    //     // $this->attributes['national_id'] = Crypt::encryptString($value);
    //     $this->attributes['national_id'] = hash('sha256', $value);
    // }

    // Decrypt national_id when retrieving
    // public function getNationalIdAttribute($value)
    // {
    //     return $value ? Crypt::decryptString($value) : null;
    // }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Define the visits relationship
    public function visits()
    {
        return $this->hasMany(PatientVisit::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'status'];

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    public function patients()
    {
        return $this->hasMany(Patient::class);
    }
}

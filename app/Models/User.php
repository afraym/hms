<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function hasAnyRole($roles)
    {
        $roles = is_array($roles) ? $roles : explode('|', $roles);
        return in_array($this->role, $roles);
    }

    /**
     * Find user by email or phone
     */
    public static function findByEmailOrPhone($identifier)
    {
        return static::where('email', $identifier)
                     ->orWhere('phone', $identifier)
                     ->first();
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute()
    {
        return ucfirst($this->role);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is manager
     */
    public function isManager()
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is reception
     */
    public function isReception()
    {
        return $this->role === 'reception';
    }
}

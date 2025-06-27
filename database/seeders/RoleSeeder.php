<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'مدير النظام'],
            ['name' => 'manager', 'display_name' => 'مدير'],
            ['name' => 'reception', 'display_name' => 'استقبال'],
            ['name' => 'kitchen', 'display_name' => 'مطبخ'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
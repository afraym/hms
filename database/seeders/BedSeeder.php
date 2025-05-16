<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bed;

class BedSeeder extends Seeder
{
    public function run(): void
    {
        // العناية المركزة: 58 سرير
        for ($i = 1; $i <= 58; $i++) {
            Bed::create([
                'bed_number' => 'ICU-' . $i,
                'room_number' => ceil($i / 2),
                'status' => 'متاح',
                'department' => 'العناية المركزة',
            ]);
        }
        // الطب الباطني العام: 24 سرير
        for ($i = 1; $i <= 24; $i++) {
            Bed::create([
                'bed_number' => 'IM-' . $i,
                'room_number' => ceil($i / 2),
                'status' => 'متاح',
                'department' => 'الطب الباطني العام',
            ]);
        }
        // الحضانات: 34 سرير
        for ($i = 1; $i <= 34; $i++) {
            Bed::create([
                'bed_number' => 'NICU-' . $i,
                'room_number' => ceil($i / 2),
                'status' => 'متاح',
                'department' => 'الحضانات',
            ]);
        }
        // الأقسام الأخرى: 68-76 سرير (نختار 72 كمعدل)
        for ($i = 1; $i <= 72; $i++) {
            Bed::create([
                'bed_number' => 'OTH-' . $i,
                'room_number' => ceil($i / 2),
                'status' => 'متاح',
                'department' => 'الأقسام الأخرى',
            ]);
        }
    }
}

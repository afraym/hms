<?php

namespace App\Imports;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PatientsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        try {
            Log::info('Importing row:', $row);

            return new Patient([
                'medical_id' => $row['رقم مسلسل المريض'] ?? null,
                'full_name' => $row['الاسم رباعي'] ?? null,
                'gender' => $this->translateGender($row['النوع'] ?? null),
                'national_id' => $row['الرقم القومي'] ?? null,
                'address' => $row['العنوان'] ?? null,
                'phone' => $row['رقم التليفون'] ?? null,
                'department_id' => $this->getDepartmentId($row['قسم الدخول'] ?? null),
                'admission_time' => $this->transformDate($row['وقت الدخول'] ?? null),
                'companion_name' => $row['مرافق المريض عند تسجيل الدخول'] ?? null,
                'companion_national_id' => $row['الرقم القومي للمرافق'] ?? null,
                'companion_phone' => $row['رقم التليفون للمرافق'] ?? null,
                'companion_relation' => $row['صلة القرابة'] ?? null,
                'status' => 'waiting',
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error importing row: ' . $e->getMessage());
            throw $e;
        }
    }

    private function translateGender($gender)
    {
        return match (trim($gender)) {
            'ذكر' => 'male',
            'انثى', 'أنثى' => 'female',
            default => null,
        };
    }

    private function getDepartmentId($departmentName)
    {
        $departments = [
            'قسم القلب' => 1,
            'عناية الجهاز الهضمي' => 2,
            'قسم الباطنة' => 3,
            'قسم الروماتيزم' => 4,
            'قسم الباطنة العامة' => 5,
            'عناية القلب' => 6,
        ];

        return $departments[$departmentName] ?? null;
    }

    private function transformDate($value)
    {
        if (empty($value)) {
            return now();
        }

        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            Log::error('Date parsing error: ' . $e->getMessage());
            return now();
        }
    }

    public function headingRow(): int
    {
        return 1; // First row contains headers
    }
}
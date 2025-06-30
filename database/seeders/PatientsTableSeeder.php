<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Patient;
use App\Models\PatientVisit;

class PatientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = database_path('seeders/20-06-2025.csv');
        $file = fopen($csvFile, 'r');

        // Skip the header row
        $header = fgetcsv($file);

        // Map CSV headers (Arabic) to database columns (English)
        $columnMapping = [
            'رقم مسلسل المريض' => 'medical_id',
            'الاسم رباعي' => 'full_name',
            'النوع' => 'gender',
            'الرقم القومي' => 'national_id',
            'العنوان' => 'address',
            'رقم التليفون' => 'phone',
            'اسم مرافق المريض عند تسجيل الدخول' => 'companion_name',
            'الرقم القومي للمرافق' => 'companion_national_id',
            'رقم تليفون المرافق' => 'companion_phone',
            'صلة القرابة' => 'companion_relation',
            'قسم الدخول' => 'department_id',
            'وقت الدخول' => 'admission_time',
            'وقت الخروج' => 'discharge_time',
        ];

        $insertedPatientsCount = 0;
        $insertedVisitsCount = 0;

        while (($row = fgetcsv($file)) !== false) {
            $rowAssoc = array_combine($header, $row); // Create associative array for easier access

            // Extract patient data
            $patientData = [
                'medical_id' => $rowAssoc['رقم مسلسل المريض'] ?? null,
                'full_name' => $rowAssoc['الاسم رباعي'] ?? null,
                'gender' => $this->translateGender($rowAssoc['النوع'] ?? null),
                'national_id' => $this->cleanNationalId($rowAssoc['الرقم القومي'] ?? null),
                'address' => $rowAssoc['العنوان'] ?? null,
                'phone' => $this->cleanPhoneNumber($rowAssoc['رقم التليفون'] ?? null),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Check if the patient already exists
            $existingPatient = Patient::where('national_id', $patientData['national_id'])->first();

            if ($existingPatient) {
                // If the patient exists, create a new visit
                $visitData = $this->extractVisitData($rowAssoc, $existingPatient->id);
                PatientVisit::create($visitData);
                $insertedVisitsCount++;
            } else {
                // If the patient does not exist, create the patient and the initial visit
                $newPatient = Patient::create($patientData);
                $visitData = $this->extractVisitData($rowAssoc, $newPatient->id);
                PatientVisit::create($visitData);
                $insertedPatientsCount++;
                $insertedVisitsCount++;
            }
        }

        fclose($file);

        $this->command->info("Successfully seeded {$insertedPatientsCount} patients and {$insertedVisitsCount} visits.");
    }

    private function translateGender($gender)
    {
        return match (trim($gender)) {
            'ذكر' => 'male',
            'انثى', 'أنثى' => 'female',
            default => null,
        };
    }

    private function cleanNationalId($value)
    {
        $cleanedValue = preg_replace('/[^0-9]/', '', $value);
        return (strlen($cleanedValue) === 14 && ctype_digit($cleanedValue)) ? $cleanedValue : null;
    }

    private function cleanPhoneNumber($value)
    {
        $cleanedValue = preg_replace('/[^0-9]/', '', $value);
        return (strlen($cleanedValue) >= 10 && strlen($cleanedValue) <= 15 && ctype_digit($cleanedValue)) ? $cleanedValue : null;
    }

    private function extractVisitData($rowAssoc, $patientId)
    {
        return [
            'patient_id' => $patientId,
            'department_id' => $this->getDepartmentId($rowAssoc['قسم الدخول'] ?? null),
            'bed_id' => null, // Assuming bed_id is not provided in the CSV
            'companion_name' => $rowAssoc['اسم مرافق المريض عند تسجيل الدخول'] ?? null,
            'companion_relation' => $rowAssoc['صلة القرابة'] ?? null,
            'companion_phone' => $this->cleanPhoneNumber($rowAssoc['رقم التليفون للمرافق'] ?? null),
            'companion_national_id' => $this->cleanNationalId($rowAssoc['الرقم القومي للمرافق'] ?? null),
            'visit_at' => $this->transformDate($rowAssoc['وقت الدخول'] ?? null),
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function getDepartmentId($departmentName)
    {
        $department = DB::table('departments')->where('name', 'LIKE', "%{$departmentName}%")->first();
        return $department ? $department->id : null;
    }

    private function transformDate($value)
    {
        if (empty($value)) {
            return now();
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return now();
        }
    }
}
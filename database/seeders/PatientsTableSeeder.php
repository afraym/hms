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
        $csvFile = database_path('seeders/30-06-2025.csv');
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

        // Prepare to store repeated entries
        $repeatedEntries = [];
        $processedNationalIds = [];
        $duplicateEntries = [];

        while (($row = fgetcsv($file)) !== false) {
            $rowAssoc = array_combine($header, $row); // Create associative array for easier access

            // Extract patient data
            $patientData = [
                'medical_id' => $rowAssoc['رقم مسلسل المريض'] ?? null,
                'full_name' => $rowAssoc['الاسم رباعي'] ?? null,
                'gender' => $this->verifyGenderFromNationalId($rowAssoc['الرقم القومي'] ?? null),
                'national_id' => $this->cleanNationalId($rowAssoc['الرقم القومي'] ?? null),
                'address' => $rowAssoc['العنوان'] ?? null,
                'phone' => $this->cleanPhoneNumber($rowAssoc['رقم التليفون'] ?? null),
                'date_of_birth' => $this->extractBirthdateFromNationalId($rowAssoc['الرقم القومي'] ?? null),
                'governorate' => $this->extractGovernorateFromNationalId($rowAssoc['الرقم القومي'] ?? null),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Check if the patient already exists by `national_id`
            $existingPatient = Patient::where('national_id', $patientData['national_id'])->first();

            if ($existingPatient) {
                // Use the first medical_id for repeated national_id
                $patientData['medical_id'] = $existingPatient->medical_id;

                // Check if the current medical_id is different from the existing one
                if ($existingPatient->medical_id !== $rowAssoc['رقم مسلسل المريض']) {
                    $repeatedEntries[] = $rowAssoc; // Store the repeated entry
                }

                // Create a new visit for the existing patient
                $visitData = $this->extractVisitData($rowAssoc, $existingPatient->id);
                PatientVisit::create($visitData);
                $insertedVisitsCount++;
            } else {
                // If the patient does not exist, create the patient and the initial visit
                // If the medical_id already exists, generate a unique one
                if (Patient::where('medical_id', $patientData['medical_id'])->exists()) {
                    $patientData['medical_id'] = 'MED-' . uniqid() . '-'. $patientData['medical_id'];
                }
                $newPatient = Patient::create($patientData);
                $visitData = $this->extractVisitData($rowAssoc, $newPatient->id);
                PatientVisit::create($visitData);
                $insertedPatientsCount++;
                $insertedVisitsCount++;
            }

            // Check for duplicate medical_id
            if (Patient::where('medical_id', $patientData['medical_id'])->exists()) {
                $duplicateEntries[] = $rowAssoc; // Store duplicate entry for review
            }

            // Mark the national_id as processed
            $processedNationalIds[] = $patientData['national_id'];
        }

        fclose($file);

        // Save repeated entries to a separate CSV file
        $this->saveRepeatedEntries($header, $repeatedEntries);
        $this->saveDuplicateEntries($header, $duplicateEntries);

        $this->command->info("Successfully seeded {$insertedPatientsCount} patients and {$insertedVisitsCount} visits.");
    }

    private function saveRepeatedEntries($header, $repeatedEntries)
    {
        $outputFile = database_path('seeders/repeated_entries.csv');
        $file = fopen($outputFile, 'w');

        // Write the header row
        fputcsv($file, $header);

        // Write the repeated entries
        foreach ($repeatedEntries as $entry) {
            fputcsv($file, $entry);
        }

        fclose($file);

        $this->command->info("Repeated entries saved to repeated_entries.csv.");
    }

    private function saveDuplicateEntries($header, $duplicateEntries)
    {
        $outputFile = database_path('seeders/duplicate_entries.csv');
        $file = fopen($outputFile, 'w');

        // Write the header row
        fputcsv($file, $header);

        // Write the duplicate entries
        foreach ($duplicateEntries as $entry) {
            fputcsv($file, $entry);
        }

        fclose($file);

        $this->command->info("Duplicate entries saved to duplicate_entries.csv.");
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
        // Remove any unwanted characters
        $cleanedValue = preg_replace('/[^a-zA-Z0-9]/', '', $value);

        // Accept IDs that are either 14 digits or start with a letter (e.g., passports)
        if ((strlen($cleanedValue) === 14 && ctype_digit($cleanedValue)) || preg_match('/^[a-zA-Z]/', $cleanedValue)) {
            return $cleanedValue;
        }

        return null;
    }

    private function cleanPhoneNumber($value)
    {
        // Remove any unwanted characters
        $cleanedValue = preg_replace('/[^0-9]/', '', $value);

        // Ensure the phone number starts with '0'
        // if (!empty($cleanedValue) && $cleanedValue[0] !== '0') {
        //     $cleanedValue = '0' . $cleanedValue;
        // }

        // Validate the phone number length
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
            'companion_phone' => $this->cleanPhoneNumber($rowAssoc['رقم تليفون المرافق'] ?? null),
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

    private function extractBirthdateFromNationalId($nationalId)
    {
        if (strlen($nationalId) === 14) {
            $yearPrefix = substr($nationalId, 0, 1) === '2' ? '19' : '20';
            $year = $yearPrefix . substr($nationalId, 1, 2);
            $month = substr($nationalId, 3, 2);
            $day = substr($nationalId, 5, 2);

            try {
                return \Carbon\Carbon::createFromFormat('Y-m-d', "$year-$month-$day")->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    private function extractGovernorateFromNationalId($nationalId)
    {
        $governorateCodes = [
            '01' => 'القاهرة',
            '02' => 'الإسكندرية',
            '03' => 'بورسعيد',
            '04' => 'السويس',
            '11' => 'دمياط',
            '12' => 'الدقهلية',
            '13' => 'الشرقية',
            '14' => 'القليوبية',
            '15' => 'كفر الشيخ',
            '16' => 'الغربية',
            '17' => 'المنوفية',
            '18' => 'البحيرة',
            '19' => 'الإسماعيلية',
            '21' => 'الجيزة',
            '22' => 'بني سويف',
            '23' => 'الفيوم',
            '24' => 'المنيا',
            '25' => 'أسيوط',
            '26' => 'سوهاج',
            '27' => 'قنا',
            '28' => 'أسوان',
            '29' => 'الأقصر',
            '31' => 'البحر الأحمر',
            '32' => 'الوادي الجديد',
            '33' => 'مطروح',
            '34' => 'شمال سيناء',
            '35' => 'جنوب سيناء',
        ];

        $governorateCode = substr($nationalId, 7, 2);

        return $governorateCodes[$governorateCode] ?? 'غير معروف';
    }

    private function verifyGenderFromNationalId($nationalId)
    {
        if (strlen($nationalId) === 14) {
            $genderDigit = substr($nationalId, 12, 1);
            return $genderDigit % 2 === 0 ? 'female' : 'male';
        }

        return null;
    }
}
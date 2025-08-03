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
        $csvFile = database_path('seeders/29-07-2025.csv');
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

        // First pass: collect all rows and find first visit date for each medical ID
        $allRows = [];
        $medicalIdFirstVisit = [];
        
        while (($row = fgetcsv($file)) !== false) {
            $rowAssoc = array_combine($header, $row);
            $allRows[] = $rowAssoc;
            
            $medicalId = $rowAssoc['رقم مسلسل المريض'] ?? null;
            
            // Extract date from medical ID if it follows the format "11803{yy}{mm}{dd}xxx"
            $firstVisitDate = $this->extractDateFromMedicalId($medicalId);
            
            // Debug: Log the medical ID and extracted date for first few records
            static $debugCount = 0;
            if ($debugCount < 5) {
                $this->command->info("Medical ID: '$medicalId' -> Extracted Date: " . ($firstVisitDate ? $firstVisitDate->format('Y-m-d H:i:s') : 'NULL'));
                $debugCount++;
            }
            
            if ($medicalId && $firstVisitDate) {
                if (!isset($medicalIdFirstVisit[$medicalId])) {
                    $medicalIdFirstVisit[$medicalId] = $firstVisitDate;
                } else {
                    // Keep the earliest visit date using Carbon comparison
                    if ($firstVisitDate->lt($medicalIdFirstVisit[$medicalId])) {
                        $medicalIdFirstVisit[$medicalId] = $firstVisitDate;
                    }
                }
            }
        }
        
        fclose($file);

        $insertedPatientsCount = 0;
        $insertedVisitsCount = 0;

        // Prepare to store repeated entries
        $repeatedEntries = [];
        $processedNationalIds = [];
        $duplicateEntries = [];
        $processedMedicalIds = []; // Track which medical IDs have been processed as patients

        // Second pass: process all rows with correct first visit dates
        foreach ($allRows as $rowAssoc) {
            $currentMedicalId = $rowAssoc['رقم مسلسل المريض'] ?? null;
            
            // Extract patient data using the first visit date for this medical ID
            $patientData = [
                'medical_id' => $currentMedicalId,
                'full_name' => $rowAssoc['الاسم رباعي'] ?? null,
                'gender' => $this->verifyGenderFromNationalId($rowAssoc['الرقم القومي'] ?? null),
                'national_id' => $this->cleanNationalId($rowAssoc['الرقم القومي'] ?? null),
                'address' => $rowAssoc['العنوان'] ?? null,
                'phone' => $this->cleanPhoneNumber($rowAssoc['رقم التليفون'] ?? null),
                'date_of_birth' => $this->extractBirthdateFromNationalId($rowAssoc['الرقم القومي'] ?? null),
                'governorate' => $this->extractGovernorateFromNationalId($rowAssoc['الرقم القومي'] ?? null),
                'created_at' => $medicalIdFirstVisit[$currentMedicalId] ?? now(), // Use first visit date as patient creation date
                'updated_at' => now(),
            ];

            // Check if the patient already exists by `national_id` (only if national_id is not null)
            $existingPatient = null;
            if (!empty($patientData['national_id'])) {
                $existingPatient = Patient::where('national_id', $patientData['national_id'])->first();
            }
            
            // Also check if we already processed this medical_id as a patient
            if (!$existingPatient && !empty($currentMedicalId)) {
                $existingPatient = Patient::where('medical_id', $currentMedicalId)->first();
            }

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
                // But only if we haven't already processed this medical_id
                if (!in_array($currentMedicalId, $processedMedicalIds)) {
                    // If the medical_id already exists in database, generate a unique one
                    if (Patient::where('medical_id', $patientData['medical_id'])->exists()) {
                        $patientData['medical_id'] = 'MED-' . uniqid() . '-'. $patientData['medical_id'];
                    }
                    
                    // Save the patient even if national_id is null
                    try {
                        $newPatient = Patient::create($patientData);
                        $visitData = $this->extractVisitData($rowAssoc, $newPatient->id);
                        PatientVisit::create($visitData);
                        $insertedPatientsCount++;
                        $insertedVisitsCount++;
                        
                        // Mark this medical_id as processed
                        $processedMedicalIds[] = $currentMedicalId;
                    } catch (\Exception $e) {
                        $this->command->error("Failed to create patient with medical_id: {$patientData['medical_id']}. Error: " . $e->getMessage());
                        continue; // Skip this record and continue with the next
                    }
                } else {
                    // Patient with this medical_id already exists, just add a visit
                    $existingPatientByMedicalId = Patient::where('medical_id', $currentMedicalId)->first();
                    if ($existingPatientByMedicalId) {
                        $visitData = $this->extractVisitData($rowAssoc, $existingPatientByMedicalId->id);
                        PatientVisit::create($visitData);
                        $insertedVisitsCount++;
                    }
                }
            }

            // Mark the national_id as processed (only if it's not null)
            if (!empty($patientData['national_id'])) {
                $processedNationalIds[] = $patientData['national_id'];
            }
        }

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
            'visit_at' => $this->transformDate($rowAssoc['وقت الدخول'] ?? null), // Now returns Carbon instance
            'notes' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function getDepartmentId($departmentName)
    {
        if (empty($departmentName)) {
            return null;
        }

        // First, try to find the department as is
        $department = DB::table('departments')->where('name', 'LIKE', "%{$departmentName}%")->first();
        
        if ($department) {
            return $department->id;
        }

        // If not found, clean the department name by removing unwanted text and numbers
        $cleanedName = $departmentName;
        
        // Remove "قسم" and "دخول مشترك" from the name
        $cleanedName = str_replace(['قسم', 'دخول مشترك'], '', $cleanedName);
        
        // Remove numbers from the name
        $cleanedName = preg_replace('/\d+/', '', $cleanedName);
        
        // Trim whitespace
        $cleanedName = trim($cleanedName);
        
        if (!empty($cleanedName)) {
            // Check if the cleaned name exists
            $department = DB::table('departments')->where('name', 'LIKE', "%{$cleanedName}%")->first();
            
            if ($department) {
                return $department->id;
            }

            // If still not found, create a new department with the cleaned name
            $newDepartmentId = DB::table('departments')->insertGetId([
                'name' => $cleanedName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("Created new department: {$cleanedName} with ID: {$newDepartmentId}");
            return $newDepartmentId;
        }

        return null;
    }

    private function transformDate($value)
    {
        if (empty($value)) {
            return now();
        }

        try {
            // Parse the date and return as Carbon instance (Laravel timestamp)
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return now();
        }
    }

    private function extractBirthdateFromNationalId($nationalId)
    {
        if (strlen($nationalId) === 14) {
            // Extract year from positions 2-3 (index 1-2)
            $year = substr($nationalId, 1, 2);
            // Extract month from positions 4-5 (index 3-4)
            $month = substr($nationalId, 3, 2);
            // Extract day from positions 6-7 (index 5-6)
            $day = substr($nationalId, 5, 2);
            
            // Determine century based on first digit
            $century = substr($nationalId, 0, 1);
            $fullYear = '';
            if ($century === '2') {
                $fullYear = '19' . $year;
            } elseif ($century === '3') {
                $fullYear = '20' . $year;
            } else {
                $fullYear = '20' . $year; // Default to 20xx for other cases
            }

            try {
                // Return as Carbon date instance
                return \Carbon\Carbon::createFromFormat('Y-m-d', "$fullYear-$month-$day");
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

        // Extract governorate code from positions 12-13 (index 11-12)
        $governorateCode = substr($nationalId, 11, 2);

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

    private function extractDateFromMedicalId($medicalId)
    {
        if (!$medicalId || strlen($medicalId) < 11) {
            return null;
        }

        try {
            // Check if medical ID follows the format "11803{yy}{mm}{dd}xxx"
            if (substr($medicalId, 0, 5) === '11803') {
                // Extract date parts from positions 5-10 (yy mm dd)
                $year = substr($medicalId, 5, 2);
                $month = substr($medicalId, 7, 2);
                $day = substr($medicalId, 9, 2);
                
                // Validate extracted values
                if (!is_numeric($year) || !is_numeric($month) || !is_numeric($day)) {
                    return null;
                }
                
                // Convert 2-digit year to 4-digit year
                // Assume years 70-99 are 1970-1999, years 00-69 are 2000-2069
                $fullYear = $year >= 70 ? '19' . $year : '20' . $year;
                
                // Validate date values
                if ($month < 1 || $month > 12 || $day < 1 || $day > 31) {
                    return null;
                }
                
                // Validate year range
                if ($fullYear < 1970 || $fullYear > date('Y')) {
                    return null;
                }

                // Return as Carbon date instance (set time to beginning of day)
                return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "$fullYear-$month-$day 00:00:00");
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    public function map($patient): array
    {
        $lastVisit = $patient->visits->sortByDesc('visit_at')->first();
        
        // Calculate visits for the current filter period
        $periodVisits = $this->countVisitsInPeriod($patient);
        
        // Get current/primary department
        $currentDepartment = optional($patient->department)->name;
        
        // Get treatment type from the most recent visit
        $treatmentType = $lastVisit ? ($lastVisit->treatment_type ?? '-') : '-';
        
        return [
            floatval($patient->medical_id),
            $patient->full_name,
            floatval($patient->national_id),
            floatval(str_replace('UHI', '', $patient->uhi_number)),
            $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('Y-m-d') : '',
            $this->calculateAge($patient->date_of_birth),
            $patient->gender == 'male' ? 'ذكر' : 'أنثى',
            $patient->phone,
            $patient->address,
            $patient->governorate,
            $currentDepartment ?: 'غير محدد', // Current department
            $this->translateStatus($patient->status),
            $patient->companion_name,
            $patient->companion_phone,
            $patient->companion_relation,
            \Carbon\Carbon::parse($patient->created_at)->format('Y-m-d H:i'),
            $patient->visits->count(),
            $lastVisit ? \Carbon\Carbon::parse($lastVisit->visit_at)->format('Y-m-d H:i') : '-',
            $periodVisits, // Visits in the filtered period
            $treatmentType, // Treatment type (نوع المعاملة)
            optional($patient->creator)->name
        ];
    }

    public function headings(): array
    {
        return [
            'الرقم الطبي',
            'الاسم',
            'الرقم القومي',
            'رقم التأمين الصحي',
            'تاريخ الميلاد',
            'السن',
            'النوع',
            'رقم الهاتف',
            'العنوان',
            'المحافظة',
            'القسم الحالي', // Current department
            'الحالة',
            'اسم المرافق',
            'هاتف المرافق',
            'صلة القرابة',
            'تاريخ التسجيل',
            'إجمالي الزيارات',
            'آخر زيارة',
            'زيارات الفترة', // Visits in the selected period
            'نوع المعاملة', // Treatment type
            'بواسطة'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            
            // Format ID columns as numbers with 0 decimals
            'A2:A1000' => [
                'numberFormat' => ['formatCode' => '0'] // Medical ID
            ],
            'C2:C1000' => [
                'numberFormat' => ['formatCode' => '0'] // National ID
            ],
            'D2:D1000' => [
                'numberFormat' => ['formatCode' => '0'] // UHI number
            ],
            
            // Highlight department column (K)
            'K1' => [
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => 'E8F5E8'] // Light green for department header
                ]
            ],
            
            // Highlight treatment type column (T)
            'T1' => [
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => 'FFE8CC'] // Light orange for treatment type header
                ]
            ],
            
            // Highlight patients with visits in the selected period (column S)
            'S2:S1000' => [
                'conditional' => [
                    [
                        'type' => 'greaterThan',
                        'value' => 0,
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'E8F5E8'] // Light green
                        ]
                    ]
                ]
            ],
            
            // Highlight patients with no total visits (column Q)
            'Q2:Q1000' => [
                'conditional' => [
                    [
                        'type' => 'equal',
                        'value' => 0,
                        'fill' => [
                            'fillType' => 'solid',
                            'color' => ['rgb' => 'FFE6E6'] // Light red
                        ]
                    ]
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Set right-to-left for Arabic text
                $event->sheet->getDelegate()->setRightToLeft(true);
                
                // Auto-fit columns for better readability
                foreach(range('A','U') as $col) {
                    $event->sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
                
                // Make specific columns wider
                $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(20); // Current department
                $event->sheet->getDelegate()->getColumnDimension('T')->setWidth(25); // Treatment type
                
                // Add borders to all cells with data
                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                $event->sheet->getDelegate()->getStyle('A1:U' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);
                
                // Add explanatory notes at the bottom
                $noteRow = $lastRow + 2;
                $event->sheet->getDelegate()->setCellValue('A' . $noteRow, 
                    'ملاحظات:');
                $event->sheet->getDelegate()->setCellValue('A' . ($noteRow + 1), 
                    '• القسم الحالي: القسم المسجل عليه المريض حالياً');
                $event->sheet->getDelegate()->setCellValue('A' . ($noteRow + 2), 
                    '• نوع المعاملة: نوع العلاج أو الخدمة المقدمة للمريض');
                $event->sheet->getDelegate()->setCellValue('A' . ($noteRow + 3), 
                    '• المرضى الذين لديهم 0 زيارات إجمالية مميزون بخلفية حمراء فاتحة');
                $event->sheet->getDelegate()->setCellValue('A' . ($noteRow + 4), 
                    '• المرضى الذين لديهم زيارات في الفترة المحددة مميزون بخلفية خضراء فاتحة');
                
                // Style the notes
                $event->sheet->getDelegate()->getStyle('A' . $noteRow . ':A' . ($noteRow + 4))->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'color' => ['rgb' => '666666']
                    ]
                ]);
            },
        ];
    }
}
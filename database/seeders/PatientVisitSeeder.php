<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PatientVisit;
use App\Models\Patient;

class PatientVisitSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();

        foreach ($patients as $patient) {
            $visitsCount = rand(1, 4);
            for ($i = 0; $i < $visitsCount; $i++) {
                PatientVisit::create([
                    'patient_id' => $patient->id,
                    'type'       => ['in', 'out'][array_rand(['in', 'out'])],
                    'visit_at'   => now()->subDays(rand(1, 30)),
                    'notes'      => 'ملاحظة زيارة تجريبية للمريض رقم ' . $patient->id,
                ]);
            }
        }
    }
}

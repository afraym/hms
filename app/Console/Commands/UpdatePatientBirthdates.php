<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;
use Carbon\Carbon;

class UpdatePatientBirthdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patients:update-birthdates {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update patient birthdates based on their national ID';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
        }

        $this->info('Starting to update patient birthdates...');
        
        // Get all patients with national IDs
        $patients = Patient::whereNotNull('national_id')
                          ->where('national_id', '!=', '')
                          ->get();

        $this->info("Found {$patients->count()} patients with national IDs");

        $updatedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        $progressBar = $this->output->createProgressBar($patients->count());
        $progressBar->start();

        foreach ($patients as $patient) {
            try {
                $extractedBirthdate = $this->extractBirthdateFromNationalId($patient->national_id);
                
                if ($extractedBirthdate) {
                    // Check if birthdate is different from current one
                    $currentBirthdate = $patient->date_of_birth ? Carbon::parse($patient->date_of_birth) : null;
                    
                    if (!$currentBirthdate || !$currentBirthdate->equalTo($extractedBirthdate)) {
                        if (!$dryRun) {
                            $patient->update(['date_of_birth' => $extractedBirthdate]);
                        }
                        
                        $this->newLine();
                        $this->line("Updated patient ID {$patient->id} ({$patient->full_name}):");
                        $this->line("  National ID: {$patient->national_id}");
                        $this->line("  Old birthdate: " . ($currentBirthdate ? $currentBirthdate->format('Y-m-d') : 'NULL'));
                        $this->line("  New birthdate: {$extractedBirthdate->format('Y-m-d')}");
                        $this->line("  Age: {$extractedBirthdate->age} years");
                        
                        $updatedCount++;
                    } else {
                        $skippedCount++;
                    }
                } else {
                    $this->newLine();
                    $this->warn("Could not extract birthdate for patient ID {$patient->id} with National ID: {$patient->national_id}");
                    $skippedCount++;
                }
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error processing patient ID {$patient->id}: " . $e->getMessage());
                $errorCount++;
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        
        // Summary
        $this->info('Update Summary:');
        $this->table(
            ['Status', 'Count'],
            [
                ['Updated', $updatedCount],
                ['Skipped (same date or invalid)', $skippedCount],
                ['Errors', $errorCount],
                ['Total processed', $patients->count()]
            ]
        );

        if ($dryRun) {
            $this->warn('This was a DRY RUN - no actual changes were made.');
            $this->info('Run without --dry-run to apply changes.');
        }

        return Command::SUCCESS;
    }

    /**
     * Extract birthdate from Egyptian National ID
     *
     * @param string $nationalId
     * @return Carbon|null
     */
    private function extractBirthdateFromNationalId($nationalId)
    {
        if (strlen($nationalId) !== 14 || !ctype_digit($nationalId)) {
            return null;
        }

        // Extract year from positions 2-3 (index 1-2)
        $year = substr($nationalId, 1, 2);
        // Extract month from positions 4-5 (index 3-4)
        $month = substr($nationalId, 3, 2);
        // Extract day from positions 6-7 (index 5-6)
        $day = substr($nationalId, 5, 2);
        
        // Validate extracted values
        if (!is_numeric($year) || !is_numeric($month) || !is_numeric($day)) {
            return null;
        }
        
        // Determine century based on first digit
        $century = substr($nationalId, 0, 1);
        $fullYear = '';
        if ($century === '2') {
            $fullYear = '19' . $year;
        } elseif ($century === '3') {
            $fullYear = '20' . $year;
        } else {
            // For other cases, use logic based on current year
            // Assume years 70-99 are 1970-1999, years 00-69 are 2000-2069
            $currentYear = date('Y');
            $currentCentury = substr($currentYear, 0, 2);
            
            if ($year >= 70) {
                $fullYear = '19' . $year;
            } else {
                $fullYear = '20' . $year;
            }
        }

        // Validate date values
        if ($month < 1 || $month > 12 || $day < 1 || $day > 31) {
            return null;
        }
        
        // Validate year range (reasonable birth year range)
        if ($fullYear < 1900 || $fullYear > date('Y')) {
            return null;
        }

        try {
            // Create and validate the date
            $birthdate = Carbon::createFromFormat('Y-m-d', "$fullYear-$month-$day");
            
            // Additional validation - check if the date is valid
            if ($birthdate->format('Y-m-d') !== "$fullYear-$month-$day") {
                return null;
            }
            
            // Check if the date is not in the future
            if ($birthdate->isFuture()) {
                return null;
            }
            
            return $birthdate;
        } catch (\Exception $e) {
            return null;
        }
    }
}
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate a medical ID in the format: 11803YYMMDDSSS
        $date = now();
        $year = $date->format('y');
        $month = $date->format('m');
        $day = $date->format('d');
        $sequence = str_pad($this->faker->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);
        $medicalId = "11803{$year}{$month}{$day}{$sequence}";

        return [
            'full_name' => $this->faker->firstName, // Egyptian first name
            // 'second_name' => $this->faker->lastName, // Egyptian last name
            // 'third_name' => $this->faker->lastName, // Optional
            // 'fourth_name' => $this->faker->lastName, // Optional
            'email' => $this->faker->unique()->safeEmail, // Egyptian email
            'phone' => $this->faker->numerify('010########'), // Egyptian phone number
            'phone2' => $this->faker->optional()->numerify('011########'), // Optional Egyptian phone number
            'national_id' => $this->faker->unique()->numerify('##############'), // 14-digit Egyptian national ID
            'date_of_birth' => $this->faker->date('Y-m-d', '2005-01-01'), // Date of birth
            'gender' => $this->faker->randomElement(['ذكر', 'أنثى']), // Changed to Arabic
            'medical_id' => $medicalId,
            'uhi_number' => $this->faker->optional()->numerify('UHI#########'),
            'department_id' => \App\Models\Department::inRandomOrder()->first()?->id,
            'status' => $this->faker->randomElement(['admitted', 'waiting', 'discharged']),
        ];
    }
}

<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{
    public function fetchNationalIdInfo(Request $request)
    {
        $nationalId = $request->query('national_id');
        $response = Http::withHeaders([
            'Referer' => 'https://mom.manpower.gov.eg/Personal/Register/Index',
            'Accept' => 'application/json',
        ])->get("https://mom.manpower.gov.eg/Personal/Register/PersonalBasicdataSearchByNationalNo", [
            'NationalNo' => $nationalId,
        ]);

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type'));
    }
 
    // Generate a random Egyptian national ID
    public static function generateEgyptianNationalId($gender = null) {
        // 1. Century (2 for 1900s, 3 for 2000s)
        $century = rand(2, 3);
    
        // 2. Year, Month, Day
        $year = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
        $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT); // To avoid invalid dates
    
        // 3. Governorate code (choose from real codes)
        $governorates = [
            '01','02','03','04','11','12','13','14','15','16','17','18','19','21','22','23','24','25','26','27','28','29','31','32','33','34','35','88'
        ];
        $gov = $governorates[array_rand($governorates)];
    
        // 4. Serial number (3 digits)
        $serial = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
        // 5. Gender digit (odd=male, even=female)
        if ($gender === 'male') {
            $gender_digit = rand(1, 9) | 1; // force odd
        } elseif ($gender === 'female') {
            $gender_digit = rand(0, 8) & ~1; // force even
        } else {
            $gender_digit = rand(0, 9);
        }
    
        return "{$century}{$year}{$month}{$day}{$gov}{$serial}{$gender_digit}";
    }
}

// Example usage:
echo ProxyController::generateEgyptianNationalId('male');   // e.g. 30101011234567
echo ProxyController::generateEgyptianNationalId('female'); // e.g. 30101011234568
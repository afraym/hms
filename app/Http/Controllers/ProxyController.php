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
}
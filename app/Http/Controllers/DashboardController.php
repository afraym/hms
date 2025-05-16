<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\PatientVisit;

class DashboardController extends Controller
{
    public function index()
    {
        $beds_count = Bed::count();
        $empty_beds = Bed::where('status', 'متاح')->count();
        $patients_count = Patient::count();

        // دخول المرضى هذا الشهر
        $patients_in_month = PatientVisit::where('type', 'in')
            ->whereMonth('visit_at', now()->month)
            ->whereYear('visit_at', now()->year)
            ->count();

        // خروج المرضى هذا الشهر
        $patients_out_month = PatientVisit::where('type', 'out')
            ->whereMonth('visit_at', now()->month)
            ->whereYear('visit_at', now()->year)
            ->count();

        // دخول المرضى هذا الأسبوع
        $patients_in_week = PatientVisit::where('type', 'in')
            ->whereBetween('visit_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // خروج المرضى هذا الأسبوع
        $patients_out_week = PatientVisit::where('type', 'out')
            ->whereBetween('visit_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return view('admin.dashboard', compact(
            'beds_count', 'empty_beds', 'patients_count',
            'patients_in_month', 'patients_out_month',
            'patients_in_week', 'patients_out_week'
        ));
    }
}

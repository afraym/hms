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
        $occupied_beds = Bed::where('status', 'محجوز')->count(); // تم تصحيح الحالة
        $patients_in_month = PatientVisit::whereMonth('visit_at', now()->month)
            ->whereYear('visit_at', now()->year)
            ->where('type', 'in')
            ->count();
        $patients_out_month = PatientVisit::whereMonth('visit_at', now()->month)
            ->whereYear('visit_at', now()->year)
            ->where('type', 'out')
            ->count();
        $new_patients = Patient::where('created_at', '>=', now()->subWeek())->count();
        $daily_visits = PatientVisit::whereDate('visit_at', now())->count();

        return view('admin.dashboard', compact(
            'beds_count', 
            'empty_beds', 
            'occupied_beds', 
            'patients_in_month', 
            'patients_out_month', 
            'new_patients', 
            'daily_visits'
        ));
    }
    
    /**
     * Get weekly new patients data for chart
     */
    public function getWeeklyPatientsData()
    {
        $weeklyData = [];
        $days = ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = Patient::whereDate('created_at', $date)->count();
            $weeklyData[] = $count;
        }
        
        return response()->json([
            'labels' => $days,
            'data' => $weeklyData
        ]);
    }
    
    /**
     * Get monthly bed occupancy data for chart
     */
    public function getMonthlyBedsData()
    {
        $monthlyData = [];
        $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 
                  'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            // Count bed assignments in that month
            $count = PatientVisit::whereYear('visit_at', $date->year)
                ->whereMonth('visit_at', $date->month)
                ->where('type', 'in')
                ->whereNotNull('bed_id')
                ->count();
            $monthlyData[] = $count;
        }
        
        return response()->json([
            'labels' => $months,
            'data' => $monthlyData
        ]);
    }
    
    /**
     * Get daily visits data for chart (hourly breakdown)
     */
    public function getDailyVisitsData()
    {
        $hourlyData = [];
        $hours = ['6 ص', '9 ص', '12 ظ', '3 م', '6 م', '9 م', '12 ص'];
        $hourRanges = [
            [6, 9],   // 6-9 AM
            [9, 12],  // 9-12 PM
            [12, 15], // 12-3 PM
            [15, 18], // 3-6 PM
            [18, 21], // 6-9 PM
            [21, 24], // 9-12 AM
            [0, 6]    // 12-6 AM (next day)
        ];
        
        foreach ($hourRanges as $index => $range) {
            if ($range[0] > $range[1]) {
                // Handle overnight range (12-6 AM)
                $count = PatientVisit::whereDate('visit_at', now())
                    ->where(function($query) use ($range) {
                        $query->whereTime('visit_at', '>=', sprintf('%02d:00:00', $range[0]))
                              ->orWhereTime('visit_at', '<', sprintf('%02d:00:00', $range[1]));
                    })
                    ->count();
            } else {
                $count = PatientVisit::whereDate('visit_at', now())
                    ->whereTime('visit_at', '>=', sprintf('%02d:00:00', $range[0]))
                    ->whereTime('visit_at', '<', sprintf('%02d:00:00', $range[1]))
                    ->count();
            }
            $hourlyData[] = $count;
        }
        
        return response()->json([
            'labels' => $hours,
            'data' => $hourlyData
        ]);
    }
}

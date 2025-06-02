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
        $occupied_beds = Bed::where('status', 'مشغول')->count();
        $patients_in_month = PatientVisit::whereMonth('visit_at', now()->month)->count();
        $patients_out_month = PatientVisit::whereMonth('visit_at', now()->month)->where('type', 'out')->count();
        $new_patients = Patient::where('created_at', '>=', now()->subWeek())->count();
        $daily_visits = PatientVisit::whereDate('visit_at', now())->count();

        // Generate chart data dynamically
        $chartBarsData = [
            'labels' => ['الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت', 'الأحد'],
            'data' => $this->getWeeklyPatientData()
        ];

        $chartLineData = [
            'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر'],
            'data' => $this->getMonthlyBedOccupancyData()
        ];

        $chartLineTasksData = [
            'labels' => ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر'],
            'data' => $this->getMonthlyVisitData()
        ];

        return view('admin.dashboard', compact('beds_count', 'empty_beds', 'occupied_beds', 'patients_in_month', 'patients_out_month', 'new_patients', 'daily_visits', 'chartBarsData', 'chartLineData', 'chartLineTasksData'));
    }

    private function getWeeklyPatientData()
    {
        // Get patient data for the last 7 days
        /*
        $weeklyData = PatientVisit::selectRaw('DAYNAME(visit_at) as day, COUNT(*) as count')
            ->whereBetween('visit_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->groupBy('day')
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->pluck('count', 'day');

        // Ensure all days are included
        $days = ['الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت', 'الأحد'];
        return array_map(fn($day) => $weeklyData[$day] ?? 0, $days);
        */

        // Return dummy data for testing
        return [12, 8, 15, 10, 7, 14, 9];
    }

    private function getMonthlyBedOccupancyData()
    {
        // Get bed occupancy data for the last 9 months
        /*
        $monthlyData = Bed::selectRaw('MONTHNAME(updated_at) as month, COUNT(*) as count')
            ->where('status', 'مشغول')
            ->whereBetween('updated_at', [now()->subMonths(9), now()])
            ->groupBy('month')
            ->orderByRaw("FIELD(month, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September')")
            ->pluck('count', 'month');

        // Ensure all months are included
        $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر'];
        return array_map(fn($month) => $monthlyData[$month] ?? 0, $months);
        */

        // Return dummy data for testing
        return [20, 18, 22, 19, 25, 21, 23, 17, 20];
    }

    private function getMonthlyVisitData()
    {
        // Get visit data for the last 9 months
        /*
        $monthlyData = PatientVisit::selectRaw('MONTHNAME(visit_at) as month, COUNT(*) as count')
            ->whereBetween('visit_at', [now()->subMonths(9), now()])
            ->groupBy('month')
            ->orderByRaw("FIELD(month, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September')")
            ->pluck('count', 'month');

        // Ensure all months are included
        $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر'];
        return array_map(fn($month) => $monthlyData[$month] ?? 0, $months);
        */

        // Return dummy data for testing
        return [30, 28, 35, 32, 40, 38, 36, 29, 31];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\PatientVisit;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visits = PatientVisit::with('patient')->latest()->get();
        return view('admin.patient_visit.index', compact('visits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::all();
        return view('admin.patient_visit.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'type'      => 'required|in:in,out',
            'visit_at'  => 'required|date',
            'notes'     => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'bed_id' => 'nullable|exists:beds,id',
            'companion_name' => 'nullable|string|max:255',
            'companion_relation' => 'nullable|string|max:255',
            'companion_phone' => 'nullable|string|max:20',
            'companion_national_id' => 'nullable|string|max:14',
        ]);

        PatientVisit::create($validated);

        return redirect()->route('patient_visits.index')->with('success', 'تم تسجيل الزيارة بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(PatientVisit $patientVisit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PatientVisit $patientVisit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PatientVisit $patientVisit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PatientVisit $patientVisit)
    {
        //
    }

    /**
     * Update companion details for patient visits.
     */
    public function updateCompanionDetails()
    {
        $visits = PatientVisit::all();

        foreach ($visits as $visit) {
            // Check if companion_relation starts with '1' and move it to companion_phone
            if (str_starts_with($visit->companion_relation, '1')) {
                $visit->companion_phone = $visit->companion_relation;
                $visit->companion_relation = null;
            }

            // Check if companion_relation is a valid national ID (14 digits)
            if (preg_match('/^\d{14}$/', $visit->companion_relation)) {
                $visit->companion_national_id = $visit->companion_relation;
                $visit->companion_relation = null;
            }

            // Check if companion_relation contains a time-like value and move it to visit_at
            if (preg_match('/^(\d{1,2}:\d{2})\s?(صباحا|مساء)$/u', $visit->companion_relation, $matches)) {
                $time = $matches[1]; // Extract the time (e.g., "10:06")
                $period = $matches[2]; // Extract the period (e.g., "صباحا" or "مساء")

                // Convert to 24-hour format
                $formattedTime = $this->convertTo24HourFormat($time, $period);

                // Set visit_at with today's date and the converted time
                $visit->visit_at = now()->format('Y-m-d') . ' ' . $formattedTime;
                $visit->companion_relation = null;
            }

            $visit->save();
        }

        return response()->json(['message' => 'Companion details updated successfully']);
    }

    private function convertTo24HourFormat($time, $period)
    {
        // Convert Arabic time period to 24-hour format
        $dateTime = \Carbon\Carbon::createFromFormat('h:i A', $time . ' ' . ($period === 'صباحا' ? 'AM' : 'PM'));
        return $dateTime->format('H:i:s');
    }
}

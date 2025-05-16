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
}

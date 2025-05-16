<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\Bed;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patients = Patient::all();
        return view('admin.patient.index', compact('patients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.patient.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'second_name'   => 'nullable|string|max:255',
            'third_name'    => 'nullable|string|max:255',
            'fourth_name'   => 'nullable|string|max:255',
            'email'         => 'nullable|email|unique:patients,email',
            'phone'         => 'nullable|string|max:20',
            'phone2'        => 'nullable|string|max:20',
            'national_id'   => 'nullable|string|max:50',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|string|max:10',
            'bed_id'        => 'required|exists:beds,id',
        ]);

        $patient = Patient::create($validated);

       Bed::where('id', $validated['bed_id'])->update(['status' => 'محجوز']);

        return response()->json([
            'message' => 'Patient created successfully!',
            'patient' => $patient
        ]);
    }

    /**
     * Store a dummy patient for testing.
     */
    public function storeDummy()
    {
        $dummyData = [
            'name'         => 'John Doe',
            'email'        => 'john.doe@example.com',
            'phone'        => '1234567890',
            'national_id'  => 'A123456789',
            'date_of_birth'=> '1990-01-01',
            'gender'       => 'male',
        ];

        $patient = Patient::create($dummyData);

        return response()->json([
            'message' => 'Dummy patient created successfully!',
            'patient' => $patient
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        //
    }
}

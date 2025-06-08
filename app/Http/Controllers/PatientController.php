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
        $patients = Patient::orderBy('created_at', 'desc')->paginate(50);
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
            'first_name'    => 'max:255',
            'second_name'   => 'nullable|max:255',
            'third_name'    => 'nullable|max:255',
            'fourth_name'   => 'nullable|max:255',
            'email'         => 'nullable|max:255',
            'phone'         => 'nullable|max:20',
            'national_id'   => 'nullable|max:14',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|max:10',
            'bed_id'        => 'nullable|exists:beds,id',
        ]);

        // Check if a patient with the same national ID exists
        $existingPatient = Patient::where('national_id', $validated['national_id'])->first();

        if ($existingPatient) {
            // Store a new visit for the existing patient
            $existingPatient->visits()->create([
                'type'     => 'in', // Assuming it's an "in" visit
                'visit_at' => now(),
                'notes'    => 'Visit recorded for existing patient',
            ]);

            return redirect()->route('patients.show', $existingPatient->id)
                ->with('success', 'تم تسجيل دخول للمريض الموجود بالفعل.');
        }

        // Create a new patient if they don't exist
        $patient = Patient::create($validated);

        // Store the initial visit for the new patient
        $patient->visits()->create([
            'type'     => 'in', // Assuming it's an "in" visit
            'visit_at' => now(),
            'notes'    => 'Initial visit recorded for new patient',
        ]);

        // Update the bed status if a bed is assigned
        if (!empty($validated['bed_id'])) {
            Bed::where('id', $validated['bed_id'])->update(['status' => 'محجوز']);
        }

        return redirect()->route('patients.show', $patient->id)
            ->with('success', 'تم إضافة المريض وتسجيل دخوله بنجاح.');
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
        $patient->load('visits.bed'); // Load visits and related bed data
        return view('admin.patient.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        return view('admin.patient.edit', compact('patient'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name'    => '|max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|max:20',
            'national_id'   => 'nullable|max:50|unique:patients,national_id,' . $patient->id,
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|max:10',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.show', $patient->id)->with('success', 'تم تحديث بيانات المريض بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'تم حذف المريض بنجاح.');
    }

    /**
     * Discharge the specified patient.
     */
    public function discharge(Patient $patient)
    {
        // Check if the patient has a bed assigned
        if ($patient->bed_id) {
            // Update the bed status to "متاح"
            Bed::where('id', $patient->bed_id)->update(['status' => 'متاح']);
        }

        // Store a discharge visit
        $patient->visits()->create([
            'type'     => 'out',
            'visit_at' => now(),
            'notes'    => 'Discharged by system',
        ]);

        // Update the patient's bed_id to null (discharge)
        $patient->update(['bed_id' => null]);

        return redirect()->route('patients.index')->with('success', 'تم تسجيل خروج المريض بنجاح.');
    }

    /**
     * Store a new visit for the patient.
     */
    public function storeVisit(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'type'      => '|in:in,out',
            'visit_at'  => '|date',
            'notes'     => 'nullable',
        ]);

        $visit = $patient->visits()->create($validated);

        return redirect()->route('patients.show', $patient->id)->with('success', 'تم تسجيل الزيارة بنجاح.');
    }

    /**
     * Check if a national ID exists and return patient details if it does.
     */
    public function checkNationalId(Request $request)
    {
        $request->validate([
            'national_id' => 'required|string|max:14',
        ]);

        $patient = Patient::where('national_id', $request->national_id)->first();

        if ($patient) {
            return response()->json([
                'exists' => true,
                'patient' => [
                    'first_name' => $patient->first_name,
                    'second_name' => $patient->second_name,
                    'third_name' => $patient->third_name,
                    'fourth_name' => $patient->fourth_name,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'phone2' => $patient->phone2,
                    'date_of_birth' => $patient->date_of_birth,
                    'gender' => $patient->gender,
                    'address' => $patient->address,
                    'governorate' => $patient->governorate,
                ],
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Search for patients by query.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search patients by first name, second name, phone, or national ID
        $patients = Patient::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('second_name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->orWhere('national_id', 'LIKE', "%{$query}%")
            ->get();

        return view('admin.patient.index', compact('patients'))->with('query', $query);
    }

    /**
     * Ajax search for patients.
     */
    public function ajaxSearch(Request $request)
    {
        $query = $request->input('query');

        // Search patients by first name, second name, phone, or national ID
        $patients = Patient::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('second_name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->orWhere('national_id', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($patients);
    }
}

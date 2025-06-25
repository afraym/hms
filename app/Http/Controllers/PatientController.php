<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\Bed;
use App\Models\Attchment;

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

    private function generateMedicalId()
    {
        $today = now()->format('Y-m-d'); // تاريخ اليوم
        $year = now()->format('y'); // آخر رقمين من السنة
        $month = now()->format('m'); // الشهر
        $day = now()->format('d'); // اليوم

        // البحث عن آخر رقم طبي تم إنشاؤه اليوم
        $lastPatient = Patient::whereDate('created_at', $today)->orderBy('medical_id', 'desc')->first();

        // تحديد الرقم الجديد
        $lastId = $lastPatient ? intval(substr($lastPatient->medical_id, -3)) : 0;
        $newId = str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);

        // توليد الرقم الطبي النهائي
        return "11803{$year}{$month}{$day}{$newId}";
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // توليد الرقم الطبي
        $medicalId = $this->generateMedicalId();

        // جلب الأقسام من جدول الأقسام (Department)
        $departments = \App\Models\Department::all();

        return view('admin.patient.create', compact('medicalId', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $validated = $request->validate([
            'first_name'    => 'nullable|max:255',
            'second_name'   => 'nullable|max:255',
            'third_name'    => 'nullable|max:255',
            'fourth_name'   => 'nullable|max:255',
            'email'         => 'nullable|max:255',
            'phone'         => 'nullable|max:20',
            'national_id'   => 'nullable|max:14',
            'uhi_number'    => 'nullable|max:50',
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|max:10',
            'medical_id'    => 'nullable',
            'bed_id'        => 'nullable',
            'department'    => 'nullable|max:255',
            'companion_name' => 'nullable|max:255',
            'companion_relation' => 'nullable|max:255',
        ]);

        // Check if a patient with the same national ID, medical ID, or UHI number exists
        $existingPatient = Patient::where(function($query) use ($validated) {
            if (!empty($validated['national_id'])) {
                $query->where('national_id', $validated['national_id']);
            }
            if (!empty($validated['medical_id'])) {
                $query->orWhere('medical_id', $validated['medical_id']);
            }
            if (!empty($validated['uhi_number'])) {
                $query->orWhere('uhi_number', $validated['uhi_number']);
            }
        })->first();

        if ($existingPatient) {
            // Store a new visit for the existing patient
            $existingPatient->visits()->create([
                'type'     => 'in', // Assuming it's an "in" visit
                'visit_at' => now(),
                'notes'    => 'تم تسجيل دخول للمريض الموجود بالفعل.',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل دخول للمريض الموجود بالفعل.',
                'patient_id' => $existingPatient->id,
            ]);
        }

        // Create a new patient if they don't exist
        $patient = Patient::create($validated);

        // Update the status based on bed assignment
        $status = !empty($validated['bed_id']) ? 'admitted' : 'waiting';
        $patient->update(['status' => $status]);

        // Store the initial visit for the new patient
        $patient->visits()->create([
            'type'     => 'in', // Assuming it's an "in" visit
            'visit_at' => now(),
            'notes'    => 'اول زيارة للمريض عند تسجيل الدخول.',
        ]);

        // Update the bed status if a bed is assigned
        if (!empty($validated['bed_id'])) {
            Bed::where('id', $validated['bed_id'])->update(['status' => 'محجوز']);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المريض وتسجيل دخوله بنجاح.',
            'patient_id' => $patient->id,
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
        $patient->load(['visits.bed', 'attachments']); // Load visits, related bed data, and attachments
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
            'first_name'    => 'max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|max:20',
            'national_id'   => 'nullable|max:50|unique:patients,national_id,' . $patient->id,
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|max:10',
            'bed_id'        => 'nullable|exists:beds,id', // Ensure bed_id is valid
            'companion_name' => 'nullable|max:255',
            'companion_relation' => 'nullable|max:255',
        ]);

        // Update the patient's data
        $patient->update($validated);

        // Update the status based on bed assignment
        if (!empty($validated['bed_id'])) {
            $patient->update(['status' => 'admitted']);

            // Update the bed status to "محجوز"
            Bed::where('id', $validated['bed_id'])->update(['status' => 'محجوز']);
        } else {
            $patient->update(['status' => 'waiting']);
        }

        return redirect()->route('patients.show', $patient->id)->with('success', 'تم تحديث بيانات المريض بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        // Release bed if patient has one assigned
        if ($patient->bed_id) {
            Bed::where('id', $patient->bed_id)->update(['status' => 'متاح']);
        }
        
        // Store a discharge visit before soft deleting
        $patient->visits()->create([
            'type' => 'out',
            'visit_at' => now(),
            'notes' => 'تم حذف المريض من النظام'
        ]);

        $patient->delete(); // This will now soft delete
        
        return redirect()->route('patients.index')
            ->with('success', 'تم حذف المريض بنجاح مع الاحتفاظ بسجله.');
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

        // Update the patient's bed_id to null and status to "discharged"
        // If using Eloquent relationships, detach the bed properly
        $patient->bed_id = null;
        $patient->status = 'discharged';
        $patient->save();

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
            'national_id' => 'nullable|string|max:14',
            'medical_id'  => 'nullable|string',
            'uhi_number'  => 'nullable|string',
        ]);

        $patient = Patient::where(function($query) use ($request) {
            if ($request->filled('national_id')) {
                $query->orWhere('national_id', $request->national_id);
            }
            if ($request->filled('medical_id')) {
                $query->orWhere('medical_id', $request->medical_id);
            }
            if ($request->filled('uhi_number')) {
                $query->orWhere('uhi_number', $request->uhi_number);
            }
        })->first();

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
                    'national_id' => $patient->national_id,
                    'medical_id' => $patient->medical_id,
                    'uhi_number' => $patient->uhi_number,
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

    /**
     * Print labels for patients.
     */
    public function printLabels(Patient $patient)
    {
        $patients = collect([$patient]); // ضع المريض في Collection ليتعامل معه الـBlade كقائمة
        $repeat = 8; // أو العدد الذي تريده
        return view('admin.patient.labels', compact('patients', 'repeat'));
    }

    /**
     * Sync patients data.
     */
    public function sync(Request $request)
    {
        $pendingActions = $request->input('pendingSync');
        $results = [];
        
        foreach ($pendingActions as $action) {
            try {
                switch ($action['action']) {
                    case 'create':
                        $patient = Patient::create($action['data']);
                        $results[] = [
                            'success' => true,
                            'id' => $action['id'],
                            'synced_id' => $patient->id
                        ];
                        break;
                        
                    case 'update':
                        Patient::find($action['data']['id'])->update($action['data']);
                        $results[] = [
                            'success' => true,
                            'id' => $action['id']
                        ];
                        break;
                }
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'id' => $action['id'],
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return response()->json($results);
    }
}

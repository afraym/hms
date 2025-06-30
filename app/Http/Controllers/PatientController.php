<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\Bed;
use App\Models\Attchment;
use Illuminate\Support\Facades\Cache;
use App\Exports\PatientsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PatientsImport;
use Illuminate\Support\Facades\Log;

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
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:14|unique:patients,national_id',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'medical_id' => 'required|unique:patients,medical_id',
        ]);

        $validated['created_by'] = auth()->id();
        $patient = Patient::create($validated);

        // Create the initial visit
        $visitData = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'bed_id' => 'nullable|exists:beds,id',
            'companion_name' => 'nullable|string|max:255',
            'companion_relation' => 'nullable|string|max:255',
            'companion_phone' => 'nullable|string|max:20',
            'companion_national_id' => 'nullable|string|max:14',
        ]);

        $visitData['patient_id'] = $patient->id;
        $visitData['type'] = 'in';
        $visitData['visit_at'] = now();
        PatientVisit::create($visitData);

        return redirect()->route('patients.index')->with('success', 'تم إضافة المريض وتسجيل دخوله بنجاح.');
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
            'full_name'    => 'max:255',
            'email'         => 'nullable|email|max:255',
            'phone'         => 'nullable|max:20',
            'companion_phone' => 'nullable|max:20', // Updated from phone2
            'companion_name' => 'nullable|max:255',
            'companion_relation' => 'nullable|max:255',
            'companion_national_id' => 'nullable|string|max:14|unique:patients,companion_national_id,' . $patient->id,
            'national_id'   => 'nullable|max:50|unique:patients,national_id,' . $patient->id,
            'date_of_birth' => 'nullable|date',
            'gender'        => 'nullable|max:10',
            'bed_id'        => 'nullable|exists:beds,id', // Ensure bed_id is valid
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

        $patient = Patient::withTrashed()->where(function($query) use ($request) {
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
                'deleted' => $patient->trashed(),
                'patient' => [
                    'id' => $patient->id,
                    'full_name' => $patient->full_name,
                    'national_id' => $patient->national_id,
                    'medical_id' => $patient->medical_id,
                    'uhi_number' => $patient->uhi_number,
                    'email' => $patient->email,
                    'phone' => $patient->phone,
                    'date_of_birth' => $patient->date_of_birth,
                    'gender' => $patient->gender,
                    'status' => $patient->status,
                    'address' => $patient->address,
                    'governorate' => $patient->governorate,
                    'department_id' => $patient->department_id,
                    'bed_id' => $patient->bed_id,
                    'created_at' => $patient->created_at,
                    'companion_name' => $patient->companion_name,
                    'companion_relation' => $patient->companion_relation,
                    'companion_phone' => $patient->companion_phone,
                    'companion_national_id' => $patient->companion_national_id,
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
        $patients = Patient::where('full_name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->orWhere('national_id', 'LIKE', "%{$query}%")
            ->orWhere('medical_id', 'LIKE', "%{$query}%")
            ->orWhere('uhi_number', 'LIKE', "%{$query}%")
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
        $patients = Patient::where('full_name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->orWhere('national_id', 'LIKE', "%{$query}%")
            ->orWhere('medical_id', 'LIKE', "%{$query}%")
            ->orWhere('uhi_number', 'LIKE', "%{$query}%")
            ->get();

        return response()->json($patients);
    }

    /**
     * Print labels for patients.
     */
    public function printLabels(Patient $patient, Request $request)
    {
        $patients = collect([$patient]);
        $repeat = (int) $request->input('labels', 4); // Default to 4 labels total
        $totalCells = 40; // Keep 40 cells (10 rows × 4 columns)
        
        if ($request->ajax()) {
            // For Ajax requests, return just the table content
            return view('admin.patient.labels', compact('patients', 'repeat', 'totalCells'))
                ->renderSections()['content'];
        }
        
        return view('admin.patient.labels', compact('patients', 'repeat', 'totalCells'));
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
                        // إزالة الحقول التي قد تسبب تعارض في الإنشاء
                        unset($action['data']['id']);
                        $patient = Patient::create($action['data']);
                        $results[] = [
                            'success' => true,
                            'id' => $action['id'],
                            'synced_id' => $patient->id
                        ];
                        break;

                    case 'update':
                        $data = $action['data'];
                        $id = $data['id'] ?? null;
                        if ($id) {
                            // إزالة الحقول التي لا يجب تحديثها أو تسبب تعارض
                            unset($data['created_at'], $data['id']);
                            $patient = Patient::find($id);
                            if ($patient) {
                                $patient->update($data);
                                $results[] = [
                                    'success' => true,
                                    'id' => $action['id']
                                ];
                            } else {
                                $results[] = [
                                    'success' => false,
                                    'id' => $action['id'],
                                    'error' => 'Patient not found'
                                ];
                            }
                        } else {
                            $results[] = [
                                'success' => false,
                                'id' => $action['id'],
                                'error' => 'Missing patient id'
                            ];
                        }
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

    /**
     * Upload an attachment for the patient.
     */
    public function uploadAttachment(Request $request, Patient $patient)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'description' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $path = $file->store($patient->getAttachmentPath(), 'public');

        $attachment = $patient->attachments()->create([
            'file' => $path,
            'original_name' => $file->getClientOriginalName(),
            'type' => $file->getClientMimeType(),
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم رفع المرفق بنجاح',
            'attachment' => $attachment
        ]);
    }

    /**
     * Export patients data to Excel.
     */
    public function export() 
    {
        return Excel::download(
            new PatientsExport, 
            'patients-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Display a listing of trashed patients.
     */
    public function trashed()
    {
        $patients = Patient::onlyTrashed()->paginate(50);
        return view('admin.patient.trashed', compact('patients'));
    }

    /**
     * Restore the specified patient.
     */
    public function restore($id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);
        $patient->restore();

        return redirect()->route('patients.trashed')->with('success', 'تم استعادة المريض بنجاح.');
    }

    /**
     * Import patients data from an Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            $file = $request->file('file');
            Excel::import(new PatientsImport, $file);

            return redirect()->route('patients.index')
                ->with('success', 'تم استيراد المرضى بنجاح');
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'فشل الاستيراد: ' . $e->getMessage());
        }
    }

    /**
     * Display the form for importing patients.
     */
    public function importForm()
    {
        return view('admin.patient.import');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Bed;
use App\Models\Attachment;
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
        $patients = Patient::latest()->paginate(100);
        return view('admin.patient.index', compact('patients'));
    }

    private function generateMedicalId()
    {
        $today = now()->format('Y-m-d'); // تاريخ اليوم
        $year = now()->format('y'); // آخر رقمين من السنة
        $month = now()->format('m'); // الشهر
        $day = now()->format('d'); // اليوم

        // البحث عن آخر رقم طبي تم إنشاؤه اليوم
        $lastPatient = Patient::whereDate('created_at', $today)
            ->where('medical_id', 'LIKE', "11803{$year}{$month}{$day}%")
            ->orderBy('medical_id', 'desc')
            ->first();

        // تحديد الرقم الجديد
        $lastId = 0;
        if ($lastPatient) {
            $lastId = intval(substr($lastPatient->medical_id, -3));
        }
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
        $departments = Department::all();
        
        // جلب الأسرة المتاحة
        $beds = Bed::where('status', 'متاح')->get();

        return view('admin.patient.create', compact('medicalId', 'departments', 'beds'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate patient data
        $patientData = $request->validate([
            'full_name' => 'nullable|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:14',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,ذكر,أنثى',
            'medical_id' => 'required|string',
            'uhi_number' => 'nullable|string',
            'address' => 'nullable|string',
            'governorate' => 'nullable|string',
            'notes' => 'nullable|string',
            'blood_type' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'occupation' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        // Convert created_at from datetime-local format to Y-m-d H:i:s format
        if (!empty($patientData['created_at'])) {
            $patientData['created_at'] = \Carbon\Carbon::parse($patientData['created_at'])->format('Y-m-d H:i:s');
        }

        // Validate visit data
        $visitData = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'bed_id' => 'nullable|exists:beds,id',
            'companion_name' => 'nullable|string|max:255',
            'companion_relation' => 'nullable|string|max:255',
            'companion_phone' => 'nullable|string|max:20',
            'companion_national_id' => 'nullable|string|max:14',
            'visit_notes' => 'nullable|string',
        ]);

        // Validate attachments
        $request->validate([
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt'
        ]);

        // Check if patient already exists
        $existingPatient = $this->findExistingPatient($patientData);

        if ($existingPatient) {
            // Patient exists, create new visit using helper method
            $visit = $this->createVisitForExistingPatient(
                $existingPatient, 
                $visitData, 
                $patientData['created_at'] ?? null
            );
            
            // Handle file attachments for existing patient
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $path = $file->store('patient_attachments/' . $existingPatient->id, 'public');
                        
                        $existingPatient->attachments()->create([
                            'file' => $path,
                            'original_name' => $originalName,
                            'type' => $file->getClientMimeType(),
                            'description' => 'مرفق مع زيارة جديدة'
                        ]);
                    }
                }
            }
            
            // Check if it's an AJAX request
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تسجيل زيارة جديدة للمريض الموجود بنجاح.',
                    'patient' => $existingPatient,
                    'visit' => $visit,
                    'new_medical_id' => $this->generateMedicalId()
                ]);
            }
            
            return redirect()->route('patients.show', $existingPatient->id)
                ->with('success', 'تم تسجيل زيارة جديدة للمريض الموجود بنجاح.');
        } else {
            // Create new patient
            $patientData['created_by'] = auth()->id();
            // Status should only change when a bed is allocated
            $patientData['status'] = !empty($visitData['bed_id']) ? 'admitted' : 'waiting';
            
            // Handle created_at if provided
            if (isset($patientData['created_at'])) {
                $patientData['created_at'] = \Carbon\Carbon::parse($patientData['created_at']);
            }
            
            $patient = Patient::create($patientData);

            // Create the initial visit
            $visitData['patient_id'] = $patient->id;
            $visitData['type'] = 'in';
            $visitData['visit_at'] = $patientData['created_at'] ?? now();
            $visitData['notes'] = $visitData['visit_notes'] ?? 'أول زيارة - تسجيل دخول';
            unset($visitData['visit_notes']);
            
            // Create the visit with department_id
            PatientVisit::create($visitData);
            
            // Handle bed assignment
            if (!empty($visitData['bed_id'])) {
                Bed::where('id', $visitData['bed_id'])->update(['status' => 'محجوز']);
            }

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $path = $file->store('patient_attachments/' . $patient->id, 'public');
                        
                        $patient->attachments()->create([
                            'file' => $path,
                            'original_name' => $originalName,
                            'type' => $file->getClientMimeType(),
                            'description' => 'مرفق مع تسجيل المريض'
                        ]);
                    }
                }
            }

            // Check if it's an AJAX request
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إضافة المريض وتسجيل دخوله بنجاح.',
                    'patient' => $patient,
                    'new_medical_id' => $this->generateMedicalId()
                ]);
            }

            return redirect()->route('patients.show', $patient->id)
                ->with('success', 'تم إضافة المريض وتسجيل دخوله بنجاح.');
        }
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
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:14|unique:patients,national_id,' . $patient->id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,ذكر,أنثى',
            'uhi_number' => 'nullable|string|unique:patients,uhi_number,' . $patient->id,
            'address' => 'nullable|string',
            'governorate' => 'nullable|string',
            'notes' => 'nullable|string',
            'blood_type' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'occupation' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Update the patient's data
        $patient->update($validated);

        return redirect()->route('patients.show', $patient->id)->with('success', 'تم تحديث بيانات المريض بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        // Find the latest 'in' visit with a bed assignment and release it
        $lastInVisit = $patient->visits()
            ->where('type', 'in')
            ->whereNotNull('bed_id')
            ->latest('visit_at')
            ->first();

        if ($lastInVisit && $lastInVisit->bed_id) {
            Bed::where('id', $lastInVisit->bed_id)->update(['status' => 'متاح']);
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
        // Find the latest 'in' visit with a bed assignment
        $lastInVisit = $patient->visits()
            ->where('type', 'in')
            ->whereNotNull('bed_id')
            ->latest('visit_at')
            ->first();

        // Release the bed if there was one assigned
        if ($lastInVisit && $lastInVisit->bed_id) {
            Bed::where('id', $lastInVisit->bed_id)->update(['status' => 'متاح']);
        }

        // Store a discharge visit
        $patient->visits()->create([
            'type'     => 'out',
            'visit_at' => now(),
            'notes'    => 'Discharged by system',
        ]);

        // Update the patient's status to "discharged"
        $patient->status = 'discharged';
        $patient->save();

        return redirect()->route('patients.index')->with('success', 'تم تسجيل خروج المريض بنجاح.');
    }

    /**
     * Store a new visit for the patient.
     */
    public function storeVisit(Request $request, Patient $patient)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:in,out',
                'visit_at' => 'required|date',
                'notes' => 'nullable|string',
                'department_id' => 'nullable|exists:departments,id',
                'bed_id' => 'nullable|exists:beds,id',
                'companion_name' => 'nullable|string|max:255',
                'companion_relation' => 'nullable|string|max:255',
                'companion_phone' => 'nullable|string|max:20',
                'companion_national_id' => 'nullable|string|max:14',
            ]);

            $visit = $patient->visits()->create($validated);

            // Update patient status based on visit type and bed allocation
            if ($validated['type'] === 'in') {
                // Status should only change to admitted when a bed is allocated
                if (!empty($validated['bed_id'])) {
                    $patient->update(['status' => 'admitted']);
                    Bed::where('id', $validated['bed_id'])->update(['status' => 'محجوز']);
                } else {
                    $patient->update(['status' => 'waiting']);
                }
            } elseif ($validated['type'] === 'out') {
                $patient->update(['status' => 'discharged']);
                
                // Release any assigned bed from the latest 'in' visit
                $lastInVisit = $patient->visits()
                    ->where('type', 'in')
                    ->whereNotNull('bed_id')
                    ->latest('visit_at')
                    ->first();
                
                if ($lastInVisit && $lastInVisit->bed_id) {
                    Bed::where('id', $lastInVisit->bed_id)->update(['status' => 'متاح']);
                }
            }

            // Check if it's an AJAX request
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تسجيل الزيارة بنجاح.',
                    'visit' => $visit
                ]);
            }

            return redirect()->route('patients.show', $patient->id)->with('success', 'تم تسجيل الزيارة بنجاح.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في التحقق من البيانات.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حفظ الزيارة: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Create a new visit form for existing patient.
     */
    public function createVisit(Patient $patient)
    {
        $departments = Department::all();
        $beds = Bed::where('status', 'متاح')->get();
        
        return view('admin.patient.create-visit', compact('patient', 'departments', 'beds'));
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
            // Get latest visit information
            $latestVisit = $patient->visits()->latest('visit_at')->first();
            
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
                    'notes' => $patient->notes,
                    'blood_type' => $patient->blood_type,
                    'marital_status' => $patient->marital_status,
                    'occupation' => $patient->occupation,
                    'is_active' => $patient->is_active,
                    'created_at' => $patient->created_at,
                    'latest_visit' => $latestVisit ? [
                        'id' => $latestVisit->id,
                        'type' => $latestVisit->type,
                        'visit_at' => $latestVisit->visit_at,
                        'department_id' => $latestVisit->department_id,
                        'bed_id' => $latestVisit->bed_id,
                        'companion_name' => $latestVisit->companion_name,
                        'companion_relation' => $latestVisit->companion_relation,
                        'companion_phone' => $latestVisit->companion_phone,
                        'companion_national_id' => $latestVisit->companion_national_id,
                    ] : null,
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

    /**
     * Create a visit for an existing patient.
     */
    private function createVisitForExistingPatient(Patient $patient, array $visitData, $visitTime = null)
    {
        $visitData['patient_id'] = $patient->id;
        $visitData['type'] = 'in';
        $visitData['visit_at'] = $visitTime ?? now();
        
        // Handle visit notes
        $visitData['notes'] = $visitData['visit_notes'] ?? 'زيارة جديدة';
        unset($visitData['visit_notes']);
        
        // Create the visit with department_id
        $visit = PatientVisit::create($visitData);
        
        // Update patient status only when a bed is allocated
        if (!empty($visitData['bed_id'])) {
            $patient->update(['status' => 'admitted']);
            Bed::where('id', $visitData['bed_id'])->update(['status' => 'محجوز']);
        } else {
            $patient->update(['status' => 'waiting']);
        }
        
        return $visit;
    }

    /**
     * Find existing patient by multiple identifiers.
     */
    private function findExistingPatient(array $patientData)
    {
        $query = Patient::query();
        
        if (!empty($patientData['national_id'])) {
            $patient = $query->where('national_id', $patientData['national_id'])->first();
            if ($patient) return $patient;
        }
        
        if (!empty($patientData['medical_id'])) {
            $patient = Patient::where('medical_id', $patientData['medical_id'])->first();
            if ($patient) return $patient;
        }
        
        if (!empty($patientData['uhi_number'])) {
            $patient = Patient::where('uhi_number', $patientData['uhi_number'])->first();
            if ($patient) return $patient;
        }
        
        return null;
    }

    /**
     * Mark the patient as deceased.
     */
    public function markDeceased(Patient $patient)
    {
        // Find the latest 'in' visit with a bed assignment and release it
        $lastInVisit = $patient->visits()
            ->where('type', 'in')
            ->whereNotNull('bed_id')
            ->latest('visit_at')
            ->first();

        // Release the bed if there was one assigned
        if ($lastInVisit && $lastInVisit->bed_id) {
            Bed::where('id', $lastInVisit->bed_id)->update(['status' => 'متاح']);
        }

        // Store a death record as a visit
        $patient->visits()->create([
            'type'     => 'out',
            'visit_at' => now(),
            'notes'    => 'وفاة',
        ]);

        // Update the patient's status to "deceased"
        $patient->status = 'deceased';
        $patient->save();

        return redirect()->route('patients.index')->with('success', 'تم تسجيل وفاة المريض. رحمه الله وأسكنه فسيح جناته.');
    }

    /**
     * Generate a new medical ID for AJAX requests.
     */
    public function generateNewMedicalId()
    {
        $newMedicalId = $this->generateMedicalId();
        return response()->json(['medical_id' => $newMedicalId]);
    }

    /**
     * Show the form for editing a specific visit.
     */
    public function editVisit(Patient $patient, PatientVisit $visit)
    {
        // Check if this is an AJAX request asking for visit data
        if (request()->wantsJson()) {
            return response()->json([
                'id' => $visit->id,
                'type' => $visit->type,
                'visit_at' => $visit->visit_at,
                'department_id' => $visit->department_id,
                'bed_id' => $visit->bed_id,
                'companion_name' => $visit->companion_name,
                'companion_relation' => $visit->companion_relation,
                'companion_phone' => $visit->companion_phone,
                'companion_national_id' => $visit->companion_national_id,
                'notes' => $visit->notes,
            ]);
        }

        $departments = Department::all();
        $beds = Bed::where('status', 'متاح')->orWhere('id', $visit->bed_id)->get();
        
        return view('admin.patient.edit-visit', compact('patient', 'visit', 'departments', 'beds'));
    }

    /**
     * Update the specified visit in storage.
     */
    public function updateVisit(Request $request, Patient $patient, PatientVisit $visit)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:in,out',
                'visit_at' => 'required|date',
                'notes' => 'nullable|string',
                'department_id' => 'nullable|exists:departments,id',
                'bed_id' => 'nullable|exists:beds,id',
                'companion_name' => 'nullable|string|max:255',
                'companion_relation' => 'nullable|string|max:255',
                'companion_phone' => 'nullable|string|max:20',
                'companion_national_id' => 'nullable|string|max:14',
            ]);

            // Handle bed changes
            $oldBedId = $visit->bed_id;
            $newBedId = $validated['bed_id'] ?? null;

            // If bed changed, update bed statuses
            if ($oldBedId && $oldBedId != $newBedId) {
                Bed::where('id', $oldBedId)->update(['status' => 'متاح']);
            }
            if ($newBedId && $oldBedId != $newBedId) {
                Bed::where('id', $newBedId)->update(['status' => 'محجوز']);
            }

            // Update the visit
            $visit->update($validated);

            // Update patient status based on visit type and bed allocation
            if ($validated['type'] === 'in') {
                $patient->update(['status' => !empty($validated['bed_id']) ? 'admitted' : 'waiting']);
            } elseif ($validated['type'] === 'out') {
                $patient->update(['status' => 'discharged']);
            }

            // Check if it's an AJAX request
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث بيانات الزيارة بنجاح.',
                    'visit' => $visit
                ]);
            }

            return redirect()->route('patients.show', $patient->id)->with('success', 'تم تحديث بيانات الزيارة بنجاح.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في التحقق من البيانات.',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تعديل الزيارة بنجاح.',
                    // 'success' => false,
                    // 'message' => 'حدث خطأ أثناء تحديث الزيارة: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Delete the specified visit.
     */
    public function deleteVisit(Request $request, Patient $patient, PatientVisit $visit)
    {
        try {
            // Release the bed if it was assigned
            if ($visit->bed_id) {
                Bed::where('id', $visit->bed_id)->update(['status' => 'متاح']);
            }

            // Delete the visit
            $visit->delete();

            // Update patient status based on remaining visits
            $lastVisit = $patient->visits()->latest('visit_at')->first();
            if ($lastVisit) {
                if ($lastVisit->type === 'in') {
                    $patient->update(['status' => $lastVisit->bed_id ? 'admitted' : 'waiting']);
                } else {
                    $patient->update(['status' => 'discharged']);
                }
            } else {
                $patient->update(['status' => 'waiting']);
            }

            // Check if it's an AJAX request
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف الزيارة بنجاح.'
                ]);
            }

            return redirect()->route('patients.show', $patient->id)->with('success', 'تم حذف الزيارة بنجاح.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف الزيارة: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Get visit data for AJAX requests.
     */
    public function getVisit(PatientVisit $visit)
    {
        return response()->json([
            'id' => $visit->id,
            'type' => $visit->type,
            'visit_at' => $visit->visit_at,
            'department_id' => $visit->department_id,
            'bed_id' => $visit->bed_id,
            'companion_name' => $visit->companion_name,
            'companion_relation' => $visit->companion_relation,
            'companion_phone' => $visit->companion_phone,
            'companion_national_id' => $visit->companion_national_id,
            'notes' => $visit->notes,
        ]);
    }
}

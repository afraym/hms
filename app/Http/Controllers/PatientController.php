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
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'latest_visit'); // Default to latest visit
        
        $query = Patient::with(['visits' => function($query) {
            $query->latest('visit_at');
        }]);
        
        switch ($sortBy) {
            case 'latest_visit':
                // Sort by latest visit date (patients with recent visits first)
                $patients = $query->leftJoin('patient_visits', 'patients.id', '=', 'patient_visits.patient_id')
                                 ->select('patients.*')
                                 ->selectRaw('MAX(patient_visits.visit_at) as latest_visit_date')
                                 ->groupBy('patients.id')
                                 ->orderByDesc('latest_visit_date')
                                 ->orderByDesc('patients.created_at') // Secondary sort by registration date
                                 ->paginate(100);
                break;
                
            case 'oldest_visit':
                // Sort by oldest visit date (patients who haven't visited recently)
                $patients = $query->leftJoin('patient_visits', 'patients.id', '=', 'patient_visits.patient_id')
                                 ->select('patients.*')
                                 ->selectRaw('MAX(patient_visits.visit_at) as latest_visit_date')
                                 ->groupBy('patients.id')
                                 ->orderBy('latest_visit_date')
                                 ->orderBy('patients.created_at')
                                 ->paginate(100);
                break;
                
            case 'no_visits':
                // Patients with no visits
                $patients = $query->whereDoesntHave('visits')
                                 ->orderByDesc('patients.created_at')
                                 ->paginate(100);
                break;
                
            case 'registration_date':
                // Sort by registration date (newest first)
                $patients = $query->orderByDesc('patients.created_at')
                                 ->paginate(100);
                break;
                
            case 'name':
                // Sort by patient name
                $patients = $query->orderBy('patients.full_name')
                                 ->paginate(100);
                break;
                
            default:
                $patients = $query->latest()->paginate(100);
        }
        
        // Add latest visit information to each patient
        $patients->getCollection()->transform(function ($patient) {
            $latestVisit = $patient->visits->first();
            $patient->latest_visit_date = $latestVisit ? $latestVisit->visit_at : null;
            $patient->latest_visit_type = $latestVisit ? $latestVisit->type : null;
            return $patient;
        });
        
        return view('admin.patient.index', compact('patients', 'sortBy'));
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
        $messages = [
            'full_name.required' => 'الاسم الكامل مطلوب',
            'full_name.string' => 'الاسم الكامل يجب أن يكون نص',
            'full_name.max' => 'الاسم الكامل يجب ألا يتجاوز 255 حرف',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرف',
            'phone.string' => 'رقم الهاتف يجب أن يكون نص',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرف',
            'national_id.string' => 'الرقم القومي يجب أن يكون نص',
            'national_id.max' => 'الرقم القومي يجب ألا يتجاوز 14 حرف',
            'national_id.unique' => 'الرقم القومي موجود مسبقاً',
            'date_of_birth.date' => 'تاريخ الميلاد غير صحيح',
            'gender.in' => 'الجنس غير صحيح',
            'uhi_number.string' => 'رقم التأمين الصحي يجب أن يكون نص',
            'uhi_number.unique' => 'رقم التأمين الصحي موجود مسبقاً',
            'medical_id.required' => 'رقم الملف الطبي مطلوب',
            'medical_id.string' => 'رقم الملف الطبي يجب أن يكون نص',
            'medical_id.unique' => 'رقم الملف الطبي موجود مسبقاً',
            'address.string' => 'العنوان يجب أن يكون نص',
            'governorate.string' => 'المحافظة يجب أن تكون نص',
            'notes.string' => 'الملاحظات يجب أن تكون نص',
            'blood_type.string' => 'فصيلة الدم يجب أن تكون نص',
            'marital_status.string' => 'الحالة الاجتماعية يجب أن تكون نص',
            'occupation.string' => 'المهنة يجب أن تكون نص',
            'is_active.boolean' => 'حالة النشاط يجب أن تكون صحيح أو خطأ',
            'companion_name.string' => 'اسم المرافق يجب أن يكون نص',
            'companion_name.max' => 'اسم المرافق يجب ألا يتجاوز 255 حرف',
            'companion_phone.string' => 'هاتف المرافق يجب أن يكون نص',
            'companion_phone.max' => 'هاتف المرافق يجب ألا يتجاوز 20 حرف',
            'companion_relation.string' => 'صلة القرابة يجب أن تكون نص',
            'companion_relation.max' => 'صلة القرابة يجب ألا تتجاوز 255 حرف',
            'companion_national_id.string' => 'الرقم القومي للمرافق يجب أن يكون نص',
            'companion_national_id.max' => 'الرقم القومي للمرافق يجب ألا يتجاوز 14 حرف',
            'type.required' => 'نوع الزيارة مطلوب',
            'type.in' => 'نوع الزيارة غير صحيح',
            'visit_at.required' => 'تاريخ ووقت الزيارة مطلوب',
            'visit_at.date' => 'تاريخ ووقت الزيارة غير صحيح',
            'department_id.exists' => 'القسم المحدد غير موجود',
            'bed_id.exists' => 'السرير المحدد غير موجود',
            'attachments.*.file' => 'المرفق يجب أن يكون ملف',
            'attachments.*.max' => 'حجم المرفق يجب ألا يتجاوز 10 ميجابايت',
            'attachments.*.mimes' => 'نوع المرفق غير مدعوم',
        ];

        $validated = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:14',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,ذكر,أنثى',
            'uhi_number' => 'nullable|string',
            'medical_id' => 'required|string',
            'address' => 'nullable|string',
            'governorate' => 'nullable|string',
            'notes' => 'nullable|string',
            'blood_type' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'occupation' => 'nullable|string',
            'is_active' => 'boolean',
            'companion_name' => 'nullable|string|max:255',
            'companion_phone' => 'nullable|string|max:20',
            'companion_relation' => 'nullable|string|max:255',
            'companion_national_id' => 'nullable|string|max:14',
            // 'type' => 'required|in:in,out',
            // 'visit_at' => 'required|date',
            'department_id' => 'nullable|exists:departments,id',
            'bed_id' => 'nullable|exists:beds,id',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif',
        ], $messages);

        // Convert created_at from datetime-local format to Y-m-d H:i:s format
        // Prepare patient data for existence check and created_at conversion
        $patientData = $validated;

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
        $messages = [
        'full_name.required' => 'الاسم الكامل مطلوب',
        'full_name.string' => 'الاسم الكامل يجب أن يكون نص',
        'full_name.max' => 'الاسم الكامل يجب ألا يتجاوز 255 حرف',
        'email.email' => 'البريد الإلكتروني غير صحيح',
        'email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرف',
        'phone.string' => 'رقم الهاتف يجب أن يكون نص',
        'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرف',
        'national_id.string' => 'الرقم القومي يجب أن يكون نص',
        'national_id.max' => 'الرقم القومي يجب ألا يتجاوز 14 حرف',
        'national_id.unique' => 'الرقم القومي موجود مسبقاً',
        'date_of_birth.date' => 'تاريخ الميلاد غير صحيح',
        'gender.in' => 'الجنس غير صحيح',
        'uhi_number.string' => 'رقم التأمين الصحي يجب أن يكون نص',
        'uhi_number.unique' => 'رقم التأمين الصحي موجود مسبقاً',
        'medical_id.required' => 'رقم الملف الطبي مطلوب',
        'medical_id.string' => 'رقم الملف الطبي يجب أن يكون نص',
        'medical_id.unique' => 'رقم الملف الطبي موجود مسبقاً',
        'address.string' => 'العنوان يجب أن يكون نص',
        'governorate.string' => 'المحافظة يجب أن تكون نص',
        'notes.string' => 'الملاحظات يجب أن تكون نص',
        'blood_type.string' => 'فصيلة الدم يجب أن تكون نص',
        'marital_status.string' => 'الحالة الاجتماعية يجب أن تكون نص',
        'occupation.string' => 'المهنة يجب أن تكون نص',
        'is_active.boolean' => 'حالة النشاط يجب أن تكون صحيح أو خطأ',
        'companion_name.string' => 'اسم المرافق يجب أن يكون نص',
        'companion_name.max' => 'اسم المرافق يجب ألا يتجاوز 255 حرف',
        'companion_phone.string' => 'هاتف المرافق يجب أن يكون نص',
        'companion_phone.max' => 'هاتف المرافق يجب ألا يتجاوز 20 حرف',
        'companion_relation.string' => 'صلة القرابة يجب أن تكون نص',
        'companion_relation.max' => 'صلة القرابة يجب ألا تتجاوز 255 حرف',
        'companion_national_id.string' => 'الرقم القومي للمرافق يجب أن يكون نص',
        'companion_national_id.max' => 'الرقم القومي للمرافق يجب ألا يتجاوز 14 حرف',
    ];
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:14|unique:patients,national_id,' . $patient->id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,ذكر,أنثى',
            'uhi_number' => 'nullable|string|unique:patients,uhi_number,' . $patient->id,
            'medical_id' => 'required|string|unique:patients,medical_id,' . $patient->id,
            'address' => 'nullable|string',
            'governorate' => 'nullable|string',
            'notes' => 'nullable|string',
            'blood_type' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'occupation' => 'nullable|string',
            'is_active' => 'boolean',
            // Add companion fields to validation
            // 'companion_name' => 'nullable|string|max:255',
            // 'companion_phone' => 'nullable|string|max:20',
            // 'companion_relation' => 'nullable|string|max:255',
            // 'companion_national_id' => 'nullable|string|max:14',
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
    public function export(Request $request) 
    {
        $dateFilter = $request->get('date_filter');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Validate custom date range
        if ($dateFilter === 'custom') {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ], [
                'start_date.required' => 'تاريخ البداية مطلوب',
                'start_date.date' => 'تاريخ البداية غير صحيح',
                'end_date.required' => 'تاريخ النهاية مطلوب',
                'end_date.date' => 'تاريخ النهاية غير صحيح',
                'end_date.after_or_equal' => 'تاريخ النهاية يجب أن يكون بعد أو مساوٍ لتاريخ البداية',
            ]);
        }
        
        // Generate filename based on filter
        $filename = 'patients-';
        
        switch ($dateFilter) {
            case 'today':
                $filename .= 'today-' . now()->format('Y-m-d');
                break;
            case 'yesterday':
                $filename .= 'yesterday-' . now()->subDay()->format('Y-m-d');
                break;
            case 'this_week':
                $filename .= 'this-week-' . now()->format('Y-m-d');
                break;
            case 'this_month':
                $filename .= 'this-month-' . now()->format('Y-m');
                break;
            case 'last_month':
                $filename .= 'last-month-' . now()->subMonth()->format('Y-m');
                break;
            case 'this_year':
                $filename .= 'this-year-' . now()->format('Y');
                break;
            case 'last_year':
                $filename .= 'last-year-' . now()->subYear()->format('Y');
                break;
            case 'custom':
                $filename .= 'custom-' . $startDate . '-to-' . $endDate;
                break;
            default:
                $filename .= 'all-' . now()->format('Y-m-d');
        }
        
        $filename .= '.xlsx';
        
        return Excel::download(
            new PatientsExport($dateFilter, $startDate, $endDate), 
            $filename
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

    /**
     * Permanently delete the specified patient.
     */
    public function forceDelete($id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);
        
        // Delete all related visits
        $patient->visits()->delete();
        
        // Delete all related attachments
        foreach ($patient->attachments as $attachment) {
            // Delete the actual file
            if (file_exists(storage_path('app/public/' . $attachment->file))) {
                unlink(storage_path('app/public/' . $attachment->file));
            }
            $attachment->delete();
        }
        
        // Permanently delete the patient
        $patient->forceDelete();
        
        return redirect()->route('patients.trashed')->with('success', 'تم حذف المريض نهائياً من النظام.');
    }

    /**
     * Show the form for editing a trashed patient.
     */
    public function editTrashed($id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);
        
        if (!$patient->trashed()) {
            return redirect()->route('patients.edit', $patient->id);
        }
        
        return view('admin.patient.edit-trashed', compact('patient'));
    }

    /**
     * Update a trashed patient.
     */
    public function updateTrashed(Request $request, $id)
    {
        $patient = Patient::withTrashed()->findOrFail($id);
        
        $messages = [
            'full_name.required' => 'الاسم الكامل مطلوب',
            'full_name.string' => 'الاسم الكامل يجب أن يكون نص',
            'full_name.max' => 'الاسم الكامل يجب ألا يتجاوز 255 حرف',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرف',
            'phone.string' => 'رقم الهاتف يجب أن يكون نص',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرف',
            'national_id.string' => 'الرقم القومي يجب أن يكون نص',
            'national_id.max' => 'الرقم القومي يجب ألا يتجاوز 14 حرف',
            'national_id.unique' => 'الرقم القومي موجود مسبقاً',
            'date_of_birth.date' => 'تاريخ الميلاد غير صحيح',
            'gender.in' => 'الجنس غير صحيح',
            'uhi_number.string' => 'رقم التأمين الصحي يجب أن يكون نص',
            'uhi_number.unique' => 'رقم التأمين الصحي موجود مسبقاً',
            'medical_id.required' => 'رقم الملف الطبي مطلوب',
            'medical_id.string' => 'رقم الملف الطبي يجب أن يكون نص',
            'medical_id.unique' => 'رقم الملف الطبي موجود مسبقاً',
            'address.string' => 'العنوان يجب أن يكون نص',
            'governorate.string' => 'المحافظة يجب أن تكون نص',
            'notes.string' => 'الملاحظات يجب أن تكون نص',
            'blood_type.string' => 'فصيلة الدم يجب أن تكون نص',
            'marital_status.string' => 'الحالة الاجتماعية يجب أن تكون نص',
            'occupation.string' => 'المهنة يجب أن تكون نص',
            'companion_name.string' => 'اسم المرافق يجب أن يكون نص',
            'companion_name.max' => 'اسم المرافق يجب ألا يتجاوز 255 حرف',
            'companion_phone.string' => 'هاتف المرافق يجب أن يكون نص',
            'companion_phone.max' => 'هاتف المرافق يجب ألا يتجاوز 20 حرف',
            'companion_relation.string' => 'صلة القرابة يجب أن تكون نص',
            'companion_relation.max' => 'صلة القرابة يجب ألا تتجاوز 255 حرف',
            'companion_national_id.string' => 'الرقم القومي للمرافق يجب أن يكون نص',
            'companion_national_id.max' => 'الرقم القومي للمرافق يجب ألا يتجاوز 14 حرف',
        ];

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:14|unique:patients,national_id,' . $patient->id,
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,ذكر,أنثى',
            'uhi_number' => 'nullable|string|unique:patients,uhi_number,' . $patient->id,
            'medical_id' => 'required|string|unique:patients,medical_id,' . $patient->id,
            'address' => 'nullable|string',
            'governorate' => 'nullable|string',
            'notes' => 'nullable|string',
            'blood_type' => 'nullable|string',
            'marital_status' => 'nullable|string',
            'occupation' => 'nullable|string',
            'companion_name' => 'nullable|string|max:255',
            'companion_phone' => 'nullable|string|max:20',
            'companion_relation' => 'nullable|string|max:255',
            'companion_national_id' => 'nullable|string|max:14',
        ], $messages);

        // Update the patient's data
        $patient->update($validated);

        return redirect()->route('patients.trashed')->with('success', 'تم تحديث بيانات المريض المحذوف بنجاح.');
    }

    /**
     * Upload attachments for a patient.
     */
    public function uploadAttachments(Request $request, Patient $patient)
    {
        try {
            // Debug: Log what we're receiving
            \Log::info('Upload request data:', [
                'files' => $request->hasFile('attachments'),
                'file_count' => $request->hasFile('attachments') ? count($request->file('attachments')) : 0,
                'all_files' => $request->allFiles(),
                'request_data' => $request->all()
            ]);

            $messages = [
                'attachments.required' => 'يرجى اختيار ملف واحد على الأقل',
                'attachments.*.file' => 'الملف المرفوع غير صحيح',
                'attachments.*.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت',
                'attachments.*.mimes' => 'نوع الملف غير مسموح. الأنواع المسموحة: pdf, doc, docx, jpg, jpeg, png, gif',
            ];

            // Check if files exist first
            if (!$request->hasFile('attachments')) {
                return redirect()->back()->withErrors(['attachments' => 'يرجى اختيار ملف واحد على الأقل'])->withInput();
            }

            $validated = $request->validate([
                'attachments' => 'required|array|min:1',
                'attachments.*' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png,gif',
                'description' => 'nullable|string|max:255',
            ], $messages);

            $uploadedFiles = [];
            $errors = [];

            foreach ($request->file('attachments') as $index => $file) {
                try {
                    // Check if file is valid
                    if (!$file->isValid()) {
                        $errors[] = "الملف رقم " . ($index + 1) . " غير صالح";
                        continue;
                    }

                    // Generate unique filename
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $filename = pathinfo($originalName, PATHINFO_FILENAME);
                    $uniqueName = $filename . '_' . time() . '_' . uniqid() . '.' . $extension;
                    
                    // Store the file
                    $path = $file->storeAs('patient_attachments', $uniqueName, 'public');
                    
                    if (!$path) {
                        $errors[] = "فشل في حفظ الملف: " . $originalName;
                        continue;
                    }

                    // Create attachment record
                    $attachment = $patient->attachments()->create([
                        'original_name' => $originalName,
                        'filename' => $uniqueName,
                        'file_path' => $path,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                        'type' => $this->getAttachmentType($extension),
                        'description' => $validated['description'] ?? null,
                        'uploaded_by' => auth()->id(),
                    ]);

                    $uploadedFiles[] = $attachment;

                } catch (\Exception $e) {
                    \Log::error('Error uploading individual file: ' . $e->getMessage());
                    $errors[] = "خطأ في رفع الملف: " . ($file->getClientOriginalName() ?? 'غير معروف');
                }
            }

            if (empty($uploadedFiles)) {
                $errorMessage = 'فشل في رفع جميع الملفات.';
                if (!empty($errors)) {
                    $errorMessage .= ' الأخطاء: ' . implode(', ', $errors);
                }
                return redirect()->back()->with('error', $errorMessage);
            }

            $message = count($uploadedFiles) === 1 
                ? 'تم رفع الملف بنجاح' 
                : 'تم رفع ' . count($uploadedFiles) . ' ملف بنجاح';

            if (!empty($errors)) {
                $message .= '. أخطاء: ' . implode(', ', $errors);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error in uploadAttachments: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء رفع الملفات: ' . $e->getMessage());
        }
    }

    /**
     * Delete a patient attachment.
     */
    public function deleteAttachment(Patient $patient, $attachmentId)
    {
        try {
            $attachment = $patient->attachments()->findOrFail($attachmentId);
            
            // Delete the actual file
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            
            // Delete the database record
            $attachment->delete();
            
            return redirect()->back()->with('success', 'تم حذف المرفق بنجاح');
            
        } catch (\Exception $e) {
            \Log::error('Error deleting attachment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف المرفق');
        }
    }

    /**
     * Determine attachment type based on file extension.
     */
    private function getAttachmentType($extension)
    {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $documentTypes = ['pdf', 'doc', 'docx'];
        
        if (in_array(strtolower($extension), $imageTypes)) {
            return 'image';
        } elseif (in_array(strtolower($extension), $documentTypes)) {
            return 'document';
        }
        
        return 'other';
    }
}

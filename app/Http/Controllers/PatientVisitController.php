<?php

namespace App\Http\Controllers;

use App\Models\PatientVisit;
use App\Models\Patient;
use App\Models\Department;
use App\Models\Bed;
use Illuminate\Http\Request;

class PatientVisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $visits = PatientVisit::with(['patient', 'department', 'bed'])
            ->latest('visit_at')
            ->paginate(50);
        return view('admin.patient_visit.index', compact('visits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::where('is_active', true)->get();
        $departments = Department::all();
        $beds = Bed::where('status', 'متاح')->get();
        
        return view('admin.patient_visit.create', compact('patients', 'departments', 'beds'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
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

        $visit = PatientVisit::create($validated);
        
        // Update patient status based on visit type
        $patient = Patient::find($validated['patient_id']);
        if ($validated['type'] === 'in') {
            $patient->update(['status' => 'admitted']);
            
            // If bed is assigned, mark it as occupied
            if (!empty($validated['bed_id'])) {
                Bed::where('id', $validated['bed_id'])->update(['status' => 'محجوز']);
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

        return redirect()->route('patient_visits.index')->with('success', 'تم تسجيل الزيارة بنجاح');
    }

    /**
     * Store a visit from patient edit page (AJAX).
     */
    public function storeFromPatient(Request $request, Patient $patient)
    {
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

        $validated['patient_id'] = $patient->id;
        $visit = PatientVisit::create($validated);
        
        // Update patient status based on visit type
        if ($validated['type'] === 'in') {
            // Only set to admitted if a bed is allocated, otherwise keep as waiting
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

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الزيارة بنجاح',
            'visit' => $visit
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(PatientVisit $patientVisit)
    {
        $patientVisit->load(['patient', 'department', 'bed']);
        
        // Check if it's an AJAX request for JSON response
        if (request()->wantsJson()) {
            return response()->json([
                'id' => $patientVisit->id,
                'patient_id' => $patientVisit->patient_id,
                'type' => $patientVisit->type,
                'visit_at' => $patientVisit->visit_at->format('Y-m-d\TH:i:s'),
                'notes' => $patientVisit->notes,
                'department_id' => $patientVisit->department_id,
                'bed_id' => $patientVisit->bed_id,
                'companion_name' => $patientVisit->companion_name,
                'companion_relation' => $patientVisit->companion_relation,
                'companion_phone' => $patientVisit->companion_phone,
                'companion_national_id' => $patientVisit->companion_national_id,
            ]);
        }
        
        return view('admin.patient_visit.show', compact('patientVisit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PatientVisit $patientVisit)
    {
        $patients = Patient::where('is_active', true)->get();
        $departments = Department::all();
        $beds = Bed::all(); // Show all beds for editing
        
        return view('admin.patient_visit.edit', compact('patientVisit', 'patients', 'departments', 'beds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PatientVisit $patientVisit)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
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

        // Handle bed status changes
        $oldBedId = $patientVisit->bed_id;
        $newBedId = $validated['bed_id'];
        
        if ($oldBedId && $oldBedId !== $newBedId) {
            // Release old bed
            Bed::where('id', $oldBedId)->update(['status' => 'متاح']);
        }
        
        if ($newBedId && $validated['type'] === 'in') {
            // Assign new bed
            Bed::where('id', $newBedId)->update(['status' => 'محجوز']);
        }

        $patientVisit->update($validated);

        // Update patient status based on visit type and bed allocation
        $patient = $patientVisit->patient;
        if ($validated['type'] === 'in') {
            // Only set to admitted if a bed is allocated, otherwise keep as waiting
            if (!empty($validated['bed_id'])) {
                $patient->update(['status' => 'admitted']);
            } else {
                $patient->update(['status' => 'waiting']);
            }
        } elseif ($validated['type'] === 'out') {
            $patient->update(['status' => 'discharged']);
        }

        // Check if it's an AJAX request for JSON response
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الزيارة بنجاح',
                'visit' => $patientVisit
            ]);
        }

        return redirect()->route('patient_visits.index')->with('success', 'تم تحديث الزيارة بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PatientVisit $patientVisit)
    {
        // Release bed if assigned
        if ($patientVisit->bed_id && $patientVisit->type === 'in') {
            Bed::where('id', $patientVisit->bed_id)->update(['status' => 'متاح']);
        }
        
        $patientVisit->delete();

        return redirect()->route('patient_visits.index')->with('success', 'تم حذف الزيارة بنجاح');
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

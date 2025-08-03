<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Illuminate\Http\Request;

class BedController extends Controller
{
    /**
     * Display a listing of the beds.
     */
    public function index(Request $request)
    {
        $query = Bed::with('department');
        
        // Filter by department if provided
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        
        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $beds = $query->get();
        
        // Return JSON for API requests
        if ($request->wantsJson()) {
            return response()->json($beds);
        }
        
        return view('admin.beds.index', compact('beds'));
    }

    /**
     * Show the form for creating a new bed.
     */
    public function create()
    {
        $departments = \App\Models\Department::all(); // Fetch all departments
        return view('admin.beds.create', compact('departments'));
    }

    /**
     * Store a newly created bed in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bed_number' => 'required|string|max:255',
            'room_number' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'status' => 'required|in:متاح,محجوز,صيانة',
        ]);

        Bed::create($validated);

        return redirect()->route('beds.index')->with('success', 'تم إضافة السرير بنجاح.');
    }

    /**
     * Show the form for editing the specified bed.
     */
    public function edit(Bed $bed)
    {
        $departments = \App\Models\Department::all();
        return view('admin.beds.edit', compact('bed', 'departments'));
    }

    /**
     * Update the specified bed in storage.
     */
    public function update(Request $request, Bed $bed)
    {
        $request->validate([
            'bed_number' => 'required|unique:beds,bed_number,' . $bed->id,
            'room_number' => 'required',
            'status' => 'required|in:متاح,محجوز,صيانة',
            'department' => 'required|string|max:255',
        ]);

        $bed->update($request->all());

        return redirect()->route('beds.index')->with('success', 'تم تحديث بيانات السرير.');
    }

    /**
     * Remove the specified bed from storage.
     */
    public function destroy(Bed $bed)
    {
        $bed->delete();

        return redirect()->route('beds.index')->with('success', 'Bed deleted successfully.');
    }
}

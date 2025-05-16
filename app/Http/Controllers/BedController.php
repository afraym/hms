<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Illuminate\Http\Request;

class BedController extends Controller
{
    /**
     * Display a listing of the beds.
     */
    public function index()
    {
        $beds = Bed::all();
        return view('admin.beds.index', compact('beds'));
    }

    /**
     * Show the form for creating a new bed.
     */
    public function create()
    {
        return view('admin.beds.create');
    }

    /**
     * Store a newly created bed in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bed_number' => 'required|unique:beds,bed_number',
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        Bed::create($request->all());

        return redirect()->route('beds.index')->with('success', 'Bed created successfully.');
    }

    /**
     * Show the form for editing the specified bed.
     */
    public function edit(Bed $bed)
    {
        return view('beds.edit', compact('bed'));
    }

    /**
     * Update the specified bed in storage.
     */
    public function update(Request $request, Bed $bed)
    {
        $request->validate([
            'bed_number' => 'required|unique:beds,bed_number,' . $bed->id,
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $bed->update($request->all());

        return redirect()->route('beds.index')->with('success', 'Bed updated successfully.');
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

<?php
@extends('layouts.app')

@section('content')
    <h1>Hospital Beds</h1>
    <a href="{{ route('beds.create') }}" class="btn btn-primary">Add New Bed</a>
    <table class="table">
        <thead>
            <tr>
                <th>Bed Number</th>
                <th>Status</th>
                <th>Patient Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($beds as $bed)
                <tr>
                    <td>{{ $bed->bed_number }}</td>
                    <td>{{ $bed->status }}</td>
                    <td>{{ $bed->patient_name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('beds.edit', $bed) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('beds.destroy', $bed) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
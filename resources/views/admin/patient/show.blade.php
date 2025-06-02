@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>تفاصيل المريض</h4>
        </div>
        <div class="card-body">
            <p><strong>الاسم الأول:</strong> {{ $patient->first_name }}</p>
            <p><strong>البريد الإلكتروني:</strong> {{ $patient->email }}</p>
            <p><strong>رقم الهاتف:</strong> {{ $patient->phone }}</p>
            <p><strong>الرقم القومي:</strong> {{ $patient->national_id }}</p>
            <p><strong>تاريخ الميلاد:</strong> {{ $patient->date_of_birth }}</p>
            <p><strong>الجنس:</strong> {{ $patient->gender }}</p>
            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-warning">تعديل</a>
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">رجوع</a>
        </div>
    </div>
</div>
@endsection
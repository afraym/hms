@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>تعديل بيانات المريض</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('patients.update', $patient->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label>الاسم الأول</label>
                    <input type="text" name="first_name" class="form-control" value="{{ $patient->first_name }}" required>
                </div>
                <div class="mb-3">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ $patient->email }}">
                </div>
                <div class="mb-3">
                    <label>رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" value="{{ $patient->phone }}">
                </div>
                <div class="mb-3">
                    <label>الرقم القومي</label>
                    <input type="text" name="national_id" class="form-control" value="{{ $patient->national_id }}">
                </div>
                <div class="mb-3">
                    <label>تاريخ الميلاد</label>
                    <input type="date" name="date_of_birth" class="form-control" value="{{ $patient->date_of_birth }}">
                </div>
                <div class="mb-3">
                    <label>الجنس</label>
                    <select name="gender" class="form-control">
                        <option value="male" @if($patient->gender == 'male') selected @endif>ذكر</option>
                        <option value="female" @if($patient->gender == 'female') selected @endif>أنثى</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">حفظ التعديلات</button>
                <a href="{{ route('patients.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection
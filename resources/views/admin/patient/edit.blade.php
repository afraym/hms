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
                <div class="input-group input-group-static mb-4">
                    <label for="first_name">الاسم الأول</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="{{ $patient->first_name }}" required>
                </div>
                <div class="input-group input-group-static mb-4">
                    <label for="second_name">الاسم الثاني</label>
                    <input type="text" name="second_name" id="second_name" class="form-control" value="{{ $patient->second_name }}">
                </div>
                <div class="input-group input-group-static mb-4">
                    <label for="third_name">الاسم الثالث</label>
                    <input type="text" name="third_name" id="third_name" class="form-control" value="{{ $patient->third_name }}">
                </div>
                <div class="input-group input-group-static mb-4">
                    <label for="fourth_name">الاسم الرابع</label>
                    <input type="text" name="fourth_name" id="fourth_name" class="form-control" value="{{ $patient->fourth_name }}">
                </div>
                <div class="input-group input-group-static mb-4">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ $patient->email }}">
                </div>
                <div class="input-group input-group-static mb-4">
                    <label for="phone">رقم الهاتف</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ $patient->phone }}">
                </div>
                <div class="input-group input-group-static mb-4">
                    <label for="national_id">الرقم القومي</label>
                    <input type="text" name="national_id" id="national_id" class="form-control" value="{{ $patient->national_id }}">
                </div>
                <div class="input-group input-group-static mb-4">
                    <label for="date_of_birth">تاريخ الميلاد</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ $patient->date_of_birth }}">
                </div>
                <div class="input-group input-group-static mb-4">
                    <label for="gender">الجنس</label>
                    <select name="gender" id="gender" class="form-control">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach((input, index) => {
            input.addEventListener('focus', () => {
                input.style.borderColor = '#007bff'; // Highlight focused input
            });
            input.addEventListener('blur', () => {
                input.style.borderColor = ''; // Remove highlight on blur
            });
        });

        // Automatically focus the first input field
        if (inputs.length > 0) {
            inputs[0].focus();
        }
    });
</script>
@endpush
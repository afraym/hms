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
                    <label for="full_name">الاسم </label>
                    <input type="text" name="full_name" id="full_name" class="form-control" value="{{ $patient->full_name }}" required>
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
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('Y-m-d') : '') }}">
                </div>
                <div class="input-group input-group-static mb-4">
                    <label for="gender">الجنس</label>
                    <select name="gender" id="gender" class="form-control">
                        <option value="male" @if($patient->gender == 'male') selected @endif>ذكر</option>
                        <option value="female" @if($patient->gender == 'female') selected @endif>أنثى</option>
                    </select>
                </div>
                {{-- Add companion name and phone --}}
                <div class="input-group input-group-static mb-4">
                    <label for="companion_name">اسم المرافق</label>
                    <input type="text" name="companion_name" id="companion_name" class="form-control" value="{{ $patient->companion_name }}">
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="companion_phone">هاتف المرافق</label>
                    <input type="text" name="companion_phone" id="companion_phone" class="form-control" value="{{ $patient->companion_phone }}">
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="companion_relation">صلة القرابة</label>
                    <input type="text" name="companion_relation" id="companion_relation" class="form-control" value="{{ $patient->companion_relation }}">
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="companion_national_id">الرقم القومي للمرافق</label>
                    <input type="text" name="companion_national_id" id="companion_national_id" class="form-control" value="{{ $patient->companion_national_id }}">
                </div>
                <button type="submit" class="btn btn-success">حفظ التعديلات</button>
                <a href="{{ route('patients.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>

            <!-- Form to upload attachments -->
            <form action="{{ route('patients.attachments.upload', $patient->id) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                @csrf
                <div class="mb-2">
                    <input type="file" name="file" required class="form-control">
                </div>
                <div class="mb-2">
                    <input type="text" name="description" class="form-control" placeholder="وصف المرفق (اختياري)">
                </div>
                <button type="submit" class="btn btn-success">رفع المرفق</button>
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
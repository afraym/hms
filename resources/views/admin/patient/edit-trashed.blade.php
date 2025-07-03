@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="material-icons opacity-10">check_circle</i>
            <strong>نجح!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="material-icons opacity-10">error</i>
            <strong>خطأ!</strong> يرجى تصحيح الأخطاء التالية:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4><i class="material-icons">edit</i> تعديل مريض محذوف</h4>
            <div>
                <span class="badge bg-gradient-danger me-2">محذوف</span>
                <small class="text-muted">تم الحذف: {{ $patient->deleted_at ? $patient->deleted_at->format('Y-m-d H:i') : 'غير محدد' }}</small>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-warning" role="alert">
                <i class="material-icons opacity-10">warning</i>
                <strong>تنبيه:</strong> هذا المريض محذوف من النظام. يمكنك تعديل بياناته ثم استعادته لاحقاً.
            </div>

            <form action="{{ route('patients.update-trashed', $patient->id) }}" method="POST" id="patientEditForm">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="full_name">الاسم الكامل</label>
                            <input type="text" name="full_name" id="full_name" 
                                   class="form-control @error('full_name') is-invalid @enderror" 
                                   value="{{ old('full_name', $patient->full_name) }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="medical_id">رقم الملف الطبي</label>
                            <input type="text" name="medical_id" id="medical_id" 
                                   class="form-control @error('medical_id') is-invalid @enderror" 
                                   value="{{ old('medical_id', $patient->medical_id) }}" required>
                            @error('medical_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="phone">رقم الهاتف</label>
                            <input type="text" name="phone" id="phone" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $patient->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="national_id">الرقم القومي</label>
                            <input type="text" name="national_id" id="national_id" 
                                   class="form-control @error('national_id') is-invalid @enderror" 
                                   value="{{ old('national_id', $patient->national_id) }}" maxlength="14">
                            @error('national_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="uhi_number">رقم التأمين الصحي</label>
                            <input type="text" name="uhi_number" id="uhi_number" 
                                   class="form-control @error('uhi_number') is-invalid @enderror" 
                                   value="{{ old('uhi_number', $patient->uhi_number) }}" placeholder="اختياري">
                            @error('uhi_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="email">البريد الإلكتروني</label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $patient->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="date_of_birth">تاريخ الميلاد</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" 
                                   class="form-control @error('date_of_birth') is-invalid @enderror" 
                                   value="{{ old('date_of_birth', $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('Y-m-d') : '') }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="gender">الجنس</label>
                            <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                                <option value="">اختر الجنس</option>
                                <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>أنثى</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="address">العنوان</label>
                    <textarea name="address" id="address" 
                              class="form-control @error('address') is-invalid @enderror" 
                              rows="2">{{ old('address', $patient->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="blood_type">فصيلة الدم</label>
                            <select name="blood_type" id="blood_type" class="form-control @error('blood_type') is-invalid @enderror">
                                <option value="">اختر فصيلة الدم</option>
                                <option value="A+" {{ old('blood_type', $patient->blood_type) == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('blood_type', $patient->blood_type) == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('blood_type', $patient->blood_type) == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('blood_type', $patient->blood_type) == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('blood_type', $patient->blood_type) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('blood_type', $patient->blood_type) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('blood_type', $patient->blood_type) == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('blood_type', $patient->blood_type) == 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                            @error('blood_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="marital_status">الحالة الاجتماعية</label>
                            <select name="marital_status" id="marital_status" class="form-control @error('marital_status') is-invalid @enderror">
                                <option value="">اختر الحالة الاجتماعية</option>
                                <option value="single" {{ old('marital_status', $patient->marital_status) == 'single' ? 'selected' : '' }}>أعزب</option>
                                <option value="married" {{ old('marital_status', $patient->marital_status) == 'married' ? 'selected' : '' }}>متزوج</option>
                                <option value="divorced" {{ old('marital_status', $patient->marital_status) == 'divorced' ? 'selected' : '' }}>مطلق</option>
                                <option value="widowed" {{ old('marital_status', $patient->marital_status) == 'widowed' ? 'selected' : '' }}>أرمل</option>
                            </select>
                            @error('marital_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="occupation">المهنة</label>
                            <input type="text" name="occupation" id="occupation" 
                                   class="form-control @error('occupation') is-invalid @enderror" 
                                   value="{{ old('occupation', $patient->occupation) }}">
                            @error('occupation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="governorate">المحافظة</label>
                            <input type="text" name="governorate" id="governorate" 
                                   class="form-control @error('governorate') is-invalid @enderror" 
                                   value="{{ old('governorate', $patient->governorate) }}">
                            @error('governorate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Companion Information --}}
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">بيانات المرافق الافتراضية</h6>
                        <small class="text-muted">هذه البيانات تستخدم كقيم افتراضية عند إضافة زيارات جديدة</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="companion_name">اسم المرافق</label>
                            <input type="text" name="companion_name" id="companion_name" 
                                   class="form-control @error('companion_name') is-invalid @enderror" 
                                   value="{{ old('companion_name', $patient->companion_name) }}">
                            @error('companion_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="companion_phone">هاتف المرافق</label>
                            <input type="text" name="companion_phone" id="companion_phone" 
                                   class="form-control @error('companion_phone') is-invalid @enderror" 
                                   value="{{ old('companion_phone', $patient->companion_phone) }}">
                            @error('companion_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="companion_relation">صلة القرابة</label>
                            <input type="text" name="companion_relation" id="companion_relation" 
                                   class="form-control @error('companion_relation') is-invalid @enderror" 
                                   value="{{ old('companion_relation', $patient->companion_relation) }}">
                            @error('companion_relation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="input-group input-group-static mb-4">
                            <label for="companion_national_id">الرقم القومي للمرافق</label>
                            <input type="text" name="companion_national_id" id="companion_national_id" 
                                   class="form-control @error('companion_national_id') is-invalid @enderror" 
                                   value="{{ old('companion_national_id', $patient->companion_national_id) }}" maxlength="14">
                            @error('companion_national_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="notes">ملاحظات</label>
                    <textarea name="notes" id="notes" 
                              class="form-control @error('notes') is-invalid @enderror" 
                              rows="3">{{ old('notes', $patient->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success" id="saveBtn">
                        <i class="material-icons">save</i> حفظ التعديلات
                    </button>
                    <form action="{{ route('patients.restore', $patient->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary" onclick="return confirm('هل تريد استعادة هذا المريض؟')">
                            <i class="material-icons">restore</i> استعادة المريض
                        </button>
                    </form>
                    <a href="{{ route('patients.trashed') }}" class="btn btn-secondary">
                        <i class="material-icons">arrow_back</i> العودة
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Form validation feedback
    const form = document.getElementById('patientEditForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="material-icons">hourglass_empty</i> جاري الحفظ...';
        });
    }

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // Add input validation feedback
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('invalid', function() {
            this.classList.add('is-invalid');
        });
        
        input.addEventListener('input', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
});
</script>
@endpush
@endsection
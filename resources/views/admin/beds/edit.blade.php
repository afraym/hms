@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">تعديل السرير</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <form method="POST" action="{{ route('beds.update', $bed->id) }}">
                                    @csrf
                                    @method('PUT')

                                    {{-- Bed Number --}}
                                    <div class="input-group input-group-static mb-4 @if($bed->bed_number) is-filled @endif">
                                        <label class="form-label">رقم السرير</label>
                                        <input type="text" name="bed_number" class="form-control @error('bed_number') is-invalid @enderror" 
                                               value="{{ old('bed_number', $bed->bed_number) }}" required>
                                        @error('bed_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Room Number --}}
                                    <div class="input-group input-group-static mb-4 @if($bed->room_number) is-filled @endif">
                                        <label class="form-label">رقم الغرفة</label>
                                        <input type="text" name="room_number" class="form-control @error('room_number') is-invalid @enderror" 
                                               value="{{ old('room_number', $bed->room_number) }}" required>
                                        @error('room_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Department --}}
                                    <div class="input-group input-group-static mb-4 @if($bed->department_id) is-filled @endif">
                                        <label class="form-label">القسم</label>
                                        <select name="department_id" class="form-control @error('department_id') is-invalid @enderror" required>
                                            <option value="">اختر القسم</option>
                                            @foreach(\App\Models\Department::all() as $department)
                                                <option value="{{ $department->id }}" 
                                                    {{ old('department_id', $bed->department_id) == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Status --}}
                                    <div class="input-group input-group-static mb-4 @if($bed->status) is-filled @endif">
                                        <label class="form-label">الحالة</label>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                            <option value="">اختر الحالة</option>
                                            <option value="متاح" {{ old('status', $bed->status) == 'متاح' ? 'selected' : '' }}>
                                                متاح
                                            </option>
                                            <option value="محجوز" {{ old('status', $bed->status) == 'محجوز' ? 'selected' : '' }}>
                                                محجوز
                                            </option>
                                            <option value="صيانة" {{ old('status', $bed->status) == 'صيانة' ? 'selected' : '' }}>
                                                صيانة
                                            </option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="material-icons">save</i>
                                                تحديث السرير
                                            </button>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="{{ route('beds.index') }}" class="btn btn-secondary w-100">
                                                <i class="material-icons">arrow_back</i>
                                                العودة للقائمة
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Success/Error Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
        <div class="d-flex align-items-center">
            <i class="material-icons me-2">check_circle</i>
            {{ session('success') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
        <div class="d-flex align-items-center">
            <i class="material-icons me-2">error</i>
            {{ session('error') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Handle form field focusing for Material Design
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        // Add focused class if input has value
        if (input.value.trim() !== '') {
            input.closest('.input-group-outline').classList.add('is-filled');
        }

        // Handle focus events
        input.addEventListener('focus', function() {
            this.closest('.input-group-outline').classList.add('is-focused');
        });

        input.addEventListener('blur', function() {
            this.closest('.input-group-outline').classList.remove('is-focused');
            if (this.value.trim() !== '') {
                this.closest('.input-group-outline').classList.add('is-filled');
            } else {
                this.closest('.input-group-outline').classList.remove('is-filled');
            }
        });

        // Handle input events for real-time validation
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.closest('.input-group-outline').classList.add('is-filled');
            } else {
                this.closest('.input-group-outline').classList.remove('is-filled');
            }
        });
    });

    // Handle select fields
    const selects = document.querySelectorAll('select.form-control');
    selects.forEach(select => {
        if (select.value !== '') {
            select.closest('.input-group-outline').classList.add('is-filled');
        }

        select.addEventListener('change', function() {
            if (this.value !== '') {
                this.closest('.input-group-outline').classList.add('is-filled');
            } else {
                this.closest('.input-group-outline').classList.remove('is-filled');
            }
        });
    });

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const bedNumber = document.querySelector('input[name="bed_number"]').value.trim();
        const roomNumber = document.querySelector('input[name="room_number"]').value.trim();
        const departmentId = document.querySelector('select[name="department_id"]').value;
        const status = document.querySelector('select[name="status"]').value;

        if (!bedNumber || !roomNumber || !departmentId || !status) {
            e.preventDefault();
            showToast('يرجى ملء جميع الحقول المطلوبة', 'error');
            return false;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="material-icons">hourglass_empty</i> جاري التحديث...';
        submitBtn.disabled = true;

        // Re-enable button after 3 seconds (in case of validation errors)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 3000);
    });
});

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="material-icons me-2">${type === 'error' ? 'error' : 'info'}</i>
            ${message}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        const bsAlert = new bootstrap.Alert(toast);
        bsAlert.close();
    }, 5000);
}
</script>

<style>
.form-control:focus {
    border-color: #e91e63;
    box-shadow: 0 0 0 0.2rem rgba(233, 30, 99, 0.25);
}

.input-group-outline.is-filled .form-label,
.input-group-outline.is-focused .form-label {
    color: #e91e63;
    font-size: 0.75rem;
    transform: translateY(-1.25rem) scale(0.8);
}

.btn-primary {
    background: linear-gradient(310deg, #e91e63, #ad1457);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(310deg, #ad1457, #e91e63);
    transform: translateY(-1px);
    box-shadow: 0 7px 14px rgba(233, 30, 99, 0.4);
}

.btn-secondary {
    background: linear-gradient(310deg, #6c757d, #495057);
    border: none;
}

.btn-secondary:hover {
    background: linear-gradient(310deg, #495057, #6c757d);
    transform: translateY(-1px);
    box-shadow: 0 7px 14px rgba(108, 117, 125, 0.4);
}

.card {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.alert {
    border-radius: 0.5rem;
    border: none;
}

.alert-success {
    background: linear-gradient(310deg, #4caf50, #2e7d32);
    color: white;
}

.alert-danger {
    background: linear-gradient(310deg, #f44336, #c62828);
    color: white;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}

select.form-control {
    appearance: none;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

.input-group-outline {
    position: relative;
    background: white;
    border-radius: 0.375rem;
    transition: all 0.15s ease-in-out;
}

.input-group-outline .form-label {
    position: absolute;
    top: 50%;
    left: 0.75rem;
    transform: translateY(-50%);
    transition: all 0.15s ease-in-out;
    pointer-events: none;
    color: #6b7280;
    font-size: 1rem;
    z-index: 1;
    background: white;
    padding: 0 0.25rem;
}

.input-group-outline .form-control {
    background: transparent;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    transition: all 0.15s ease-in-out;
}
</style>
@endsection
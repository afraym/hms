@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">تعديل القسم</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <form method="POST" action="{{ route('departments.update', $department->id) }}">
                                    @csrf
                                    @method('PUT')

                                    {{-- Department Name --}}
                                    <div class="input-group input-group-static mb-4 @if($department->name) is-filled @endif">
                                        <label class="form-label">اسم القسم</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name', $department->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Status --}}
                                    <div class="input-group input-group-static mb-4 @if($department->is_active !== null) is-filled @endif">
                                        <label class="form-label">حالة القسم</label>
                                        <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
                                            <option value="">اختر الحالة</option>
                                            <option value="1" {{ old('is_active', $department->is_active) == 1 ? 'selected' : '' }}>
                                                نشط
                                            </option>
                                            <option value="0" {{ old('is_active', $department->is_active) == 0 ? 'selected' : '' }}>
                                                غير نشط
                                            </option>
                                        </select>
                                        @error('is_active')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Department Statistics (Read-only) --}}
                                    <div class="row my-4">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-header pb-0">
                                                    <h6>إحصائيات القسم</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center">
                                                                <i class="material-icons text-primary">hotel</i>
                                                                <span class="ms-2">عدد الأسرة: 
                                                                    <strong>{{ $department->beds_count ?? 0 }}</strong>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center">
                                                                <i class="material-icons text-success">people</i>
                                                                <span class="ms-2">عدد المرضى: 
                                                                    <strong>{{ $department->patients_count ?? 0 }}</strong>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center">
                                                                <i class="material-icons text-info">date_range</i>
                                                                <span class="ms-2">تاريخ الإنشاء: 
                                                                    <strong>{{ $department->created_at->format('Y-m-d') }}</strong>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="d-flex align-items-center">
                                                                <i class="material-icons text-warning">update</i>
                                                                <span class="ms-2">آخر تحديث: 
                                                                    <strong>{{ $department->updated_at->format('Y-m-d') }}</strong>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Buttons --}}
                                    <div class="row mt-4">
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="material-icons">save</i>
                                                تحديث القسم
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="{{ route('departments.index') }}" class="btn btn-secondary w-100">
                                                <i class="material-icons">arrow_back</i>
                                                العودة للقائمة
                                            </a>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-danger w-100" onclick="confirmDelete()">
                                                <i class="material-icons">delete</i>
                                                حذف القسم
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                {{-- Delete Form (Hidden) --}}
                                <form id="deleteForm" method="POST" action="{{ route('departments.destroy', $department->id) }}" style="display: none;">
                                    @csrf
                                    @method('DELETE')
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

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="material-icons text-danger" style="font-size: 48px;">warning</i>
                    <div class="ms-3">
                        <h6>هل أنت متأكد من حذف هذا القسم؟</h6>
                        <p class="text-muted mb-0">اسم القسم: <strong>{{ $department->name }}</strong></p>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <i class="material-icons">info</i>
                    <strong>تحذير:</strong> سيتم حذف القسم نهائياً ولا يمكن التراجع عن هذا الإجراء.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="material-icons">cancel</i>
                    إلغاء
                </button>
                <button type="button" class="btn btn-danger" onclick="submitDelete()">
                    <i class="material-icons">delete_forever</i>
                    حذف نهائي
                </button>
            </div>
        </div>
    </div>
</div>

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
        // Add filled class if input has value
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
        const name = document.querySelector('input[name="name"]').value.trim();
        const isActive = document.querySelector('select[name="is_active"]').value;

        if (!name || isActive === '') {
            e.preventDefault();
            showToast('يرجى ملء جميع الحقول المطلوبة', 'error');
            return false;
        }

        if (name.length < 2) {
            e.preventDefault();
            showToast('اسم القسم يجب أن يكون أكثر من حرفين', 'error');
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

function confirmDelete() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function submitDelete() {
    const deleteBtn = document.querySelector('#deleteModal .btn-danger');
    deleteBtn.innerHTML = '<i class="material-icons">hourglass_empty</i> جاري الحذف...';
    deleteBtn.disabled = true;
    
    document.getElementById('deleteForm').submit();
}

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

.btn-danger {
    background: linear-gradient(310deg, #f44336, #c62828);
    border: none;
}

.btn-danger:hover {
    background: linear-gradient(310deg, #c62828, #f44336);
    transform: translateY(-1px);
    box-shadow: 0 7px 14px rgba(244, 67, 54, 0.4);
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

.alert-warning {
    background: linear-gradient(310deg, #ff9800, #f57c00);
    color: white;
    border: none;
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

.modal-content {
    border-radius: 1rem;
    border: none;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.modal-header {
    border-bottom: 1px solid #e5e7eb;
    border-radius: 1rem 1rem 0 0;
}

.modal-footer {
    border-top: 1px solid #e5e7eb;
    border-radius: 0 0 1rem 1rem;
}
</style>
@endsection
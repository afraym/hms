@extends('layouts.admin')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-lg-12">
            <div class="card my-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>إضافة مستخدم جديد</h6>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="material-icons opacity-10">arrow_back</i>
                        العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="material-icons opacity-10">error</i>
                            يرجى تصحيح الأخطاء التالية:
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-md-6">
                                <h6 class="text-dark text-gradient mb-3">
                                    <i class="material-icons opacity-10">person</i>
                                    المعلومات الأساسية
                                </h6>
                                
                                <div class="input-group input-group-static mb-4">
                                    <label>الاسم الكامل <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="input-group input-group-static mb-4">
                                    <label>البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="input-group input-group-static mb-4">
                                    <label>رقم الهاتف (اختياري)</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="input-group input-group-static mb-4">
                                    <label>الدور الوظيفي <span class="text-danger">*</span></label>
                                    <select class="form-control @error('role') is-invalid @enderror" name="role" required>
                                        <option value="">اختر الدور الوظيفي</option>
                                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                                            مدير عام
                                        </option>
                                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>
                                            مدير فرعي
                                        </option>
                                        <option value="reception" {{ old('role') === 'reception' ? 'selected' : '' }}>
                                            موظف استقبال
                                        </option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-md-6">
                                <h6 class="text-dark text-gradient mb-3">
                                    <i class="material-icons opacity-10">lock</i>
                                    معلومات الأمان
                                </h6>

                                <div class="input-group input-group-static mb-4">
                                    <label>كلمة المرور <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">يجب أن تكون كلمة المرور 8 أحرف على الأقل</small>
                                </div>

                                <div class="input-group input-group-static mb-4">
                                    <label>تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           name="password_confirmation" required>
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Role Permissions Info -->
                                <div class="card bg-gradient-light">
                                    <div class="card-body">
                                        <h6 class="text-dark mb-3">
                                            <i class="material-icons opacity-10">admin_panel_settings</i>
                                            صلاحيات الأدوار
                                        </h6>
                                        <div class="row">
                                            <div class="col-12 mb-2">
                                                <span class="badge bg-gradient-danger me-2">مدير عام</span>
                                                <small class="text-dark">جميع الصلاحيات</small>
                                            </div>
                                            <div class="col-12 mb-2">
                                                <span class="badge bg-gradient-warning me-2">مدير فرعي</span>
                                                <small class="text-dark">إدارة المرضى والأسرة</small>
                                            </div>
                                            <div class="col-12">
                                                <span class="badge bg-gradient-info me-2">موظف استقبال</span>
                                                <small class="text-dark">تسجيل المرضى فقط</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Notice -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-info" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="material-icons opacity-10 me-2">security</i>
                                        <div>
                                            <strong>ملاحظة أمنية:</strong>
                                            سيتم إنشاء حساب المستخدم بالمعلومات المدخلة. يُنصح بتغيير كلمة المرور عند أول تسجيل دخول.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-end">
                                <a href="{{ route('users.index') }}" class="btn btn-light me-2">
                                    <i class="material-icons opacity-10">cancel</i>
                                    إلغاء
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="material-icons opacity-10">person_add</i>
                                    إنشاء المستخدم
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password strength indicator
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="password_confirmation"]');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            updatePasswordStrength(strength);
        });
    }
    
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword && confirmPassword.length > 0) {
                this.classList.add('is-invalid');
                showPasswordMismatchError(this);
            } else {
                this.classList.remove('is-invalid');
                removePasswordMismatchError(this);
            }
        });
    }
    
    function checkPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        return strength;
    }
    
    function updatePasswordStrength(strength) {
        // Remove existing strength indicator
        const existing = document.querySelector('.password-strength');
        if (existing) existing.remove();
        
        if (strength > 0) {
            const strengthDiv = document.createElement('div');
            strengthDiv.className = 'password-strength mt-1';
            
            let strengthText = '';
            let strengthClass = '';
            
            switch (strength) {
                case 1:
                case 2:
                    strengthText = 'ضعيفة';
                    strengthClass = 'text-danger';
                    break;
                case 3:
                    strengthText = 'متوسطة';
                    strengthClass = 'text-warning';
                    break;
                case 4:
                case 5:
                    strengthText = 'قوية';
                    strengthClass = 'text-success';
                    break;
            }
            
            strengthDiv.innerHTML = `<small class="${strengthClass}">قوة كلمة المرور: ${strengthText}</small>`;
            passwordInput.closest('.input-group').appendChild(strengthDiv);
        }
    }
    
    function showPasswordMismatchError(input) {
        if (!input.parentNode.querySelector('.password-mismatch-error')) {
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback password-mismatch-error';
            feedback.textContent = 'كلمات المرور غير متطابقة';
            input.parentNode.appendChild(feedback);
        }
    }
    
    function removePasswordMismatchError(input) {
        const feedback = input.parentNode.querySelector('.password-mismatch-error');
        if (feedback) {
            feedback.remove();
        }
    }
});
</script>
@endsection
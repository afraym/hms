@extends('layouts.admin')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>تعديل المستخدم: {{ $user->name }}</h6>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                        <i class="material-icons opacity-10">arrow_back</i>
                        العودة للقائمة
                    </a>
                </div>
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="material-icons opacity-10">check_circle</i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="input-group input-group-static mb-4">
                            <label>الاسم <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group input-group-static mb-4">
                            <label>الايميل <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="input-group input-group-static mb-4">
                            <label>الدور <span class="text-danger">*</span></label>
                            <select class="form-control @error('role') is-invalid @enderror" name="role" required>
                                <option value="">اختر الدور</option>
                                <option value="admin" {{ (old('role', $user->role) === 'admin') ? 'selected' : '' }}>مدير</option>
                                <option value="manager" {{ (old('role', $user->role) === 'manager') ? 'selected' : '' }}>مدير فرعي</option>
                                <option value="reception" {{ (old('role', $user->role) === 'reception') ? 'selected' : '' }}>استقبال</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info" role="alert">
                            <i class="material-icons opacity-10">info</i>
                            لتغيير كلمة المرور، استخدم زر "إعادة تعيين كلمة المرور" من قائمة المستخدمين.
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">إلغاء</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="material-icons opacity-10">save</i>
                            تحديث المستخدم
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
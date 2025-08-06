@extends('layouts.admin')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>تفاصيل المستخدم</h6>
                    <div class="d-flex gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                            <i class="material-icons opacity-10">edit</i>
                            تعديل
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="material-icons opacity-10">arrow_back</i>
                            العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="material-icons opacity-10">check_circle</i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h6>المعلومات الأساسية</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <img src="{{ asset('assets/img/user-avatar.png') }}" 
                                             alt="Avatar" 
                                             class="avatar" 
                                             style="width: 100px; height: 100px; border-radius: 50%;">
                                    </div>
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">
                                                <i class="material-icons opacity-10">badge</i>
                                                المعرف:
                                            </th>
                                            <td>{{ $user->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <i class="material-icons opacity-10">person</i>
                                                الاسم:
                                            </th>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <i class="material-icons opacity-10">email</i>
                                                الايميل:
                                            </th>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <i class="material-icons opacity-10">admin_panel_settings</i>
                                                الدور:
                                            </th>
                                            <td>
                                                @switch($user->role)
                                                    @case('admin')
                                                        <span class="badge bg-gradient-danger">مدير</span>
                                                        @break
                                                    @case('manager')
                                                        <span class="badge bg-gradient-warning">مدير فرعي</span>
                                                        @break
                                                    @case('reception')
                                                        <span class="badge bg-gradient-info">استقبال</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-gradient-secondary">غير محدد</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        @if($user->phone)
                                        <tr>
                                            <th>
                                                <i class="material-icons opacity-10">phone</i>
                                                الهاتف:
                                            </th>
                                            <td>{{ $user->phone }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>
                                                <i class="material-icons opacity-10">verified</i>
                                                تأكيد الايميل:
                                            </th>
                                            <td>
                                                @if($user->email_verified_at)
                                                    <span class="badge bg-gradient-success">مؤكد</span>
                                                    <small class="text-muted d-block">{{ $user->email_verified_at->format('Y-m-d H:i') }}</small>
                                                @else
                                                    <span class="badge bg-gradient-warning">غير مؤكد</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <i class="material-icons opacity-10">schedule</i>
                                                تاريخ الإنشاء:
                                            </th>
                                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <i class="material-icons opacity-10">update</i>
                                                آخر تحديث:
                                            </th>
                                            <td>{{ $user->updated_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h6>الإجراءات السريعة</h6>
                                </div>
                                <div class="card-body">
                                    @if($user->id !== auth()->id())
                                        <div class="d-grid gap-2">
                                            <form action="{{ route('users.reset-password', $user) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إعادة تعيين كلمة المرور لهذا المستخدم؟');">
                                                @csrf
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="material-icons opacity-10">lock_reset</i>
                                                    إعادة تعيين كلمة المرور
                                                </button>
                                            </form>

                                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger w-100">
                                                    <i class="material-icons opacity-10">delete</i>
                                                    حذف المستخدم
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="alert alert-info text-center" role="alert">
                                            <i class="material-icons opacity-10">info</i>
                                            <br>
                                            لا يمكنك تنفيذ إجراءات على حسابك الشخصي
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Additional Statistics Card -->
                            <div class="card mt-4">
                                <div class="card-header pb-0">
                                    <h6>إحصائيات المستخدم</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="border-end">
                                                <h4 class="text-primary">{{ \Carbon\Carbon::parse($user->created_at)->diffInDays() }}</h4>
                                                <p class="text-muted mb-0">يوم منذ التسجيل</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="text-info">{{ \Carbon\Carbon::parse($user->updated_at)->diffInDays() }}</h4>
                                            <p class="text-muted mb-0">يوم منذ آخر تحديث</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
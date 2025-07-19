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
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="material-icons opacity-10">error</i>
            <strong>خطأ!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4><i class="material-icons">delete</i> المرضى المحذوفين</h4>
            <a href="{{ route('patients.index') }}" class="btn btn-primary">
                <i class="material-icons">arrow_back</i> العودة للمرضى
            </a>
        </div>
        <div class="card-body">
            @if($patients->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>الاسم الكامل</th>
                                <th>الرقم الطبي</th>
                                <th>الرقم القومي</th>
                                <th>رقم الهاتف</th>
                                <th>تاريخ الحذف</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $patient)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset($patient->gender == 'male' ? 'assets/img/male.png' : ($patient->gender == 'female' ? 'assets/img/female.png' : 'assets/img/default.png')) }}" 
                                                 alt="Avatar" class="avatar avatar-sm me-3">
                                            <div>
                                                <h6 class="mb-0">{{ $patient->full_name }}</h6>
                                                <small class="text-muted">{{ $patient->gender == 'male' ? 'ذكر' : 'أنثى' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-gradient-info">{{ $patient->medical_id }}</span>
                                    </td>
                                    <td>{{ $patient->national_id ?? 'غير محدد' }}</td>
                                    <td>{{ $patient->phone ?? 'غير محدد' }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $patient->deleted_at ? $patient->deleted_at->format('Y-m-d H:i') : 'غير محدد' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <!-- Edit Button -->
                                            <a href="{{ route('patients.edit-trashed', $patient->id) }}" 
                                               class="btn  btn-outline-warning" 
                                               title="تعديل">
                                                <i class="material-icons">edit</i>
                                            </a>

                                            <!-- Restore Button -->
                                            <form action="{{ route('patients.restore', $patient->id) }}" 
                                                  method="POST" 
                                                  style="display:inline;"
                                                  onsubmit="return confirm('هل أنت متأكد من استعادة هذا المريض؟')">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn  btn-outline-success" 
                                                        title="استعادة">
                                                    <i class="material-icons">restore</i>
                                                </button>
                                            </form>

                                            <!-- Permanent Delete Button -->
                                            <form action="{{ route('patients.force-delete', $patient->id) }}" 
                                                  method="POST" 
                                                  style="display:inline;"
                                                  onsubmit="return confirm('تحذير: هذا سيحذف المريض نهائياً من النظام مع جميع بياناته وزياراته. هل أنت متأكد؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn  btn-outline-danger" 
                                                        title="حذف نهائي">
                                                    <i class="material-icons">delete_forever</i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $patients->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="material-icons" style="font-size: 4rem; color: #999;">delete_sweep</i>
                    <h5 class="mt-3 text-muted">لا يوجد مرضى محذوفين</h5>
                    <p class="text-muted">جميع المرضى المحذوفين سيظهرون هنا</p>
                    <a href="{{ route('patients.index') }}" class="btn btn-primary">
                        <i class="material-icons">arrow_back</i> العودة للمرضى
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endpush
@endsection
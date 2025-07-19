@extends('layouts.admin') 

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group input-group-dynamic mb-4">
                <label class="form-label">ابحث عن مريض بالاسم او الرقم القومي او الرقم الطبي ...</label>
                <input type="text" id="searchQuery" class="form-control">
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card my-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>قائمة المرضى</h6>
                    <div class="d-flex gap-2 align-items-center">
                        <!-- Sort Options -->
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="material-icons opacity-10">sort</i>
                                ترتيب حسب
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                                <li><h6 class="dropdown-header">ترتيب المرضى</h6></li>
                                <li><a class="dropdown-item {{ $sortBy == 'latest_visit' ? 'active' : '' }}" href="{{ route('patients.index', ['sort_by' => 'latest_visit']) }}">
                                    <i class="material-icons opacity-10">schedule</i> آخر زيارة
                                </a></li>
                                <li><a class="dropdown-item {{ $sortBy == 'oldest_visit' ? 'active' : '' }}" href="{{ route('patients.index', ['sort_by' => 'oldest_visit']) }}">
                                    <i class="material-icons opacity-10">history</i> أقدم زيارة
                                </a></li>
                                <li><a class="dropdown-item {{ $sortBy == 'no_visits' ? 'active' : '' }}" href="{{ route('patients.index', ['sort_by' => 'no_visits']) }}">
                                    <i class="material-icons opacity-10">person_off</i> بدون زيارات
                                </a></li>
                                <li><a class="dropdown-item {{ $sortBy == 'registration_date' ? 'active' : '' }}" href="{{ route('patients.index', ['sort_by' => 'registration_date']) }}">
                                    <i class="material-icons opacity-10">person_add</i> تاريخ التسجيل
                                </a></li>
                                <li><a class="dropdown-item {{ $sortBy == 'name' ? 'active' : '' }}" href="{{ route('patients.index', ['sort_by' => 'name']) }}">
                                    <i class="material-icons opacity-10">abc</i> الاسم
                                </a></li>
                            </ul>
                        </div>

                        <a href="{{ route('patients.create') }}" class="btn btn-primary">إضافة مريض جديد</a>
                        
                        <!-- Export Dropdown Button -->
                        <div class="dropdown">
                            <button class="btn btn-success dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="material-icons opacity-10">file_download</i>
                                تصدير Excel
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                <li><h6 class="dropdown-header">تصدير حسب التاريخ</h6></li>
                                <li><a class="dropdown-item" href="{{ route('patients.export', ['date_filter' => 'today']) }}">
                                    <i class="material-icons opacity-10">today</i> اليوم
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('patients.export', ['date_filter' => 'yesterday']) }}">
                                    <i class="material-icons opacity-10">yesterday</i> أمس
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('patients.export', ['date_filter' => 'this_week']) }}">
                                    <i class="material-icons opacity-10">date_range</i> هذا الأسبوع
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('patients.export', ['date_filter' => 'this_month']) }}">
                                    <i class="material-icons opacity-10">calendar_month</i> هذا الشهر
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('patients.export', ['date_filter' => 'last_month']) }}">
                                    <i class="material-icons opacity-10">calendar_month</i> الشهر الماضي
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('patients.export', ['date_filter' => 'this_year']) }}">
                                    <i class="material-icons opacity-10">calendar_view_year</i> هذا العام
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('patients.export', ['date_filter' => 'last_year']) }}">
                                    <i class="material-icons opacity-10">calendar_view_year</i> العام الماضي
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#customExportModal">
                                    <i class="material-icons opacity-10">date_range</i> فترة مخصصة
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('patients.export') }}">
                                    <i class="material-icons opacity-10">select_all</i> تصدير الكل
                                </a></li>
                            </ul>
                        </div>
                        
                        <a href="{{ route('patients.trashed') }}" class="btn btn-secondary">
                            <i class="material-icons opacity-10">delete</i>
                            عرض المرضى المحذوفين
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <!-- Sort Information Badge -->
                    <div class="px-3 mb-3">
                        @switch($sortBy)
                            @case('latest_visit')
                                <span class="badge bg-gradient-info">
                                    <i class="material-icons opacity-10">schedule</i>
                                    مرتب حسب آخر زيارة (الأحدث أولاً)
                                </span>
                                @break
                            @case('oldest_visit')
                                <span class="badge bg-gradient-warning">
                                    <i class="material-icons opacity-10">history</i>
                                    مرتب حسب آخر زيارة (الأقدم أولاً)
                                </span>
                                @break
                            @case('no_visits')
                                <span class="badge bg-gradient-secondary">
                                    <i class="material-icons opacity-10">person_off</i>
                                    المرضى بدون زيارات
                                </span>
                                @break
                            @case('registration_date')
                                <span class="badge bg-gradient-primary">
                                    <i class="material-icons opacity-10">person_add</i>
                                    مرتب حسب تاريخ التسجيل
                                </span>
                                @break
                            @case('name')
                                <span class="badge bg-gradient-success">
                                    <i class="material-icons opacity-10">abc</i>
                                    مرتب أبجدياً حسب الاسم
                                </span>
                                @break
                        @endswitch
                    </div>

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 text-end" id="patientsTable">
                            <thead>
                                <tr>
                                    <th>الصورة</th>
                                    <th>الاسم</th>
                                    <th>رقم الملف</th>
                                    <th>الرقم القومي</th>
                                    <th>الجنس</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="patientsTableBody">
                                @forelse($patients as $patient)
                                <tr>
                                    <td>
                                        <img src="{{ asset($patient->gender == 'male' ? 'assets/img/male.png' : ($patient->gender == 'female' ? 'assets/img/female.png' : 'assets/img/default.png')) }}" 
                                             alt="Avatar" 
                                             class="avatar" 
                                             style="width: 50px; height: 50px; border-radius: 50%;">
                                    </td>
                                    <td>{{ $patient->full_name }}</td>
                                    <td>
                                        {{ $patient->medical_id }}
                                    </td>
                                    <td>{{ $patient->national_id }}</td>
                                    <td>{{ $patient->gender == 'male' ? 'ذكر' : ($patient->gender == 'female' ? 'أنثى' : 'غير محدد') }}</td>
                                    {{-- <td>
                                        @if($patient->latest_visit_date)
                                            <div>
                                                <small class="text-muted">
                                                    {{ \Carbon\Carbon::parse($patient->latest_visit_date)->format('Y-m-d H:i') }}
                                                </small>
                                                <br>
                                                <span class="badge {{ $patient->latest_visit_type == 'in' ? 'bg-gradient-info' : 'bg-gradient-success' }}">
                                                    {{ $patient->latest_visit_type == 'in' ? 'دخول' : 'خروج' }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted">لا يوجد </span>
                                        @endif
                                    </td> --}}
                                    <td>
                                        @switch($patient->status)
                                            @case('admitted')
                                                <span class="badge bg-info">محجوز في سرير</span>
                                                @break
                                            @case('waiting')
                                                <span class="badge bg-warning"> انتظار</span>
                                                @break
                                            @case('discharged')
                                                <span class="badge bg-success">خرج</span>
                                                @break
                                            @case('deceased')
                                                <span class="badge bg-dark">وفاة</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">غير محدد</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-sm bg-gradient-info">عرض</a>
                                        <a href="{{ route('patients.print.label', $patient->id) }}" class="btn btn-sm btn-primary">
                                         الملصقات
                                        </a>
                                        <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-sm bg-gradient-warning">تعديل</a>
                                        @if($patient->status !== 'deceased')
                                        <form action="{{ route('patients.discharge', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد تسجيل خروج هذا المريض؟');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm bg-gradient-success" aria-label="تسجيل خروج" title="تسجيل خروج">تسجيل خروج</button>
                                        </form>
                                        <form action="{{ route('patients.deceased', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من تسجيل وفاة هذا المريض؟ لا يمكن التراجع عن هذا الإجراء.');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm bg-gradient-dark" aria-label="تسجيل وفاة" title="تسجيل وفاة">تسجيل وفاة</button>
                                        </form>
                                        @endif
                                        <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المريض؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-gradient-danger" aria-label="حذف المريض" title="حذف المريض">حذف</button>
                                        </form>
                                        
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">لا يوجد مرضى</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination links --}}
                    <div class="d-flex justify-content-center">
                        {{ $patients->appends(['sort_by' => $sortBy])->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Date Range Export Modal -->
<div class="modal fade" id="customExportModal" tabindex="-1" aria-labelledby="customExportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customExportModalLabel">تصدير فترة مخصصة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('patients.export') }}" method="GET">
                <div class="modal-body">
                    <input type="hidden" name="date_filter" value="custom">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-4">
                                <label for="start_date">من تاريخ</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-4">
                                <label for="end_date">إلى تاريخ</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" role="alert">
                        <i class="material-icons opacity-10">info</i>
                        سيتم تصدير جميع المرضى المسجلين في الفترة المحددة
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="material-icons opacity-10">file_download</i> تصدير
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchQueryInput = document.getElementById('searchQuery');
    const patientsTableBody = document.getElementById('patientsTableBody');

    if (searchQueryInput) {
        searchQueryInput.addEventListener('input', function () {
            const query = this.value;

            // Show loading spinner
            patientsTableBody.innerHTML = '<tr><td colspan="8" class="text-center">جاري البحث...</td></tr>';

            // Send AJAX request to search patients
            fetch(`/patients/ajax-search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    // Clear the table body
                    patientsTableBody.innerHTML = '';

                    // Populate the table with the search results
                    if (data.length > 0) {
                        data.forEach(patient => {
                            const gender = patient.gender === 'male' ? 'ذكر' : (patient.gender === 'female' ? 'أنثى' : 'غير محدد');
                            const avatar = patient.gender === 'male' ? '/assets/img/male.png' : (patient.gender === 'female' ? '/assets/img/female.png' : '/assets/img/default.png');

                            const row = `
                                <tr>
                                    <td>
                                        <img src="${avatar}" alt="Avatar" class="avatar" style="width: 50px; height: 50px; border-radius: 50%;">
                                    </td>
                                    <td>${patient.full_name}</td>
                                    <td>${patient.medical_id}</td>

                                    <td>${patient.national_id || 'غير محدد'}</td>
                                    <td>${gender}</td>
                                    <td>
                                        ${patient.latest_visit_date ? `
                                            <div>
                                                <small class="text-muted">
                                                    ${new Date(patient.latest_visit_date).toLocaleString('ar-EG', {
                                                        year: 'numeric',
                                                        month: '2-digit',
                                                        day: '2-digit',
                                                        hour: '2-digit',
                                                        minute: '2-digit'
                                                    })}
                                                </small>
                                                <br>
                                                <span class="badge ${patient.latest_visit_type === 'in' ? 'bg-gradient-success' : 'bg-gradient-info'}">
                                                    ${patient.latest_visit_type === 'in' ? 'دخول' : 'خروج'}
                                                </span>
                                            </div>
                                        ` : '<span class="text-muted">لا يوجد</span>'}
                                    </td>
                                    <td>
                                        ${
                                            patient.status === 'admitted'
                                                ? '<span class="badge bg-info">محجوز في سرير</span>'
                                                : patient.status === 'waiting'
                                                    ? '<span class="badge bg-warning">في انتظار سرير</span>'
                                                    : patient.status === 'discharged'
                                                        ? '<span class="badge bg-success">خرج</span>'
                                                        : patient.status === 'deceased'
                                                            ? '<span class="badge bg-dark">متوفى</span>'
                                                            : '<span class="badge bg-light text-dark">غير محدد</span>'
                                        }
                                    </td>
                                    <td>
                                        <a href="/admin/patients/${patient.id}" class="btn btn-sm bg-gradient-info">عرض</a>
                                        <a href="/admin/patients/${patient.id}/label" class="btn btn-sm btn-primary">
                                         الملصقات
                                        </a>
                                        <a href="/admin/patients/${patient.id}/edit" class="btn btn-sm bg-gradient-warning">تعديل</a>
                                        ${patient.status !== 'deceased' ? `
                                        <form action="/admin/patients/${patient.id}/discharge" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد تسجيل خروج هذا المريض؟');">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                            <input type="hidden" name="_method" value="PATCH">
                                            <button type="submit" class="btn btn-sm bg-gradient-success" aria-label="تسجيل خروج" title="تسجيل خروج">تسجيل خروج</button>
                                        </form>
                                        <form action="/admin/patients/${patient.id}/deceased" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من تسجيل وفاة هذا المريض؟ لا يمكن التراجع عن هذا الإجراء.');">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                            <input type="hidden" name="_method" value="PATCH">
                                            <button type="submit" class="btn btn-sm bg-gradient-dark" aria-label="تسجيل وفاة" title="تسجيل وفاة">تسجيل وفاة</button>
                                        </form>
                                        ` : ''}
                                        <form action="/admin/patients/${patient.id}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المريض؟');">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-sm bg-gradient-danger" aria-label="حذف المريض" title="حذف المريض">حذف</button>
                                        </form>
                                        
                                    </td>
                                </tr>
                            `;
                            patientsTableBody.insertAdjacentHTML('beforeend', row);
                        });
                    } else {
                        patientsTableBody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center">لا توجد نتائج مطابقة.</td>
                            </tr>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    patientsTableBody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center text-danger">حدث خطأ أثناء البحث.</td>
                        </tr>
                    `;
                });
        });
    }

    // Set default dates for custom export modal
    const today = new Date().toISOString().split('T')[0];
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (startDateInput && endDateInput) {
        // Set default start date to beginning of current month
        const firstDayOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
        startDateInput.value = firstDayOfMonth;
        endDateInput.value = today;
        
        // Validate date range
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if (endDateInput.value < this.value) {
                endDateInput.value = this.value;
            }
        });
    }
});
</script>
@endsection
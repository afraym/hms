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

                    <!-- Fixed Horizontal Scrollbar at Top -->
                    <div class="px-3 mb-2">
                        <div id="topScrollbar" class="bg-light border rounded" style="overflow-x: auto; overflow-y: hidden; height: 20px;">
                            <div id="topScrollbarContent" style="height: 1px; min-width: 1500px;"></div>
                        </div>
                    </div>

                    <!-- Table Container with Scrolling -->
                    <div id="tableContainer" style="height: 600px; overflow: auto; width: 100%;" class="px-3">
                        <table class="table align-items-center mb-0 text-end" id="patientsTable" style="min-width: 1500px;">
                            <thead class="sticky-top bg-white">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="min-width: 80px;">الصورة</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="min-width: 150px;">الاسم</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="min-width: 100px;">رقم الملف</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="min-width: 120px;">الرقم القومي</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="min-width: 80px;">الجنس</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="min-width: 120px;">الحالة</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="min-width: 120px;">أضيف بواسطة</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="min-width: 130px;">تاريخ الإضافة</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="min-width: 130px;">آخر زيارة</th>
                                    <th class="text-secondary opacity-7" style="min-width: 350px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="patientsTableBody">
                                @forelse($patients as $patient)
                                <tr>
                                    <td style="min-width: 80px;">
                                        <img src="{{ asset($patient->gender == 'male' ? 'assets/img/male.png' : ($patient->gender == 'female' ? 'assets/img/female.png' : 'assets/img/default.png')) }}" 
                                             alt="Avatar" 
                                             class="avatar" 
                                             style="width: 50px; height: 50px; border-radius: 50%;">
                                    </td>
                                    <td style="min-width: 150px;">{{ $patient->full_name }}</td>
                                    <td style="min-width: 100px;">{{ $patient->medical_id }}</td>
                                    <td style="min-width: 120px;">{{ $patient->national_id }}</td>
                                    <td style="min-width: 80px;">{{ $patient->gender == 'male' ? 'ذكر' : ($patient->gender == 'female' ? 'أنثى' : 'غير محدد') }}</td>
                                    <td style="min-width: 120px;">
                                        @switch($patient->status)
                                            @case('admitted')
                                                <span class="badge bg-info">محجوز في سرير</span>
                                                @break
                                            @case('waiting')
                                                <span class="badge bg-warning">انتظار</span>
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
                                    <td style="min-width: 120px;">
                                        @if($patient->created_by_user)
                                            <span class="text-sm">{{ $patient->created_by_user->name }}</span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td style="min-width: 130px;">
                                        <div class="d-flex flex-column">
                                            <span class="text-sm">{{ $patient->created_at->format('Y-m-d') }}</span>
                                            <small class="text-muted">{{ $patient->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td style="min-width: 130px;">
                                        @php
                                            $latestVisit = $patient->visits->sortByDesc('visit_at')->first();
                                        @endphp
                                        @if($latestVisit)
                                            <div class="d-flex flex-column">
                                                <span class="text-sm">{{ \Carbon\Carbon::parse($latestVisit->visit_at)->format('Y-m-d') }}</span>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($latestVisit->visit_at)->format('H:i') }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">لا توجد زيارات</span>
                                        @endif
                                    </td>
                                    <td style="min-width: 350px;">
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-sm bg-gradient-info">عرض</a>
                                            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-sm bg-gradient-warning">تعديل</a>
                                            @if($patient->status !== 'deceased')
                                            <form action="{{ route('patients.discharge', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد تسجيل خروج هذا المريض؟');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm bg-gradient-success" aria-label="تسجيل خروج" title="تسجيل خروج">خروج</button>
                                            </form>
                                            <form action="{{ route('patients.deceased', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من تسجيل وفاة هذا المريض؟ لا يمكن التراجع عن هذا الإجراء.');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm bg-gradient-dark" aria-label="تسجيل وفاة" title="تسجيل وفاة">وفاة</button>
                                            </form>
                                            @endif
                                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المريض؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm bg-gradient-danger" aria-label="حذف المريض" title="حذف المريض">حذف</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">لا يوجد مرضى</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Custom Styles -->
                    <style>
                    /* Top scrollbar styling */
                    #topScrollbar {
                        position: sticky;
                        top: 0;
                        z-index: 11;
                        margin-bottom: 10px;
                    }

                    #topScrollbar::-webkit-scrollbar {
                        height: 12px;
                    }

                    #topScrollbar::-webkit-scrollbar-track {
                        background: #f1f3f4;
                        border-radius: 6px;
                    }

                    #topScrollbar::-webkit-scrollbar-thumb {
                        background: #6c757d;
                        border-radius: 6px;
                    }

                    #topScrollbar::-webkit-scrollbar-thumb:hover {
                        background: #495057;
                    }

                    /* Table container scrollbar styling */
                    #tableContainer {
                        scrollbar-width: thin;
                        scrollbar-color: #6c757d #f8f9fa;
                    }

                    #tableContainer::-webkit-scrollbar:vertical {
                        width: 8px;
                    }

                    #tableContainer::-webkit-scrollbar:horizontal {
                        height: 8px;
                    }

                    #tableContainer::-webkit-scrollbar-track {
                        background: #f8f9fa;
                        border-radius: 4px;
                    }

                    #tableContainer::-webkit-scrollbar-thumb {
                        background: #6c757d;
                        border-radius: 4px;
                    }

                    #tableContainer::-webkit-scrollbar-thumb:hover {
                        background: #495057;
                    }

                    #tableContainer::-webkit-scrollbar-corner {
                        background: #f8f9fa;
                    }

                    /* Sticky header styling */
                    .table thead th {
                        position: sticky;
                        top: 0;
                        z-index: 10;
                        background-color: white !important;
                        border-bottom: 2px solid #dee2e6;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    }

                    /* Table cells styling */
                    .table td, .table th {
                        white-space: nowrap;
                        text-overflow: ellipsis;
                        padding: 0.75rem 0.5rem;
                    }

                    .table td:last-child {
                        white-space: normal;
                    }

                    /* Responsive design */
                    @media (max-width: 768px) {
                        .table td, .table th {
                            font-size: 0.875rem;
                        }
                        
                        .btn-sm {
                            font-size: 0.75rem;
                            padding: 0.25rem 0.5rem;
                        }
                    }
                    </style>

                    {{-- Pagination links --}}
                    <div class="d-flex justify-content-center mt-3">
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
    const topScrollbar = document.getElementById('topScrollbar');
    const tableContainer = document.getElementById('tableContainer');
    const topScrollbarContent = document.getElementById('topScrollbarContent');
    const table = document.getElementById('patientsTable');

    // Sync top scrollbar with table horizontal scroll
    function syncScrollbars() {
        if (table && topScrollbarContent) {
            const tableWidth = table.scrollWidth;
            const containerWidth = tableContainer.clientWidth;
            
            if (tableWidth > containerWidth) {
                topScrollbarContent.style.width = tableWidth + 'px';
                topScrollbar.style.display = 'block';
            } else {
                topScrollbar.style.display = 'none';
            }
        }
    }

    // Sync scrolling between top scrollbar and table
    topScrollbar.addEventListener('scroll', function() {
        tableContainer.scrollLeft = topScrollbar.scrollLeft;
    });

    tableContainer.addEventListener('scroll', function() {
        topScrollbar.scrollLeft = tableContainer.scrollLeft;
    });

    // Initialize scrollbar sync
    syncScrollbars();

    // Re-sync when window is resized
    window.addEventListener('resize', syncScrollbars);

    if (searchQueryInput) {
        searchQueryInput.addEventListener('input', function () {
            const query = this.value;

            // Show loading spinner
            patientsTableBody.innerHTML = '<tr><td colspan="10" class="text-center">جاري البحث...</td></tr>';

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

                            // Format created by user info
                            let createdByHtml = '<span class="text-muted">غير محدد</span>';
                            if (patient.created_by_user) {
                                createdByHtml = `<span class="text-sm">${patient.created_by_user.name}</span>`;
                            }

                            // Format created at date
                            const createdAt = new Date(patient.created_at);
                            const createdAtHtml = `
                                <div class="d-flex flex-column">
                                    <span class="text-sm">${createdAt.toLocaleDateString('en-CA')}</span>
                                    <small class="text-muted">${createdAt.toLocaleTimeString('en-GB', {hour: '2-digit', minute: '2-digit'})}</small>
                                </div>
                            `;

                            // Format last visit
                            let lastVisitHtml = '<span class="text-muted">لا توجد زيارات</span>';
                            if (patient.visits && patient.visits.length > 0) {
                                const latestVisit = patient.visits.reduce((latest, visit) => {
                                    return new Date(visit.visit_at) > new Date(latest.visit_at) ? visit : latest;
                                });
                                const visitDate = new Date(latestVisit.visit_at);
                                const visitType = latestVisit.type === 'emergency' ? 'طوارئ' : 
                                                 latestVisit.type === 'regular' ? 'عادية' : 'متابعة';
                                
                                lastVisitHtml = `
                                    <div class="d-flex flex-column">
                                        <span class="text-sm">${visitDate.toLocaleDateString('en-CA')}</span>
                                        <small class="text-muted">${visitDate.toLocaleTimeString('en-GB', {hour: '2-digit', minute: '2-digit'})}</small>
                                        <small class="badge badge-sm bg-gradient-info mt-1">${visitType}</small>
                                    </div>
                                `;
                            }

                            const row = `
                                <tr>
                                    <td style="min-width: 80px;">
                                        <img src="${avatar}" alt="Avatar" class="avatar" style="width: 50px; height: 50px; border-radius: 50%;">
                                    </td>
                                    <td style="min-width: 150px;">${patient.full_name}</td>
                                    <td style="min-width: 100px;">${patient.medical_id}</td>
                                    <td style="min-width: 120px;">${patient.national_id || 'غير محدد'}</td>
                                    <td style="min-width: 80px;">${gender}</td>
                                    <td style="min-width: 120px;">
                                        ${
                                            patient.status === 'admitted'
                                                ? '<span class="badge bg-info">محجوز في سرير</span>'
                                                : patient.status === 'waiting'
                                                    ? '<span class="badge bg-warning">انتظار</span>'
                                                    : patient.status === 'discharged'
                                                        ? '<span class="badge bg-success">خرج</span>'
                                                        : patient.status === 'deceased'
                                                            ? '<span class="badge bg-dark">وفاة</span>'
                                                            : '<span class="badge bg-light text-dark">غير محدد</span>'
                                        }
                                    </td>
                                    <td style="min-width: 120px;">${createdByHtml}</td>
                                    <td style="min-width: 130px;">${createdAtHtml}</td>
                                    <td style="min-width: 130px;">${lastVisitHtml}</td>
                                    <td style="min-width: 350px;">
                                        <div class="d-flex flex-wrap gap-1">
                                            <a href="/admin/patients/${patient.id}" class="btn btn-sm bg-gradient-info">عرض</a>
                                            <a href="/admin/patients/${patient.id}/edit" class="btn btn-sm bg-gradient-warning">تعديل</a>
                                            ${patient.status !== 'deceased' ? `
                                            <form action="/admin/patients/${patient.id}/discharge" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد تسجيل خروج هذا المريض؟');">
                                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                                <input type="hidden" name="_method" value="PATCH">
                                                <button type="submit" class="btn btn-sm bg-gradient-success" aria-label="تسجيل خروج" title="تسجيل خروج">خروج</button>
                                            </form>
                                            <form action="/admin/patients/${patient.id}/deceased" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من تسجيل وفاة هذا المريض؟ لا يمكن التراجع عن هذا الإجراء.');">
                                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                                <input type="hidden" name="_method" value="PATCH">
                                                <button type="submit" class="btn btn-sm bg-gradient-dark" aria-label="تسجيل وفاة" title="تسجيل وفاة">وفاة</button>
                                            </form>
                                            ` : ''}
                                            <form action="/admin/patients/${patient.id}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المريض؟');">
                                                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-sm bg-gradient-danger" aria-label="حذف المريض" title="حذف المريض">حذف</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            `;
                            patientsTableBody.insertAdjacentHTML('beforeend', row);
                        });
                    } else {
                        patientsTableBody.innerHTML = `
                            <tr>
                                <td colspan="10" class="text-center">لا توجد نتائج مطابقة.</td>
                            </tr>
                        `;
                    }

                    // Re-sync scrollbars after updating content
                    setTimeout(syncScrollbars, 100);
                })
                .catch(error => {
                    console.error('Error:', error);
                    patientsTableBody.innerHTML = `
                        <tr>
                            <td colspan="10" class="text-center text-danger">حدث خطأ أثناء البحث.</td>
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
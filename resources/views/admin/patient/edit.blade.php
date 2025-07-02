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
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">بيانات المرافق الافتراضية</h6>
                        <small class="text-muted">هذه البيانات تستخدم كقيم افتراضية عند إضافة زيارات جديدة</small>
                    </div>
                </div>
                
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
            <form action="{{ route('patients.attachments.upload', $patient->id) }}" method="POST" enctype="multipart/form-data" class="mb-3" id="attachmentForm">
                @csrf
                <div class="input-group input-group-static mb-4">
                    <label for="attachments">مرفقات المريض</label>
                    <input id="attachments" name="attachments[]" type="file" class="form-control" multiple>
                </div>
                <div class="mb-2">
                    <input type="text" name="description" class="form-control" placeholder="وصف المرفق (اختياري)">
                </div>
                <button type="submit" class="btn btn-success">رفع المرفقات</button>
            </form>
        </div>
    </div>

    <!-- Patient Visits Management Section -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>إدارة دخول وخروج المريض</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVisitModal">
                إضافة جديد
            </button>
        </div>
        <div class="card-body">
            @if($patient->visits->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>النوع</th>
                                <th>التاريخ والوقت</th>
                                <th>القسم</th>
                                <th>السرير</th>
                                <th>المرافق</th>
                                <th>الملاحظات</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patient->visits()->latest('visit_at')->get() as $visit)
                                <tr>
                                    <td>
                                        <span class="badge {{ $visit->type == 'in' ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                            {{ $visit->type == 'in' ? 'دخول' : 'خروج' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $arabicDays = [
                                                'Sunday' => 'الأحد',
                                                'Monday' => 'الإثنين', 
                                                'Tuesday' => 'الثلاثاء',
                                                'Wednesday' => 'الأربعاء',
                                                'Thursday' => 'الخميس',
                                                'Friday' => 'الجمعة',
                                                'Saturday' => 'السبت'
                                            ];
                                            $dayName = $arabicDays[$visit->visit_at->format('l')];
                                            $date = $visit->visit_at->format('Y-m-d');
                                            $hour = (int)$visit->visit_at->format('H');
                                            $minute = $visit->visit_at->format('i');
                                            $period = $hour < 12 ? 'صباحا' : 'مساء';
                                            $displayHour = $hour == 0 ? 12 : ($hour > 12 ? $hour - 12 : $hour);
                                            $formattedTime = sprintf('%02d:%s %s', $displayHour, $minute, $period);
                                        @endphp
                                        {{ $dayName }} {{ $date }} {{ $formattedTime }}
                                    </td>
                                    <td>{{ $visit->department->name ?? 'غير محدد' }}</td>
                                    <td>
                                        @if($visit->bed)
                                            <span class="badge bg-gradient-info">{{ $visit->bed->bed_number }}</span>
                                            <small class="text-muted d-block">غرفة {{ $visit->bed->room_number }}</small>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($visit->companion_name)
                                            <strong>{{ $visit->companion_name }}</strong>
                                            @if($visit->companion_relation)
                                                <small class="text-muted d-block">({{ $visit->companion_relation }})</small>
                                            @endif
                                            @if($visit->companion_phone)
                                                <small class="text-muted d-block">{{ $visit->companion_phone }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($visit->notes ?? 'لا توجد', 30) }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editVisit({{ $visit->id }})">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('patient_visits.destroy', $visit->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الزيارة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                    <p class="mt-2">لا توجد سجلات مسجلة لهذا المريض</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVisitModal">
                        إضافة أول سجل
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Visit Modal -->
    <div class="modal fade" id="addVisitModal" tabindex="-1" aria-labelledby="addVisitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addVisitModalLabel">إضافة جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('patients.visits.store', $patient->id) }}" method="POST" id="addVisitForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="visit_type">النوع</label>
                                    <select name="type" id="visit_type" class="form-control" required>
                                        <option value="">اختر النوع</option>
                                        <option value="in">دخول</option>
                                        <option value="out">خروج</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="visit_at">التاريخ والوقت</label>
                                    <input type="datetime-local" name="visit_at" id="visit_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="visit_department_id">القسم</label>
                                    <select name="department_id" id="visit_department_id" class="form-control">
                                        <option value="">اختر القسم</option>
                                        @foreach(\App\Models\Department::all() as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="visit_bed_id">السرير</label>
                                    <select name="bed_id" id="visit_bed_id" class="form-control">
                                        <option value="">اختر السرير</option>
                                        @foreach(\App\Models\Bed::where('status', 'متاح')->get() as $bed)
                                            <option value="{{ $bed->id }}">{{ $bed->bed_number }} - {{ $bed->room_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="visit_companion_name">اسم المرافق</label>
                                    <input type="text" name="companion_name" id="visit_companion_name" class="form-control" placeholder="اسم المرافق" value="{{ $patient->companion_name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="visit_companion_relation">صلة القرابة</label>
                                    <input type="text" name="companion_relation" id="visit_companion_relation" class="form-control" placeholder="صلة القرابة" value="{{ $patient->companion_relation }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="visit_companion_phone">هاتف المرافق</label>
                                    <input type="text" name="companion_phone" id="visit_companion_phone" class="form-control" placeholder="هاتف المرافق" value="{{ $patient->companion_phone }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="visit_companion_national_id">الرقم القومي للمرافق</label>
                                    <input type="text" name="companion_national_id" id="visit_companion_national_id" class="form-control" placeholder="الرقم القومي للمرافق" value="{{ $patient->companion_national_id }}">
                                </div>
                            </div>
                        </div>

                        <div class="input-group input-group-static mb-3">
                            <label for="visit_notes">الملاحظات</label>
                            <textarea name="notes" id="visit_notes" class="form-control" rows="3" placeholder="ملاحظات إضافية..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Visit Modal -->
    <div class="modal fade" id="editVisitModal" tabindex="-1" aria-labelledby="editVisitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVisitModalLabel">تعديل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editVisitForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Same form fields as add visit modal but with different IDs -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="edit_visit_type">النوع</label>
                                    <select name="type" id="edit_visit_type" class="form-control" required>
                                        <option value="">اختر النوع</option>
                                        <option value="in">دخول</option>
                                        <option value="out">خروج</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="edit_visit_at">التاريخ والوقت</label>
                                    <input type="datetime-local" name="visit_at" id="edit_visit_at" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="edit_visit_department_id">القسم</label>
                                    <select name="department_id" id="edit_visit_department_id" class="form-control">
                                        <option value="">اختر القسم</option>
                                        @foreach(\App\Models\Department::all() as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="edit_visit_bed_id">السرير</label>
                                    <select name="bed_id" id="edit_visit_bed_id" class="form-control">
                                        <option value="">اختر السرير</option>
                                        @foreach(\App\Models\Bed::all() as $bed)
                                            <option value="{{ $bed->id }}">{{ $bed->bed_number }} - {{ $bed->room_number }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="edit_visit_companion_name">اسم المرافق</label>
                                    <input type="text" name="companion_name" id="edit_visit_companion_name" class="form-control" placeholder="اسم المرافق">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="edit_visit_companion_relation">صلة القرابة</label>
                                    <input type="text" name="companion_relation" id="edit_visit_companion_relation" class="form-control" placeholder="صلة القرابة">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="edit_visit_companion_phone">هاتف المرافق</label>
                                    <input type="text" name="companion_phone" id="edit_visit_companion_phone" class="form-control" placeholder="هاتف المرافق">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group input-group-static mb-3">
                                    <label for="edit_visit_companion_national_id">الرقم القومي للمرافق</label>
                                    <input type="text" name="companion_national_id" id="edit_visit_companion_national_id" class="form-control" placeholder="الرقم القومي للمرافق">
                                </div>
                            </div>
                        </div>

                        <div class="input-group input-group-static mb-3">
                            <label for="edit_visit_notes">الملاحظات</label>
                            <textarea name="notes" id="edit_visit_notes" class="form-control" rows="3" placeholder="ملاحظات إضافية..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-warning">تحديث</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Custom styles for Bootstrap Icons in fileinput */
.fileinput-upload .bi::before,
.fileinput-remove .bi::before,
.file-thumbnail-footer .bi::before,
.file-actions .bi::before {
    font-family: "bootstrap-icons" !important;
    display: inline-block;
    vertical-align: -.125em;
    line-height: 1;
}

/* Spin animation for upload icon */
@keyframes bi-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.bi-arrow-clockwise.bi-spin {
    animation: bi-spin 1s linear infinite;
}

/* Ensure proper icon display */
.kv-file-upload .bi,
.kv-file-remove .bi,
.file-thumbnail-footer .bi {
    font-size: 1rem;
    margin-right: 0.25rem;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let accumulatedFiles = [];
    
    // Destroy any existing fileinput instance to prevent conflicts
    if ($('#attachments').hasClass('file-input')) {
        $('#attachments').fileinput('destroy');
    }
    
    $('#attachments').fileinput({
        theme: 'bs5',
        language: 'ar',
        allowedFileExtensions: ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
        showUpload: true,
        showRemove: true,
        showCancel: true,
        browseOnZoneClick: true,
        overwriteInitial: false,
        maxFileCount: 10,
        validateInitialCount: true,
        browseLabel: 'اختر الملفات',
        uploadLabel: 'رفع',
        removeLabel: 'إزالة الكل',
        cancelLabel: 'إلغاء',
        msgPlaceholder: 'اختر الملفات...',
        dropZoneTitle: 'اسحب وأفلت الملفات هنا أو انقر للاختيار',
        fileActionSettings: {
            showUpload: false,
            showZoom: true,
            showDrag: false,
            removeIcon: '<i class="bi bi-trash"></i>',
            removeClass: 'btn btn-sm btn-kv btn-outline-danger',
            zoomIcon: '<i class="bi bi-zoom-in"></i>',
            zoomClass: 'btn btn-sm btn-kv btn-outline-secondary'
        },
        uploadIcon: '<i class="bi bi-arrow-clockwise bi-spin"></i>',
        uploadClass: 'btn btn-success',
        removeClass: 'btn btn-danger',
        cancelClass: 'btn btn-secondary',
        browseIcon: '<i class="bi bi-folder2-open"></i>',
        browseClass: 'btn btn-primary',
        removeIcon: '<i class="bi bi-trash"></i>',
        cancelIcon: '<i class="bi bi-x-circle"></i>',
        uploadUrl: '{{ route("patients.attachments.upload", $patient->id) }}',
        uploadExtraData: function() {
            return {
                _token: '{{ csrf_token() }}',
                description: $('input[name="description"]').val()
            };
        }
    }).on('filebatchselected', function(event, files) {
        // Add new files to accumulated array
        for (let i = 0; i < files.length; i++) {
            accumulatedFiles.push(files[i]);
        }
        
        // Create a new DataTransfer object to hold all files
        const dt = new DataTransfer();
        
        // Add all accumulated files to DataTransfer
        accumulatedFiles.forEach(file => {
            dt.items.add(file);
        });
        
        // Update the input's files property
        event.target.files = dt.files;
        
        // Refresh the file input display
        $(this).fileinput('refresh', {
            showUpload: true,
            showRemove: true
        });
    }).on('fileclear', function(event) {
        // Clear accumulated files when user clicks remove all
        accumulatedFiles = [];
    }).on('fileremoved', function(event, id, index) {
        // Remove file from accumulated array
        accumulatedFiles.splice(index, 1);
        
        // Recreate DataTransfer with remaining files
        const dt = new DataTransfer();
        accumulatedFiles.forEach(file => {
            dt.items.add(file);
        });
        
        event.target.files = dt.files;
    }).on('filebatchuploadsuccess', function(event, data) {
        // Clear accumulated files after successful upload
        accumulatedFiles = [];
        
        // Show success message
        if (data.response && data.response.success) {
            alert('تم رفع المرفقات بنجاح!');
            location.reload(); // Reload to show new attachments
        }
    }).on('filebatchuploaderror', function(event, data) {
        console.error('Upload error:', data);
        alert('حدث خطأ أثناء رفع المرفقات.');
    });

    // Visit management functionality
    $('#visit_type').on('change', function() {
        const type = $(this).val();
        const bedSelect = $('#visit_bed_id');
        
        if (type === 'out') {
            bedSelect.prop('disabled', true).val('');
        } else {
            bedSelect.prop('disabled', false);
        }
    });

    $('#edit_visit_type').on('change', function() {
        const type = $(this).val();
        const bedSelect = $('#edit_visit_bed_id');
        
        if (type === 'out') {
            bedSelect.prop('disabled', true).val('');
        } else {
            bedSelect.prop('disabled', false);
        }
    });

    // Filter beds by department
    $('#visit_department_id').on('change', function() {
        const departmentId = $(this).val();
        const bedSelect = $('#visit_bed_id');
        
        if (departmentId) {
            filterBedsByDepartment(departmentId, bedSelect);
        } else {
            loadAllAvailableBeds(bedSelect);
        }
    });

    $('#edit_visit_department_id').on('change', function() {
        const departmentId = $(this).val();
        const bedSelect = $('#edit_visit_bed_id');
        
        if (departmentId) {
            filterBedsByDepartment(departmentId, bedSelect);
        } else {
            loadAllBeds(bedSelect);
        }
    });
});

// Helper functions for bed filtering
function filterBedsByDepartment(departmentId, bedSelect) {
    fetch(`{{ url('admin/beds') }}?department_id=${departmentId}&status=متاح`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(beds => {
        bedSelect.empty().append('<option value="">اختر السرير</option>');
        beds.forEach(bed => {
            bedSelect.append(`<option value="${bed.id}">${bed.bed_number} - ${bed.room_number}</option>`);
        });
    })
    .catch(error => {
        console.error('Error fetching beds:', error);
    });
}

function loadAllAvailableBeds(bedSelect) {
    fetch(`{{ url('admin/beds') }}?status=متاح`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(beds => {
        bedSelect.empty().append('<option value="">اختر السرير</option>');
        beds.forEach(bed => {
            bedSelect.append(`<option value="${bed.id}">${bed.bed_number} - ${bed.room_number}</option>`);
        });
    })
    .catch(error => {
        console.error('Error fetching beds:', error);
    });
}

function loadAllBeds(bedSelect) {
    fetch(`{{ url('admin/beds') }}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(beds => {
        bedSelect.empty().append('<option value="">اختر السرير</option>');
        beds.forEach(bed => {
            bedSelect.append(`<option value="${bed.id}">${bed.bed_number} - ${bed.room_number}</option>`);
        });
    })
    .catch(error => {
        console.error('Error fetching beds:', error);
    });
}

// Function to edit a visit
function editVisit(visitId) {
    // Fetch visit data and populate the edit modal
    fetch(`{{ url('admin/patients') }}/{{ $patient->id }}/visits/${visitId}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Populate the edit form fields
            $('#edit_visit_type').val(data.type);
            $('#edit_visit_at').val(data.visit_at.substring(0, 16)); // Format for datetime-local
            $('#edit_visit_department_id').val(data.department_id || '');
            $('#edit_visit_bed_id').val(data.bed_id || '');
            $('#edit_visit_companion_name').val(data.companion_name || '');
            $('#edit_visit_companion_relation').val(data.companion_relation || '');
            $('#edit_visit_companion_phone').val(data.companion_phone || '');
            $('#edit_visit_companion_national_id').val(data.companion_national_id || '');
            $('#edit_visit_notes').val(data.notes || '');
            
            // Set the form action URL
            $('#editVisitForm').attr('action', `{{ url('admin/patients') }}/{{ $patient->id }}/visits/${visitId}`);
            
            // Show the modal
            $('#editVisitModal').modal('show');
        })
        .catch(error => {
            console.error('Error fetching visit data:', error);
            $.toast({
                heading: 'خطأ في تحميل البيانات',
                text: 'حدث خطأ في تحميل بيانات الزيارة: ' + error.message,
                icon: 'error',
                position: 'top-right',
                showHideTransition: 'slide'
            });
        });
}

// Handle visit form submissions
$('#addVisitForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = $(this).find('button[type="submit"]');
    const originalText = submitButton.text();
    
    submitButton.prop('disabled', true).text('جارٍ الحفظ...');
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            $('#addVisitModal').modal('hide');
            $.toast({
                heading: 'نجح الحفظ',
                text: data.message || 'تم حفظ الزيارة بنجاح',
                icon: 'success',
                position: 'top-right',
                showHideTransition: 'slide'
            });
            setTimeout(() => location.reload(), 1500); // Reload after showing toast
        } else {
            $.toast({
                heading: 'خطأ في الحفظ',
                text: data.message || 'حدث خطأ أثناء حفظ الزيارة',
                icon: 'error',
                position: 'top-right',
                showHideTransition: 'slide'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        $.toast({
            heading: 'خطأ في الحفظ',
            text: 'حدث خطأ أثناء حفظ الزيارة: ' + error.message,
            icon: 'error',
            position: 'top-right',
            showHideTransition: 'slide'
        });
    })
    .finally(() => {
        submitButton.prop('disabled', false).text(originalText);
    });
});

$('#editVisitForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = $(this).find('button[type="submit"]');
    const originalText = submitButton.text();
    
    submitButton.prop('disabled', true).text('جارٍ التحديث...');
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            $('#editVisitModal').modal('hide');
            $.toast({
                heading: 'نجح التحديث',
                text: data.message || 'تم تحديث الزيارة بنجاح',
                icon: 'success',
                position: 'top-right',
                showHideTransition: 'slide'
            });
            setTimeout(() => location.reload(), 1500); // Reload after showing toast
        } else {
            $.toast({
                heading: 'خطأ في التحديث',
                text: data.message || 'حدث خطأ أثناء تحديث الزيارة',
                icon: 'error',
                position: 'top-right',
                showHideTransition: 'slide'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        $.toast({
            heading: 'خطأ في التحديث',
            text: 'حدث خطأ أثناء تحديث الزيارة: ' + error.message,
            icon: 'error',
            position: 'top-right',
            showHideTransition: 'slide'
        });
    })
        alert('حدث خطأ أثناء تحديث الزيارة');
    })
    .finally(() => {
        submitButton.prop('disabled', false).text(originalText);
    });
});

// Clear form when modals are hidden
$('#addVisitModal').on('hidden.bs.modal', function() {
    $('#addVisitForm')[0].reset();
    $('#visit_bed_id').prop('disabled', false);
    
    // Reset to default companion values
    $('#visit_companion_name').val('{{ $patient->companion_name }}');
    $('#visit_companion_relation').val('{{ $patient->companion_relation }}');
    $('#visit_companion_phone').val('{{ $patient->companion_phone }}');
    $('#visit_companion_national_id').val('{{ $patient->companion_national_id }}');
    $('#visit_at').val('{{ now()->format('Y-m-d\TH:i') }}');
});

$('#editVisitModal').on('hidden.bs.modal', function() {
    $('#editVisitForm')[0].reset();
    $('#edit_visit_bed_id').prop('disabled', false);
});
</script>
@endpush

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
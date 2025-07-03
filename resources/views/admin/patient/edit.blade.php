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
        <div class="card-header">
            <h4>تعديل بيانات المريض</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('patients.update', $patient->id) }}" method="POST" id="patientEditForm">
                @csrf
                @method('PUT')
                
                <div class="input-group input-group-static mb-4">
                    <label for="full_name">الاسم الكامل</label>
                    <input type="text" name="full_name" id="full_name" 
                           class="form-control @error('full_name') is-invalid @enderror" 
                           value="{{ old('full_name', $patient->full_name) }}" required>
                    @error('full_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="medical_id">رقم الملف الطبي</label>
                    <div class="input-group">
                        <input type="text" name="medical_id" id="medical_id" 
                               class="form-control @error('medical_id') is-invalid @enderror" 
                               value="{{ old('medical_id', $patient->medical_id) }}" required>
                        <button type="button" class="btn btn-outline-primary" id="generateMedicalIdBtn">
                            <i class="material-icons">refresh</i> توليد رقم جديد
                        </button>
                    </div>
                    @error('medical_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div id="medicalIdInfo" class="mt-1 text-info" style="font-size: 0.95em;"></div>
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="uhi_number">رقم التأمين الصحي</label>
                    <input type="text" name="uhi_number" id="uhi_number" 
                           class="form-control @error('uhi_number') is-invalid @enderror" 
                           value="{{ old('uhi_number', $patient->uhi_number) }}" placeholder="اختياري">
                    @error('uhi_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="phone">رقم الهاتف</label>
                    <input type="text" name="phone" id="phone" 
                           class="form-control @error('phone') is-invalid @enderror" 
                           value="{{ old('phone', $patient->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="national_id">الرقم القومي</label>
                    <input type="text" name="national_id" id="national_id" 
                           class="form-control @error('national_id') is-invalid @enderror" 
                           value="{{ old('national_id', $patient->national_id) }}" maxlength="14">
                    @error('national_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="date_of_birth">تاريخ الميلاد</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" 
                           class="form-control @error('date_of_birth') is-invalid @enderror" 
                           value="{{ old('date_of_birth', $patient->date_of_birth ? \Carbon\Carbon::parse($patient->date_of_birth)->format('Y-m-d') : '') }}">
                    @error('date_of_birth')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

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

                <div class="input-group input-group-static mb-4">
                    <label for="address">العنوان</label>
                    <textarea name="address" id="address" 
                              class="form-control @error('address') is-invalid @enderror" 
                              rows="2">{{ old('address', $patient->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Add companion name and phone --}}
                {{-- <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">بيانات المرافق الافتراضية</h6>
                        <small class="text-muted">هذه البيانات تستخدم كقيم افتراضية عند إضافة زيارات جديدة</small>
                    </div>
                </div>
                
                <div class="input-group input-group-static mb-4">
                    <label for="companion_name">اسم المرافق</label>
                    <input type="text" name="companion_name" id="companion_name" 
                           class="form-control @error('companion_name') is-invalid @enderror" 
                           value="{{ old('companion_name', $patient->companion_name) }}">
                    @error('companion_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="companion_phone">هاتف المرافق</label>
                    <input type="text" name="companion_phone" id="companion_phone" 
                           class="form-control @error('companion_phone') is-invalid @enderror" 
                           value="{{ old('companion_phone', $patient->companion_phone) }}">
                    @error('companion_phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="companion_relation">صلة القرابة</label>
                    <input type="text" name="companion_relation" id="companion_relation" 
                           class="form-control @error('companion_relation') is-invalid @enderror" 
                           value="{{ old('companion_relation', $patient->companion_relation) }}">
                    @error('companion_relation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group input-group-static mb-4">
                    <label for="companion_national_id">الرقم القومي للمرافق</label>
                    <input type="text" name="companion_national_id" id="companion_national_id" 
                           class="form-control @error('companion_national_id') is-invalid @enderror" 
                           value="{{ old('companion_national_id', $patient->companion_national_id) }}" maxlength="14">
                    @error('companion_national_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> --}}

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success" id="saveBtn">
                        <i class="material-icons">save</i> حفظ التعديلات
                    </button>
                    <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-secondary">
                        <i class="material-icons">arrow_back</i> العودة
                    </a>
                </div>
            </form>

            <!-- Form to upload attachments -->
        <form action="{{ route('patients.attachments.upload', $patient->id) }}" method="POST" enctype="multipart/form-data" class="mb-3 mt-4" id="attachmentForm">                @csrf
                <!-- attachments card here -->
            </form>

            <!-- Display Existing Attachments -->
            @if($patient->attachments->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h6>المرفقات الموجودة ({{ $patient->attachments->count() }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>اسم الملف</th>
                                    <th>النوع</th>
                                    <th>الحجم</th>
                                    <th>الوصف</th>
                                    <th>تاريخ الرفع</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patient->attachments()->latest()->get() as $attachment)
                                    <tr>
                                        <td>
                                            <i class="material-icons opacity-10">
                                                @if($attachment->type === 'image')
                                                    image
                                                @elseif($attachment->type === 'document')
                                                    description
                                                @else
                                                    attachment
                                                @endif
                                            </i>
                                            {{ Str::limit($attachment->original_name, 30) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-gradient-{{ $attachment->type === 'image' ? 'success' : ($attachment->type === 'document' ? 'info' : 'secondary') }}">
                                                {{ $attachment->type === 'image' ? 'صورة' : ($attachment->type === 'document' ? 'مستند' : 'أخرى') }}
                                            </span>
                                        </td>
                                        <td>{{ $attachment->formatted_size }}</td>
                                        <td>{{ $attachment->description ?? '-' }}</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $attachment->created_at->format('Y-m-d H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ $attachment->url }}" class="btn btn-sm btn-outline-primary" target="_blank" title="عرض">
                                                <i class="material-icons">visibility</i>
                                            </a>
                                            <a href="{{ $attachment->url }}" class="btn btn-sm btn-outline-success" download="{{ $attachment->original_name }}" title="تحميل">
                                                <i class="material-icons">download</i>
                                            </a>
                                            <form action="{{ route('patients.attachments.delete', [$patient->id, $attachment->id]) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المرفق؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                    <i class="material-icons">delete</i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Patient Visits Management Section -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>إدارة دخول وخروج المريض</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVisitModal">
                <i class="material-icons">add</i> إضافة جديد
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
                                            <i class="material-icons">edit</i>
                                        </button>
                                        <form action="{{ route('patient_visits.destroy', $visit->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="material-icons">delete</i>
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
                    <i class="material-icons" style="font-size: 3rem;">calendar_today</i>
                    <p class="mt-2">لا توجد سجلات مسجلة لهذا المريض</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVisitModal">
                        <i class="material-icons">add</i> إضافة أول سجل
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
                        <button type="submit" class="btn btn-success">
                            <i class="material-icons">save</i> حفظ
                        </button>
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
                        <button type="submit" class="btn btn-warning">
                            <i class="material-icons">update</i> تحديث
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Medical ID generation
    const btn = document.getElementById('generateMedicalIdBtn');
    const input = document.getElementById('medical_id');
    const info = document.getElementById('medicalIdInfo');
    
    if (btn) {
        btn.addEventListener('click', function () {
            btn.disabled = true;
            btn.innerHTML = '<i class="material-icons">hourglass_empty</i> جاري التوليد...';
            
            fetch('{{ route("generate.medical.id") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.medical_id) {
                    input.value = data.medical_id;
                    info.innerHTML = '<i class="material-icons text-success">check_circle</i> تم جلب رقم طبي جديد: <strong>' + data.medical_id + '</strong>';
                    
                    // Clear success message after 3 seconds
                    setTimeout(() => {
                        info.innerHTML = '';
                    }, 3000);
                } else {
                    info.innerHTML = '<i class="material-icons text-danger">error</i> <span class="text-danger">تعذر جلب رقم طبي جديد</span>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                info.innerHTML = '<i class="material-icons text-danger">error</i> <span class="text-danger">تعذر الاتصال بالخادم</span>';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="material-icons">refresh</i> توليد رقم جديد';
            });
        });
    }

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

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    toast.innerHTML = `
        <i class="material-icons opacity-10">${type === 'success' ? 'check_circle' : 'error'}</i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        toast.classList.add('fade');
        setTimeout(() => {
            toast.remove();
        }, 500);
    }, 3000);
}

// Visit management functions (same as before)
function editVisit(visitId) {
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
        document.getElementById('edit_visit_type').value = data.type;
        document.getElementById('edit_visit_at').value = data.visit_at.substring(0, 16);
        document.getElementById('edit_visit_department_id').value = data.department_id || '';
        document.getElementById('edit_visit_bed_id').value = data.bed_id || '';
        document.getElementById('edit_visit_companion_name').value = data.companion_name || '';
        document.getElementById('edit_visit_companion_relation').value = data.companion_relation || '';
        document.getElementById('edit_visit_companion_phone').value = data.companion_phone || '';
        document.getElementById('edit_visit_companion_national_id').value = data.companion_national_id || '';
        document.getElementById('edit_visit_notes').value = data.notes || '';
        
        // Set the form action URL
        document.getElementById('editVisitForm').action = `{{ url('admin/patients') }}/{{ $patient->id }}/visits/${visitId}`;
        
        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('editVisitModal'));
        modal.show();
    })
    .catch(error => {
        console.error('Error fetching visit data:', error);
        showToast('حدث خطأ في تحميل بيانات الزيارة: ' + error.message, 'danger');
    });
}

// Handle form submissions with AJAX
document.getElementById('addVisitForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="material-icons">hourglass_empty</i> جارٍ الحفظ...';
    
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
            const modal = bootstrap.Modal.getInstance(document.getElementById('addVisitModal'));
            modal.hide();
            showToast(data.message || 'تم حفظ الزيارة بنجاح', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'حدث خطأ أثناء حفظ الزيارة', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('حدث خطأ أثناء حفظ الزيارة: ' + error.message, 'danger');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
});

document.getElementById('editVisitForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="material-icons">hourglass_empty</i> جارٍ التحديث...';
    
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
            const modal = bootstrap.Modal.getInstance(document.getElementById('editVisitModal'));
            modal.hide();
            showToast(data.message || 'تم تحديث الزيارة بنجاح', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'حدث خطأ أثناء تحديث الزيارة', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('حدث خطأ أثناء تحديث الزيارة: ' + error.message, 'danger');
    })
    .finally(() => {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
});
</script>
@endpush
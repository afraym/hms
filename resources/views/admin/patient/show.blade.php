@extends('layouts.admin')

@push('styles')
<style>
    .patient-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: rgb(211, 195, 195);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .info-badge {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        padding: 8px 15px;
        margin: 5px 0;
        display: inline-block;
    }
    
    .status-active {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
    }
    
    .companion-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-radius: 15px;
        padding: 15px;
        margin: 10px 0;
    }
    
    .visit-table {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4><i class="material-icons">person</i> التفاصيل الشخصية</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>الاسم الكامل:</strong> 
                        {{ $patient->full_name }} 
                        {{-- {{ $patient->second_name }} 
                        {{ $patient->third_name }} 
                        {{ $patient->fourth_name }} --}}
                    </p>
                    <p><strong>الرقم الطبي:</strong> 
                        <span class="badge bg-gradient-primary">{{ $patient->medical_id ?? 'غير محدد' }}</span>
                    </p>
                    <p><strong>رقم التأمين الصحي الشامل:</strong> 
                        @if($patient->uhi_number)
                            <span class="badge bg-gradient-info">{{ $patient->uhi_number }}</span>
                            <img src="{{ asset('assets/img/uhi.png') }}" alt="UHI Icon" style="width: 20px; height: 25px; vertical-align: middle; margin-right: 5px;">
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </p>
                    <p><strong>رقم الهاتف:</strong> {{ $patient->phone ?? 'غير محدد' }}</p>
                    <p><strong>البريد الإلكتروني:</strong> {{ $patient->email ?? 'غير محدد' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>الرقم القومي:</strong> 
                        @if($patient->national_id)
                            <span class="badge bg-gradient-secondary">{{ $patient->national_id }}</span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </p>
                    <p><strong>العنوان:</strong> {{ $patient->address ?? 'غير محدد' }}</p>
                    <p><strong>المحافظة:</strong> {{ $patient->governorate ?? 'غير محدد' }}</p>
                    <p><strong>الحالة:</strong> 
                        @if($patient->status == 'admitted')
                            <span class="badge bg-gradient-success">مُدخل</span>
                        @elseif($patient->status == 'discharged')
                            <span class="badge bg-gradient-secondary">خارج</span>
                        @elseif($patient->status == 'waiting')
                            <span class="badge bg-gradient-warning">في الانتظار</span>
                        @elseif($patient->status == 'deceased')
                            <span class="badge bg-gradient-dark">متوفى</span>
                        @else
                            <span class="badge bg-gradient-light">غير محدد</span>
                        @endif
                    </p>
                    <p><strong>تاريخ التسجيل:</strong> 
                        {{ $patient->created_at ? $patient->created_at->timezone('Africa/Cairo')->format('Y-m-d H:i') : 'غير محدد' }}
                    </p>
                </div>
            </div>
            
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>تاريخ الميلاد:</strong> 
                        @if($patient->date_of_birth)
                            {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('Y-m-d') }} 
                            <span class="text-muted">({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} سنة)</span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </p>
                    <p><strong>الجنس:</strong> 
                        @if($patient->gender == 'male')
                            <span class="badge bg-gradient-info">ذكر</span>
                        @elseif($patient->gender == 'female')
                            <span class="badge bg-gradient-primary">أنثى</span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>القسم الحالي:</strong> 
                        @php
                            $currentVisit = $patient->visits()->where('type', 'in')->whereNotNull('department_id')->latest('visit_at')->first();
                        @endphp
                        @if($currentVisit && $currentVisit->department)
                            <span class="badge bg-gradient-success">{{ $currentVisit->department->name }}</span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </p>
                    <p><strong>السرير الحالي:</strong> 
                        @php
                            $currentBed = $patient->visits()->where('type', 'in')->whereNotNull('bed_id')->latest('visit_at')->first();
                        @endphp
                        @if($currentBed && $currentBed->bed)
                            <span class="badge bg-gradient-warning">{{ $currentBed->bed->bed_number }}</span>
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </p>
                </div>
            </div>
            
            <hr>
            <h5>تفاصيل الدخول والخروج</h5>
            @if($patient->visits->isNotEmpty())
                <div class="visit-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>نوع </th>
                                <th>تاريخ </th>
                                <th>القسم</th>
                                <th>السرير</th>
                                <th>اسم المرافق</th>
                                <th>صلة القرابة</th>
                                <th>هاتف المرافق</th>
                                <th>الرقم القومي للمرافق</th>
                                <th>ملاحظات</th>
                                <!-- <th>الإجراءات</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patient->visits as $visit)
                                <tr>
                                    <td>{{ $visit->type == 'in' ? 'دخول' : 'خروج' }}</td>
                                    <td>{{ $visit->visit_at }}</td>
                                    <td>{{ $visit->department->name ?? 'غير محدد' }}</td>
                                    <td>{{ $visit->bed->bed_number ?? 'غير محدد' }}</td>
                                    <td>{{ $visit->companion_name ?? 'غير محدد' }}</td>
                                    <td>{{ $visit->companion_relation ?? 'غير محدد' }}</td>
                                    <td>{{ $visit->companion_phone ?? 'غير محدد' }}</td>
                                    <td>{{ $visit->companion_national_id ?? 'غير محدد' }}</td>
                                    <td>{{ Str::limit($visit->notes ?? 'لا توجد', 30) }}</td>
                                    <!-- <td>
                                        <button class="btn bg-gradient-warning" onclick="editVisit({{ $visit->id }})" title="تعديل">
                                            <i class="material-icons">edit</i>
                                        </button>
                                        <form action="{{ route('patients.visits.delete', [$patient->id, $visit->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الزيارة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn bg-gradient-danger" title="حذف">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    </td> -->
                                </tr>
                                
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p>لا توجد تفاصيل دخول أو خروج لهذا المريض.</p>
            @endif

            <hr>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('patients.edit', $patient->id) }}" class="btn bg-gradient-warning">
                    <i class="material-icons">edit</i> تعديل البيانات
                </a>
                <!-- <a href="#" class="btn bg-gradient-secondary">
                    <i class="material-icons">print</i> طباعة الملصقات
                </a> -->
                {{-- <a href="{{ route('patients.visits.create', $patient->id) }}" class="btn bg-gradient-info">
                    <i class="material-icons">add</i> إضافة زيارة جديدة
                </a> --}}
                @if($patient->status !== 'discharged')
                    <form action="{{ route('patients.discharge', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد تسجيل خروج هذا المريض؟');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn bg-gradient-success">
                            <i class="material-icons">logout</i> تسجيل خروج
                        </button>
                    </form>
                @endif
                @if($patient->status !== 'deceased')
                    <form action="{{ route('patients.deceased', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من تسجيل وفاة هذا المريض؟');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn bg-gradient-dark">
                            <i class="material-icons">sentiment_very_dissatisfied</i> تسجيل وفاة
                        </button>
                    </form>
                @endif
                <a href="{{ route('patients.index') }}" class="btn bg-gradient-secondary">
                    <i class="material-icons">arrow_back</i> رجوع
                </a>
            </div>
        </div>
    </div>

    <div class="card mt-4">
    <div class="card-header">
        <h5>المرفقات</h5>
    </div>
    <div class="card-body">
        @if($patient->attachments->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>اسم الملف</th>
                            <th>النوع</th>
                            <th>الوصف</th>
                            <th>تاريخ الرفع</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patient->attachments as $attachment)
                            <tr>
                                <td>{{ $attachment->original_name }}</td>
                                <td>{{ $attachment->type }}</td>
                                <td>{{ $attachment->description }}</td>
                                <td>{{ $attachment->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                    <a href="{{ $attachment->url }}" class="btn bg-gradient-info" target="_blank">
                                        <i class="material-icons">visibility</i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted">لا توجد مرفقات</p>
        @endif
    </div>    </div>
</div>

<!-- Edit Visit Modal -->
<div class="modal fade" id="editVisitModal" tabindex="-1" aria-labelledby="editVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVisitModalLabel">تعديل الزيارة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editVisitForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-4">
                                <label for="edit_visit_type">نوع الزيارة</label>
                                <select name="type" id="edit_visit_type" class="form-control" required>
                                    <option value="in">دخول</option>
                                    <option value="out">خروج</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-4">
                                <label for="edit_visit_at">تاريخ ووقت الزيارة</label>
                                <input type="datetime-local" name="visit_at" id="edit_visit_at" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-4">
                                <label for="edit_visit_department_id">القسم</label>
                                <select name="department_id" id="edit_visit_department_id" class="form-control">
                                    <option value="">اختر القسم</option>
                                    @foreach(App\Models\Department::all() as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-4">
                                <label for="edit_visit_bed_id">السرير</label>
                                <select name="bed_id" id="edit_visit_bed_id" class="form-control">
                                    <option value="">اختر السرير</option>
                                    @foreach(App\Models\Bed::all() as $bed)
                                        <option value="{{ $bed->id }}">{{ $bed->bed_number }} - {{ $bed->room_number }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <h6>بيانات المرافق</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-4">
                                <label for="edit_visit_companion_name">اسم المرافق</label>
                                <input type="text" name="companion_name" id="edit_visit_companion_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-4">
                                <label for="edit_visit_companion_relation">صلة القرابة</label>
                                <input type="text" name="companion_relation" id="edit_visit_companion_relation" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-4">
                                <label for="edit_visit_companion_phone">هاتف المرافق</label>
                                <input type="text" name="companion_phone" id="edit_visit_companion_phone" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-static mb-4">
                                <label for="edit_visit_companion_national_id">الرقم القومي للمرافق</label>
                                <input type="text" name="companion_national_id" id="edit_visit_companion_national_id" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="input-group input-group-static mb-4">
                        <label for="edit_visit_notes">ملاحظات</label>
                        <textarea name="notes" id="edit_visit_notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn bg-gradient-success">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


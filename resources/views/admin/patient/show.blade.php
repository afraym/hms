@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>تفاصيل المريض</h4>
        </div>
        <div class="card-body">
            <p><strong>الاسم الكامل:</strong> 
                {{ $patient->full_name }} 
                {{-- {{ $patient->second_name }} 
                {{ $patient->third_name }} 
                {{ $patient->fourth_name }} --}}
            </p>
            <p><strong>البريد الإلكتروني:</strong> {{ $patient->email }}</p>
            <p><strong>رقم الهاتف:</strong> {{ $patient->phone }}</p>
            <p><strong>الرقم القومي:</strong> {{ $patient->national_id }}</p>
            <p><strong>تاريخ الميلاد:</strong> 
                {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('Y-m-d') }} 
                ({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} سنة)
            </p>
            <p><strong>الجنس:</strong> 
                @if($patient->gender == 'male')
                    ذكر
                @elseif($patient->gender == 'female')
                    أنثى
                @else
                    غير محدد
                @endif
            </p>
            
            <hr>
            <h5>تفاصيل الدخول والخروج</h5>
            @if($patient->visits->isNotEmpty())
                <table class="table">
                    <thead>
                        <tr>
                            <th>نوع التردد</th>
                            <th>تاريخ التردد</th>
                            <th>القسم</th>
                            <th>السرير</th>
                            <th>اسم المرافق</th>
                            <th>صلة القرابة</th>
                            <th>هاتف المرافق</th>
                            <th>الرقم القومي للمرافق</th>
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
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>لا توجد تفاصيل دخول أو خروج لهذا المريض.</p>
            @endif

            <p><strong>بيانات المرافق:</strong></p>
<ul>
    <li><strong>الاسم:</strong> {{ $patient->companion_name ?: 'غير محدد' }}</li>
    <li><strong>الاسم:</strong> {{ $patient->companion_national_id ?: 'غير محدد' }}</li>
    <li><strong>الهاتف:</strong> {{ $patient->companion_phone ?: 'غير محدد' }}</li>
    <li><strong>صلة القرابة:</strong> {{ $patient->companion_relation ?: 'غير محدد' }}</li>
</ul>

            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-warning">تعديل</a>
             <a href="{{ route('patients.print.label', $patient->id) }}" class="btn bg-gradient-secondary">
                                            طباعة الملصقات
                                        </a>
                                        
            <form action="{{ route('patients.discharge', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد تسجيل خروج هذا المريض؟');">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-secondary">تسجيل خروج</button>
            </form>
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">رجوع</a>
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
                                    <a href="{{ $attachment->url }}" class="btn btn-sm btn-info" target="_blank">
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
    </div>
</div>
</div>
@endsection
@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <h4>تفاصيل المريض</h4>
        </div>
        <div class="card-body">
            <p><strong>الاسم الكامل:</strong> 
                {{ $patient->first_name }} 
                {{ $patient->second_name }} 
                {{ $patient->third_name }} 
                {{ $patient->fourth_name }}
            </p>
            <p><strong>البريد الإلكتروني:</strong> {{ $patient->email }}</p>
            <p><strong>رقم الهاتف:</strong> {{ $patient->phone }}</p>
            <p><strong>الرقم القومي:</strong> {{ $patient->national_id }}</p>
            <p><strong>تاريخ الميلاد:</strong> {{ $patient->date_of_birth }}</p>
            <p><strong>الجنس:</strong> {{ $patient->gender }}</p>
            
            <hr>
            <h5>تفاصيل الزيارات</h5>
            @if($patient->visits->isNotEmpty())
                <table class="table">
                    <thead>
                        <tr>
                            <th>نوع الزيارة</th>
                            <th>تاريخ الزيارة</th>
                            <th>ملاحظات</th>
                            <th>السرير</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patient->visits as $visit)
                            <tr>
                                <td>{{ $visit->type == 'in' ? 'دخول' : 'خروج' }}</td>
                                <td>{{ $visit->visit_at }}</td>
                                <td>{{ $visit->notes ?? 'لا توجد ملاحظات' }}</td>
                                <td>{{ $visit->bed->bed_number ?? 'غير محدد' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>لا توجد زيارات لهذا المريض.</p>
            @endif

            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-warning">تعديل</a>
            <form action="{{ route('patients.discharge', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد تسجيل خروج هذا المريض؟');">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-secondary">تسجيل خروج</button>
            </form>
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">رجوع</a>
        </div>
    </div>
</div>
@endsection
@extends('layouts.admin') 

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>قائمة المرضى</h6>
                    <a href="{{ route('patients.create') }}" class="btn btn-primary">إضافة مريض جديد</a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 text-end">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>رقم الجوال</th>
                                    <th>الرقم الوطني</th>
                                    <th>تاريخ الميلاد</th>
                                    <th>الجنس</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patients as $patient)
                                <tr>
                                    <td>{{ $patient->id }}</td>
                                    <td>
                                        {{ $patient->first_name }}
                                        {{ $patient->second_name }}
                                        {{ $patient->third_name }}
                                        {{ $patient->fourth_name }}
                                    </td>
                                    <td>{{ $patient->email }}</td>
                                    <td>{{ $patient->phone }}</td>
                                    <td>{{ $patient->national_id }}</td>
                                    <td>{{ $patient->date_of_birth }}</td>
                                    <td>{{ $patient->gender }}</td>
                                    <td>
                                        <form action="{{ route('patients.discharge', $patient->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger">تسجيل خروج</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                @if($patients->isEmpty())
                                <tr>
                                    <td colspan="8" class="text-center">لا يوجد مرضى</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
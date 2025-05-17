@extends('layouts.admin') 

@section('content')
<div class="container-fluid py-2">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
                <div class="card-header pb-0">
                    <h6>قائمة المرضى</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 text-end">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الاسم</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">البريد الإلكتروني</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">رقم الجوال</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الرقم الوطني</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">تاريخ الميلاد</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الجنس</th>
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
                                </tr>
                                @endforeach
                                @if($patients->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center">لا يوجد مرضى</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
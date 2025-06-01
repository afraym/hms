{{-- filepath: resources/views/admin/patient_visits/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between">
                    <h6>زيارات المرضى</h6>
                    <a href="{{ route('patient_visits.create') }}" class="btn btn-primary btn-sm">إضافة زيارة</a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">اسم المريض</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">نوع الزيارة</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">تاريخ الزيارة</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ملاحظات</th>
                                    <th class="text-secondary opacity-7">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visits as $visit)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $visit->patient->first_name }} 
                                        {{ $visit->patient->second_name }} 
                                        {{ $visit->patient->third_name }} 
                                        {{ $visit->patient->fourth_name }}
                                    </td>
                                    <td>
                                        <span class="badge badge-sm 
                                            {{ $visit->type == 'in' ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                            {{ $visit->type == 'in' ? 'دخول' : 'خروج' }}
                                        </span>
                                    </td>
                                    <td>{{ $visit->visit_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $visit->notes ?? 'لا توجد ملاحظات' }}</td>
                                    <td>
                                        <a href="{{ route('patient_visits.edit', $visit) }}" class="btn btn-link text-dark px-3 mb-0">
                                            <i class="material-symbols-rounded text-sm me-2">edit</i>تعديل
                                        </a>
                                        <form action="{{ route('patient_visits.destroy', $visit) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0" onclick="return confirm('هل أنت متأكد؟')">
                                                <i class="material-symbols-rounded text-sm me-2">delete</i>حذف
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد زيارات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
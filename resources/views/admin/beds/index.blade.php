{{-- filepath: c:\laragon\www\hms\resources\views\admin\beds\index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>قائمة الأسرة</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 text-end">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">رقم السرير</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">رقم الغرفة</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">الحالة</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">اسم المريض</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">تاريخ الإنشاء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($beds as $bed)
                                <tr>
                                    <td>{{ $bed->id }}</td>
                                    <td>{{ $bed->bed_number }}</td>
                                    <td>{{ $bed->room_number }}</td>
                                    <td>{{ $bed->status }}</td>
                                    <td>{{ $bed->patient_name ?? '-' }}</td>
                                    <td>{{ $bed->created_at ? $bed->created_at->format('Y-m-d') : '-' }}</td>
                                </tr>
                                @endforeach
                                @if($beds->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">لا يوجد أسرة</td>
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
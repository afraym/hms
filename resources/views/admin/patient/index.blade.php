@extends('layouts.admin') 

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-lg-12">
            <div class="card my-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>قائمة المرضى</h6>
                    <a href="{{ route('patients.create') }}" class="btn btn-primary">إضافة مريض جديد</a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 text-end">
                            <thead>
                                <tr>
                                    <th>الاسم الأول</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>رقم الهاتف</th>
                                    <th>الرقم القومي</th>
                                    <th>الجنس</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patients as $patient)
                                <tr>
                                    <td>{{ $patient->first_name }}</td>
                                    <td>{{ $patient->email }}</td>
                                    <td>{{ $patient->phone }}</td>
                                    <td>{{ $patient->national_id }}</td>
                                    <td>{{ $patient->gender }}</td>
                                    <td>
                                        <a href="{{ route('patients.show', $patient->id) }}" class="btn bg-gradient-info">عرض</a>
                                        <a href="{{ route('patients.edit', $patient->id) }}" class="btn bg-gradient-warning">تعديل</a>
                                        <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المريض؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn bg-gradient-danger" aria-label="حذف المريض" title="حذف المريض">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                @if($patients->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center">لا يوجد مرضى</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination links --}}
                    <div class="d-flex justify-content-center">
                        {{ $patients->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
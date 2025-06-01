{{-- filepath: c:\laragon\www\hms\resources\views\admin\departments\index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>قائمة الأقسام</h6>
                    <a href="{{ route('departments.create') }}" class="btn btn-primary">إضافة قسم جديد</a>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>اسم القسم</th>
                                    <th>عدد الأسرة</th>
                                    <th>عدد المرضى</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop through departments --}}
                                @forelse($departments as $department)
                                <tr>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->beds_count }}</td>
                                    <td>{{ $department->patients_count }}</td>
                                    <td>{{ $department->status }}</td>
                                    <td>
                                        <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-sm btn-warning">تعديل</a>
                                        <form action="{{ route('departments.destroy', $department->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد أقسام</td>
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
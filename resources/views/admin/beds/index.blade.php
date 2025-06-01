{{-- filepath: c:\laragon\www\hms\resources\views\admin\beds\index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>قائمة الأسرة</h6>
                    <a href="{{ route('beds.create') }}" class="btn btn-primary">إضافة سرير جديد</a>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th>رقم السرير</th>
                                    <th>رقم الغرفة</th>
                                    <th>القسم</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Loop through beds --}}
                                @forelse($beds as $bed)
                                <tr>
                                    <td>{{ $bed->bed_number }}</td>
                                    <td>{{ $bed->room_number }}</td>
                                    <td>{{ $bed->department->name ?? 'غير محدد' }}</td>
                                    <td>{{ $bed->status }}</td>
                                    <td>
                                        <a href="{{ route('beds.edit', $bed->id) }}" class="btn btn-sm btn-warning">تعديل</a>
                                        <form action="{{ route('beds.destroy', $bed->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد أسرة</td>
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
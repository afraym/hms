{{-- filepath: c:\laragon\www\hms\resources\views\admin\department\create.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header pb-0">
          <h6>إضافة قسم جديد</h6>
        </div>
        <div class="card-body">
          <form action="{{ route('departments.store') }}" method="POST">
            @csrf
            <div class="form-group">
              <label for="name">اسم القسم</label>
              <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="status">الحالة</label>
              <select name="status" id="status" class="form-control" required>
                <option value="نشط">نشط</option>
                <option value="غير نشط">غير نشط</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary mt-3">إضافة</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
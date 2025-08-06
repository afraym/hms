@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header pb-0">
          <h6>إضافة قسم جديد</h6>
        </div>
        <div class="card-body">
          <form action="{{ route('departments.store') }}" method="POST">
            @csrf
            <div class="input-group input-group-dynamic mb-4">
              <label for="name" class="form-label">اسم القسم</label>
              <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="input-group input-group-static mb-4">
              <label for="status" class="ms-0">الحالة</label>
              <select name="is_active" id="status" class="form-control" required>
                <option value="1">نشط</option>
                <option value="0">غير نشط</option>
              </select>
            </div>
            <button type="submit" class="btn bg-gradient-info mt-3">إضافة</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
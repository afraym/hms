@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>إضافة سرير جديد</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('beds.store') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="col-md-6 mb-3">
                        <div class="input-group input-group-outline my-3">
                            <label for="bed_number" class="form-label">رقم السرير</label>
                            <input type="text" class="form-control @error('bed_number') is-invalid @enderror" id="bed_number" name="bed_number" value="{{ old('bed_number') }}">
                            @error('bed_number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        <div class="col-md-6 mb-3">
                        <div class="input-group input-group-outline my-3">
                            <label for="room_number" class="form-label">رقم الغرفة</label>
                            <input type="text" class="form-control @error('room_number') is-invalid @enderror" id="room_number" name="room_number" value="{{ old('room_number') }}">
                            @error('room_number') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        <div class="col-md-6 mb-3">
                        <div class="input-group input-group-outline my-3">
                            <label for="department" class="form-label">القسم</label>
                            <input type="text" class="form-control @error('department') is-invalid @enderror" id="department" name="department" value="{{ old('department') }}">
                            @error('department') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        <div class="col-md-6 mb-3">
                        <div class="input-group input-group-static mb-3">
                            <label for="status" class="form-label">الحالة</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="">اختر الحالة</option>
                                <option value="متاح" {{ old('status') == 'متاح' ? 'selected' : '' }}>متاح</option>
                                <option value="محجوز" {{ old('status') == 'محجوز' ? 'selected' : '' }}>محجوز</option>
                                <option value="صيانة" {{ old('status') == 'صيانة' ? 'selected' : '' }}>صيانة</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        </div>
                        <button type="submit" class="btn btn-dark">حفظ</button>
                        <a href="{{ route('beds.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
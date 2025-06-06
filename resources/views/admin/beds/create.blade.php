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
                    <form id="bedForm" action="{{ route('beds.store') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-dynamic mb-4">
                                <label for="bed_number" class="form-label">رقم السرير</label>
                                <input type="text" class="form-control @error('bed_number') is-invalid @enderror" id="bed_number" name="bed_number" value="{{ old('bed_number') }}">
                                @error('bed_number') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-dynamic mb-4">
                                <label for="room_number" class="form-label">رقم الغرفة</label>
                                <input type="text" class="form-control @error('room_number') is-invalid @enderror" id="room_number" name="room_number" value="{{ old('room_number') }}">
                                @error('room_number') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="department_id" class="ms-0">القسم</label>
                                <select class="form-control @error('department_id') is-invalid @enderror" id="department_id" name="department_id">
                                    <option value="" disabled selected>اختر القسم</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="input-group input-group-static mb-4">
                                <label for="status" class="ms-0">الحالة</label>
                                <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
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

<script>
    $(document).ready(function () {
        $('#bedForm').on('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const form = $(this);
            const formData = form.serialize();

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                success: function (response) {
                    $.notify("تم حفظ السرير بنجاح!", {
                        className: "success",
                        position: "top center" // Position the toast in the center
                    });
                    form[0].reset(); // Reset the form inputs
                },
                error: function (xhr) {
                    let errorMessage = "حدث خطأ أثناء حفظ البيانات.";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join("<br>");
                    }
                    $.notify(errorMessage, {
                        className: "error",
                        position: "top center" // Position the toast in the center
                    });
                }
            });
        });
    });
</script>
@endsection
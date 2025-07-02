@extends('layouts.admin')

@push('styles')
<!-- Bootstrap Icons for file input icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<!-- Additional FontAwesome for file input icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<!-- jQuery Toast Plugin CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css" rel="stylesheet">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom styling for Select2 with Arabic */
    .select2-container--default .select2-selection--single {
        height: 40px;
        border: 1px solid #d2d6da;
        border-radius: 0.375rem;
        padding: 6px 12px;
        direction: rtl;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #495057;
        line-height: 28px;
        padding-right: 0;
        padding-left: 20px;
        text-align: right;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: auto;
        left: 1px;
    }
    
    .select2-dropdown {
        direction: rtl;
        text-align: right;
    }
    
    .select2-container--default .select2-results__option {
        text-align: right;
        padding: 6px 12px;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        text-align: right;
        direction: rtl;
    }
    
    /* Focus styling */
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
<!-- Custom styles for file upload zone -->
<style>
.file-drop-zone {
    border: 2px dashed #007bff;
    border-radius: 10px;
    text-align: center;
    padding: 40px 20px;
    margin: 20px 0;
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}
.file-drop-zone:hover {
    border-color: #0056b3;
    background-color: #e9ecef;
}
.file-drop-zone-title {
    font-size: 1.1rem;
    font-weight: 500;
    color: #495057;
    margin-bottom: 10px;
}
.file-preview {
    margin-top: 20px;
}
.kv-fileinput-caption {
    text-align: right !important;
}
.file-loading:before {
    content: "جاري تحميل منطقة الملفات...";
    display: block;
    text-align: center;
    padding: 20px;
    color: #6c757d;
}
/* FontAwesome Icons spin animation */
.fa-spin, .fas.fa-spin {
    animation: fa-spin 1s linear infinite;
}
@keyframes fa-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
/* Ensure FontAwesome Icons display properly */
.fas, .far, .fab {
    font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands" !important;
    font-style: normal;
    font-weight: 900;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
    vertical-align: -.125em;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>إضافة مريض جديد</h6>
                </div>
                <div class="card-body">
                    <form id="patientForm" action="{{ route('patients.store') }}" method="POST" autocomplete="on" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="national_id" class="form-label">الرقم القومي</label>
                                    <input type="text" class="form-control @error('national_id') is-invalid @enderror"
                                     id="national_id" name="national_id" value="{{ old('national_id') }}" maxlength="14">
                                    {{-- <div id="nationalIdInfo" class="form-text text-muted"></div> --}}
                                </div>
                                @error('national_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

<div class="col-md-6 mb-3">
    <div class="input-group input-group-dynamic mb-3">
        <label for="full_name" class="form-label">الاسم كاملاً</label>
        <input type="text" 
               class="form-control @error('full_name') is-invalid @enderror" 
               id="full_name" 
               name="full_name" 
               value="{{ old('full_name') }}">
    </div>
    @error('full_name') <span class="text-danger">{{ $message }}</span> @enderror
</div>

                            {{-- <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                     id="email" name="email" value="{{ old('email') }}">
                                </div>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div> --}}

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                     id="phone" name="phone" value="{{ old('phone') }}">
                                </div>
                                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                           

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="address" class="form-label">العنوان</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                     id="address" name="address" value="{{ old('address') }}">
                                </div>
                                @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-static mb-3">
                                    <label for="department_id"> القسم </label> <span style="padding-right: 1%;" class="material-icons-round">domain
</span> 
                                    <select name="department_id" id="department_id" class="form-control @error('department_id') is-invalid @enderror" >
                                        <option value="">اختر القسم</option>
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
                                <div class="input-group input-group-static mb-3">
                                    <label for="bed_id" class="ms-0">اختر السرير</label> <span style="padding-right: 1%;" class="material-icons-round">
hotel
</span>
                                    <select class="form-control @error('bed_id') is-invalid @enderror" id="bed_id" name="bed_id">
                                        <option value="">اختر السرير</option>
                                        @foreach(\App\Models\Bed::where('status', 'متاح')->get() as $bed)
                                            <option value="{{ $bed->id }}" {{ old('bed_id') == $bed->id ? 'selected' : '' }}>
                                                {{ $bed->bed_number }} - {{ $bed->department }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('bed_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="companion_name" class="form-label">اسم المُرافق</label>
                                    <input type="text" class="form-control @error('companion_name') is-invalid @enderror"
                                           id="companion_name" name="companion_name" value="{{ old('companion_name') }}">
                                </div>
                                @error('companion_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="companion_national_id" class="form-label">الرقم القومي للمُرافق</label>
                                    <input type="text" 
                                           class="form-control @error('companion_national_id') is-invalid @enderror"
                                           id="companion_national_id" 
                                           name="companion_national_id" 
                                           value="{{ old('companion_national_id') }}" 
                                           maxlength="14">
                                </div>
                                @error('companion_national_id') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                             <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="companion_phone" class="form-label">رقم هاتف المُرافق</label>
                                    <input type="text" class="form-control @error('companion_phone') is-invalid @enderror"
                                     id="companion_phone" name="companion_phone" value="{{ old('companion_phone') }}">
                                </div>
                                @error('companion_phone') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="companion_relation" class="form-label">صلة القرابة</label>
                                    <input type="text" class="form-control @error('companion_relation') is-invalid @enderror"
                                           id="companion_relation" name="companion_relation" value="{{ old('companion_relation') }}">
                                </div>
                                @error('companion_relation') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-static mb-3 is-filled">
                                    <label for="medicalId" class="form-label">الرقم الطبي:</label>
                                    <input type="text" id="medicalId" name="medical_id" value="{{ $medicalId }}" class="form-control" autofocus>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="uhi_number" class="form-label">
                                        رقم التأمين الصحي الشامل
                                        <img src="{{ asset('assets/img/uhi.png') }}" alt="UHI Icon" style="width: 20px; height: 25px; vertical-align: middle;">
                                        :
                                    </label>
                                    <input type="text" name="uhi_number" id="uhi_number" class="form-control" value="{{ old('uhi_number') }}">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-static my-3">
                                    <label for="date_of_birth">تاريخ الميلاد</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                     id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                </div>
                                @error('date_of_birth') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                                                        <div class="col-md-6 mb-3">
                                <div class="input-group input-group-static mb-3">
                                    <label for="governorate" class="ms-0">المحافظة </label>
                                    <select id="governorate" name="governorate" class="form-control @error('governorate') is-invalid @enderror" >
                                        <option value="">اختر المحافظة</option>
                                        <option value="أسوان" {{ old('governorate') == 'أسوان' ? 'selected' : '' }}>أسوان</option>
                                        <option value="الأقصر" {{ old('governorate') == 'الأقصر' ? 'selected' : '' }}>الأقصر</option>
                                        <option value="قنا" {{ old('governorate') == 'قنا' ? 'selected' : '' }}>قنا</option>
                                        <option value="سوهاج" {{ old('governorate') == 'سوهاج' ? 'selected' : '' }}>سوهاج</option>
                                        <option value="أسيوط" {{ old('governorate') == 'أسيوط' ? 'selected' : '' }}>أسيوط</option>
                                        <option value="المنيا" {{ old('governorate') == 'المنيا' ? 'selected' : '' }}>المنيا</option>
                                        <option value="بني سويف" {{ old('governorate') == 'بني سويف' ? 'selected' : '' }}>بني سويف</option>
                                        <option value="الفيوم" {{ old('governorate') == 'الفيوم' ? 'selected' : '' }}>الفيوم</option>
                                        <option value="الجيزة" {{ old('governorate') == 'الجيزة' ? 'selected' : '' }}>الجيزة</option>
                                        <option value="القاهرة" {{ old('governorate') == 'القاهرة' ? 'selected' : '' }}>القاهرة</option>
                                        <option value="القليوبية" {{ old('governorate') == 'القليوبية' ? 'selected' : '' }}>القليوبية</option>
                                        <option value="الشرقية" {{ old('governorate') == 'الشرقية' ? 'selected' : '' }}>الشرقية</option>
                                        <option value="الدقهلية" {{ old('governorate') == 'الدقهلية' ? 'selected' : '' }}>الدقهلية</option>
                                        <option value="الغربية" {{ old('governorate') == 'الغربية' ? 'selected' : '' }}>الغربية</option>
                                        <option value="المنوفية" {{ old('governorate') == 'المنوفية' ? 'selected' : '' }}>المنوفية</option>
                                        <option value="كفر الشيخ" {{ old('governorate') == 'كفر الشيخ' ? 'selected' : '' }}>كفر الشيخ</option>
                                        <option value="البحيرة" {{ old('governorate') == 'البحيرة' ? 'selected' : '' }}>البحيرة</option>
                                        <option value="الإسكندرية" {{ old('governorate') == 'الإسكندرية' ? 'selected' : '' }}>الإسكندرية</option>
                                        <option value="دمياط" {{ old('governorate') == 'دمياط' ? 'selected' : '' }}>دمياط</option>
                                        <option value="الإسماعيلية" {{ old('governorate') == 'الإسماعيلية' ? 'selected' : '' }}>الإسماعيلية</option>
                                        <option value="بورسعيد" {{ old('governorate') == 'بورسعيد' ? 'selected' : '' }}>بورسعيد</option>
                                        <option value="السويس" {{ old('governorate') == 'السويس' ? 'selected' : '' }}>السويس</option>
                                    </select>
                                </div>
                                @error('governorate') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block mb-2">الجنس</label>
                                <div class="d-flex align-items-center">
                                    <div class="form-check me-4">
                                        <input class="form-check-input" type="radio" name="gender" id="maleRadio" value="male" {{ old('gender') == 'male' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="maleRadio">ذكر</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="femaleRadio" value="female" {{ old('gender') == 'female' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="femaleRadio">أنثى</label>
                                    </div>
                                </div>
                                @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Add this inside the form after the gender field --}}
                         <div class="col-md-6 mb-3">
    <div class="input-group input-group-static my-3">
        <label for="created_at">وقت الدخول</label>
        <input type="datetime-local" 
               class="form-control @error('created_at') is-invalid @enderror"
               id="created_at" 
               name="created_at" 
               value="{{ old('created_at', now()->timezone('Africa/Cairo')->format('Y-m-d\TH:i')) }}">
    </div>
    @error('created_at') <span class="text-danger">{{ $message }}</span> @enderror
</div>
                            <div class="col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">المرفقات</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="file-loading">
                                            <input id="attachments" name="attachments[]" type="file" multiple>
                                        </div>
                                        <small class="text-muted">يمكنك رفع ملفات PDF, الصور, المستندات (الحد الأقصى: 10 ملفات، 10 ميجابايت لكل ملف)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn bg-gradient-dark">حفظ</button>
                        <a href="{{ route('patients.index') }}" class="btn bg-gradient-secondary">إلغاء</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Egyptian National ID detection --}}
<script>
function detectEgyptianNationalIdInfo(nationalId) {
    if (!/^\d{14}$/.test(nationalId)) {
        return null;
    }
    const century = nationalId[0];
    const year = nationalId.substr(1, 2);
    const month = nationalId.substr(3, 2);
    const day = nationalId.substr(5, 2);
    const governorate = nationalId.substr(7, 2);
    const genderDigit = nationalId.substr(12, 1);

    let fullYear = '';
    if (century === '2') fullYear = '19' + year;
    else if (century === '3') fullYear = '20' + year;
    else fullYear = '18' + year;

    const gender = (parseInt(genderDigit) % 2 === 0) ? 'أنثى' : 'ذكر';
    const birthdate = `${fullYear}-${month}-${day}`;
    const governorates = {
        '01': 'القاهرة', '02': 'الإسكندرية', '03': 'بورسعيد', '04': 'السويس',
        '11': 'دمياط', '12': 'الدقهلية', '13': 'الشرقية', '14': 'القليوبية',
        '15': 'كفر الشيخ', '16': 'الغربية', '17': 'المنوفية', '18': 'البحيرة',
        '19': 'الإسماعيلية', '21': 'الجيزة', '22': 'بني سويف', '23': 'الفيوم',
        '24': 'المنيا', '25': 'أسيوط', '26': 'سوهاج', '27': 'قنا', '28': 'أسوان',
        '29': 'الأقصر'
    };
    return {
        birthdate,
        gender,
        governorate: governorates[governorate] || 'غير معروف'
    };
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const nationalIdInput = document.getElementById('national_id');
    const firstNameInput = document.getElementById('full_name');
    // const secondNameInput = document.getElementById('second_name');
    // const thirdNameInput = document.getElementById('third_name');
    // const fourthNameInput = document.getElementById('fourth_name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const companion_phoneInput = document.getElementById('companion_phone');
    const birthdateInput = document.getElementById('date_of_birth');
    const addressInput = document.getElementById('address');
    const governorateInput = document.getElementById('governorate');
    const genderMaleRadio = document.getElementById('maleRadio');
    const genderFemaleRadio = document.getElementById('femaleRadio');

    // Helper: add 'is-filled' to parent if value exists
    function markFilled(input) {
        if (!input) return;
        const group = input.closest('.input-group') || input.closest('.input-group-static') || input.parentElement;
        if (group) {
            if (input.value && input.value !== '') {
                group.classList.add('is-filled');
            } else {
                group.classList.remove('is-filled');
            }
        }
    }

    function fillAndMark(input, value) {
        if (!input) return;
        input.value = value || '';
        markFilled(input);
    }

    if (nationalIdInput) {
        nationalIdInput.addEventListener('input', function () {
            const nationalId = this.value;

            if (nationalId.length === 14) {
                // استخرج المعلومات من الرقم القومي
                const info = detectEgyptianNationalIdInfo(nationalId);
                if (info) {
                    fillAndMark(birthdateInput, info.birthdate);
                    if (governorateInput) {
                        governorateInput.value = info.governorate;
                        markFilled(governorateInput);
                    }
                    // gender radio
                    if (info.gender === 'ذكر' && genderMaleRadio) {
                        genderMaleRadio.checked = true;
                        genderMaleRadio.closest('.form-check').classList.add('is-filled');
                        if (genderFemaleRadio) genderFemaleRadio.closest('.form-check').classList.remove('is-filled');
                    }
                    if (info.gender === 'أنثى' && genderFemaleRadio) {
                        genderFemaleRadio.checked = true;
                        genderFemaleRadio.closest('.form-check').classList.add('is-filled');
                        if (genderMaleRadio) genderMaleRadio.closest('.form-check').classList.remove('is-filled');
                    }
                }

                // جلب بيانات المريض من السيرفر إذا كان موجود
                fetch("{{ route('patients.checkNationalId') }}?national_id=" + nationalId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            const patient = data.patient;
                            
                            // Basic Information
                            fillAndMark(document.getElementById('full_name'), patient.full_name);
                            fillAndMark(document.getElementById('national_id'), patient.national_id);
                            fillAndMark(document.getElementById('phone'), patient.phone);
                            fillAndMark(document.getElementById('address'), patient.address);
                            fillAndMark(document.getElementById('governorate'), patient.governorate);
                            fillAndMark(document.getElementById('medicalId'), patient.medical_id);
                            fillAndMark(document.getElementById('uhi_number'), patient.uhi_number);

                            // Handle birth date properly
                            const birthDateInput = document.getElementById('date_of_birth');
                            if (birthDateInput && patient.date_of_birth) {
                                // Convert the date to YYYY-MM-DD format for the date input
                                const birthDate = new Date(patient.date_of_birth);
                                const formattedDate = birthDate.toISOString().split('T')[0];
                                fillAndMark(birthDateInput, formattedDate);
                                
                                // Add visual feedback
                                birthDateInput.closest('.input-group').classList.add('is-filled');
                                birthDateInput.classList.add('has-value');
                            } else if (nationalId.length === 14) {
                                // Try to extract birth date from national ID if available
                                const info = detectEgyptianNationalIdInfo(nationalId);
                                if (info && info.birthdate) {
                                    fillAndMark(birthDateInput, info.birthdate);
                                    birthDateInput.closest('.input-group').classList.add('is-filled');
                                    birthDateInput.classList.add('has-value');
                                }
                            }

                            // Department and Bed
                            if (patient.department_id) {
                                const departmentSelect = document.getElementById('department_id');
                                departmentSelect.value = patient.department_id;
                                markFilled(departmentSelect);
                            }

                            if (patient.bed_id) {
                                const bedSelect = document.getElementById('bed_id');
                                bedSelect.value = patient.bed_id;
                                markFilled(bedSelect);
                            }

                            // Companion Information
                            fillAndMark(document.getElementById('companion_name'), patient.companion_name);
                            fillAndMark(document.getElementById('companion_national_id'), patient.companion_national_id);
                            fillAndMark(document.getElementById('companion_phone'), patient.companion_phone);
                            fillAndMark(document.getElementById('companion_relation'), patient.companion_relation);

                            // Gender Selection
                            if (patient.gender === 'ذكر' && genderMaleRadio) {
                                genderMaleRadio.checked = true;
                                genderMaleRadio.closest('.form-check').classList.add('is-filled');
                                if (genderFemaleRadio) genderFemaleRadio.closest('.form-check').classList.remove('is-filled');
                            }
                            if (patient.gender === 'أنثى' && genderFemaleRadio) {
                                genderFemaleRadio.checked = true;
                                genderFemaleRadio.closest('.form-check').classList.add('is-filled');
                                if (genderMaleRadio) genderMaleRadio.closest('.form-check').classList.remove('is-filled');
                            }

                            // Created At (Admission Time)
                            if (patient.created_at) {
                                const createdAtInput = document.getElementById('created_at');
                                if (createdAtInput) {
                                    const date = new Date(patient.created_at);
                                    const egyptianDate = new Date(date.getTime() + (2 * 60 * 60 * 1000));
                                    const localDateTime = egyptianDate.toISOString().slice(0, 16);
                                    fillAndMark(createdAtInput, localDateTime);
                                }
                            }

                            // Show success toast
                            $.toast({
                                heading: 'تم العثور على المريض',
                                text: 'تم ملئ جميع البيانات المتوفرة تلقائياً',
                                icon: 'success',
                                position: 'top-center',
                                hideAfter: 6000,
                                showHideTransition: 'fade',
                                bgColor: '#28a745',
                                textColor: '#fff',
                                loaderBg: '#fff'
                            });

                            // Add visual indicator that form is pre-filled
                            form.classList.add('is-prefilled');
                        } else {
                            // Reset form if no patient found
                            form.reset();
                            form.classList.remove('is-prefilled');
                            
                            $.toast({
                                heading: 'مريض جديد',
                                text: 'هذا المريض غير مسجل من قبل',
                                icon: 'info',
                                position: 'top-center',
                                hideAfter: 6000,
                                showHideTransition: 'fade'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nationalIdInput = document.getElementById('national_id');

    if (nationalIdInput) {
        nationalIdInput.addEventListener('input', function () {
            const nationalId = this.value;

            if (nationalId.length === 14) {
                fetch("{{ route('patients.checkNationalId') }}?national_id=" + nationalId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            if (data.deleted) {
                                // Prompt to restore the patient
                                if (confirm('هذا المريض محذوف. هل تريد استعادته؟')) {
                                    fetch("{{ route('patients.restore', ':id') }}".replace(':id', data.patient.id), {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Accept': 'application/json',
                                        },
                                    })
                                    .then(response => response.json())
                                    .then(result => {
                                        if (result.success) {
                                            alert('تم استعادة المريض بنجاح.');
                                            location.reload();
                                        } else {
                                            alert('حدث خطأ أثناء استعادة المريض.');
                                        }
                                    })
                                    .catch(error => console.error('Error:', error));
                                }
                            } else {
                                // alert('هذا المريض موجود بالفعل.');
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    }
});
</script>
<script>
// Initialize file input plugin for attachments
$(document).ready(function() {
    let allFiles = []; // Store all selected files
    let fileInput = $("#attachments");
    let isInitialized = false;
    
    // Destroy any existing initialization
    if (fileInput.hasClass('file-input')) {
        fileInput.fileinput('destroy');
    }
    
    fileInput.fileinput({
        rtl: true,
        language: "ar",
        theme: "fa5",
        showUpload: false,
        showRemove: true,
        showCancel: false,
        showBrowse: true,
        browseOnZoneClick: true,
        dropZoneEnabled: true,
        dropZoneTitle: '<i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i><br><h5 class="text-primary">اسحب الملفات هنا أو اضغط للاختيار</h5>',
        dropZoneClickTitle: '<br><small class="text-muted">(يمكنك إضافة ملفات متعددة)</small>',
        allowedFileExtensions: ["jpg", "png", "jpeg", "gif", "pdf", "doc", "docx", "txt", "xls", "xlsx"],
        maxFileCount: 10,
        maxFileSize: 10240, // 10 MB
        overwriteInitial: false,
        initialPreviewAsData: true,
        multiple: true,
        validateInitialCount: true,
        autoReplace: false,
        uploadIcon: '<i class="fas fa-cloud-upload-alt"></i>',
        removeIcon: '<i class="fas fa-trash"></i>',
        cancelIcon: '<i class="fas fa-times-circle"></i>',
        browseIcon: '<i class="fas fa-folder-open"></i>',
        removeTitle: 'إزالة الملف',
        cancelTitle: 'إلغاء الرفع',
        browseLabel: ' <i class="fas fa-folder-open"></i> تصفح الملفات',
        removeLabel: ' <i class="fas fa-trash"></i> إزالة الكل',
        cancelLabel: 'إلغاء',
        fileActionSettings: {
            removeIcon: '<i class="fas fa-trash text-danger"></i>',
            removeClass: 'btn btn-sm btn-kv btn-outline-danger',
            removeTitle: 'إزالة الملف',
            uploadIcon: '<i class="fas fa-upload text-info"></i>',
            uploadClass: 'btn btn-sm btn-kv btn-outline-info',
            uploadTitle: 'رفع الملف',
            zoomIcon: '<i class="fas fa-search-plus text-warning"></i>',
            zoomClass: 'btn btn-sm btn-kv btn-outline-warning',
            zoomTitle: 'عرض تفاصيل الملف',
            dragIcon: '<i class="fas fa-arrows-alt text-info"></i>',
            dragClass: 'btn btn-sm btn-kv btn-outline-info',
            dragTitle: 'حرك لإعادة الترتيب',
            dragSettings: {},
            indicatorNew: '<i class="fas fa-plus-circle text-warning"></i>',
            indicatorSuccess: '<i class="fas fa-check-circle text-success"></i>',
            indicatorError: '<i class="fas fa-exclamation-circle text-danger"></i>',
            indicatorLoading: '<i class="fas fa-sync-alt fa-spin text-muted"></i>'
        },
        msgFilesTooMany: "عدد الملفات المحددة ({n}) يتجاوز الحد الأقصى المسموح به {m}.",
        msgFileNotFound: "الملف '{name}' غير موجود!",
        msgFileSecured: "قيود الأمان تمنع قراءة الملف '{name}'.",
        msgFileNotReadable: "الملف '{name}' غير قابل للقراءة.",
        msgFilePreviewAborted: "تم إلغاء معاينة الملف '{name}'.",
        msgFilePreviewError: "حدث خطأ أثناء قراءة الملف '{name}'.",
        msgInvalidFileName: "أحرف غير صالحة أو غير مدعومة في اسم الملف '{name}'.",
        msgInvalidFileType: "نوع ملف غير صالح لـ '{name}'. الأنواع المدعومة فقط: '{types}'.",
        msgInvalidFileExtension: "امتداد ملف غير صالح لـ '{name}'. الامتدادات المدعومة فقط: '{extensions}'.",
        msgSizeTooLarge: "الملف '{name}' ({size} كيلوبايت) يتجاوز الحد الأقصى المسموح للحجم {maxSize} كيلوبايت.",
        msgFilesTooFew: "يجب اختيار {n} ملف على الأقل للرفع.",
        msgFileTypes: {
            'image': 'صورة',
            'html': 'HTML',
            'text': 'نص',
            'video': 'فيديو',
            'audio': 'صوت',
            'flash': 'فلاش',
            'pdf': 'PDF',
            'object': 'كائن'
        },
        previewFileIcon: '<i class="fas fa-file fa-2x text-info"></i>',
        previewFileIconSettings: {
            'doc': '<i class="fas fa-file-word fa-2x text-primary"></i>',
            'docx': '<i class="fas fa-file-word fa-2x text-primary"></i>',
            'xls': '<i class="fas fa-file-excel fa-2x text-success"></i>',
            'xlsx': '<i class="fas fa-file-excel fa-2x text-success"></i>',
            'ppt': '<i class="fas fa-file-powerpoint fa-2x text-danger"></i>',
            'pptx': '<i class="fas fa-file-powerpoint fa-2x text-danger"></i>',
            'pdf': '<i class="fas fa-file-pdf fa-2x text-danger"></i>',
            'zip': '<i class="fas fa-file-archive fa-2x text-warning"></i>',
            'rar': '<i class="fas fa-file-archive fa-2x text-warning"></i>',
            'tar': '<i class="fas fa-file-archive fa-2x text-warning"></i>',
            'gz': '<i class="fas fa-file-archive fa-2x text-warning"></i>',
            'jpg': '<i class="fas fa-file-image fa-2x text-info"></i>',
            'jpeg': '<i class="fas fa-file-image fa-2x text-info"></i>',
            'png': '<i class="fas fa-file-image fa-2x text-info"></i>',
            'gif': '<i class="fas fa-file-image fa-2x text-info"></i>',
            'txt': '<i class="fas fa-file-alt fa-2x text-secondary"></i>'
        },
        previewFileIconClass: 'file-other-icon',
        layoutTemplates: {
            actionDelete: '<button type="button" class="kv-file-remove {removeClass}" title="{removeTitle}"{dataUrl}{dataKey}>{removeIcon}</button>',
            actionUpload: '<button type="button" class="kv-file-upload {uploadClass}" title="{uploadTitle}">{uploadIcon}</button>',
            actionZoom: '<button type="button" class="kv-file-zoom {zoomClass}" title="{zoomTitle}">{zoomIcon}</button>',
            actionDrag: '<button type="button" class="kv-file-drag {dragClass}" title="{dragTitle}">{dragIcon}</button>'
        },
        previewFileExtSettings: {
            'doc': function(ext) {
                return ext && ['doc', 'docx'].indexOf(ext) > -1;
            },
            'xls': function(ext) {
                return ext && ['xls', 'xlsx'].indexOf(ext) > -1;
            },
            'ppt': function(ext) {
                return ext && ['ppt', 'pptx'].indexOf(ext) > -1;
            },
            'zip': function(ext) {
                return ext && ['zip', 'rar', 'tar', 'gzip', 'gz'].indexOf(ext) > -1;
            },
            'image': function(ext) {
                return ext && ['jpg', 'jpeg', 'png', 'gif'].indexOf(ext) > -1;
            },
            'text': function(ext) {
                return ext && ['txt', 'csv'].indexOf(ext) > -1;
            }
        }
    });

    // Simple approach: allow the plugin to handle files normally
    // but track them for form submission
    let selectedFiles = [];

    // Handle file selection events
    fileInput.on('fileselect', function(event, numFiles, label) {
        console.log('Files selected: ' + numFiles);
    });

    // Handle file batch selection
    fileInput.on('filebatchselected', function(event, files) {
        console.log('Batch files selected: ' + files.length);
        selectedFiles = Array.from(files);

        // Prevent the plugin from clearing existing files
        setTimeout(function() {
            fileInput.fileinput('refresh');
        }, 100);
    });

    // Handle file loading
    fileInput.on('fileloaded', function(event, file, previewId, index, reader) {
        console.log('File loaded: ' + file.name);
    });

    // Handle individual file removal
    fileInput.on('fileremoved', function(event, id, index) {
        console.log('File removed at index: ' + index);
        if (selectedFiles[index]) {
            selectedFiles.splice(index, 1);
            console.log(`Files remaining: ${selectedFiles.length}`);
        }
    });

    // Handle clearing all files
    fileInput.on('fileclear', function(event) {
        console.log('All files cleared');
        selectedFiles = [];
    });

    // Handle file errors
    fileInput.on('fileerror', function(event, data, msg) {
        console.error('File error:', msg);

        // Show user-friendly error message
        $.toast({
            heading: 'خطأ في الملف',
            text: msg || 'حدث خطأ أثناء تحميل الملف',
            icon: 'error',
            position: 'top-center',
            hideAfter: 5000
        });
    });

    // Refresh the plugin display
    setTimeout(function() {
        fileInput.fileinput('refresh');
    }, 50);

    // Add custom styling after initialization
    setTimeout(function() {
        $('.file-drop-zone').addClass('border-2 border-dashed border-primary rounded bg-light p-4');
        $('.file-drop-zone-title').addClass('text-primary fw-bold');
        $('.kv-file-content').addClass('text-center');

        // Style the file preview thumbnails
        $('.file-preview-frame').addClass('m-2 border rounded shadow-sm');
        $('.file-actions').addClass('text-center mt-2');

        // Fix drop zone icons
        $('.file-drop-zone .file-drop-zone-title').html('<i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i><br><h5 class="text-primary">اسحب الملفات هنا أو اضغط للاختيار</h5><br><small class="text-muted">(يمكنك إضافة ملفات متعددة)</small>');
    }, 300);
});
</script>
@endsection

@push('scripts')
<!-- jQuery Toast Plugin JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('patientForm');
    const submitButton = form.querySelector('button[type="submit"]');
    const medicalIdInput = document.getElementById('medicalId');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        // Disable submit button to prevent double submission
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>جاري الحفظ...';
        
        const formData = new FormData(form);

        try {
            // Online submission with AJAX
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                console.log('Patient saved successfully:', data);
                
                $.toast({
                    heading: 'نجاح',
                    text: data.message || 'تم حفظ المريض بنجاح!',
                    icon: 'success',
                    position: 'top-center',
                    hideAfter: 6000,
                    showHideTransition: 'fade'
                });
                
                // Reset form
                form.reset();
                
                // Update medical ID with new generated one
                if (data.new_medical_id) {
                    console.log('Setting new medical ID:', data.new_medical_id);
                    medicalIdInput.value = data.new_medical_id;
                } else {
                    console.log('No new medical ID in response, generating new one...');
                    // Fallback: generate new medical ID
                    try {
                        const medicalIdResponse = await fetch('{{ route("generate.medical.id") }}', {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (medicalIdResponse.ok) {
                            const medicalIdData = await medicalIdResponse.json();
                            console.log('Generated new medical ID:', medicalIdData.medical_id);
                            medicalIdInput.value = medicalIdData.medical_id;
                        }
                    } catch (medicalIdError) {
                        console.error('Failed to generate new medical ID:', medicalIdError);
                    }
                }
                
                // Reset datetime to current time
                const datetimeInput = document.getElementById('created_at');
                if (datetimeInput) {
                    const now = new Date();
                    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                    datetimeInput.value = now.toISOString().slice(0, 16);
                }
                
            } else {
                console.error('Error response:', data);
                let errorMessage = data.message || "حدث خطأ أثناء الحفظ.";
                if (data.errors) {
                    console.error('Validation errors:', data.errors);
                    // Display validation errors
                    const errorMessages = [];
                    Object.values(data.errors).forEach(fieldErrors => {
                        if (Array.isArray(fieldErrors)) {
                            errorMessages.push(...fieldErrors);
                        } else {
                            errorMessages.push(fieldErrors);
                        }
                    });
                    errorMessage = errorMessages.join('<br>');
                }
                
                $.toast({
                    heading: 'خطأ',
                    text: errorMessage,
                    icon: 'error',
                    position: 'top-center',
                    hideAfter: 8000,
                    showHideTransition: 'fade'
                });
            }

        } catch (error) {
            console.error('Request failed:', error);
            $.toast({
                heading: 'خطأ',
                text: 'حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.',
                icon: 'error',
                position: 'top-center',
                hideAfter: 6000,
                showHideTransition: 'fade'
            });
        } finally {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = 'حفظ';
        }
    });
});
</script>
@endpush
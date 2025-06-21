@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>إضافة مريض جديد</h6>
                </div>
                <div class="card-body">
                    <form id="patientForm" action="{{ route('patients.store') }}" method="POST" autocomplete="on">
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
                                    <label for="first_name" class="form-label">الاسم الأول</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name"  value="{{ old('first_name') }}">
                                </div>
                                @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="second_name" class="form-label">اسم الأب</label>
                                    <input type="text" class="form-control @error('second_name') is-invalid @enderror" id="second_name" name="second_name" value="{{ old('second_name') }}">
                                </div>
                                @error('second_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="third_name" class="form-label">اسم الجد</label>
                                    <input type="text" class="form-control @error('third_name') is-invalid @enderror"
                                     id="third_name" name="third_name" value="{{ old('third_name') }}">
                                </div>
                                @error('third_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="fourth_name" class="form-label">اسم العائلة</label>
                                    <input type="text" class="form-control @error('fourth_name') is-invalid @enderror"
                                     id="fourth_name" name="fourth_name" value="{{ old('fourth_name') }}">
                                </div>
                                @error('fourth_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                     id="email" name="email" value="{{ old('email') }}">
                                </div>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

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
                                    <label for="phone2" class="form-label">رقم الهاتف الثاني</label>
                                    <input type="text" class="form-control @error('phone2') is-invalid @enderror"
                                     id="phone2" name="phone2" value="{{ old('phone2') }}">
                                </div>
                                @error('phone2') <span class="text-danger">{{ $message }}</span> @enderror
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
                                    <label for="department_id">القسم</label>
                                    <select name="department_id" id="department_id" class="form-control" >
                                        <option value="">اختر القسم</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ (old('department_id', $patient->department_id ?? '') == $department->id) ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-static mb-3">
                                    <label for="bed_id" class="ms-0">اختر السرير</label>
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
                                    <label for="companion_name" class="form-label">اسم المرافق</label>
                                    <input type="text" class="form-control @error('companion_name') is-invalid @enderror"
                                           id="companion_name" name="companion_name" value="{{ old('companion_name') }}">
                                </div>
                                @error('companion_name') <span class="text-danger">{{ $message }}</span> @enderror
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
                                    <input type="text" name="uhi_number" id="uhi_number" class="form-control" value="{{ old('uhi_number', $patient->uhi_number ?? '') }}">
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
                                    <label for="governorate" class="ms-0">المحافظة</label>
                                    <select id="governorate" name="governorate" class="form-control @error('governorate') is-invalid @enderror">
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
                                        <input class="form-check-input" type="radio" name="gender" id="maleRadio" value="male" {{ old('gender', $patient->gender ?? '') == 'ذكر' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="maleRadio">ذكر</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="gender" id="femaleRadio" value="female" {{ old('gender', $patient->gender ?? '') == 'أنثى' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="femaleRadio">أنثى</label>
                                    </div>
                                </div>
                                @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- <div class="col-md-12">
                                <input id="attachments" type="file" class="file" data-preview-file-type="text" name="files[]" multiple>
                            </div> --}}
                            {{-- <div class="col-md-12 mb-3">
                                <div class="input-group input-group-dynamic mb-3">
                                    <label for="notes" class="form-label">ملاحظات</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes">{{ old('notes') }}</textarea>
                                </div>
                                @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                        </div> --}}
                        <button type="submit" class="btn btn-dark">حفظ</button>
                        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">إلغاء</a>
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

// document.addEventListener('DOMContentLoaded', function() {
//     const nationalIdInput = document.getElementById('national_id');
//     const infoDiv = document.getElementById('nationalIdInfo');
//     const birthdateInput = document.getElementById('date_of_birth');
//     const genderInput = document.getElementById('gender');
//     const governorateInput = document.getElementById('governorate');
//     const firstNameInput = document.getElementById('first_name');
//     const secondNameInput = document.getElementById('second_name');
//     const thirdNameInput = document.getElementById('third_name');
//     const fourthNameInput = document.getElementById('fourth_name');

//     if(nationalIdInput) {
//         nationalIdInput.addEventListener('input', function() {
//             const nationalId = this.value;
//             const info = detectEgyptianNationalIdInfo(nationalId);
//             if (info) {
//                 if (birthdateInput) birthdateInput.value = info.birthdate;
//                 if (genderInput) genderInput.value = info.gender;
//                 if (governorateInput) governorateInput.value = info.governorate;

//                 // Fetch name from external API
//                 fetch(`/proxy/national-id?national_id=${nationalId}`)
//                     .then(response => response.json())
//                     .then(data => {
//                         if (data && data.basicData) {
//                             // Fill first name
//                             if (firstNameInput && data.basicData.FisrtName) {
//                                 firstNameInput.value = data.basicData.FisrtName;
//                                 firstNameInput.focus();
//                             }
//                             // Fill second name
//                             if (secondNameInput && data.basicData.SecondName) {
//                                 secondNameInput.value = data.basicData.SecondName;
//                                 secondNameInput.focus();
//                             }
//                             // Fill third name
//                             if (thirdNameInput && data.basicData.ThirdName) {
//                                 thirdNameInput.value = data.basicData.ThirdName;
//                                 thirdNameInput.focus();
//                             }
//                             // Fill fourth name
//                             if (fourthNameInput && data.basicData.FourthName) {
//                                 fourthNameInput.value = data.basicData.FourthName;
//                                 fourthNameInput.focus();
//                             }
//                             // Fill email
//                             const emailInput = document.getElementById('email');
//                             if (emailInput && data.basicData.Email) {
//                                 emailInput.value = data.basicData.Email;
//                                 emailInput.focus();
//                             }
//                             // Fill first phone
//                             const phone1Input = document.getElementById('phone');
//                             if (phone1Input && data.basicData.Mobile1) {
//                                 phone1Input.value = data.basicData.Mobile1;
//                                 phone1Input.focus();
//                             }
//                             // Fill second phone
//                             const phone2Input = document.getElementById('phone2');
//                             if (phone2Input && data.basicData.Mobile2) {
//                                 phone2Input.value = data.basicData.Mobile2;
//                                 phone2Input.focus();
//                             }
//                             // Fill address
//                             const addressInput = document.getElementById('address');
//                             if (addressInput && data.basicData.Address) {
//                                 addressInput.value = data.basicData.Address;
//                                 addressInput.focus();
//                             }
//                         }
//                     })
//                     .catch(error => {
//                         // Handle error
//                     });
//             } else {
//                 infoDiv.innerHTML = '';
//                 if (birthdateInput) birthdateInput.value = '';
//                 if (genderInput) genderInput.value = '';
//                 if (governorateInput) governorateInput.value = '';
//             }
//         });
//     }
// });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('patientForm');

        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(response => {
                if (!response.ok) {
                    throw response.json();
                }
                return response.json();
            })
            .then(data => {
                $.toast({
                    heading: 'نجاح',
                    text: 'تم حفظ المريض بنجاح!',
                    showHideTransition: 'slide',
                    icon: 'success',
                    position: 'top-center', // Position the toast in the center
                    bgColor: '#28a745', // Background color
                    textColor: '#fff',  // Text color
                    loaderBg: '#fff',   // Loader background color
                });
                form.reset(); // Reset the form
            })
            .catch(async error => {
                const errors = await error;
                let errorMessage = "حدث خطأ أثناء حفظ البيانات.";
                if (errors && errors.errors) {
                    errorMessage = Object.values(errors.errors).join("<br>");
                }
                $.toast({
                    heading: 'خطأ',
                    text: errorMessage,
                    showHideTransition: 'fade',
                    icon: 'error',
                    position: 'top-center', // Position the toast in the center
                });
            });
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const nationalIdInput = document.getElementById('national_id');
    const firstNameInput = document.getElementById('first_name');
    const secondNameInput = document.getElementById('second_name');
    const thirdNameInput = document.getElementById('third_name');
    const fourthNameInput = document.getElementById('fourth_name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const phone2Input = document.getElementById('phone2');
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
                            fillAndMark(firstNameInput, patient.first_name);
                            fillAndMark(secondNameInput, patient.second_name);
                            fillAndMark(thirdNameInput, patient.third_name);
                            fillAndMark(fourthNameInput, patient.fourth_name);
                            fillAndMark(emailInput, patient.email);
                            fillAndMark(phoneInput, patient.phone);
                            fillAndMark(phone2Input, patient.phone2);
                            fillAndMark(birthdateInput, patient.date_of_birth);
                            fillAndMark(addressInput, patient.address);
                            fillAndMark(governorateInput, patient.governorate);
                            // gender radio
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
                            $.toast({
                                heading: 'نجاح',
                                text: 'هذا الرقم القومي موجود بالفعل وتم ملء البيانات تلقائيًا.',
                                showHideTransition: 'slide',
                                icon: 'success',
                                position: 'top-center',
                                bgColor: '#28a745',
                                textColor: '#fff',
                                loaderBg: '#fff',
                            });
                        } else {
                            $.toast({
                                heading: 'معلومة',
                                text: 'هذا المريض أول مرة يتم تسجيله في النظام.',
                                showHideTransition: 'fade',
                                icon: 'info',
                                position: 'top-center',
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
@endsection
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
                                <div class="input-group input-group-dynamic mb-4">
                                    <label for="national_id" class="form-label">الرقم القومي</label>
                                    <input type="text" class="form-control @error('national_id') is-invalid @enderror"
                                     id="national_id" name="national_id" value="{{ old('national_id') }}" maxlength="14">
                                    <div id="nationalIdInfo" class="form-text text-muted"></div>
                                </div>
                                @error('national_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>


                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-4">
                                    <label for="first_name" class="form-label">الاسم الأول</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name"  value="{{ old('first_name') }}">
                                </div>
                                @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-4">
                                    <label for="second_name" class="form-label">اسم الأب</label>
                                    <input type="text" class="form-control @error('second_name') is-invalid @enderror" id="second_name" name="second_name" value="{{ old('second_name') }}">
                                </div>
                                @error('second_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-4">
                                    <label for="third_name" class="form-label">اسم الجد</label>
                                    <input type="text" class="form-control @error('third_name') is-invalid @enderror"
                                     id="third_name" name="third_name" value="{{ old('third_name') }}">
                                </div>
                                @error('third_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-4">
                                    <label for="fourth_name" class="form-label">اسم العائلة</label>
                                    <input type="text" class="form-control @error('fourth_name') is-invalid @enderror"
                                     id="fourth_name" name="fourth_name" value="{{ old('fourth_name') }}">
                                </div>
                                @error('fourth_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-4">
                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                     id="email" name="email" value="{{ old('email') }}">
                                </div>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-4">
                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                     id="phone" name="phone" value="{{ old('phone') }}">
                                </div>
                                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-4">
                                    <label for="phone2" class="form-label">رقم الهاتف الثاني</label>
                                    <input type="text" class="form-control @error('phone2') is-invalid @enderror"
                                     id="phone2" name="phone2" value="{{ old('phone2') }}">
                                </div>
                                @error('phone2') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-dynamic mb-4">
                                    <label for="address" class="form-label">العنوان</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror"
                                     id="address" name="address" value="{{ old('address') }}">
                                </div>
                                @error('address') <span class="text-danger">{{ $message }}</span> @enderror
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
                                <div class="input-group input-group-static mb-4">
                                    <label for="gender" class="ms-0">الجنس</label>
                                    <select id="gender" name="gender" class="form-control @error('gender') is-invalid @enderror">
                                        <option value="">اختر</option>
                                        <option value="ذكر" {{ old('gender') == 'ذكر' ? 'selected' : '' }}>ذكر</option>
                                        <option value="أنثى" {{ old('gender') == 'أنثى' ? 'selected' : '' }}>أنثى</option>
                                    </select>
                                </div>
                                @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="input-group input-group-static mb-4">
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
                                <div class="input-group input-group-static mb-4">
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
                        </div>
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
    const infoDiv = document.getElementById('nationalIdInfo');
    const firstNameInput = document.getElementById('first_name');
    const secondNameInput = document.getElementById('second_name');
    const thirdNameInput = document.getElementById('third_name');
    const fourthNameInput = document.getElementById('fourth_name');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const phone2Input = document.getElementById('phone2');
    const birthdateInput = document.getElementById('date_of_birth');
    const genderInput = document.getElementById('gender');
    const addressInput = document.getElementById('address');
    const governorateInput = document.getElementById('governorate');

    if (nationalIdInput) {
        nationalIdInput.addEventListener('input', function () {
            const nationalId = this.value;

            if (nationalId.length === 14) {
                fetch(`/api/check-national-id?national_id=${nationalId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            const patient = data.patient;
                            if (firstNameInput) firstNameInput.value = patient.first_name || '';
                            if (secondNameInput) secondNameInput.value = patient.second_name || '';
                            if (thirdNameInput) thirdNameInput.value = patient.third_name || '';
                            if (fourthNameInput) fourthNameInput.value = patient.fourth_name || '';
                            if (emailInput) emailInput.value = patient.email || '';
                            if (phoneInput) phoneInput.value = patient.phone || '';
                            if (phone2Input) phone2Input.value = patient.phone2 || '';
                            if (birthdateInput) birthdateInput.value = patient.date_of_birth || '';
                            if (genderInput) genderInput.value = patient.gender || '';
                            if (addressInput) addressInput.value = patient.address || '';
                            if (governorateInput) governorateInput.value = patient.governorate || '';
                            // Focus all inputs
                            if (firstNameInput) firstNameInput.focus();
                            if (secondNameInput) secondNameInput.focus();
                            if (thirdNameInput) thirdNameInput.focus();
                            if (fourthNameInput) fourthNameInput.focus();
                            if (emailInput) emailInput.focus();
                            if (phoneInput) phoneInput.focus();
                            if (phone2Input) phone2Input.focus();
                            if (birthdateInput) birthdateInput.focus();
                            if (genderInput) genderInput.focus();
                            if (addressInput) addressInput.focus();
                            if (governorateInput) governorateInput.focus();
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
                        infoDiv.innerHTML = '<span class="text-danger">حدث خطأ أثناء البحث عن الرقم القومي.</span>';
                    });
            } else {
                infoDiv.innerHTML = '';
            }
        });
    }
});
</script>
@endsection
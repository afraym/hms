@extends('layouts.admin') 

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
    <div class="input-group input-group-dynamic mb-4">
        <label class="form-label">ابحث عن مريض بالاسم او الرقم القومي...</label>
        <input type="text" id="searchQuery" class="form-control">
    </div>
</div>
        <div class="col-lg-12">
            <div class="card my-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>قائمة المرضى</h6>
                    <a href="{{ route('patients.create') }}" class="btn btn-primary">إضافة مريض جديد</a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 text-end" id="patientsTable">
                            <thead>
                                <tr>
                                    <th>الصورة</th>
                                    <th>الاسم الأول</th>
                                    <th>الاسم الثاني</th>
                                    <th>رقم الهاتف</th>
                                    <th>الرقم القومي</th>
                                    <th>الجنس</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="patientsTableBody">
                                @foreach($patients as $patient)
                                <tr>
                                    <td>
                                        <img src="{{ asset($patient->gender == 'male' ? 'assets/img/male.png' : ($patient->gender == 'female' ? 'assets/img/female.png' : 'assets/img/default.png')) }}" 
                                             alt="Avatar" 
                                             class="avatar" 
                                             style="width: 50px; height: 50px; border-radius: 50%;">
                                    </td>
                                    <td>{{ $patient->first_name }}</td>
                                    <td>{{ $patient->second_name }}</td>
                                    <td>{{ $patient->phone }}</td>
                                    <td>{{ $patient->national_id }}</td>
                                    <td>{{ $patient->gender == 'male' ? 'ذكر' : ($patient->gender == 'female' ? 'أنثى' : 'غير محدد') }}</td>
                                    <td>
                                        <a href="{{ route('patients.show', $patient->id) }}" class="btn bg-gradient-info">عرض</a>
                                        <a href="{{ route('patients.edit', $patient->id) }}" class="btn bg-gradient-warning">تعديل</a>
                                        <form action="{{ route('patients.discharge', $patient->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد تسجيل خروج هذا المريض؟');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn bg-gradient-secondary" aria-label="تسجيل خروج" title="تسجيل خروج">تسجيل خروج</button>
                                        </form>
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
                                    <td colspan="7" class="text-center">لا يوجد مرضى</td>
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchQueryInput = document.getElementById('searchQuery');
    const patientsTableBody = document.getElementById('patientsTableBody');

    if (searchQueryInput) {
        searchQueryInput.addEventListener('input', function () {
            const query = this.value;

            // Show loading spinner
            patientsTableBody.innerHTML = '<tr><td colspan="7" class="text-center">جاري البحث...</td></tr>';

            // Send AJAX request to search patients
            fetch(`/patients/ajax-search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    // Clear the table body
                    patientsTableBody.innerHTML = '';

                    // Populate the table with the search results
                    if (data.length > 0) {
                        data.forEach(patient => {
                            const gender = patient.gender === 'male' ? 'ذكر' : (patient.gender === 'female' ? 'أنثى' : 'غير محدد');
                            const avatar = patient.gender === 'male' ? '/assets/img/male.png' : (patient.gender === 'female' ? '/assets/img/female.png' : '/assets/img/default.png');

                            const row = `
                                <tr>
                                    <td>
                                        <img src="${avatar}" alt="Avatar" class="avatar" style="width: 50px; height: 50px; border-radius: 50%;">
                                    </td>
                                    <td>${patient.first_name}</td>
                                    <td>${patient.second_name}</td>
                                    <td>${patient.phone}</td>
                                    <td>${patient.national_id}</td>
                                    <td>${gender}</td>
                                    <td>
                                        <a href="/patients/${patient.id}" class="btn bg-gradient-info">عرض</a>
                                        <a href="/patients/${patient.id}/edit" class="btn bg-gradient-warning">تعديل</a>
                                        <form action="/patients/${patient.id}/discharge" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد تسجيل خروج هذا المريض؟');">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                            <input type="hidden" name="_method" value="PATCH">
                                            <button type="submit" class="btn bg-gradient-secondary" aria-label="تسجيل خروج" title="تسجيل خروج">تسجيل خروج</button>
                                        </form>
                                        <form action="/patients/${patient.id}" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد أنك تريد حذف هذا المريض؟');">
                                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn bg-gradient-danger" aria-label="حذف المريض" title="حذف المريض">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            `;
                            patientsTableBody.insertAdjacentHTML('beforeend', row);
                        });
                    } else {
                        patientsTableBody.innerHTML = `
                            <tr>
                                <td colspan="7" class="text-center">لا توجد نتائج مطابقة.</td>
                            </tr>
                        `;
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    }
});
</script>
@endsection
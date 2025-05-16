@extends('layouts.admin') 
@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <!-- عدد الأسرة الكلي -->
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between p-3 pt-2">
                    <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                        <i class="material-symbols-rounded opacity-10">bed</i>
                    </div>
                    <div class="text-start pt-1">
                        <p class="text-sm mb-0 text-capitalize">إجمالي الأسرة</p>
                        <h4 class="mb-0">{{ $beds_count }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-start">
                        <span class="text-info text-sm font-weight-bolder ms-1">{{ $empty_beds }}</span>
                        سرير فارغ
                    </p>
                </div>
            </div>
        </div>
        <!-- نسبة الأسرة الفارغة -->
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between p-3 pt-2">
                    <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                        <i class="material-symbols-rounded opacity-10">hotel_class</i>
                    </div>
                    <div class="text-start pt-1">
                        <p class="text-sm mb-0 text-capitalize">نسبة الأسرة الفارغة</p>
                        <h4 class="mb-0">
                            {{ $beds_count > 0 ? round(($empty_beds / $beds_count) * 100, 1) : 0 }}%
                        </h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-start">
                        <span class="text-success text-sm font-weight-bolder ms-1">{{ $empty_beds }}</span>
                        من أصل {{ $beds_count }}
                    </p>
                </div>
            </div>
        </div>
        <!-- دخول المرضى هذا الشهر -->
        <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between p-3 pt-2">
                    <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                        <i class="material-symbols-rounded opacity-10">person_add</i>
                    </div>
                    <div class="text-start pt-1">
                        <p class="text-sm mb-0 text-capitalize">دخول المرضى (شهري)</p>
                        <h4 class="mb-0">{{ $patients_in_month }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-start">
                        <span class="text-success text-sm font-weight-bolder ms-1">
                            {{ $patients_count > 0 ? round(($patients_in_month / $patients_count) * 100, 1) : 0 }}%
                        </span>
                        من إجمالي المرضى
                    </p>
                </div>
            </div>
        </div>
        <!-- خروج المرضى هذا الشهر -->
        <div class="col-lg-3 col-sm-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between p-3 pt-2">
                    <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                        <i class="material-symbols-rounded opacity-10">logout</i>
                    </div>
                    <div class="text-start pt-1">
                        <p class="text-sm mb-0 text-capitalize">خروج المرضى (شهري)</p>
                        <h4 class="mb-0">{{ $patients_out_month }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-start">
                        <span class="text-danger text-sm font-weight-bolder ms-1">
                            {{ $patients_count > 0 ? round(($patients_out_month / $patients_count) * 100, 1) : 0 }}%
                        </span>
                        من إجمالي المرضى
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- إحصائيات أسبوعية -->
    <div class="row mt-4">
        <!-- دخول المرضى هذا الأسبوع -->
        <div class="col-lg-6 col-sm-12 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between p-3 pt-2">
                    <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                        <i class="material-symbols-rounded opacity-10">person_add</i>
                    </div>
                    <div class="text-start pt-1">
                        <p class="text-sm mb-0 text-capitalize">دخول المرضى (أسبوعي)</p>
                        <h4 class="mb-0">{{ $patients_in_week }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-start">
                        <span class="text-success text-sm font-weight-bolder ms-1">
                            {{ $patients_count > 0 ? round(($patients_in_week / $patients_count) * 100, 1) : 0 }}%
                        </span>
                        من إجمالي المرضى
                    </p>
                </div>
            </div>
        </div>
        <!-- خروج المرضى هذا الأسبوع -->
        <div class="col-lg-6 col-sm-12 mb-lg-0 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between p-3 pt-2">
                    <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark text-center border-radius-lg">
                        <i class="material-symbols-rounded opacity-10">logout</i>
                    </div>
                    <div class="text-start pt-1">
                        <p class="text-sm mb-0 text-capitalize">خروج المرضى (أسبوعي)</p>
                        <h4 class="mb-0">{{ $patients_out_week }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0 text-start">
                        <span class="text-danger text-sm font-weight-bolder ms-1">
                            {{ $patients_count > 0 ? round(($patients_out_week / $patients_count) * 100, 1) : 0 }}%
                        </span>
                        من إجمالي المرضى
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
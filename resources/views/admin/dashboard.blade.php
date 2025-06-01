@extends('layouts.admin') 
@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
            <i class="material-icons opacity-10">bed</i>
          </div>
          <div class="text-start pt-1">
            <p class="text-sm mb-0 text-capitalize">إجمالي الأسرة</p>
            <h4 class="mb-0">{{ $beds_count }}</h4>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-3">
          <p class="mb-0 text-start"><span class="text-success text-sm font-weight-bolder ms-1">{{ $empty_beds }}</span> سرير فارغ</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
            <i class="material-icons opacity-10">hotel_class</i>
          </div>
          <div class="text-start pt-1">
            <p class="text-sm mb-0 text-capitalize">نسبة الأسرة الفارغة</p>
            <h4 class="mb-0">{{ $beds_count > 0 ? round(($empty_beds / $beds_count) * 100, 1) : 0 }}%</h4>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-3">
          <p class="mb-0 text-start"><span class="text-success text-sm font-weight-bolder ms-1">{{ $empty_beds }}</span> من أصل {{ $beds_count }}</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6 mb-lg-0 mb-4">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
            <i class="material-icons opacity-10">person_add</i>
          </div>
          <div class="text-start pt-1">
            <p class="text-sm mb-0 text-capitalize">دخول المرضى (شهري)</p>
            <h4 class="mb-0">{{ $patients_in_month }}</h4>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-3">
          <p class="mb-0 text-start"><span class="text-success text-sm font-weight-bolder ms-1">+5%</span> من الشهر الماضي</p>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
            <i class="material-icons opacity-10">logout</i>
          </div>
          <div class="text-start pt-1">
            <p class="text-sm mb-0 text-capitalize">خروج المرضى (شهري)</p>
            <h4 class="mb-0">{{ $patients_out_month }}</h4>
          </div>
        </div>
        <hr class="dark horizontal my-0">
        <div class="card-footer p-3">
          <p class="mb-0 text-start"><span class="text-danger text-sm font-weight-bolder ms-1">-2%</span> مقارنة بالشهر الماضي</p>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-4" id="stats-cards">
    <div class="col-lg-4 col-md-6 mt-4 mb-4">
      <div class="card z-index-2">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
          <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
            <div class="chart">
              <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h6 class="mb-0">عدد المرضى الجدد</h6>
          <p class="text-sm">تم تسجيل {{ $new_patients }} مريض جديد هذا الأسبوع</p>
          <hr class="dark horizontal">
          <div class="d-flex">
            <i class="material-icons text-sm my-auto ms-1">schedule</i>
            <p class="mb-0 text-sm">تم التحديث منذ يومين</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 mt-4 mb-4">
      <div class="card z-index-2">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
          <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
            <div class="chart">
              <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
            </div>
          </div>
        </div>
        <div class="card-body">
          <h6 class="mb-0">عدد الأسرة المشغولة</h6>
          <p class="text-sm">تم إشغال {{ $occupied_beds }} سرير هذا الأسبوع</p>
          <hr class="dark horizontal">
          <div class="d-flex">
            <i class="material-icons text-sm my-auto ms-1">schedule</i>
            <p class="mb-0 text-sm">تم التحديث منذ 4 دقائق</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mt-4 mb-3">
      <div class="card z-index-2">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
          <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
            <div class="chart">
              <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
            </div>
          </div>
        </div>
        <div class="card-body">
            <h6 class="mb-0">عدد المرضى المترددين</h6>
          <p class="text-sm">تم تسجيل {{ $daily_visits }} زيارة اليوم</p>
          <hr class="dark horizontal">
          <div class="d-flex">
            <i class="material-icons text-sm my-auto me-1">schedule</i>
            <p class="mb-0 text-sm">تم التحديث للتو</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="chart-bars-data" style="display: none;">{{ json_encode($chartBarsData) }}</div>
  <div id="chart-line-data" style="display: none;">{{ json_encode($chartLineData) }}</div>
  <div id="chart-line-tasks-data" style="display: none;">{{ json_encode($chartLineTasksData) }}</div>
</div>
@endsection
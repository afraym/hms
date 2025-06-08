<footer class="footer py-4">
    <div class="container-fluid">
        <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
                <div class="copyright text-center text-sm text-lg-end">
                   © <script>
                            document.write(new Date().getFullYear())
                        </script>,
                        تم التطوير بواسطة
                        <a href="https://afraym.com" class="font-weight-bold " target="_blank">شركة أمان
                            <img src="{{ asset('assets/img/amanlogo.png') }}" alt="Logo" class="hover-image">
                        </a>
                        لخدمة أهل أسوان الكرام.
                </div>
            </div>
        </div>
    </div>
</footer>
</div>
</main>
<div class="fixed-plugin">
  <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
    <i class="material-symbols-rounded py-2">settings</i>
  </a>
  <div class="card shadow-lg">
    <div class="card-header pb-0 pt-3">
      <div class="float-end">
        <h5 class="mt-3 mb-0">اعدادات الواجهة</h5>
        <p> خيارات لوحة التحكم.</p>
      </div>
      <div class="float-start mt-4">
        <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
          <i class="material-symbols-rounded">clear</i>
        </button>
      </div>
    </div>
    <hr class="horizontal dark my-1">
    <div class="card-body pt-sm-3 pt-0">
      <!-- Sidebar Backgrounds -->
      <div>
        <h6 class="mb-0">ألوان الشريط الجانبي</h6>
      </div>
      <a href="javascript:void(0)" class="switch-trigger background-color">
        <div class="badge-colors my-2 text-end">
          <span class="badge filter bg-gradient-primary" data-color="primary" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-dark active" data-color="dark" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
          <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
        </div>
      </a>
      <!-- Sidenav Type -->
      <div class="mt-3">
        <h6 class="mb-0">نوع الشريط الجانبي</h6>
        <p class="text-sm">اختر بين أنواع الشريط الجانبي المختلفة.</p>
      </div>
      <div class="d-flex">
        <button class="btn bg-gradient-dark px-3 mb-2" data-class="bg-gradient-dark" onclick="sidebarType(this)">داكن</button>
        <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">شفاف</button>
        <button class="btn bg-gradient-dark px-3 mb-2 active me-2" data-class="bg-white" onclick="sidebarType(this)">أبيض</button>
      </div>
      <p class="text-sm d-xl-none d-block mt-2">يمكنك تغيير نوع الشريط الجانبي فقط في عرض سطح المكتب.</p>
      <!-- Navbar Fixed -->
      <div class="mt-3 d-flex">
        <h6 class="mb-0">تثبيت شريط التنقل</h6>
        <div class="form-check form-switch me-auto my-auto">
          <input class="form-check-input mt-1 float-end me-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
        </div>
      </div>
      <hr class="horizontal dark my-3">
      <div class="mt-2 d-flex">
        <h6 class="mb-0">فاتح / داكن</h6>
        <div class="form-check form-switch me-auto my-auto">
          <input class="form-check-input mt-1 float-end me-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
        </div>
      </div>
      <hr class="horizontal dark my-sm-4">
    </div>
  </div>
</div>
<!-- Core JS Files -->
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<!-- jQuery Toast Plugin -->
<link rel="stylesheet" href="{{ asset('assets/css/jquery.toast.min.css') }}">
<script src="{{ asset('assets/js/jquery.toast.min.js') }}"></script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>
<!-- Control Center for Material Dashboard -->
<script src="{{ asset('assets/js/material-dashboard.min.js?v=3.2.0') }}"></script>
<!-- Statcounter Code -->
<script type="text/javascript">
    var sc_project = 13136548;
    var sc_invisible = 1;
    var sc_security = "baf81df5";
</script>
<script type="text/javascript" src="https://www.statcounter.com/counter/counter.js" async></script>
<noscript>
    <div class="statcounter">
        <a title="Web Analytics" href="https://statcounter.com/" target="_blank">
            <img class="statcounter" src="https://c.statcounter.com/13136548/0/baf81df5/1/" alt="Web Analytics" referrerPolicy="no-referrer-when-downgrade">
        </a>
    </div>
</noscript>
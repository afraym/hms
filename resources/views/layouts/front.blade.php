<!--
=========================================================
* Material Dashboard 2 - v3.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2023 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ asset('assets/img/site.webmanifest') }}">
</head>
  <title>
     {{ __('routes.' . Route::currentRouteName()) }} | مستشفى الباطنة التخصصي 
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/fonts/inter.css') }}" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="{{ asset('assets/css/42d5adcbca.js') }}" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="{{ asset('assets/css/Material+Icons+Round.css') }}" rel="stylesheet">
  <!-- Material Symbols -->
  <link href="{{ asset('assets/css/Material+Symbols+Rounded.css') }}" rel="stylesheet" />
  <!-- CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
  <link id="pagestyle" href="{{ asset('assets/css/material-dashboard.css?v=3.2.0') }}" rel="stylesheet" />

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .hover-image {
      display: none;
      position: absolute;
        top: -510%;
        left: 50%;
        transform: translateX(-50%);
        width: 14%;
      z-index: 10;
    }

    a:hover .hover-image {
      display: block;
    }
  </style>
</head>

<body class="bg-gray-200 rtl">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
        <!-- شريط التنقل -->
        <nav class="navbar navbar-expand-lg blur border-radius-xl top-0 z-index-3 shadow position-absolute my-3 py-2 start-0 end-0 mx-4">
          <div class="container-fluid ps-2 pe-0">
            <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3" href="{{ route('admin.dashboard') }}">
              لوحة التحكم
            </a>
            <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="تبديل التنقل">
              <span class="navbar-toggler-icon mt-2">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </span>
            </button>
            <div class="collapse navbar-collapse" id="navigation">
              <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                  <a class="nav-link  align-items-center me-2 " aria-current="page" href="{{ route('admin.dashboard') }}">
                    <i class="fa fa-chart-pie opacity-6 text-dark me-1"></i>
                        لوحة التحكم
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link me-2" href="{{ route('profile') }}">
                    <i class="fa fa-user opacity-6 text-dark me-1"></i>
                    الملف الشخصي
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link me-2" href="{{ route('register') }}">
                    <i class="fas fa-user-circle opacity-6 text-dark me-1"></i>
                    تسجيل حساب
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link me-2" href="{{ route('login') }}">
                    <i class="fas fa-key opacity-6 text-dark me-1"></i>
                    تسجيل الدخول
                  </a>
                </li>
              </ul>

            </div>
          </div>
        </nav>
        <!-- نهاية شريط التنقل -->
      </div>
    </div>
  </div>
  @yield('content')
  <!-- ملفات JS الأساسية -->
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <script src="{{ asset('assets/js/material-dashboard.min.js?v=3.1.0') }}"></script>
</body>

</html>
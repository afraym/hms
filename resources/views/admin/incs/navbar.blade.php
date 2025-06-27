<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg overflow-x-hidden">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl no-print" id="navbarBlur" data-scroll="true" >
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 ">
            <li class="breadcrumb-item text-sm ps-2"><a class="opacity-5 text-dark" href="javascript:;">لوحات القيادة</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">{{ __('routes.' . Route::currentRouteName()) }}</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">{{ __('routes.' . Route::currentRouteName()) }}</h6>  </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 px-0" id="navbar">
          {{-- Add user dropdown here --}}
          <ul class="navbar-nav me-auto ms-0 justify-content-end">
            <li class="nav-item dropdown pe-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="d-flex align-items-center">
                  <i class="material-icons-round me-sm-1">person</i>
                  <span class="d-none d-sm-inline">{{ auth()->user()->name }}</span>
                </div>
              </a>
              <ul class="dropdown-menu dropdown-menu-end px-2 py-3" aria-labelledby="userDropdown">
                <li>
                  <a class="dropdown-item border-radius-md" href="{{ route('profile') }}">
                    <div class="d-flex align-items-center">
                      <i class="material-icons-round ms-2">manage_accounts</i>
                      <span>الملف الشخصي</span>
                    </div>
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item border-radius-md text-danger">
                      <div class="d-flex align-items-center">
                        <i class="material-icons-round ms-2">logout</i>
                        <span>تسجيل الخروج</span>
                      </div>
                    </button>
                  </form>
                </li>
              </ul>
            </li>

            {{-- Existing navbar items --}}
            <li class="nav-item d-xl-none pe-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>
            <li class="nav-item px-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0">
                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
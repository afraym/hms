<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-end me-3 rotate-caret bg-gradient-dark" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute start-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href="{{ route('admin.dashboard') }}">
      <img src="/assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="main_logo">
      <span class="me-1 font-weight-bold text-white">نظام إدارة المستشفى</span>
    </a>
  </div>
  <hr class="horizontal light mt-0 mb-2">
  <div class="collapse navbar-collapse px-0 w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
          <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
            <i class="material-icons-round opacity-10">dashboard</i>
          </div>
          <span class="nav-link-text me-1">لوحة التحكم</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('patients.index') ? 'active' : '' }}" href="{{ route('patients.index') }}">
          <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
            <i class="material-icons-round opacity-10">groups</i>
          </div>
          <span class="nav-link-text me-1">قائمة المرضى</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('patients.create') ? 'active' : '' }}" href="{{ route('patients.create') }}">
          <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
            <i class="material-icons-round opacity-10">group_add</i>
          </div>
          <span class="nav-link-text me-1">إدخال مريض</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('beds.index') ? 'active' : '' }}" href="{{ route('beds.index') }}">
          <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
            <i class="material-icons-round opacity-10">bed</i>
          </div>
          <span class="nav-link-text me-1">الأسرة</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('beds.create') ? 'active' : '' }}" href="{{ route('beds.create') }}">
          <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
            <i class="material-icons-round opacity-10">add</i>
          </div>
          <span class="nav-link-text me-1">إضافة سرير</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('departments.index') ? 'active' : '' }}" href="{{ route('departments.index') }}">
          <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
            <i class="material-icons-round opacity-10">business</i>
          </div>
          <span class="nav-link-text me-1">الأقسام</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('departments.create') ? 'active' : '' }}" href="{{ route('departments.create') }}">
          <div class="text-white text-center ms-2 d-flex align-items-center justify-content-center">
            <i class="material-icons-round opacity-10">add_business</i>
          </div>
          <span class="nav-link-text me-1">إضافة قسم</span>
        </a>
      </li>
    </ul>
  </div>
  <div class="sidenav-footer position-absolute w-100 bottom-0">
    <div class="mx-3">
      <a class="btn bg-gradient-primary w-100" href="https://afraym.com" type="button">الاتصال بالدعم</a>
    </div>
  </div>
</aside>
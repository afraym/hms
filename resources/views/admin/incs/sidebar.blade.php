  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-end me-2 rotate-caret bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute start-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard " target="_blank">
        <img src="/assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo">
        <span class="me-1 text-sm text-dark">Creative Tim</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse px-0 w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('admin.dashboard') }}">
            <i class="material-symbols-rounded opacity-10">dashboard</i>
            <span class="nav-link-text me-1">لوحة التحكم</span>
          </a>
        </li>
        <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('patients.index') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('patients.index') }}">
            <i class="material-symbols-rounded opacity-10">patient_list</i>
            <span class="nav-link-text me-1">قائمة المرضى</span>
        </a>
        </li>
           <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('patients.create') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('patients.create') }}">
            <i class="material-symbols-rounded opacity-10">group_add</i>
            <span class="nav-link-text me-1">ادخال مريض</span>
        </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('beds.index') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('beds.index') }}">
            <i class="material-symbols-rounded opacity-10">bed</i>
            <span class="nav-link-text me-1">الأسرة</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('beds.create') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('beds.create') }}">
            <i class="material-symbols-rounded opacity-10">add</i>
            <span class="nav-link-text me-1">إضافة سرير</span>
          </a>
        </li>

      </ul>
    </div>
  </aside>
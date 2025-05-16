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
          <a class="nav-link {{ request()->routeIs('beds.create') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('beds.create') }}">
            <i class="material-symbols-rounded opacity-10">add</i>
            <span class="nav-link-text me-1">إضافة سرير</span>
          </a>
        </li>

      </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
      <div class="mx-3">
        <a class="btn btn-outline-dark mt-4 w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard?ref=sidebarfree" type="button">Documentation</a>
        <a class="btn bg-gradient-dark w-100" href="https://www.creative-tim.com/product/material-dashboard-pro?ref=sidebarfree" type="button">Upgrade to pro</a>
      </div>
    </div>
  </aside>
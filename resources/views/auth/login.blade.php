@extends('layouts.front')

@section('content')

  <main class="main-content mt-0">
    @php
        $folder = public_path('assets/img/login-backgrounds');
        $files = collect(\File::files($folder))
            ->filter(function($file) {
                return in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp']);
            })
            ->values();
        $week = date('W'); // Get the current week number
        $image = $files->count() > 0
            ? asset('assets/img/login-backgrounds/' . $files[$week % $files->count()]->getFilename())
            : asset('assets/img/default.jpg');
    @endphp
    <div class="page-header align-items-start min-vh-100" style="background-image: url('{{ $image }}');">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">تسجيل الدخول</h4>

                </div>
              </div>
              <div class="card-body">
                <form role="form" class="text-start" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="input-group input-group-outline my-3">
                        <label class="form-label" for="email">البريد الإلكتروني</label>
                        <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                    </div>
                    @error('email')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror

                    <div class="input-group input-group-outline mb-3">
                        <label class="form-label" for="password">كلمة المرور</label>
                        <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    </div>
                    @error('password')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror

                    <div class="form-check form-switch d-flex align-items-center mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label mb-0 ms-3" for="rememberMe">تذكرني</label>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">تسجيل الدخول</button>
                    </div>

                    {{-- @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a class="text-primary" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        </div>
                    @endif --}}

                    <p class="mt-4 text-sm text-center">
                        ليس لديك حساب؟
                        <a href="{{ route('register')}}" class="text-primary text-gradient font-weight-bold">إنشاء حساب</a>
                    </p>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    <footer class="footer position-absolute bottom-2 py-2 w-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 my-auto">
                    <div class="copyright text-center text-sm text-white">
                        © <script>
                            document.write(new Date().getFullYear())
                        </script>,
                        تم التطوير بواسطة
                        <a href="https://afraym.com" class="font-weight-bold text-white" target="_blank">شركة أمان
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
{{-- l --}}
{{-- <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center bg-gradient-primary text-white">
                    <h4>{{ __('Login') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                            @error('password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Login') }}
                            </button>
                        </div>

                        <!-- Forgot Password -->
                        @if (Route::has('password.request'))
                            <div class="text-center mt-3">
                                <a class="text-primary" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection

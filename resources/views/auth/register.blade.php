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
        $week = date('W');
        $image = $files->count() > 0
            ? asset('assets/img/login-backgrounds/' . $files[$week % $files->count()]->getFilename())
            : asset('assets/img/default.jpg');
    @endphp
    
    <div class="page-header align-items-start min-vh-100" style="background-image: url('{{ $image }}');">
        <span class="mask bg-gradient-dark opacity-6"></span>
        <div class="container my-auto">
            <div class="row">
                <div class="col-lg-5 col-md-8 col-12 mx-auto">
                    <div class="card z-index-0 fadeIn3 fadeInBottom">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">إنشاء حساب جديد</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form role="form" method="POST" action="{{ route('register') }}">
                                @csrf

                                <!-- Name Input -->
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label" for="name">الاسم الكامل</label>
                                    <input id="name" type="text" name="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required autofocus>
                                </div>
                                @error('name')
                                    <span class="text-danger small mb-3 d-block">{{ $message }}</span>
                                @enderror

                                <!-- Email Input -->
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label" for="email">البريد الإلكتروني</label>
                                    <input id="email" type="email" name="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email') }}">
                                </div>
                                @error('email')
                                    <span class="text-danger small mb-3 d-block">{{ $message }}</span>
                                @enderror

                                <!-- Phone Input -->
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label" for="phone">رقم الهاتف</label>
                                    <input id="phone" type="tel" name="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone') }}" 
                                           required>
                                </div>
                                @error('phone')
                                    <span class="text-danger small mb-3 d-block">{{ $message }}</span>
                                @enderror

                                <!-- Password Input -->
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label" for="password">كلمة المرور</label>
                                    <input id="password" type="password" name="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           required>
                                </div>
                                @error('password')
                                    <span class="text-danger small mb-3 d-block">{{ $message }}</span>
                                @enderror

                                <!-- Confirm Password Input -->
                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label" for="password_confirmation">تأكيد كلمة المرور</label>
                                    <input id="password_confirmation" type="password" 
                                           name="password_confirmation" class="form-control" required>
                                </div>


                                <!-- Submit Button -->
                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">إنشاء الحساب</button>
                                </div>

                                <!-- Login Link -->
                                <p class="mt-4 text-sm text-center">
                                    لديك حساب بالفعل؟
                                    <a href="{{ route('login') }}" class="text-primary text-gradient font-weight-bold">تسجيل الدخول</a>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="footer position-absolute bottom-2 py-2 w-100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 my-auto">
                        <div class="copyright text-center text-sm text-white">
                            © <script>document.write(new Date().getFullYear())</script>,
                            تم التطوير بواسطة
                            <a href="https://aman.it.com" class="font-weight-bold text-white" target="_blank">شركة أمان
                                <img src="{{ asset('assets/img/amanlogo.png') }}" alt="Logo" class="hover-image">
                            </a>
                            اهداءً لمستشفيات اسوان الجامعية.
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</main>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone');
    
    // Format phone number as user types
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
        
        // Limit to 11 digits for Egyptian numbers
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        
        // Format the number
        if (value.length >= 3) {
            value = value.substring(0, 3) + ' ' + value.substring(3);
        }
        if (value.length >= 7) {
            value = value.substring(0, 7) + ' ' + value.substring(7);
        }
        
        e.target.value = value;
    });
    
    // Validation on blur
    phoneInput.addEventListener('blur', function(e) {
        const value = e.target.value.replace(/\D/g, '');
        
        if (value.length < 10 || value.length > 11) {
            e.target.classList.add('is-invalid');
        } else {
            e.target.classList.remove('is-invalid');
        }
    });
});
</script>
@endpush

@endsection

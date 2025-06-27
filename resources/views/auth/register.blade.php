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
        $week = 27;
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
                                <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">تسجيل حساب جديد</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form role="form" class="text-start" method="POST" action="{{ route('register') }}">
                                @csrf
                                
                                <div class="input-group input-group-outline my-3 @if(old('name')) is-filled @endif">
                                    <label class="form-label">الاسم</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                        value="{{ old('name') }}">
                                </div>
                                @error('name')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror

                                <div class="input-group input-group-outline my-3 @if(old('email')) is-filled @endif">
                                    <label class="form-label">البريد الإلكتروني</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                        value="{{ old('email') }}">
                                </div>
                                @error('email')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror

                                <div class="input-group input-group-outline my-3">
                                    <label class="form-label">كلمة المرور</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                </div>
                                @error('password')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror

                                <div class="input-group input-group-outline mb-3">
                                    <label class="form-label">تأكيد كلمة المرور</label>
                                    <input type="password" name="password_confirmation" class="form-control">
                                </div>

                                <div class="input-group input-group-static mb-3">
                                    <label class="ms-0">نوع المستخدم</label>
                                    <select name="role" class="form-control @error('role') is-invalid @enderror">
                                        <option value="reception" selected>موظف استقبال</option>
                                        {{-- <option value="manager">مدير</option>
                                        <option value="kitchen">موظف مطبخ</option> --}}
                                    </select>
                                </div>
                                @error('role')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror

                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">تسجيل</button>
                                </div>

                                <p class="mt-4 text-sm text-center">
                                    لديك حساب بالفعل؟
                                    <a href="{{ route('login')}}" class="text-primary text-gradient font-weight-bold">تسجيل الدخول</a>
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
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle input focus and blur events
    document.querySelectorAll('.input-group-outline input').forEach(input => {
        // Add is-filled class when input has value
        if (input.value) {
            input.closest('.input-group').classList.add('is-filled');
        }
        
        // Handle input events
        input.addEventListener('focus', () => {
            input.closest('.input-group').classList.add('is-focused');
        });
        
        input.addEventListener('blur', () => {
            input.closest('.input-group').classList.remove('is-focused');
            if (input.value) {
                input.closest('.input-group').classList.add('is-filled');
            } else {
                input.closest('.input-group').classList.remove('is-filled');
            }
        });
        
        // Handle input changes
        input.addEventListener('input', () => {
            if (input.value) {
                input.closest('.input-group').classList.add('is-filled');
            } else {
                input.closest('.input-group').classList.remove('is-filled');
            }
        });
    });
});
</script>
@endpush

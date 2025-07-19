<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check if the login attempts have been exceeded
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        $result = $this->attemptLogin($request);
        
        if ($result === true) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }
            return $this->sendLoginResponse($request);
        }

        // Increment login attempts
        $this->incrementLoginAttempts($request);

        return $this->sendSpecificFailedLoginResponse($request, $result);
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'البريد الإلكتروني أو رقم الهاتف مطلوب.',
            'login.string' => 'البريد الإلكتروني أو رقم الهاتف يجب أن يكون نص.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.string' => 'كلمة المرور يجب أن تكون نص.',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     * Returns: true for success, 'user_not_found' if user doesn't exist, 'wrong_password' if password is wrong
     */
    protected function attemptLogin(Request $request)
    {
        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->filled('remember');

        // Find user by email or phone
        $user = User::findByEmailOrPhone($login);

        // Check if user exists
        if (!$user) {
            return 'user_not_found';
        }

        // Check if password is correct
        if (!Hash::check($password, $user->password)) {
            return 'wrong_password';
        }

        // Check if user account is active (if you have status field)
        if (isset($user->status) && $user->status !== 'active') {
            return 'account_inactive';
        }

        // Login successful
        Auth::login($user, $remember);
        return true;
    }

    /**
     * Send specific failed login response based on failure reason.
     */
    protected function sendSpecificFailedLoginResponse(Request $request, $reason)
    {
        $messages = [
            'user_not_found' => 'البريد الإلكتروني أو رقم الهاتف غير مسجل في النظام.',
            'wrong_password' => 'كلمة المرور غير صحيحة.',
            'account_inactive' => 'حسابك غير مفعل. يرجى التواصل مع الإدارة.',
        ];

        $message = $messages[$reason] ?? 'حدث خطأ أثناء تسجيل الدخول.';

        throw ValidationException::withMessages([
            'login' => [$message],
        ]);
    }

    /**
     * Get the failed login response instance (fallback).
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'login' => ['البريد الإلكتروني أو رقم الهاتف أو كلمة المرور غير صحيحة.'],
        ]);
    }

    /**
     * Get the lockout response instance.
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        $minutes = ceil($seconds / 60);

        throw ValidationException::withMessages([
            'login' => ["تم تجاوز عدد محاولات تسجيل الدخول المسموحة. حاول مرة أخرى بعد {$minutes} دقيقة."],
        ])->status(429);
    }

    /**
     * The user has been authenticated.
     */
    protected function authenticated(Request $request, $user)
    {
        // Add success message
        session()->flash('success', 'مرحباً بك، ' . $user->name);
        
        // Log the login activity using Laravel's logger
        \Log::info('User logged in', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_phone' => $user->phone,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ]);

        // Redirect based on user role
        switch ($user->role) {
            case 'admin':
                return redirect()->intended('/admin/dashboard');
            case 'manager':
                return redirect()->intended('/manager/dashboard');
            case 'reception':
                return redirect()->intended('/reception/dashboard');
            default:
                return redirect()->intended($this->redirectPath());
        }
    }
}

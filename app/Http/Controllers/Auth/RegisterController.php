<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:15', 'unique:users', 'regex:/^[0-9+\-\s()]+$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'الاسم يجب أن يكون نص.',
            'name.max' => 'الاسم يجب ألا يتجاوز 255 حرف.',
            
            'email.string' => 'البريد الإلكتروني يجب أن يكون نص.',
            'email.email' => 'البريد الإلكتروني غير صحيح.',
            'email.max' => 'البريد الإلكتروني يجب ألا يتجاوز 255 حرف.',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم من قبل.',
            
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.string' => 'رقم الهاتف يجب أن يكون نص.',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 15 رقم.',
            'phone.unique' => 'رقم الهاتف مستخدم من قبل.',
            'phone.regex' => 'رقم الهاتف غير صحيح. يرجى إدخال رقم هاتف مصري صحيح (مثال: 01234567890).',
            
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.string' => 'كلمة المرور يجب أن تكون نص.',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Clean phone number - remove spaces, hyphens, parentheses
        $phone = preg_replace('/[^0-9+]/', '', $data['phone']);
        
        return User::create([
            'name' => trim($data['name']),
            'email' => !empty($data['email']) ? strtolower(trim($data['email'])) : null,
            'phone' => $phone,
            'password' => Hash::make($data['password']),
            'role' => 'reception', // Default role is reception
        ]);
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function registered(Request $request, $user)
    {
        session()->flash('success', 'تم إنشاء الحساب بنجاح. مرحباً بك، ' . $user->name);
        return redirect($this->redirectPath());
    }
}

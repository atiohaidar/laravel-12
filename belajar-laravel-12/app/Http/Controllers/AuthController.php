<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory; // ini make contract
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

class AuthController extends Controller
{
    protected $auth;
    protected $hasher;
    protected $validator;

    public function __construct(Factory $auth, Hasher $hasher, ValidationFactory $validator)
    {
        $this->auth = $auth;
        $this->hasher = $hasher;
        $this->validator = $validator;
    }
    
    public function register()
    {
        return view('auth.register');
    }

    public function registerPost(Request $request)
    {
        $validator = $this->validator->make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $this->hasher->make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil, silahkan login.');
    }

    public function login()
    {
        return view('auth.login');
    }

    public function loginPost(Request $request)
    {
        $validator = $this->validator->make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');

        if ($this->auth->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/users'); // Redirect ke halaman user atau dashboard
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        $this->auth->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
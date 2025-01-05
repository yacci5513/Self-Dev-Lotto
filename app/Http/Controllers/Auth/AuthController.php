<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:40|unique:users',
            'password' => 'required|string|min:8|max:16|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')
            ->with('success', '회원가입이 완료되었습니다. 로그인해주세요.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => '이메일 또는 비밀번호가 일치하지 않습니다.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function checkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|max:40'
            ]);

            $exists = User::where('email', $request->email)->exists();

            return response()->json([
                'available' => !$exists,
                'message' => $exists ? '이미 사용 중인 이메일입니다.' : '사용 가능한 이메일입니다.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'available' => false,
                'message' => '올바른 이메일 형식이 아닙니다.'
            ], 422);
        }
    }
} 
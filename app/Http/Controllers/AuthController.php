<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Hiển thị trang đăng ký
     * @return \Illuminate\Contracts\View\View
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Hiển thị trang đăng nhập
     * @return \Illuminate\Contracts\View\View
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng ký
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleRegister(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);

        Log::channel('auth')->info('Đăng ký thành công', [
            'username' => $user->username,
            'full_name' => $user->full_name,
            'email' => $user->email,
            'ip' => $request->ip(),
            'time' => now()->toDateTimeString(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với hệ thống.');
    }

    /**
     * Xử lý đăng nhập
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            Log::channel('auth')->info('Đăng nhập thành công', [
                'full_name' => Auth::user()->full_name,
                'username' => $request->username,
                'ip' => $request->ip(),
                'time' => now()->toDateTimeString(),
            ]);
            return redirect()->route('dashboard')->with('success', 'Đăng nhập thành công!');
        }

        return redirect()->route('login')->with('error', 'Tên đăng nhập hoặc mật khẩu không đúng.');
    }

    /**
     * Xử lý đăng xuất
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Đã đăng xuất thành công!');
    }
}
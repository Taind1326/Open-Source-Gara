<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;

// M01 - Xác thực
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'SoDienThoai' => 'required|string',
            'password'    => 'required|string',
        ], [], ['SoDienThoai' => 'số điện thoại', 'password' => 'mật khẩu']);

        $tk = TaiKhoan::where('SoDienThoai', $cred['SoDienThoai'])->first();

        // Chỉ báo "bị khóa" khi đã đúng mật khẩu, tránh lộ thông tin tài khoản cho người lạ
        if ($tk && Hash::check($cred['password'], $tk->MatKhau) && !$tk->dangHoatDong()) {
            return back()->withInput($request->only('SoDienThoai'))
                ->withErrors(['SoDienThoai' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ gara.']);
        }

        if (!Auth::attempt([
            'SoDienThoai' => $cred['SoDienThoai'],
            'password'    => $cred['password'],
            'TrangThai'   => TaiKhoan::HOAT_DONG,
        ])) {
            return back()->withInput($request->only('SoDienThoai'))
                ->withErrors(['SoDienThoai' => 'Số điện thoại hoặc mật khẩu không đúng.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('trang-chu'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'HoTen'       => 'required|string|max:100',
            'SoDienThoai' => ['required', 'regex:/^0\d{9}$/', 'unique:TAIKHOAN,SoDienThoai'],
            'Email'       => 'nullable|email|max:150|unique:TAIKHOAN,Email',
            'DiaChi'      => 'nullable|string|max:255',
            'password'    => ['required', 'confirmed', Password::min(6)],
        ], [
            'SoDienThoai.regex'  => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',
            'SoDienThoai.unique' => 'Số điện thoại này đã được đăng ký.',
            'Email.unique'       => 'Email này đã được sử dụng.',
        ]);

        // Đăng ký công khai luôn là khách hàng; KTV/Admin do Admin tạo
        $tk = TaiKhoan::create([
            'HoTen'       => $data['HoTen'],
            'SoDienThoai' => $data['SoDienThoai'],
            'Email'       => $data['Email'] ?? null,
            'DiaChi'      => $data['DiaChi'] ?? null,
            'MatKhau'     => $data['password'],
            'VaiTro'      => TaiKhoan::VAI_TRO_USER,
            'TrangThai'   => TaiKhoan::HOAT_DONG,
        ]);

        Auth::login($tk);
        $request->session()->regenerate();

        return redirect()->route('trang-chu')->with('success', 'Đăng ký thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // Điều hướng sau đăng nhập theo vai trò. Route của module khác chưa có thì fallback về '/'
    public function trangChu()
    {
        $user = Auth::user();

        $routeDich = match ($user->VaiTro) {
            TaiKhoan::VAI_TRO_ADMIN => 'admin.taikhoan.index',
            TaiKhoan::VAI_TRO_USER  => 'lichhen.index',   // M06
            default                 => null,              // KTV: chờ module KTV
        };

        if ($routeDich && Route::has($routeDich)) {
            return redirect()->route($routeDich);
        }

        return redirect()->route('ho-so.show');
    }
}

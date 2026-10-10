<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'Email' => ['required', 'email', 'max:100'],
            'MatKhau' => ['required', 'string', 'max:255'],
        ], [
            'Email.required' => 'Vui lòng nhập email.',
            'Email.email' => 'Email không đúng định dạng.',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $credentials = [
            'Email' => trim($data['Email']),
            'password' => $data['MatKhau'],
            'TrangThai' => 'HOAT_DONG',
        ];

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'Email' => 'Email hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('public.dichvu.index'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->merge([
            'HoTen' => trim((string) $request->input('HoTen')),
            'Email' => trim((string) $request->input('Email')),
            'SoDienThoai' => trim((string) $request->input('SoDienThoai')),
        ]);

        $data = $request->validate([
            'HoTen' => ['required', 'string', 'max:100'],
            'Email' => [
                'required',
                'email',
                'max:100',
                'unique:TAIKHOAN,Email',
            ],
            'SoDienThoai' => [
                'required',
                'regex:/^0[0-9]{9}$/',
                'unique:TAIKHOAN,SoDienThoai',
            ],
            'MatKhau' => [
                'required',
                'string',
                'max:255',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
        ], [
            'HoTen.required' => 'Vui lòng nhập họ tên.',
            'HoTen.max' => 'Họ tên không được vượt quá 100 ký tự.',
            'Email.required' => 'Vui lòng nhập email.',
            'Email.email' => 'Email không đúng định dạng.',
            'Email.unique' => 'Email đã được sử dụng.',
            'Email.max' => 'Email không được vượt quá 100 ký tự.',
            'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'SoDienThoai.regex' => 'Số điện thoại gồm 10 chữ số và bắt đầu bằng 0.',
            'SoDienThoai.unique' => 'Số điện thoại đã được sử dụng.',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
            'MatKhau.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'MatKhau.letters' => 'Mật khẩu phải có chữ cái.',
            'MatKhau.numbers' => 'Mật khẩu phải có chữ số.',
        ]);

        $taiKhoan = TaiKhoan::create([
            'HoTen' => $data['HoTen'],
            'Email' => $data['Email'],
            'SoDienThoai' => $data['SoDienThoai'],
            'MatKhau' => Hash::make($data['MatKhau']),
            'VaiTro' => 'USER',
            'DiemTichLuy' => 0,
            'TrangThai' => 'HOAT_DONG',
        ]);

        Auth::login($taiKhoan);

        $request->session()->regenerate();

        return redirect()
            ->intended(route('public.dichvu.index'))
            ->with('success', 'Đăng ký tài khoản thành công.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('public.dichvu.index');
    }
}

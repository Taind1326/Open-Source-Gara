<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\Services\GaragePassword;
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

    public function login(
        Request $request,
        GaragePassword $garagePassword
    ): RedirectResponse {
        $this->trimFields($request, ['Email']);

        $data = $request->validate([
            'Email' => [
                'bail',
                'required',
                'email',
                'max:100',
            ],
            'MatKhau' => [
                'bail',
                'required',
                'string',
                'max:255',
            ],
        ], [
            'Email.required' => 'Vui lòng nhập email.',
            'Email.email' => 'Email không đúng định dạng.',
            'Email.max' => 'Email không được vượt quá 100 ký tự.',

            'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
            'MatKhau.string' => 'Mật khẩu không hợp lệ.',
            'MatKhau.max' => 'Mật khẩu không được vượt quá 255 ký tự.',
        ]);

        $taiKhoan = TaiKhoan::query()
            ->where('Email', $data['Email'])
            ->first();

        if (
            !$taiKhoan
            || $taiKhoan->TrangThai !== 'HOAT_DONG'
            || !$garagePassword->check($taiKhoan, $data['MatKhau'])
        ) {
            throw ValidationException::withMessages([
                'Email' => 'Email hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.',
            ]);
        }

        Auth::login($taiKhoan);

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        $trangDich = match ($taiKhoan->VaiTro) {
            'ADMIN' => 'admin.dashboard',
            'TECHNICIAN' => 'ktv.lichhen.index',
            default => 'public.home',
        };

        return redirect()->route($trangDich);
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $this->trimFields($request, [
            'HoTen',
            'Email',
            'SoDienThoai',
        ]);

        $data = $request->validate([
            'HoTen' => [
                'bail',
                'required',
                'string',
                'max:100',
            ],
            'Email' => [
                'bail',
                'required',
                'email',
                'max:100',
                'unique:TAIKHOAN,Email',
            ],
            'SoDienThoai' => [
                'bail',
                'required',
                'string',
                'regex:/\A0[0-9]{9}\z/',
                'unique:TAIKHOAN,SoDienThoai',
            ],
            'MatKhau' => [
                'bail',
                'required',
                'string',
                'max:255',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'MatKhau_confirmation' => [
                'bail',
                'required',
                'string',
                'same:MatKhau',
            ],
        ], [
            'HoTen.required' => 'Vui lòng nhập họ tên.',
            'HoTen.string' => 'Họ tên không hợp lệ.',
            'HoTen.max' => 'Họ tên không được vượt quá 100 ký tự.',

            'Email.required' => 'Vui lòng nhập email.',
            'Email.email' => 'Email không đúng định dạng.',
            'Email.max' => 'Email không được vượt quá 100 ký tự.',
            'Email.unique' => 'Email đã được sử dụng.',

            'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'SoDienThoai.string' => 'Số điện thoại không hợp lệ.',
            'SoDienThoai.regex' => 'Số điện thoại gồm 10 chữ số và bắt đầu bằng 0.',
            'SoDienThoai.unique' => 'Số điện thoại đã được sử dụng.',

            'MatKhau.required' => 'Vui lòng nhập mật khẩu.',
            'MatKhau.string' => 'Mật khẩu không hợp lệ.',
            'MatKhau.max' => 'Mật khẩu không được vượt quá 255 ký tự.',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'MatKhau.letters' => 'Mật khẩu phải có chữ cái.',
            'MatKhau.mixed' => 'Mật khẩu phải có chữ hoa và chữ thường.',
            'MatKhau.numbers' => 'Mật khẩu phải có chữ số.',
            'MatKhau.symbols' => 'Mật khẩu phải có ký tự đặc biệt.',

            'MatKhau_confirmation.required' => 'Vui lòng nhập xác nhận mật khẩu.',
            'MatKhau_confirmation.string' => 'Mật khẩu xác nhận không hợp lệ.',
            'MatKhau_confirmation.same' => 'Mật khẩu xác nhận không khớp.',
        ]);

        $taiKhoan = TaiKhoan::create([
            'HoTen' => $data['HoTen'],
            'Email' => $data['Email'],
            'SoDienThoai' => $data['SoDienThoai'],
            'MatKhau' => Hash::make($data['MatKhau']),
            'VaiTro' => 'USER',
            'TrangThai' => 'HOAT_DONG',
        ]);

        Auth::login($taiKhoan);

        $request->session()->regenerate();
        $request->session()->forget('url.intended');

        return redirect()
            ->route('public.home')
            ->with('success', 'Đăng ký tài khoản thành công.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('public.home');
    }

    private function trimFields(Request $request, array $fields): void
    {
        $normalized = [];

        foreach ($fields as $field) {
            $value = $request->input($field);

            if (is_string($value)) {
                $normalized[$field] = trim($value);
            }
        }

        $request->merge($normalized);
    }
}
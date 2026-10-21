<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class TaiKhoanController extends Controller
{
    public function edit(Request $request)
    {
        return view('public.taikhoan.edit', [
            'taiKhoan' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $taiKhoan = $request->user();

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
                Rule::unique('TAIKHOAN', 'Email')
                    ->ignore($taiKhoan->MaTK, 'MaTK'),
            ],

            'SoDienThoai' => [
                'required',
                'string',
                'regex:/^0[0-9]{9}$/',
                Rule::unique('TAIKHOAN', 'SoDienThoai')
                    ->ignore($taiKhoan->MaTK, 'MaTK'),
            ],
        ], [
            'HoTen.required' => 'Vui lòng nhập họ và tên.',
            'HoTen.max' => 'Họ và tên không được vượt quá 100 ký tự.',

            'Email.required' => 'Vui lòng nhập email.',
            'Email.email' => 'Email không đúng định dạng.',
            'Email.max' => 'Email không được vượt quá 100 ký tự.',
            'Email.unique' => 'Email đã được tài khoản khác sử dụng.',

            'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'SoDienThoai.regex' => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng số 0.',
            'SoDienThoai.unique' => 'Số điện thoại đã được tài khoản khác sử dụng.',
        ]);

        // Chỉ cập nhật thông tin cá nhân, không nhận vai trò hoặc điểm từ form.
        $taiKhoan->update([
            'HoTen' => $data['HoTen'],
            'Email' => $data['Email'],
            'SoDienThoai' => $data['SoDienThoai'],
        ]);

        return redirect()
            ->route('public.taikhoan.edit')
            ->with('success', 'Đã cập nhật thông tin cá nhân.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validateWithBag('password', [
            'MatKhauHienTai' => ['required', 'string'],

            'MatKhau_confirmation' => ['bail', 'required', 'string', 'same:MatKhau'],

            'MatKhau' => [
                'required',
                'string',
                Password::min(8)->letters()->numbers(),
                'different:MatKhauHienTai',
            ],
        ], [
            'MatKhauHienTai.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'MatKhau.required' => 'Vui lòng nhập mật khẩu mới.',
            'MatKhau.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'MatKhau_confirmation.required' => 'Vui lòng nhập xác nhận mật khẩu.',
            'MatKhau_confirmation.same' => 'Xác nhận mật khẩu mới không khớp.',
            'MatKhau.different' => 'Mật khẩu mới phải khác mật khẩu hiện tại.',
        ]);

        DB::transaction(function () use ($request, $data) {
            $taiKhoan = TaiKhoan::where('MaTK', $request->user()->MaTK)
                ->lockForUpdate()
                ->firstOrFail();

            if (! Hash::check($data['MatKhauHienTai'], $taiKhoan->MatKhau)) {
                throw ValidationException::withMessages([
                    'MatKhauHienTai' => 'Mật khẩu hiện tại không đúng.',
                ])->errorBag('password');
            }

            $taiKhoan->update([
                'MatKhau' => Hash::make($data['MatKhau']),
            ]);
        });

        $request->session()->regenerate();

        return redirect()
            ->route('public.taikhoan.edit')
            ->with('success', 'Đã đổi mật khẩu thành công.');
    }
}
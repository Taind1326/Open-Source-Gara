<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

// M03 - Hồ sơ cá nhân (mọi vai trò)
class HoSoController extends Controller
{
    public function show()
    {
        return view('hoso.show', ['taiKhoan' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $tk = Auth::user();

        $data = $request->validate([
            'HoTen'       => 'required|string|max:100',
            'SoDienThoai' => ['required', 'regex:/^0\d{9}$/', Rule::unique('TAIKHOAN', 'SoDienThoai')->ignore($tk->MaTK, 'MaTK')],
            'Email'       => ['nullable', 'email', 'max:150', Rule::unique('TAIKHOAN', 'Email')->ignore($tk->MaTK, 'MaTK')],
            'DiaChi'      => 'nullable|string|max:255',
        ], [
            'SoDienThoai.regex'  => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',
            'SoDienThoai.unique' => 'Số điện thoại này đã được sử dụng.',
        ]);

        // Không cho tự đổi VaiTro/TrangThai ở đây → chỉ nhận đúng 4 trường trên
        $tk->update($data);

        return back()->with('success', 'Đã cập nhật hồ sơ.');
    }

    public function doiMatKhau(Request $request)
    {
        $data = $request->validate([
            'mat_khau_cu' => 'required|current_password',
            'password'    => ['required', 'confirmed', Password::min(6), 'different:mat_khau_cu'],
        ], [
            'mat_khau_cu.current_password' => 'Mật khẩu hiện tại không đúng.',
            'password.different'           => 'Mật khẩu mới phải khác mật khẩu cũ.',
        ]);

        Auth::user()->update(['MatKhau' => $data['password']]);

        return back()->with('success', 'Đã đổi mật khẩu.');
    }
}

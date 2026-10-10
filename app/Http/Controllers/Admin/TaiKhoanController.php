<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

// M02/M03 - Admin quản lý tài khoản
class TaiKhoanController extends Controller
{
    public function index(Request $request)
    {
        $tuKhoa = $request->input('tu_khoa');
        $vaiTro = $request->input('vai_tro');

        $dsTaiKhoan = TaiKhoan::query()
            ->when($tuKhoa, fn ($q) => $q->where(function ($q2) use ($tuKhoa) {
                $q2->where('HoTen', 'like', "%{$tuKhoa}%")
                   ->orWhere('SoDienThoai', 'like', "%{$tuKhoa}%")
                   ->orWhere('Email', 'like', "%{$tuKhoa}%");
            }))
            ->when(in_array($vaiTro, TaiKhoan::DANH_SACH_VAI_TRO, true), fn ($q) => $q->where('VaiTro', $vaiTro))
            ->orderByDesc('MaTK')
            ->paginate(15)
            ->withQueryString();

        return view('admin.taikhoan.index', compact('dsTaiKhoan', 'tuKhoa', 'vaiTro'));
    }

    public function create()
    {
        return view('admin.taikhoan.form', ['taiKhoan' => new TaiKhoan()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'HoTen'       => 'required|string|max:100',
            'SoDienThoai' => ['required', 'regex:/^0\d{9}$/', 'unique:TAIKHOAN,SoDienThoai'],
            'Email'       => 'nullable|email|max:150|unique:TAIKHOAN,Email',
            'DiaChi'      => 'nullable|string|max:255',
            'VaiTro'      => ['required', Rule::in(TaiKhoan::DANH_SACH_VAI_TRO)],
            'password'    => ['required', Password::min(6)],
        ]);

        $tk = TaiKhoan::create([
            'HoTen' => $data['HoTen'], 'SoDienThoai' => $data['SoDienThoai'],
            'Email' => $data['Email'] ?? null, 'DiaChi' => $data['DiaChi'] ?? null,
            'VaiTro' => $data['VaiTro'], 'MatKhau' => $data['password'],
            'TrangThai' => TaiKhoan::HOAT_DONG,
        ]);

        return redirect()->route('admin.taikhoan.show', $tk->MaTK)->with('success', 'Đã tạo tài khoản.');
    }

    // Xem tài khoản + danh sách xe của khách
    public function show(int $taiKhoan)
    {
        $taiKhoan = TaiKhoan::with('xe')->findOrFail($taiKhoan);

        return view('admin.taikhoan.show', compact('taiKhoan'));
    }

    public function edit(int $taiKhoan)
    {
        return view('admin.taikhoan.form', ['taiKhoan' => TaiKhoan::findOrFail($taiKhoan)]);
    }

    public function update(Request $request, int $taiKhoan)
    {
        $tk = TaiKhoan::findOrFail($taiKhoan);

        $data = $request->validate([
            'HoTen'       => 'required|string|max:100',
            'SoDienThoai' => ['required', 'regex:/^0\d{9}$/', Rule::unique('TAIKHOAN', 'SoDienThoai')->ignore($tk->MaTK, 'MaTK')],
            'Email'       => ['nullable', 'email', 'max:150', Rule::unique('TAIKHOAN', 'Email')->ignore($tk->MaTK, 'MaTK')],
            'DiaChi'      => 'nullable|string|max:255',
            'VaiTro'      => ['required', Rule::in(TaiKhoan::DANH_SACH_VAI_TRO)],
            'password'    => ['nullable', Password::min(6)],   // để trống = không đổi
        ]);

        // Không cho admin tự hạ quyền chính mình (tránh hệ thống không còn admin)
        if ($tk->MaTK === Auth::id() && $data['VaiTro'] !== TaiKhoan::VAI_TRO_ADMIN) {
            return back()->withInput()->withErrors(['VaiTro' => 'Bạn không thể tự thay đổi vai trò của chính mình.']);
        }

        $tk->fill(collect($data)->except('password')->all());
        if (!empty($data['password'])) {
            $tk->MatKhau = $data['password'];
        }
        $tk->save();

        return redirect()->route('admin.taikhoan.show', $tk->MaTK)->with('success', 'Đã cập nhật tài khoản.');
    }

    // Khóa / mở khóa (không xóa cứng vì tài khoản còn gắn với xe, lịch hẹn, phân công)
    public function doiTrangThai(int $taiKhoan)
    {
        $tk = TaiKhoan::findOrFail($taiKhoan);

        if ($tk->MaTK === Auth::id()) {
            return back()->withErrors(['error' => 'Bạn không thể tự khóa tài khoản của mình.']);
        }

        $tk->update(['TrangThai' => $tk->dangHoatDong() ? TaiKhoan::KHOA : TaiKhoan::HOAT_DONG]);

        return back()->with('success', $tk->dangHoatDong() ? 'Đã mở khóa tài khoản.' : 'Đã khóa tài khoản.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\LoaiDichVu;
use Illuminate\Http\Request;

class LoaiDichVuController extends Controller
{
    public function index(Request $request)
    {
        $tuKhoa = $request->input('tu_khoa');

        $danhSach = LoaiDichVu::when($tuKhoa, function ($query, $tuKhoa) {
            $query->where('TenLoaiDV', 'like', "%{$tuKhoa}%");
        })
        ->orderByDesc('MaLoaiDV')
        ->paginate(10)
        ->withQueryString();

        return view('loaidichvu.index', compact('danhSach', 'tuKhoa'));
    }

    public function create()
    {
        return view('loaidichvu.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'TenLoaiDV' => 'required|string|max:100',
            'MoTa' => 'nullable|string|max:500',
            'HinhAnh' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'TenLoaiDV.required' => 'Vui lòng nhập tên loại dịch vụ.',
            'HinhAnh.image' => 'File tải lên phải là hình ảnh.',
            'HinhAnh.max' => 'Hình ảnh không được vượt quá 2MB.',
        ]);

        if ($request->hasFile('HinhAnh')) {
            $data['HinhAnh'] = $request->file('HinhAnh')
                ->store('loai-dich-vu', 'public');
        }

        $data['TrangThai'] = 'HOAT_DONG';

        LoaiDichVu::create($data);

        return redirect()
            ->route('loaidichvu.index')
            ->with('success', 'Thêm loại dịch vụ thành công.');
    }

    public function edit(LoaiDichVu $loaidichvu)
    {
        return view('loaidichvu.edit', compact('loaidichvu'));
    }

    public function update(Request $request, LoaiDichVu $loaidichvu)
    {
        $data = $request->validate([
            'TenLoaiDV' => 'required|string|max:100',
            'MoTa' => 'nullable|string|max:500',
            'HinhAnh' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'TrangThai' => 'required|in:HOAT_DONG,NGUNG_HOAT_DONG',
        ]);

        if ($request->hasFile('HinhAnh')) {
            $data['HinhAnh'] = $request->file('HinhAnh')
                ->store('loai-dich-vu', 'public');
        }

        $loaidichvu->update($data);

        return redirect()
            ->route('loaidichvu.index')
            ->with('success', 'Cập nhật loại dịch vụ thành công.');
    }
}
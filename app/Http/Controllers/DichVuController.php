<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Models\LoaiDichVu;
use Illuminate\Http\Request;

class DichVuController extends Controller
{
    public function index(Request $request)
    {
        $tuKhoa = $request->input('tu_khoa');
        $maLoai = $request->input('ma_loai');

        $danhSach = DichVu::with('loaiDichVu')
            ->when($tuKhoa, function ($query, $tuKhoa) {
                $query->where('TenDV', 'like', "%{$tuKhoa}%");
            })
            ->when($maLoai, function ($query, $maLoai) {
                $query->where('MaLoaiDV', $maLoai);
            })
            ->orderByDesc('MaDV')
            ->paginate(10)
            ->withQueryString();

        $loaiDichVuList = LoaiDichVu::orderBy('TenLoaiDV')->get();

        return view('dichvu.index', compact(
            'danhSach',
            'loaiDichVuList',
            'tuKhoa',
            'maLoai'
        ));
    }

    public function create()
    {
        $loaiDichVuList = LoaiDichVu::where(
            'TrangThai',
            'HOAT_DONG'
        )->orderBy('TenLoaiDV')->get();

        return view('dichvu.create', compact('loaiDichVuList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'MaLoaiDV' => 'required|exists:LOAIDICHVU,MaLoaiDV',
            'TenDV' => 'required|string|max:150',
            'MoTa' => 'nullable|string|max:500',
            'Gia' => 'required|numeric|min:0',
            'HinhAnh' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'MaLoaiDV.required' => 'Vui lòng chọn loại dịch vụ.',
            'TenDV.required' => 'Vui lòng nhập tên dịch vụ.',
            'Gia.required' => 'Vui lòng nhập giá dịch vụ.',
            'Gia.numeric' => 'Giá dịch vụ phải là số.',
            'Gia.min' => 'Giá dịch vụ không được nhỏ hơn 0.',
        ]);

        if ($request->hasFile('HinhAnh')) {
            $data['HinhAnh'] = $request->file('HinhAnh')
                ->store('dich-vu', 'public');
        }

        $data['TrangThai'] = 'HOAT_DONG';

        DichVu::create($data);

        return redirect()
            ->route('dichvu.index')
            ->with('success', 'Thêm dịch vụ thành công.');
    }

    public function edit(DichVu $dichvu)
    {
        $loaiDichVuList = LoaiDichVu::where(
            'TrangThai',
            'HOAT_DONG'
        )->orderBy('TenLoaiDV')->get();

        return view('dichvu.edit', compact(
            'dichvu',
            'loaiDichVuList'
        ));
    }

    public function update(Request $request, DichVu $dichvu)
    {
        $data = $request->validate([
            'MaLoaiDV' => 'required|exists:LOAIDICHVU,MaLoaiDV',
            'TenDV' => 'required|string|max:150',
            'MoTa' => 'nullable|string|max:500',
            'Gia' => 'required|numeric|min:0',
            'HinhAnh' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'TrangThai' => 'required|in:HOAT_DONG,NGUNG_HOAT_DONG',
        ]);

        if ($request->hasFile('HinhAnh')) {
            $data['HinhAnh'] = $request->file('HinhAnh')
                ->store('dich-vu', 'public');
        }

        $dichvu->update($data);

        return redirect()
            ->route('dichvu.index')
            ->with('success', 'Cập nhật dịch vụ thành công.');
    }
}
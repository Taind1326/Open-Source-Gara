<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Models\LoaiDichVu;
use Illuminate\Http\Request;

class DichVuPublicController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'tu_khoa' => 'nullable|string|max:100',
            'ma_loai' => 'nullable|integer|exists:LOAIDICHVU,MaLoaiDV',
        ]);

        $tuKhoa = trim($data['tu_khoa'] ?? '');
        $maLoai = $data['ma_loai'] ?? null;

        $loaiDichVus = LoaiDichVu::where(
            'TrangThai',
            'HOAT_DONG'
        )
            ->orderBy('TenLoaiDV')
            ->get();

        $dichVus = DichVu::with('loaiDichVu')
            ->where('TrangThai', 'HOAT_DONG')
            ->whereHas('loaiDichVu', function ($query) {
                $query->where('TrangThai', 'HOAT_DONG');
            })
            ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                $query->where('TenDV', 'like', "%{$tuKhoa}%");
            })
            ->when($maLoai, function ($query) use ($maLoai) {
                $query->where('MaLoaiDV', $maLoai);
            })
            ->orderBy('TenDV')
            ->paginate(9)
            ->withQueryString();

        return view('public.dichvu.index', compact(
            'dichVus',
            'loaiDichVus',
            'tuKhoa',
            'maLoai'
        ));
    }

    public function show($maDV)
    {
        $dichVu = DichVu::with('loaiDichVu')
            ->where('TrangThai', 'HOAT_DONG')
            ->whereHas('loaiDichVu', function ($query) {
                $query->where('TrangThai', 'HOAT_DONG');
            })
            ->findOrFail($maDV);

        $dichVuLienQuan = DichVu::where(
            'TrangThai',
            'HOAT_DONG'
        )
            ->where('MaLoaiDV', $dichVu->MaLoaiDV)
            ->where('MaDV', '!=', $dichVu->MaDV)
            ->orderBy('TenDV')
            ->limit(3)
            ->get();

        return view('public.dichvu.show', compact(
            'dichVu',
            'dichVuLienQuan'
        ));
    }
}
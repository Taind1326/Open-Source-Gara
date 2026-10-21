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
            'sap_xep' => 'nullable|in:mac_dinh,gia_tang,gia_giam,ten',
            'page' => 'nullable|integer|min:1',
        ]);

        $tuKhoa = trim($data['tu_khoa'] ?? '');
        $maLoai = $data['ma_loai'] ?? null;
        $sapXep = $data['sap_xep'] ?? 'mac_dinh';

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
            ->when($sapXep === 'gia_tang', fn ($query) => $query->orderBy('Gia'))
            ->when($sapXep === 'gia_giam', fn ($query) => $query->orderByDesc('Gia'))
            ->when($sapXep === 'ten', fn ($query) => $query->orderBy('TenDV'))
            ->orderBy('MaDV')
            ->paginate(6)
            ->withQueryString();

        if ($dichVus->currentPage() > $dichVus->lastPage()) {
            return redirect()->to($dichVus->url($dichVus->lastPage()));
        }

        return view('public.dichvu.index', compact(
            'dichVus',
            'loaiDichVus',
            'tuKhoa',
            'maLoai',
            'sapXep'
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
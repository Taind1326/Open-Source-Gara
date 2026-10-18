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

        $loaiDichVus = LoaiDichVu::where('HoatDong', true)
            ->orderBy('TenLoaiDV')
            ->get();

        $query = DichVu::with('loaiDichVu')
            ->where('HoatDong', true)
            ->whereHas('loaiDichVu', function ($query) {
                $query->where('HoatDong', true);
            })
            ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                $query->where('TenDV', 'like', "%{$tuKhoa}%");
            })
            ->when($maLoai, function ($query) use ($maLoai) {
                $query->where('MaLoaiDV', $maLoai);
            });

        switch ($sapXep) {
            case 'gia_tang':
                $query->orderBy('Gia');
                break;

            case 'gia_giam':
                $query->orderByDesc('Gia');
                break;

            case 'ten':
                $query->orderBy('TenDV');
                break;
        }

        $dichVus = $query
            ->orderBy('MaDV')
            ->paginate(6)
            ->withQueryString();

        if ($dichVus->currentPage() > $dichVus->lastPage()) {
            return redirect()->to(
                $dichVus->url($dichVus->lastPage())
            );
        }

        return view('public.dichvu.index', compact(
            'dichVus',
            'loaiDichVus',
            'tuKhoa',
            'maLoai',
            'sapXep'
        ));
    }

    public function show(int $maDV)
    {
        $dichVu = DichVu::with('loaiDichVu')
            ->where('HoatDong', true)
            ->whereHas('loaiDichVu', function ($query) {
                $query->where('HoatDong', true);
            })
            ->findOrFail($maDV);

        $dichVuLienQuan = DichVu::with('loaiDichVu')
            ->where('HoatDong', true)
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
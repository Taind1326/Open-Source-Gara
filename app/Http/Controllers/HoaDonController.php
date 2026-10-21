<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HoaDonController extends Controller
{
    public function adminIndex(Request $request)
    {
        $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $hoaDons = $this->invoiceQuery()
            ->orderByRaw(
                "CASE WHEN hd.TrangThai = 'CHUA_THANH_TOAN' THEN 0 ELSE 1 END"
            )
            ->orderByDesc('hd.NgayLap')
            ->orderByDesc('hd.MaHD')
            ->paginate(15)
            ->withQueryString();

        if ($hoaDons->currentPage() > $hoaDons->lastPage()) {
            return redirect()->route('hoadon.admin.index', [
                'page' => $hoaDons->lastPage(),
            ]);
        }

        return view('hoadon.admin-index', compact('hoaDons'));
    }

    public function adminShow($maHD)
    {
        $hoaDon = $this->invoiceQuery()
            ->where('hd.MaHD', $maHD)
            ->first();

        abort_unless($hoaDon, 404);

        $chiTiets = DB::table('CTHOADON')
            ->where('MaHD', $maHD)
            ->orderBy('MaCTHD')
            ->get();

        $thanhToan = DB::table('THANHTOAN')
            ->where('MaHD', $maHD)
            ->where('TrangThai', 'THANH_CONG')
            ->first();

        $yeuCauTienMat = DB::table('THANHTOAN')
            ->where('MaHD', $maHD)
            ->where('PhuongThuc', 'TIEN_MAT')
            ->where('TrangThai', 'CHO_XU_LY')
            ->orderByDesc('MaTT')
            ->first();

        return view('hoadon.admin-show', compact(
            'hoaDon',
            'chiTiets',
            'thanhToan',
            'yeuCauTienMat'
        ));
    }

    private function invoiceQuery()
    {
        return DB::table('HOADON as hd')
            ->join('LICHHEN as lh', 'lh.MaLH', '=', 'hd.MaLH')
            ->join('XE as xe', 'xe.MaXe', '=', 'lh.MaXe')
            ->join('TAIKHOAN as tk', 'tk.MaTK', '=', 'lh.MaTK')
            ->join('PHIEUSUACHUA as sc', function ($join) {
                $join->on('sc.MaPSC', '=', 'hd.MaPSC')
                    ->on('sc.MaLH', '=', 'hd.MaLH');
            })
            ->select([
                'hd.*',
                'xe.BienSo',
                'xe.HangXe',
                'xe.DongXe',
                'tk.HoTen',
                'tk.SoDienThoai',
                'sc.NgayHoanThanh',
                'sc.BaoHanhDen',
            ]);
    }
}
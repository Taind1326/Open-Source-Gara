<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KhachHoaDonController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $hoaDons = $this->queryFor($request)
            ->orderByDesc('hd.NgayLap')
            ->orderByDesc('hd.MaHD')
            ->paginate(10)
            ->withQueryString();

        if ($hoaDons->currentPage() > $hoaDons->lastPage()) {
            return redirect()->route('hoadon.index', [
                'page' => $hoaDons->lastPage(),
            ]);
        }

        return view('public.hoadon.index', compact('hoaDons'));
    }

    public function show(Request $request, $maHD)
    {
        $hoaDon = $this->queryFor($request)
            ->where('hd.MaHD', $maHD)
            ->first();

        abort_unless($hoaDon, 404);

        $chiTiets = DB::table('CTHOADON')
            ->where('MaHD', $hoaDon->MaHD)
            ->orderBy('MaCTHD')
            ->get();

        $thanhToan = DB::table('THANHTOAN')
            ->where('MaHD', $hoaDon->MaHD)
            ->where('TrangThai', 'THANH_CONG')
            ->first();

        return view('public.hoadon.show', compact(
            'hoaDon',
            'chiTiets',
            'thanhToan'
        ));
    }

    public function printInvoice(Request $request, $maHD)
{
    $hoaDon = $this->queryFor($request)
        ->where('hd.MaHD', $maHD)
        ->first();

    abort_unless($hoaDon, 404);

    abort_unless(
        $hoaDon->TrangThai === 'DA_THANH_TOAN'
            && $hoaDon->DaXuLyDiem,
        403,
        'Chỉ được in hóa đơn sau khi thanh toán thành công.'
    );

    $chiTiets = DB::table('CTHOADON')
        ->where('MaHD', $maHD)
        ->orderBy('MaCTHD')
        ->get();

    $thanhToan = DB::table('THANHTOAN')
        ->where('MaHD', $maHD)
        ->where('TrangThai', 'THANH_CONG')
        ->first();

    return response()
        ->view('public.hoadon.print', compact(
            'hoaDon',
            'chiTiets',
            'thanhToan'
        ))
        ->header('Cache-Control', 'private, no-store');
}

    private function queryFor(Request $request)
    {
        return DB::table('HOADON as hd')
            ->join('LICHHEN as lh', 'lh.MaLH', '=', 'hd.MaLH')
            ->join('XE as xe', 'xe.MaXe', '=', 'lh.MaXe')
            ->join('TAIKHOAN as tk', 'tk.MaTK', '=', 'lh.MaTK')
            ->join('PHIEUSUACHUA as sc', function ($join) {
                $join->on('sc.MaPSC', '=', 'hd.MaPSC')
                    ->on('sc.MaLH', '=', 'hd.MaLH');
            })
            ->where('lh.MaTK', $request->user()->MaTK)
            ->select([
                'hd.*',
                'xe.BienSo',
                'xe.HangXe',
                'xe.DongXe',
                'tk.HoTen',
                'tk.SoDienThoai',
                'sc.NgayHoanThanh',
                'sc.BaoHanhDen',
                'sc.GhiChu as GhiChuSuaChua',
            ]);
    }
}
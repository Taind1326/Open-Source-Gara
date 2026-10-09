<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ThongKeController extends Controller
{
    public function index(Request $request)
    {
        $tuNgay = $request->input(
            'tu_ngay',
            now()->startOfMonth()->format('Y-m-d')
        );

        $denNgay = $request->input(
            'den_ngay',
            now()->format('Y-m-d')
        );

        try {
            $ngayBatDau = Carbon::createFromFormat(
                'Y-m-d',
                $tuNgay
            )->startOfDay();

            $ngayKetThuc = Carbon::createFromFormat(
                'Y-m-d',
                $denNgay
            )->endOfDay();
        } catch (\Throwable $e) {
            $tuNgay = now()
                ->startOfMonth()
                ->format('Y-m-d');

            $denNgay = now()->format('Y-m-d');

            $ngayBatDau = now()
                ->startOfMonth()
                ->startOfDay();

            $ngayKetThuc = now()
                ->endOfDay();
        }

        if ($ngayBatDau->gt($ngayKetThuc)) {
            $temp = $ngayBatDau;
            $ngayBatDau = $ngayKetThuc
                ->copy()
                ->startOfDay();

            $ngayKetThuc = $temp
                ->copy()
                ->endOfDay();

            $tuNgay = $ngayBatDau->format('Y-m-d');
            $denNgay = $ngayKetThuc->format('Y-m-d');
        }


        /*
        |--------------------------------------------------------------------------
        | TỔNG SỐ YÊU CẦU
        |--------------------------------------------------------------------------
        */

        $tongYeuCau = DB::table('YEUCAUSUACHUA')
            ->whereBetween(
                'NgayTao',
                [
                    $ngayBatDau,
                    $ngayKetThuc
                ]
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | DOANH THU
        |--------------------------------------------------------------------------
        */

        $doanhThu = DB::table('HOADON')
            ->where(
                'TrangThaiThanhToan',
                'DA_THANH_TOAN'
            )
            ->whereBetween(
                'NgayThanhToan',
                [
                    $ngayBatDau,
                    $ngayKetThuc
                ]
            )
            ->sum('TongThanhToan');


        /*
        |--------------------------------------------------------------------------
        | SỐ HÓA ĐƠN ĐÃ THANH TOÁN
        |--------------------------------------------------------------------------
        */

        $soHoaDonThanhToan = DB::table('HOADON')
            ->where(
                'TrangThaiThanhToan',
                'DA_THANH_TOAN'
            )
            ->whereBetween(
                'NgayThanhToan',
                [
                    $ngayBatDau,
                    $ngayKetThuc
                ]
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | SỐ HÓA ĐƠN CHƯA THANH TOÁN
        |--------------------------------------------------------------------------
        */

        $soHoaDonChuaThanhToan = DB::table('HOADON')
            ->where(
                'TrangThaiThanhToan',
                'CHUA_THANH_TOAN'
            )
            ->whereBetween(
                'NgayLap',
                [
                    $ngayBatDau,
                    $ngayKetThuc
                ]
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | TỔNG LƯỢT DỊCH VỤ
        |--------------------------------------------------------------------------
        */

        $tongLuotDichVu = DB::table('CHITIETSUACHUA')
            ->join(
                'PHIEUSUACHUA',
                'CHITIETSUACHUA.MaPSC',
                '=',
                'PHIEUSUACHUA.MaPSC'
            )
            ->join(
                'YEUCAUSUACHUA',
                'PHIEUSUACHUA.MaYC',
                '=',
                'YEUCAUSUACHUA.MaYC'
            )
            ->whereNotNull(
                'CHITIETSUACHUA.MaDV'
            )
            ->whereBetween(
                'YEUCAUSUACHUA.NgayTao',
                [
                    $ngayBatDau,
                    $ngayKetThuc
                ]
            )
            ->sum(
                'CHITIETSUACHUA.SoLuong'
            );


        /*
        |--------------------------------------------------------------------------
        | TỔNG LƯỢT PHỤ TÙNG
        |--------------------------------------------------------------------------
        */

        $tongLuotPhuTung = DB::table('CHITIETSUACHUA')
            ->join(
                'PHIEUSUACHUA',
                'CHITIETSUACHUA.MaPSC',
                '=',
                'PHIEUSUACHUA.MaPSC'
            )
            ->join(
                'YEUCAUSUACHUA',
                'PHIEUSUACHUA.MaYC',
                '=',
                'YEUCAUSUACHUA.MaYC'
            )
            ->whereNotNull(
                'CHITIETSUACHUA.MaPT'
            )
            ->whereBetween(
                'YEUCAUSUACHUA.NgayTao',
                [
                    $ngayBatDau,
                    $ngayKetThuc
                ]
            )
            ->sum(
                'CHITIETSUACHUA.SoLuong'
            );


        /*
        |--------------------------------------------------------------------------
        | TOP DỊCH VỤ
        |--------------------------------------------------------------------------
        */

        $dichVu = DB::table('CHITIETSUACHUA')
            ->join(
                'DICHVU',
                'CHITIETSUACHUA.MaDV',
                '=',
                'DICHVU.MaDV'
            )
            ->join(
                'PHIEUSUACHUA',
                'CHITIETSUACHUA.MaPSC',
                '=',
                'PHIEUSUACHUA.MaPSC'
            )
            ->join(
                'YEUCAUSUACHUA',
                'PHIEUSUACHUA.MaYC',
                '=',
                'YEUCAUSUACHUA.MaYC'
            )
            ->whereNotNull(
                'CHITIETSUACHUA.MaDV'
            )
            ->whereBetween(
                'YEUCAUSUACHUA.NgayTao',
                [
                    $ngayBatDau,
                    $ngayKetThuc
                ]
            )
            ->select(
                'DICHVU.MaDV',
                'DICHVU.TenDV'
            )
            ->selectRaw(
                'SUM(CHITIETSUACHUA.SoLuong) AS SoLuong'
            )
            ->selectRaw(
                'SUM(CHITIETSUACHUA.SoLuong * CHITIETSUACHUA.DonGia) AS ThanhTien'
            )
            ->groupBy(
                'DICHVU.MaDV',
                'DICHVU.TenDV'
            )
            ->orderByDesc(
                'SoLuong'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TOP PHỤ TÙNG
        |--------------------------------------------------------------------------
        */

        $phuTung = DB::table('CHITIETSUACHUA')
            ->join(
                'PHUTUNG',
                'CHITIETSUACHUA.MaPT',
                '=',
                'PHUTUNG.MaPT'
            )
            ->join(
                'PHIEUSUACHUA',
                'CHITIETSUACHUA.MaPSC',
                '=',
                'PHIEUSUACHUA.MaPSC'
            )
            ->join(
                'YEUCAUSUACHUA',
                'PHIEUSUACHUA.MaYC',
                '=',
                'YEUCAUSUACHUA.MaYC'
            )
            ->whereNotNull(
                'CHITIETSUACHUA.MaPT'
            )
            ->whereBetween(
                'YEUCAUSUACHUA.NgayTao',
                [
                    $ngayBatDau,
                    $ngayKetThuc
                ]
            )
            ->select(
                'PHUTUNG.MaPT',
                'PHUTUNG.TenPT'
            )
            ->selectRaw(
                'SUM(CHITIETSUACHUA.SoLuong) AS SoLuong'
            )
            ->selectRaw(
                'SUM(CHITIETSUACHUA.SoLuong * CHITIETSUACHUA.DonGia) AS ThanhTien'
            )
            ->groupBy(
                'PHUTUNG.MaPT',
                'PHUTUNG.TenPT'
            )
            ->orderByDesc(
                'SoLuong'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | DOANH THU THEO NGÀY
        |--------------------------------------------------------------------------
        */

        $doanhThuTheoNgay = DB::table('HOADON')
            ->where(
                'TrangThaiThanhToan',
                'DA_THANH_TOAN'
            )
            ->whereBetween(
                'NgayThanhToan',
                [
                    $ngayBatDau,
                    $ngayKetThuc
                ]
            )
            ->selectRaw(
                'DATE(NgayThanhToan) AS Ngay'
            )
            ->selectRaw(
                'SUM(TongThanhToan) AS DoanhThu'
            )
            ->groupByRaw(
                'DATE(NgayThanhToan)'
            )
            ->orderBy(
                'Ngay'
            )
            ->get();


        return view(
            'thongke.index',
            compact(
                'tuNgay',
                'denNgay',
                'tongYeuCau',
                'doanhThu',
                'soHoaDonThanhToan',
                'soHoaDonChuaThanhToan',
                'tongLuotDichVu',
                'tongLuotPhuTung',
                'dichVu',
                'phuTung',
                'doanhThuTheoNgay'
            )
        );
    }
}
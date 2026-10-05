<?php

namespace App\Http\Controllers;

use App\Models\HoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HoaDonController extends Controller
{
    private function layMaTK(Request $request)
    {
        if (Auth::check()) {
            return (int) Auth::id();
        }

        if (app()->environment('local') && $request->filled('maTK')) {
            return (int) $request->input('maTK');
        }

        return null;
    }

    public function adminIndex()
    {
        $danhSach = DB::table('HOADON')
            ->join(
                'PHIEUSUACHUA',
                'HOADON.MaPSC',
                '=',
                'PHIEUSUACHUA.MaPSC'
            )
            ->join(
                'YEUCAUSUACHUA',
                'PHIEUSUACHUA.MaYC',
                '=',
                'YEUCAUSUACHUA.MaYC'
            )
            ->join(
                'XE',
                'YEUCAUSUACHUA.MaXe',
                '=',
                'XE.MaXe'
            )
            ->join(
                'TAIKHOAN',
                'XE.MaTK',
                '=',
                'TAIKHOAN.MaTK'
            )
            ->select(
                'HOADON.*',
                'PHIEUSUACHUA.MaYC',
                'XE.MaTK',
                'XE.BienSo',
                'TAIKHOAN.HoTen'
            )
            ->orderByDesc('HOADON.NgayLap')
            ->get();

        $phieuChuaLap = DB::table('PHIEUSUACHUA')
            ->join(
                'YEUCAUSUACHUA',
                'PHIEUSUACHUA.MaYC',
                '=',
                'YEUCAUSUACHUA.MaYC'
            )
            ->join(
                'XE',
                'YEUCAUSUACHUA.MaXe',
                '=',
                'XE.MaXe'
            )
            ->join(
                'TAIKHOAN',
                'XE.MaTK',
                '=',
                'TAIKHOAN.MaTK'
            )
            ->leftJoin(
                'HOADON',
                'PHIEUSUACHUA.MaPSC',
                '=',
                'HOADON.MaPSC'
            )
            ->where(
                'PHIEUSUACHUA.TrangThai',
                'HOAN_THANH_KY_THUAT'
            )
            ->whereNull('HOADON.MaHD')
            ->select(
                'PHIEUSUACHUA.MaPSC',
                'PHIEUSUACHUA.MaYC',
                'PHIEUSUACHUA.NgayHoanThanh',
                'XE.BienSo',
                'TAIKHOAN.HoTen'
            )
            ->orderByDesc('PHIEUSUACHUA.NgayHoanThanh')
            ->get();

        return view(
            'hoadon.admin-index',
            compact(
                'danhSach',
                'phieuChuaLap'
            )
        );
    }

    public function taoHoaDon($maPSC)
    {
        $phieu = DB::table('PHIEUSUACHUA')
            ->where(
                'MaPSC',
                $maPSC
            )
            ->first();

        if (!$phieu) {
            return back()->withErrors([
                'error' => 'Không tìm thấy phiếu sửa chữa.'
            ]);
        }

        if (
            $phieu->TrangThai
            !== 'HOAN_THANH_KY_THUAT'
        ) {
            return back()->withErrors([
                'error' =>
                    'Phiếu sửa chữa chưa hoàn thành kỹ thuật.'
            ]);
        }

        $daCoHoaDon = HoaDon::where(
            'MaPSC',
            $maPSC
        )->exists();

        if ($daCoHoaDon) {
            return back()->withErrors([
                'error' =>
                    'Phiếu sửa chữa này đã có hóa đơn.'
            ]);
        }

        $chiTiet = DB::table('CHITIETSUACHUA')
            ->leftJoin(
                'DICHVU',
                'CHITIETSUACHUA.MaDV',
                '=',
                'DICHVU.MaDV'
            )
            ->leftJoin(
                'PHUTUNG',
                'CHITIETSUACHUA.MaPT',
                '=',
                'PHUTUNG.MaPT'
            )
            ->where(
                'CHITIETSUACHUA.MaPSC',
                $maPSC
            )
            ->select(
                'CHITIETSUACHUA.*',
                'DICHVU.TenDV',
                'PHUTUNG.TenPT'
            )
            ->get();

        if ($chiTiet->isEmpty()) {
            return back()->withErrors([
                'error' =>
                    'Phiếu sửa chữa chưa có dịch vụ hoặc phụ tùng thực tế.'
            ]);
        }

        $tongTien = 0;

        foreach ($chiTiet as $item) {
            $tongTien +=
                $item->SoLuong *
                $item->DonGia;
        }

        HoaDon::create([
            'MaPSC' => $maPSC,
            'TongTienGoc' => $tongTien,
            'DiemDaDung' => 0,
            'PhanTramGiam' => 0,
            'SoTienGiam' => 0,
            'TongThanhToan' => $tongTien,
            'TrangThaiThanhToan' =>
                'CHUA_THANH_TOAN',
        ]);

        return redirect()
            ->route(
                'hoadon.admin.index'
            )
            ->with(
                'success',
                'Lập hóa đơn thành công.'
            );
    }

    public function index(Request $request)
    {
        $maTK = $this->layMaTK($request);

        if (!$maTK) {
            return view(
                'hoadon.index',
                [
                    'danhSach' => collect(),
                    'thieuTaiKhoan' => true,
                ]
            );
        }

        $danhSach = DB::table('HOADON')
            ->join(
                'PHIEUSUACHUA',
                'HOADON.MaPSC',
                '=',
                'PHIEUSUACHUA.MaPSC'
            )
            ->join(
                'YEUCAUSUACHUA',
                'PHIEUSUACHUA.MaYC',
                '=',
                'YEUCAUSUACHUA.MaYC'
            )
            ->join(
                'XE',
                'YEUCAUSUACHUA.MaXe',
                '=',
                'XE.MaXe'
            )
            ->where(
                'XE.MaTK',
                $maTK
            )
            ->select(
                'HOADON.*',
                'PHIEUSUACHUA.MaYC',
                'XE.BienSo'
            )
            ->orderByDesc(
                'HOADON.NgayLap'
            )
            ->get();

        return view(
            'hoadon.index',
            [
                'danhSach' => $danhSach,
                'thieuTaiKhoan' => false,
            ]
        );
    }

    public function show(
        Request $request,
        $maHD
    ) {
        $maTK = $this->layMaTK($request);

        if (!$maTK) {
            return back()->withErrors([
                'error' =>
                    'Không xác định được tài khoản khách hàng.'
            ]);
        }

        $hoaDon = DB::table('HOADON')
            ->join(
                'PHIEUSUACHUA',
                'HOADON.MaPSC',
                '=',
                'PHIEUSUACHUA.MaPSC'
            )
            ->join(
                'YEUCAUSUACHUA',
                'PHIEUSUACHUA.MaYC',
                '=',
                'YEUCAUSUACHUA.MaYC'
            )
            ->join(
                'XE',
                'YEUCAUSUACHUA.MaXe',
                '=',
                'XE.MaXe'
            )
            ->join(
                'TAIKHOAN',
                'XE.MaTK',
                '=',
                'TAIKHOAN.MaTK'
            )
            ->where(
                'HOADON.MaHD',
                $maHD
            )
            ->where(
                'XE.MaTK',
                $maTK
            )
            ->select(
                'HOADON.*',
                'PHIEUSUACHUA.MaYC',
                'PHIEUSUACHUA.MaPSC',
                'PHIEUSUACHUA.GhiChu',
                'XE.BienSo',
                'XE.HangXe',
                'XE.DongXe',
                'TAIKHOAN.MaTK',
                'TAIKHOAN.HoTen',
                'TAIKHOAN.DiemTichLuy'
            )
            ->first();

        if (!$hoaDon) {
            return back()->withErrors([
                'error' =>
                    'Không tìm thấy hóa đơn.'
            ]);
        }

        $chiTiet = DB::table('CHITIETSUACHUA')
            ->leftJoin(
                'DICHVU',
                'CHITIETSUACHUA.MaDV',
                '=',
                'DICHVU.MaDV'
            )
            ->leftJoin(
                'PHUTUNG',
                'CHITIETSUACHUA.MaPT',
                '=',
                'PHUTUNG.MaPT'
            )
            ->where(
                'CHITIETSUACHUA.MaPSC',
                $hoaDon->MaPSC
            )
            ->select(
                'CHITIETSUACHUA.*',
                'DICHVU.TenDV',
                'PHUTUNG.TenPT'
            )
            ->get();

        return view(
            'hoadon.show',
            compact(
                'hoaDon',
                'chiTiet'
            )
        );
    }

    public function apDungDiem(
        Request $request,
        $maHD
    ) {
        $request->validate(
            [
                'diem' =>
                    'required|integer|in:500,1000',
            ],
            [
                'diem.required' =>
                    'Vui lòng chọn số điểm sử dụng.',
                'diem.integer' =>
                    'Số điểm không hợp lệ.',
                'diem.in' =>
                    'Chỉ được sử dụng 500 hoặc 1000 điểm.',
            ]
        );

        $maTK =
            $this->layMaTK($request);

        if (!$maTK) {
            return back()->withErrors([
                'error' =>
                    'Không xác định được tài khoản khách hàng.'
            ]);
        }

        try {

            DB::transaction(
                function () use (
                    $maHD,
                    $maTK,
                    $request
                ) {

                    $hoaDon = DB::table('HOADON')
                        ->join(
                            'PHIEUSUACHUA',
                            'HOADON.MaPSC',
                            '=',
                            'PHIEUSUACHUA.MaPSC'
                        )
                        ->join(
                            'YEUCAUSUACHUA',
                            'PHIEUSUACHUA.MaYC',
                            '=',
                            'YEUCAUSUACHUA.MaYC'
                        )
                        ->join(
                            'XE',
                            'YEUCAUSUACHUA.MaXe',
                            '=',
                            'XE.MaXe'
                        )
                        ->where(
                            'HOADON.MaHD',
                            $maHD
                        )
                        ->where(
                            'XE.MaTK',
                            $maTK
                        )
                        ->select(
                            'HOADON.*'
                        )
                        ->lockForUpdate()
                        ->first();

                    if (!$hoaDon) {
                        throw new \Exception(
                            'Không tìm thấy hóa đơn.'
                        );
                    }

                    if (
                        $hoaDon->TrangThaiThanhToan
                        === 'DA_THANH_TOAN'
                    ) {
                        throw new \Exception(
                            'Hóa đơn đã được thanh toán.'
                        );
                    }

                    if (
                        $hoaDon->DiemDaDung > 0
                    ) {
                        throw new \Exception(
                            'Hóa đơn này đã sử dụng điểm.'
                        );
                    }

                    $taiKhoan =
                        DB::table('TAIKHOAN')
                            ->where(
                                'MaTK',
                                $maTK
                            )
                            ->lockForUpdate()
                            ->first();

                    if (!$taiKhoan) {
                        throw new \Exception(
                            'Không tìm thấy tài khoản khách hàng.'
                        );
                    }

                    $diem =
                        (int) $request
                            ->input('diem');

                    if (
                        $taiKhoan->DiemTichLuy
                        < $diem
                    ) {
                        throw new \Exception(
                            'Số điểm tích lũy không đủ để sử dụng.'
                        );
                    }

                    $phanTramGiam =
                        $diem === 1000
                            ? 15
                            : 10;

                    $soTienGiam =
                        $hoaDon->TongTienGoc
                        * $phanTramGiam
                        / 100;

                    $tongThanhToan =
                        $hoaDon->TongTienGoc
                        - $soTienGiam;

                    DB::table('HOADON')
                        ->where(
                            'MaHD',
                            $maHD
                        )
                        ->update([
                            'DiemDaDung' =>
                                $diem,
                            'PhanTramGiam' =>
                                $phanTramGiam,
                            'SoTienGiam' =>
                                $soTienGiam,
                            'TongThanhToan' =>
                                $tongThanhToan,
                        ]);
                }
            );

            return back()->with(
                'success',
                'Áp dụng điểm thành công. Điểm sẽ được trừ khi thanh toán.'
            );

        } catch (\Throwable $e) {

            return back()->withErrors([
                'error' =>
                    $e->getMessage()
            ]);
        }
    }

    public function boDiem(
        Request $request,
        $maHD
    ) {
        $maTK =
            $this->layMaTK($request);

        if (!$maTK) {
            return back()->withErrors([
                'error' =>
                    'Không xác định được tài khoản khách hàng.'
            ]);
        }

        try {

            DB::transaction(
                function () use (
                    $maHD,
                    $maTK
                ) {

                    $hoaDon = DB::table('HOADON')
                        ->join(
                            'PHIEUSUACHUA',
                            'HOADON.MaPSC',
                            '=',
                            'PHIEUSUACHUA.MaPSC'
                        )
                        ->join(
                            'YEUCAUSUACHUA',
                            'PHIEUSUACHUA.MaYC',
                            '=',
                            'YEUCAUSUACHUA.MaYC'
                        )
                        ->join(
                            'XE',
                            'YEUCAUSUACHUA.MaXe',
                            '=',
                            'XE.MaXe'
                        )
                        ->where(
                            'HOADON.MaHD',
                            $maHD
                        )
                        ->where(
                            'XE.MaTK',
                            $maTK
                        )
                        ->select(
                            'HOADON.*'
                        )
                        ->lockForUpdate()
                        ->first();

                    if (!$hoaDon) {
                        throw new \Exception(
                            'Không tìm thấy hóa đơn.'
                        );
                    }

                    if (
                        $hoaDon->TrangThaiThanhToan
                        === 'DA_THANH_TOAN'
                    ) {
                        throw new \Exception(
                            'Hóa đơn đã được thanh toán, không thể bỏ điểm.'
                        );
                    }

                    DB::table('HOADON')
                        ->where(
                            'MaHD',
                            $maHD
                        )
                        ->update([
                            'DiemDaDung' =>
                                0,
                            'PhanTramGiam' =>
                                0,
                            'SoTienGiam' =>
                                0,
                            'TongThanhToan' =>
                                $hoaDon->TongTienGoc,
                        ]);
                }
            );

            return back()->with(
                'success',
                'Đã bỏ sử dụng điểm. Hóa đơn trở về giá gốc.'
            );

        } catch (\Throwable $e) {

            return back()->withErrors([
                'error' =>
                    $e->getMessage()
            ]);
        }
    }

    public function thanhToan(
        Request $request,
        $maHD
    ) {
        $request->validate(
            [
                'PhuongThucThanhToan' =>
                    'required|in:TIEN_MAT,CHUYEN_KHOAN_QR',
            ],
            [
                'PhuongThucThanhToan.required' =>
                    'Vui lòng chọn phương thức thanh toán.',
                'PhuongThucThanhToan.in' =>
                    'Phương thức thanh toán không hợp lệ.',
            ]
        );

        $maTK =
            $this->layMaTK($request);

        if (!$maTK) {
            return back()->withErrors([
                'error' =>
                    'Không xác định được tài khoản khách hàng.'
            ]);
        }

        try {

            $diemMoi =
                DB::transaction(
                    function () use (
                        $maHD,
                        $maTK,
                        $request
                    ) {

                        $hoaDon = DB::table('HOADON')
                            ->join(
                                'PHIEUSUACHUA',
                                'HOADON.MaPSC',
                                '=',
                                'PHIEUSUACHUA.MaPSC'
                            )
                            ->join(
                                'YEUCAUSUACHUA',
                                'PHIEUSUACHUA.MaYC',
                                '=',
                                'YEUCAUSUACHUA.MaYC'
                            )
                            ->join(
                                'XE',
                                'YEUCAUSUACHUA.MaXe',
                                '=',
                                'XE.MaXe'
                            )
                            ->where(
                                'HOADON.MaHD',
                                $maHD
                            )
                            ->where(
                                'XE.MaTK',
                                $maTK
                            )
                            ->select(
                                'HOADON.*'
                            )
                            ->lockForUpdate()
                            ->first();

                        if (!$hoaDon) {
                            throw new \Exception(
                                'Không tìm thấy hóa đơn.'
                            );
                        }

                        if (
                            $hoaDon->TrangThaiThanhToan
                            === 'DA_THANH_TOAN'
                        ) {
                            throw new \Exception(
                                'Hóa đơn đã được thanh toán.'
                            );
                        }

                        $taiKhoan =
                            DB::table('TAIKHOAN')
                                ->where(
                                    'MaTK',
                                    $maTK
                                )
                                ->lockForUpdate()
                                ->first();

                        if (!$taiKhoan) {
                            throw new \Exception(
                                'Không tìm thấy tài khoản khách hàng.'
                            );
                        }

                        $diemDaDung =
                            (int) $hoaDon->DiemDaDung;

                        if (
                            $taiKhoan->DiemTichLuy
                            < $diemDaDung
                        ) {
                            throw new \Exception(
                                'Số điểm hiện tại không đủ để thanh toán hóa đơn.'
                            );
                        }

                        if ($diemDaDung > 0) {
                            DB::table('TAIKHOAN')
                                ->where(
                                    'MaTK',
                                    $maTK
                                )
                                ->decrement(
                                    'DiemTichLuy',
                                    $diemDaDung
                                );
                        }

                        $diemMoi =
                            (int) floor(
                                $hoaDon->TongThanhToan
                                / 10000
                            );

                        DB::table('HOADON')
                            ->where(
                                'MaHD',
                                $maHD
                            )
                            ->update([
                                'PhuongThucThanhToan' =>
                                    $request->input(
                                        'PhuongThucThanhToan'
                                    ),
                                'TrangThaiThanhToan' =>
                                    'DA_THANH_TOAN',
                                'NgayThanhToan' =>
                                    now(),
                            ]);

                        DB::table('TAIKHOAN')
                            ->where(
                                'MaTK',
                                $maTK
                            )
                            ->increment(
                                'DiemTichLuy',
                                $diemMoi
                            );

                        DB::table('PHIEUSUACHUA')
                            ->where(
                                'MaPSC',
                                $hoaDon->MaPSC
                            )
                            ->update([
                                'TrangThai' =>
                                    'HOAN_THANH'
                            ]);

                        return $diemMoi;
                    }
                );

            return redirect()
                ->route(
                    'hoadon.index',
                    app()->environment('local')
                        && !Auth::check()
                        ? ['maTK' => $maTK]
                        : []
                )
                ->with(
                    'success',
                    "Thanh toán thành công. Bạn được cộng {$diemMoi} điểm."
                );

        } catch (\Throwable $e) {

            return back()->withErrors([
                'error' =>
                    $e->getMessage()
            ]);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ThanhToanController extends Controller
{
    private function fail(string $message): never
    {
        throw ValidationException::withMessages([
            'thanh_toan' => $message,
        ]);
    }

    private function invoiceQuery()
    {
        return DB::table('HOADON as hd')
            ->join('PHIEUSUACHUA as psc', 'hd.MaPSC', '=', 'psc.MaPSC')
            ->join('YEUCAUSUACHUA as yc', 'psc.MaYC', '=', 'yc.MaYC')
            ->join('XE as xe', 'yc.MaXe', '=', 'xe.MaXe')
            ->join('TAIKHOAN as tk', 'xe.MaTK', '=', 'tk.MaTK')
            ->select(
                'hd.*',
                'psc.MaYC',
                'psc.TrangThai as TrangThaiSuaChua',
                'xe.MaTK',
                'xe.BienSo',
                'xe.HangXe',
                'xe.DongXe',
                'tk.HoTen',
                'tk.SoDienThoai'
            );
    }

    public function adminShow(int $maHD): View
    {
        $hoaDon = $this->invoiceQuery()
            ->where('hd.MaHD', $maHD)
            ->first();

        abort_if(! $hoaDon, 404);

        $chiTiet = DB::table('CHITIETSUACHUA as ct')
            ->leftJoin('DICHVU as dv', 'ct.MaDV', '=', 'dv.MaDV')
            ->leftJoin('PHUTUNG as pt', 'ct.MaPT', '=', 'pt.MaPT')
            ->where('ct.MaPSC', $hoaDon->MaPSC)
            ->select('ct.*', 'dv.TenDV', 'pt.TenPT')
            ->get();

        return view('hoadon.admin-show', compact('hoaDon', 'chiTiet'));
    }

    public function requestPayment(
        Request $request,
        int $maHD
    ): RedirectResponse {
        $data = $request->validate([
            'PhuongThucThanhToan' => [
                'required',
                'in:TIEN_MAT,CHUYEN_KHOAN_QR',
            ],
        ], [
            'PhuongThucThanhToan.required' => 'Vui lòng chọn phương thức thanh toán.',
            'PhuongThucThanhToan.in' => 'Phương thức thanh toán không hợp lệ.',
        ]);

        DB::transaction(function () use ($request, $maHD, $data) {
            $hoaDon = DB::table('HOADON')
                ->where('MaHD', $maHD)
                ->lockForUpdate()
                ->first();

            abort_if(! $hoaDon, 404);

            $maChuXe = DB::table('PHIEUSUACHUA as psc')
                ->join('YEUCAUSUACHUA as yc', 'psc.MaYC', '=', 'yc.MaYC')
                ->join('XE as xe', 'yc.MaXe', '=', 'xe.MaXe')
                ->where('psc.MaPSC', $hoaDon->MaPSC)
                ->value('xe.MaTK');

            abort_unless(
                (int) $maChuXe === (int) $request->user()->MaTK,
                403
            );

            if ($hoaDon->TrangThaiThanhToan === 'DA_THANH_TOAN') {
                $this->fail('Hóa đơn đã được thanh toán.');
            }

            DB::table('HOADON')
                ->where('MaHD', $maHD)
                ->update([
                    'PhuongThucThanhToan' => $data['PhuongThucThanhToan'],
                ]);
        });

        return back()->with(
            'success',
            'Đã lưu phương thức thanh toán. Gara sẽ xác nhận sau khi nhận tiền.'
        );
    }

    public function confirmPayment(
        Request $request,
        int $maHD
    ): RedirectResponse {
        $data = $request->validate([
            'PhuongThucThanhToan' => [
                'required',
                'in:TIEN_MAT,CHUYEN_KHOAN_QR',
            ],
            'DaNhanTien' => ['required', 'accepted'],
        ], [
            'PhuongThucThanhToan.required' => 'Vui lòng chọn phương thức thực tế.',
            'PhuongThucThanhToan.in' => 'Phương thức thanh toán không hợp lệ.',
            'DaNhanTien.required' => 'Vui lòng xác nhận đã nhận đủ tiền.',
            'DaNhanTien.accepted' => 'Vui lòng xác nhận đã nhận đủ tiền.',
        ]);

        $diemCong = DB::transaction(function () use ($maHD, $data) {
            $hoaDon = DB::table('HOADON')
                ->where('MaHD', $maHD)
                ->lockForUpdate()
                ->first();

            abort_if(! $hoaDon, 404);

            if ($hoaDon->TrangThaiThanhToan === 'DA_THANH_TOAN') {
                $this->fail('Hóa đơn đã được xác nhận thanh toán trước đó.');
            }

            $phieu = DB::table('PHIEUSUACHUA')
                ->where('MaPSC', $hoaDon->MaPSC)
                ->lockForUpdate()
                ->first();

            if (! $phieu || $phieu->TrangThai !== 'HOAN_THANH_KY_THUAT') {
                $this->fail('Phiếu sửa chữa chưa ở trạng thái hoàn thành kỹ thuật.');
            }

            $maChuXe = DB::table('YEUCAUSUACHUA as yc')
                ->join('XE as xe', 'yc.MaXe', '=', 'xe.MaXe')
                ->where('yc.MaYC', $phieu->MaYC)
                ->value('xe.MaTK');

            $taiKhoan = DB::table('TAIKHOAN')
                ->where('MaTK', $maChuXe)
                ->lockForUpdate()
                ->first();

            if (! $taiKhoan) {
                $this->fail('Không tìm thấy tài khoản khách hàng.');
            }

            $diemDung = (int) $hoaDon->DiemDaDung;

            if (! in_array($diemDung, [0, 500, 1000], true)) {
                $this->fail('Số điểm áp dụng trên hóa đơn không hợp lệ.');
            }

            if ((int) $taiKhoan->DiemTichLuy < $diemDung) {
                $this->fail(
                    'Khách không còn đủ điểm. Khách cần bỏ hoặc chọn lại mức điểm trước khi xác nhận.'
                );
            }

            if ((float) $hoaDon->TongThanhToan < 0) {
                $this->fail('Tổng thanh toán không hợp lệ.');
            }

            $diemCong = (int) floor(
                (float) $hoaDon->TongThanhToan / 10000
            );

            DB::table('TAIKHOAN')
                ->where('MaTK', $taiKhoan->MaTK)
                ->update([
                    'DiemTichLuy' => (int) $taiKhoan->DiemTichLuy
                        - $diemDung
                        + $diemCong,
                ]);

            DB::table('HOADON')
                ->where('MaHD', $maHD)
                ->update([
                    'PhuongThucThanhToan' => $data['PhuongThucThanhToan'],
                    'TrangThaiThanhToan' => 'DA_THANH_TOAN',
                    'NgayThanhToan' => now(),
                ]);

            DB::table('PHIEUSUACHUA')
                ->where('MaPSC', $phieu->MaPSC)
                ->update([
                    'TrangThai' => 'HOAN_THANH',
                ]);

            DB::table('YEUCAUSUACHUA')
                ->where('MaYC', $phieu->MaYC)
                ->update([
                    'TrangThai' => 'HOAN_THANH',
                ]);

            DB::table('PHANCONG')
                ->where('MaYC', $phieu->MaYC)
                ->update([
                    'TrangThai' => 'HOAN_THANH',
                ]);

            return $diemCong;
        });

        return redirect()
            ->route('hoadon.admin.show', $maHD)
            ->with(
                'success',
                "Đã xác nhận thanh toán và hoàn thành hồ sơ. Khách được cộng {$diemCong} điểm."
            );
    }
}
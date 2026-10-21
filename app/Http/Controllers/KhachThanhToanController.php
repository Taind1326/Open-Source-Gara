<?php

namespace App\Http\Controllers;

use App\Models\LichHen;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KhachThanhToanController extends Controller
{
    public function choose(Request $request, $maHD)
    {
        $reference = $this->ownedInvoice($request, $maHD);

        $data = $request->validate([
            'PhuongThuc' => [
                'required',
                Rule::in(['TIEN_MAT', 'CHUYEN_KHOAN']),
            ],
        ], [
            'PhuongThuc.required' => 'Vui lòng chọn phương thức thanh toán.',
            'PhuongThuc.in' => 'Phương thức thanh toán không hợp lệ.',
        ]);

        if ($data['PhuongThuc'] === 'CHUYEN_KHOAN') {
            $this->requireDemo();
        }

        DB::transaction(function () use ($reference, $data) {
            $lichHen = LichHen::query()
                ->where('MaLH', $reference->MaLH)
                ->lockForUpdate()
                ->firstOrFail();

            $hoaDon = DB::table('HOADON')
                ->where('MaHD', $reference->MaHD)
                ->where('MaLH', $lichHen->MaLH)
                ->lockForUpdate()
                ->first();

            abort_unless($hoaDon, 404);

            $this->ensureUnpaid($lichHen, $hoaDon);

            // Đơn 0đ sẽ được hoàn tất riêng khi bấm xác nhận.
            if ((int) $hoaDon->TongTien === 0) {
                return;
            }

            $pending = DB::table('THANHTOAN')
                ->where('MaHD', $hoaDon->MaHD)
                ->where('TrangThai', 'CHO_XU_LY')
                ->orderByDesc('MaTT')
                ->lockForUpdate()
                ->get();

            if (
                $pending->count() === 1
                && $pending->first()->PhuongThuc === $data['PhuongThuc']
                && (int) $pending->first()->SoTien === (int) $hoaDon->TongTien
            ) {
                return;
            }

            // Khách đổi phương thức: đóng yêu cầu trước.
            DB::table('THANHTOAN')
                ->where('MaHD', $hoaDon->MaHD)
                ->where('TrangThai', 'CHO_XU_LY')
                ->update([
                    'TrangThai' => 'THAT_BAI',
                    'GhiChu' => 'Khách đã chọn phương án thanh toán khác.',
                ]);

            DB::table('THANHTOAN')->insert([
                'MaHD' => $hoaDon->MaHD,
                'MaGiaoDich' => 'GARAGE-' . Str::uuid(),
                'PhuongThuc' => $data['PhuongThuc'],
                'SoTien' => $hoaDon->TongTien,
                'TrangThai' => 'CHO_XU_LY',
                'NgayTao' => now('Asia/Ho_Chi_Minh'),
                'GhiChu' => $data['PhuongThuc'] === 'TIEN_MAT'
                    ? 'Khách chọn thanh toán tiền mặt tại gara.'
                    : 'Chuyển khoản mô phỏng, không thu tiền thật.',
            ]);
        }, 3);

        if ($data['PhuongThuc'] === 'CHUYEN_KHOAN') {
            return redirect()->route('hoadon.payment.qr', $maHD);
        }

        return redirect()
            ->route('hoadon.show', $maHD)
            ->with(
                'success',
                'Đã chọn thanh toán tiền mặt. Vui lòng thanh toán tại gara.'
            );
    }

    public function qr(Request $request, $maHD)
    {
        $this->requireDemo();

        $hoaDon = $this->ownedInvoice($request, $maHD);

        if ($hoaDon->TrangThai === 'DA_THANH_TOAN') {
            return redirect()->route('hoadon.show', $maHD);
        }

        $giaoDich = DB::table('THANHTOAN')
            ->where('MaHD', $maHD)
            ->where('PhuongThuc', 'CHUYEN_KHOAN')
            ->where('TrangThai', 'CHO_XU_LY')
            ->orderByDesc('MaTT')
            ->first();

        if (!$giaoDich && (int) $hoaDon->TongTien > 0) {
            return redirect()
                ->route('hoadon.show', $maHD)
                ->with('error', 'Vui lòng chọn chuyển khoản trước.');
        }

        $maNoiDung = 'GARAGE-DEMO-HD' . $hoaDon->MaHD;

        return view('public.hoadon.qr', compact(
            'hoaDon',
            'giaoDich',
            'maNoiDung'
        ));
    }

    public function demo(Request $request, $maHD)
    {
        $this->requireDemo();
        $this->ownedInvoice($request, $maHD);

        return $this->complete($request, $maHD, true);
    }

    public function confirmCash(Request $request, $maHD)
    {
        // Route này bắt buộc có middleware role:ADMIN.
        return $this->complete($request, $maHD, false);
    }

    private function complete(Request $request, $maHD, bool $demo)
    {
        $data = $request->validate([
            'MaTT' => ['required', 'integer', 'min:0'],
            'XacNhan' => ['accepted'],
        ], [
            'MaTT.required' => 'Vui lòng tải lại trang thanh toán.',
            'MaTT.integer' => 'Giao dịch không hợp lệ.',
            'MaTT.min' => 'Giao dịch không hợp lệ.',
            'XacNhan.accepted' => $demo
                ? 'Vui lòng xác nhận mô phỏng thanh toán.'
                : 'Vui lòng xác nhận đã nhận đủ tiền mặt.',
        ]);

        $reference = DB::table('HOADON as hd')
            ->join('LICHHEN as lh', 'lh.MaLH', '=', 'hd.MaLH')
            ->where('hd.MaHD', $maHD)
            ->select('hd.MaLH', 'lh.MaTK')
            ->first();

        abort_unless($reference, 404);

        if ($demo) {
            abort_unless(
                (int) $reference->MaTK === (int) $request->user()->MaTK,
                404
            );
        }

        DB::transaction(function () use (
            $request,
            $maHD,
            $reference,
            $data,
            $demo
        ) {
            $taiKhoan = TaiKhoan::query()
                ->where('MaTK', $reference->MaTK)
                ->lockForUpdate()
                ->firstOrFail();

            $lichHen = LichHen::query()
                ->where('MaLH', $reference->MaLH)
                ->where('MaTK', $taiKhoan->MaTK)
                ->lockForUpdate()
                ->firstOrFail();

            $hoaDon = DB::table('HOADON')
                ->where('MaHD', $maHD)
                ->where('MaLH', $lichHen->MaLH)
                ->lockForUpdate()
                ->first();

            abort_unless($hoaDon, 404);

            $this->ensureUnpaid($lichHen, $hoaDon);

            $phieu = DB::table('PHIEUSUACHUA')
                ->where('MaPSC', $hoaDon->MaPSC)
                ->where('MaLH', $lichHen->MaLH)
                ->lockForUpdate()
                ->first();

            if (
                !$phieu
                || !$phieu->NgayHoanThanh
                || !$phieu->DaKiemTraCuoi
            ) {
                $this->fail('Xe chưa hoàn thành sửa chữa và kiểm tra cuối.');
            }

            if (
                DB::table('THANHTOAN')
                    ->where('MaHD', $maHD)
                    ->where('TrangThai', 'THANH_CONG')
                    ->exists()
            ) {
                $this->fail('Hóa đơn đã có giao dịch thành công.');
            }

            $giaoDich = null;

            if ((int) $hoaDon->TongTien > 0) {
                $giaoDich = DB::table('THANHTOAN')
                    ->where('MaTT', $data['MaTT'])
                    ->where('MaHD', $maHD)
                    ->where('TrangThai', 'CHO_XU_LY')
                    ->where(
                        'PhuongThuc',
                        $demo ? 'CHUYEN_KHOAN' : 'TIEN_MAT'
                    )
                    ->lockForUpdate()
                    ->first();

                if (
                    !$giaoDich
                    || (int) $giaoDich->SoTien !== (int) $hoaDon->TongTien
                ) {
                    $this->fail(
                        'Yêu cầu thanh toán đã thay đổi. Vui lòng tải lại trang.'
                    );
                }
            } elseif ((int) $data['MaTT'] !== 0) {
                $this->fail('Giao dịch của hóa đơn 0đ không hợp lệ.');
            }

            $diemGiuDonKhac = (int) DB::table('PHIEUKIEMTRA as kt')
                ->join('LICHHEN as lh', 'lh.MaLH', '=', 'kt.MaLH')
                ->where('lh.MaTK', $taiKhoan->MaTK)
                ->where('lh.MaLH', '<>', $lichHen->MaLH)
                ->where('kt.TrangThaiXacNhan', 'DONG_Y')
                ->where('kt.LoaiUuDai', 'DOI_DIEM')
                ->whereIn('lh.TrangThai', [
                    LichHen::CHO_SUA_CHUA,
                    LichHen::DANG_SUA_CHUA,
                    LichHen::CHO_THANH_TOAN,
                ])
                ->sum('kt.DiemSuDung');

            $diemDung = (int) $hoaDon->DiemSuDung;

            if (
                (int) $taiKhoan->DiemTichLuy
                < $diemDung + $diemGiuDonKhac
            ) {
                $this->fail('Điểm tích lũy không đủ cho ưu đãi đã chọn.');
            }

            $diemMoi = (int) $taiKhoan->DiemTichLuy
                - $diemDung
                + (int) $hoaDon->DiemDuocCong;

            if ($diemMoi > 2147483647) {
                $this->fail('Điểm tích lũy vượt giới hạn lưu trữ.');
            }

            $now = now('Asia/Ho_Chi_Minh');

            if ($giaoDich) {
                DB::table('THANHTOAN')
                    ->where('MaTT', $giaoDich->MaTT)
                    ->update([
                        'TrangThai' => 'THANH_CONG',
                        'NgayThanhToan' => $now,
                        'MaNguoiXacNhan' => $demo
                            ? null
                            : $request->user()->MaTK,
                        'GhiChu' => $demo
                            ? 'DEMO: mô phỏng thành công, không thu tiền thật.'
                            : 'Admin xác nhận đã nhận đủ tiền mặt.',
                    ]);
            }

            DB::table('THANHTOAN')
                ->where('MaHD', $maHD)
                ->where('TrangThai', 'CHO_XU_LY')
                ->update([
                    'TrangThai' => 'THAT_BAI',
                    'GhiChu' => 'Đã đóng vì hóa đơn được thanh toán.',
                ]);

            DB::table('HOADON')
                ->where('MaHD', $maHD)
                ->update([
                    'TrangThai' => 'DA_THANH_TOAN',
                    'DaXuLyDiem' => true,
                ]);

            $taiKhoan->DiemTichLuy = $diemMoi;
            $taiKhoan->save();

            $lichHen->update([
                'TrangThai' => LichHen::HOAN_TAT,
                'NgayHoanTat' => $now,
            ]);
        }, 3);

        return redirect()
            ->route(
                $demo ? 'hoadon.show' : 'hoadon.admin.show',
                $maHD
            )
            ->with(
                'success',
                $demo
                    ? 'Mô phỏng thanh toán thành công. Bạn có thể in hóa đơn.'
                    : 'Đã nhận tiền mặt, cập nhật điểm và hoàn tất đơn.'
            );
    }

    private function ownedInvoice(Request $request, $maHD)
    {
        $hoaDon = DB::table('HOADON as hd')
            ->join('LICHHEN as lh', 'lh.MaLH', '=', 'hd.MaLH')
            ->where('hd.MaHD', $maHD)
            ->where('lh.MaTK', $request->user()->MaTK)
            ->select('hd.*')
            ->first();

        abort_unless($hoaDon, 404);

        return $hoaDon;
    }

    private function ensureUnpaid(LichHen $lichHen, object $hoaDon): void
    {
        if (
            $hoaDon->TrangThai !== 'CHUA_THANH_TOAN'
            || $hoaDon->DaXuLyDiem
        ) {
            $this->fail('Hóa đơn đã thanh toán, không xử lý lần nữa.');
        }

        if ($lichHen->TrangThai !== LichHen::CHO_THANH_TOAN) {
            $this->fail('Lịch chưa ở trạng thái chờ thanh toán.');
        }
    }

    private function requireDemo(): void
    {
        abort_unless(
            app()->environment('local')
                && config('garage.payment_demo'),
            403,
            'Chức năng mô phỏng chỉ hoạt động trong chế độ demo local.'
        );
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages([
            'thanh_toan' => $message,
        ]);
    }
}
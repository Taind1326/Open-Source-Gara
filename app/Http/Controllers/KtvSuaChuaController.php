<?php

namespace App\Http\Controllers;

use App\Models\LichHen;
use App\Models\PhanCong;
use App\Models\PhieuKiemTra;
use App\Models\PhieuSuaChua;
use App\Models\PhuTung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KtvSuaChuaController extends Controller
{
    public function start(Request $request, $maLH)
    {
        DB::transaction(function () use ($request, $maLH) {
            $lichHen = LichHen::query()
                ->where('MaLH', $maLH)
                ->lockForUpdate()
                ->firstOrFail();

            $phanCong = PhanCong::query()
                ->where('MaLH', $lichHen->MaLH)
                ->where('MaKTV', $request->user()->MaTK)
                ->lockForUpdate()
                ->first();

            abort_unless($phanCong, 404);

            if ($lichHen->TrangThai !== LichHen::CHO_SUA_CHUA) {
                $this->fail(
                    'Chỉ bắt đầu sửa khi khách đã đồng ý '
                    . 'và lịch đang ở trạng thái chờ sửa chữa.'
                );
            }

            $phieuKiemTra = PhieuKiemTra::query()
                ->where('MaLH', $lichHen->MaLH)
                ->lockForUpdate()
                ->first();

            if (
                !$phieuKiemTra
                || $phieuKiemTra->TrangThaiXacNhan
                    !== PhieuKiemTra::DONG_Y
            ) {
                $this->fail(
                    'Khách chưa xác nhận đồng ý sửa chữa.'
                );
            }

            $phieuSuaChua = PhieuSuaChua::query()
                ->where('MaLH', $lichHen->MaLH)
                ->where('MaPKT', $phieuKiemTra->MaPKT)
                ->lockForUpdate()
                ->first();

            if (!$phieuSuaChua) {
                $this->fail(
                    'Chưa có phiếu sửa chữa tương ứng '
                    . 'với phiếu kiểm tra đã được khách đồng ý.'
                );
            }

            if (
                $phieuSuaChua->NgayBatDau !== null
                || $phieuSuaChua->NgayHoanThanh !== null
            ) {
                $this->fail(
                    'Phiếu đã bắt đầu hoặc đã hoàn thành. '
                    . 'Vui lòng tải lại trang.'
                );
            }

            $noiDung = $phieuKiemTra->NoiDungDaGui;

            if (
                !is_array($noiDung)
                || !isset($noiDung['HangMuc'])
                || !is_array($noiDung['HangMuc'])
            ) {
                $this->fail(
                    'Phiếu chưa có nội dung xác nhận hợp lệ.'
                );
            }

            $soLuongCan = [];

            foreach ($noiDung['HangMuc'] as $hangMuc) {
                if (!is_array($hangMuc)) {
                    $this->fail(
                        'Nội dung hạng mục trong phiếu không hợp lệ.'
                    );
                }

                if (
                    empty($hangMuc['KTVDeXuat'])
                    || ($hangMuc['MaPT'] ?? null) === null
                ) {
                    continue;
                }

                $maPT = filter_var(
                    $hangMuc['MaPT'],
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1]]
                );

                $soLuong = filter_var(
                    $hangMuc['SoLuong'] ?? null,
                    FILTER_VALIDATE_INT,
                    [
                        'options' => [
                            'min_range' => 1,
                            'max_range' => 2147483647,
                        ],
                    ]
                );

                if ($maPT === false || $soLuong === false) {
                    $this->fail(
                        'Thông tin phụ tùng trong phiếu '
                        . 'đã xác nhận không hợp lệ.'
                    );
                }

                $daCo = $soLuongCan[$maPT] ?? 0;

                if ($soLuong > 2147483647 - $daCo) {
                    $this->fail(
                        'Tổng số lượng phụ tùng vượt giới hạn lưu trữ.'
                    );
                }

                $soLuongCan[$maPT] = $daCo + $soLuong;
            }

            // Khóa phụ tùng theo cùng thứ tự để tránh
            // hai công việc cùng lấy quá lượng hàng còn lại.
            ksort($soLuongCan, SORT_NUMERIC);

            $phuTungs = PhuTung::query()
                ->whereIn('MaPT', array_keys($soLuongCan))
                ->orderBy('MaPT')
                ->lockForUpdate()
                ->get()
                ->keyBy('MaPT');

            $loiTonKho = [];

            foreach ($soLuongCan as $maPT => $soLuong) {
                $phuTung = $phuTungs->get($maPT);

                if (!$phuTung || !$phuTung->HoatDong) {
                    $loiTonKho[] = "Phụ tùng #{$maPT} không còn hoạt động.";
                    continue;
                }

                if ($soLuong > $phuTung->SoLuongTon) {
                    $loiTonKho[] = "{$phuTung->TenPT}: cần {$soLuong}, "
                        . "kho hiện còn {$phuTung->SoLuongTon} "
                        . "{$phuTung->DonViTinh}.";
                }
            }

            // Kiểm tra đủ toàn bộ phụ tùng rồi mới trừ.
            if ($loiTonKho !== []) {
                $this->fail(
                    'Chưa thể bắt đầu sửa vì không đủ phụ tùng. '
                    . implode(' ', $loiTonKho)
                );
            }

            foreach ($soLuongCan as $maPT => $soLuong) {
                $daTru = PhuTung::query()
                    ->where('MaPT', $maPT)
                    ->where('SoLuongTon', '>=', $soLuong)
                    ->decrement('SoLuongTon', $soLuong);

                if ($daTru !== 1) {
                    $this->fail(
                        'Tồn kho đã thay đổi. '
                        . 'Vui lòng tải lại trang và thử lại.'
                    );
                }
            }

            $phieuSuaChua->update([
                'NgayBatDau' => now('Asia/Ho_Chi_Minh'),
            ]);

            $lichHen->update([
                'TrangThai' => LichHen::DANG_SUA_CHUA,
            ]);
        }, 3);

        return redirect()
            ->route('ktv.lichhen.show', $maLH)
            ->with(
                'success',
                'Đã bắt đầu sửa chữa và cập nhật tồn kho phụ tùng.'
            );
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages([
            'sua_chua' => $message,
        ]);
    }
}
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

    public function finish(Request $request, $maLH)
{
    // Kiểm tra quyền trước khi validate.
    LichHen::query()
        ->whereHas('phanCong', function ($query) use ($request) {
            $query->where('MaKTV', $request->user()->MaTK);
        })
        ->findOrFail($maLH);

    if (is_string($request->input('GhiChu'))) {
        $request->merge([
            'GhiChu' => trim($request->input('GhiChu')),
        ]);
    }

    $data = $request->validate([
        'DaKiemTraCuoi' => ['accepted'],
        'GhiChu' => ['nullable', 'string', 'max:2000'],
    ], [
        'DaKiemTraCuoi.accepted' =>
            'Vui lòng xác nhận đã kiểm tra xe lần cuối.',

        'GhiChu.string' =>
            'Ghi chú phải là văn bản.',

        'GhiChu.max' =>
            'Ghi chú không được vượt quá 2.000 ký tự.',
    ]);

    DB::transaction(function () use ($request, $maLH, $data) {
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

        if ($lichHen->TrangThai !== LichHen::DANG_SUA_CHUA) {
            $this->fail(
                'Chỉ hoàn thành sửa chữa khi xe đang được sửa.'
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
                'Chưa có phiếu kiểm tra được khách đồng ý.'
            );
        }

        $phieuSuaChua = PhieuSuaChua::query()
            ->where('MaLH', $lichHen->MaLH)
            ->where('MaPKT', $phieuKiemTra->MaPKT)
            ->lockForUpdate()
            ->first();

        if (
            !$phieuSuaChua
            || !$phieuSuaChua->NgayBatDau
            || $phieuSuaChua->NgayHoanThanh
        ) {
            $this->fail(
                'Phiếu sửa chữa chưa bắt đầu hoặc đã hoàn thành.'
            );
        }

        if (
            DB::table('HOADON')
                ->where('MaLH', $lichHen->MaLH)
                ->exists()
        ) {
            $this->fail(
                'Lịch này đã có hóa đơn. Vui lòng tải lại trang.'
            );
        }

        $noiDung = $phieuKiemTra->NoiDungDaGui;

        if (
            !is_array($noiDung)
            || !isset($noiDung['HangMuc'])
            || !is_array($noiDung['HangMuc'])
        ) {
            $this->fail(
                'Không tìm thấy nội dung khách đã xác nhận.'
            );
        }

        $chiTiets = [];
        $maChiTietDaCo = [];
        $tongTruocGiam = 0;
        $tongGiam = 0;

        foreach ($noiDung['HangMuc'] as $hangMuc) {
            if (!is_array($hangMuc)) {
                $this->fail('Nội dung phiếu xác nhận không hợp lệ.');
            }

            if (empty($hangMuc['KTVDeXuat'])) {
                continue;
            }

            foreach ([
                'MaCTLH',
                'SoLuong',
                'DonGia',
                'TenHangMuc',
                'DonViTinh',
                'TyLeGiam',
                'TienGiam',
                'ThanhTien',
            ] as $field) {
                if (!isset($hangMuc[$field])) {
                    $this->fail(
                        'Phiếu xác nhận thiếu thông tin hạng mục.'
                    );
                }
            }

            $maCTLH = (int) $hangMuc['MaCTLH'];

            if (isset($maChiTietDaCo[$maCTLH])) {
                $this->fail(
                    'Phiếu xác nhận có hạng mục bị trùng.'
                );
            }

            $maChiTietDaCo[$maCTLH] = true;

            $soLuong = (int) $hangMuc['SoLuong'];
            $donGia = (int) $hangMuc['DonGia'];
            $tienGiam = (int) $hangMuc['TienGiam'];

            if (
                $soLuong < 1
                || $soLuong > 2147483647
                || $donGia < 0
                || $donGia > 999999999999
                || (
                    $donGia > 0
                    && $soLuong > intdiv(999999999999999, $donGia)
                )
            ) {
                $this->fail(
                    'Số lượng hoặc đơn giá trong phiếu không hợp lệ.'
                );
            }

            $thanhTienGoc = $soLuong * $donGia;

            if (
                $tienGiam < 0
                || $tienGiam > $thanhTienGoc
                || $thanhTienGoc - $tienGiam
                    !== (int) $hangMuc['ThanhTien']
            ) {
                $this->fail(
                    'Chi phí hạng mục không khớp phiếu đã xác nhận.'
                );
            }

            if (
                $tongTruocGiam
                > 999999999999999 - $thanhTienGoc
            ) {
                $this->fail(
                    'Tổng hóa đơn vượt giới hạn lưu trữ.'
                );
            }

            $tongTruocGiam += $thanhTienGoc;
            $tongGiam += $tienGiam;

            $chiTiets[] = [
                'MaLH' => $lichHen->MaLH,
                'MaCTLH' => $maCTLH,
                'LoaiHangMuc' =>
                    ($hangMuc['MaDV'] ?? null) !== null
                        ? 'DICH_VU'
                        : 'PHU_TUNG',
                'TenHangMuc' => $hangMuc['TenHangMuc'],
                'DonViTinh' => $hangMuc['DonViTinh'],
                'SoLuong' => $soLuong,
                'DonGia' => $donGia,
                'TyLeGiam' => $hangMuc['TyLeGiam'],
                'TienGiam' => $tienGiam,
                'MaCTHDBaoHanh' =>
                    $hangMuc['MaCTHDBaoHanh'] ?? null,
            ];
        }

        if ($chiTiets === []) {
            $this->fail(
                'Phiếu không có hạng mục được khách đồng ý.'
            );
        }

        if (
            $tongTruocGiam !== (int) $phieuKiemTra->TongTruocGiam
            || $tongGiam !== (int) $phieuKiemTra->TienGiam
            || $tongTruocGiam - $tongGiam
                !== (int) $phieuKiemTra->TongSauGiam
        ) {
            $this->fail(
                'Tổng chi phí không khớp phiếu khách đã xác nhận.'
            );
        }

        $soChiTietHopLe = DB::table('CHITIETLICHHEN')
            ->where('MaLH', $lichHen->MaLH)
            ->whereIn('MaCTLH', array_keys($maChiTietDaCo))
            ->count();

        if ($soChiTietHopLe !== count($chiTiets)) {
            $this->fail(
                'Hạng mục xác nhận không thuộc lịch sửa chữa này.'
            );
        }

        $hoanThanhLuc = now('Asia/Ho_Chi_Minh');

        if ($hoanThanhLuc->lt($phieuSuaChua->NgayBatDau)) {
            $this->fail(
                'Thời điểm hoàn thành không được trước lúc bắt đầu.'
            );
        }

        $phieuSuaChua->update([
            'NgayHoanThanh' => $hoanThanhLuc,
            'DaKiemTraCuoi' => true,
            'GhiChu' => ($data['GhiChu'] ?? '') !== ''
                ? $data['GhiChu']
                : null,
            'BaoHanhDen' => $hoanThanhLuc->copy()->addDays(14),
        ]);

        $maHD = DB::table('HOADON')->insertGetId([
            'MaLH' => $lichHen->MaLH,
            'MaPSC' => $phieuSuaChua->MaPSC,
            'NgayLap' => $hoanThanhLuc,
            'LoaiUuDai' => $phieuKiemTra->LoaiUuDai,
            'TyLeGiam' => $phieuKiemTra->TyLeGiam,
            'DiemSuDung' => $phieuKiemTra->DiemSuDung,
            'DiemDuocCong' => 10,
            'DaXuLyDiem' => false,
            'TongTruocGiam' => $tongTruocGiam,
            'GiaTriDuocGiam' => $phieuKiemTra->GiaTriDuocGiam,
            'TienGiam' => $tongGiam,
            'TongTien' => $tongTruocGiam - $tongGiam,
            'TrangThai' => 'CHUA_THANH_TOAN',
        ], 'MaHD');

        foreach ($chiTiets as &$chiTiet) {
            $chiTiet['MaHD'] = $maHD;
        }

        unset($chiTiet);

        // ThanhTien là cột MySQL tự tính, không insert vào cột đó.
        DB::table('CTHOADON')->insert($chiTiets);

        $lichHen->update([
            'TrangThai' => LichHen::CHO_THANH_TOAN,
        ]);

        // Không trừ kho lần nữa.
        // Chưa cộng/trừ điểm và chưa ghi NgayHoanTat của lịch.
    }, 3);

    return redirect()
        ->route('ktv.lichhen.show', $maLH)
        ->with(
            'success',
            'Đã hoàn thành sửa chữa, kiểm tra cuối '
            . 'và tạo hóa đơn chờ thanh toán.'
        );
}

    private function fail(string $message): never
    {
        throw ValidationException::withMessages([
            'sua_chua' => $message,
        ]);
    }
}
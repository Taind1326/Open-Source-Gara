<?php

namespace App\Services;

use App\Models\YeuCauSuaChua;
use App\Models\PhanCong;
use App\Models\TaiKhoan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exceptions\LichHenException;

class LichHenService
{
    // Các khung giờ cố định trong ngày, hoạt động 07:30 - 20:00
    // Điều chỉnh lại nếu quy định khung giờ khác (vd: mỗi 1 tiếng)
    protected array $khungGioHopLe = [
        '07:30-09:00',
        '09:00-10:30',
        '10:30-12:00',
        '13:00-14:30',
        '14:30-16:00',
        '16:00-17:30',
        '17:30-19:00',
        '19:00-20:00',
    ];

    protected array $trangThaiKhongTinh = ['DA_HUY', 'TU_CHOI'];

    public function layDanhSachKhungGio(): array
    {
        return $this->khungGioHopLe;
    }

    public function laKhungGioHopLe(string $khungGio): bool
    {
        return in_array($khungGio, $this->khungGioHopLe, true);
    }

    // Số lịch (chưa hủy/từ chối) đã có trong 1 khung giờ của 1 ngày
    public function demSoLichDaNhan(string $ngay, string $khungGio): int
    {
        return YeuCauSuaChua::where('NgayHen', $ngay)
            ->where('KhungGioHen', $khungGio)
            ->whereNotIn('TrangThai', $this->trangThaiKhongTinh)
            ->count();
    }

    // Tổng số KTV đang hoạt động (sức chứa tối đa của 1 khung giờ)
    public function soKtvDangHoatDong(): int
    {
        return TaiKhoan::where('VaiTro', 'TECHNICIAN')
            ->where('TrangThai', 'HOAT_DONG')
            ->count();
    }

    // Còn chỗ hay FULL
    public function khungGioConCho(string $ngay, string $khungGio): bool
    {
        return $this->demSoLichDaNhan($ngay, $khungGio) < $this->soKtvDangHoatDong();
    }

    // Trả về danh sách khung giờ kèm trạng thái còn chỗ/FULL, để hiển thị lên form
    public function trangThaiTatCaKhungGio(string $ngay): array
    {
        $soKtv = $this->soKtvDangHoatDong();

        return array_map(function (string $khungGio) use ($ngay, $soKtv) {
            $daNhan = $this->demSoLichDaNhan($ngay, $khungGio);
            $conHan = $this->thoiGianKhungGioHopLe($ngay, $khungGio);

            return [
                'khung_gio' => $khungGio,
                'da_nhan'   => $daNhan,
                'suc_chua'  => $soKtv,
                'con_cho'   => $daNhan < $soKtv && $conHan,
                'da_qua_gio' => !$conHan,
            ];
        }, $this->khungGioHopLe);
    }

    // Validate ngày hẹn: không được ở quá khứ
    public function ngayHopLe(string $ngay): bool
    {
        return Carbon::parse($ngay)->startOfDay()->gte(Carbon::today());
    }

    // Kiểm tra kết hợp ngày + khung giờ so với thời điểm hiện tại
    // Nếu là hôm nay, khung giờ đã bắt đầu (hoặc đang diễn ra) thì không cho chọn nữa
    public function thoiGianKhungGioHopLe(string $ngay, string $khungGio): bool
    {
        $ngayHen = Carbon::parse($ngay)->startOfDay();
        $homNay = Carbon::today();

        if ($ngayHen->lt($homNay)) {
            return false; // ngày quá khứ
        }

        if ($ngayHen->gt($homNay)) {
            return true; // ngày tương lai, không cần so giờ
        }

        // Là hôm nay → so giờ bắt đầu khung với giờ hiện tại
        [$gioBatDau] = explode('-', $khungGio);
        $thoiDiemBatDau = Carbon::parse($ngay . ' ' . $gioBatDau);

        return $thoiDiemBatDau->gt(now());
    }

    // Danh sách KTV đang rảnh trong đúng (ngày, khung giờ) — dùng cho Admin phân công
    public function ktvRanh(string $ngay, string $khungGio)
    {
        $maKtvDaBan = PhanCong::whereHas('yeuCau', function ($q) use ($ngay, $khungGio) {
            $q->where('NgayHen', $ngay)
                ->where('KhungGioHen', $khungGio)
                ->whereNotIn('TrangThai', $this->trangThaiKhongTinh);
        })
            ->where('TrangThai', 'DANG_PHU_TRACH')
            ->pluck('MaKTV');

        return TaiKhoan::where('VaiTro', 'TECHNICIAN')
            ->where('TrangThai', 'HOAT_DONG')
            ->whereNotIn('MaTK', $maKtvDaBan)
            ->get();
    }

    // Kiểm tra lại server-side trước khi lưu PhanCong — không tin dropdown phía client
    public function ktvConRanh(int $maKTV, string $ngay, string $khungGio, ?int $boQuaMaYC = null): bool
    {
        $query = PhanCong::where('MaKTV', $maKTV)
            ->where('TrangThai', 'DANG_PHU_TRACH')
            ->whereHas('yeuCau', function ($q) use ($ngay, $khungGio) {
                $q->where('NgayHen', $ngay)
                    ->where('KhungGioHen', $khungGio)
                    ->whereNotIn('TrangThai', $this->trangThaiKhongTinh);
            });

        // Khi Admin sửa lại phân công cũ, loại chính yêu cầu đang sửa ra khỏi kiểm tra
        if ($boQuaMaYC !== null) {
            $query->where('MaYC', '!=', $boQuaMaYC);
        }

        return !$query->exists();
    }

    // Khóa toàn bộ KTV đang hoạt động theo thứ tự cố định → mọi giao dịch đặt lịch/phân công xếp hàng
    protected function khoaKtvDangHoatDong()
    {
        return TaiKhoan::where('VaiTro', 'TECHNICIAN')
            ->where('TrangThai', 'HOAT_DONG')
            ->orderBy('MaTK')
            ->lockForUpdate()
            ->get();
    }

    // User đặt lịch online — kiểm tra FULL và insert nằm chung 1 giao dịch
    public function taoLichHen(array $data, ?string $duongDanAnh = null): YeuCauSuaChua
    {
        return DB::transaction(function () use ($data, $duongDanAnh) {
            $this->khoaKtvDangHoatDong(); // phải khóa TRƯỚC khi đếm

            if (!$this->laKhungGioHopLe($data['KhungGioHen'])) {
                throw new LichHenException('Khung giờ không hợp lệ.');
            }
            if (!$this->thoiGianKhungGioHopLe($data['NgayHen'], $data['KhungGioHen'])) {
                throw new LichHenException('Thời gian đã chọn không còn hợp lệ (đã qua hoặc ở quá khứ).');
            }
            if (!$this->khungGioConCho($data['NgayHen'], $data['KhungGioHen'])) {
                throw new LichHenException('Khung giờ này đã đầy, vui lòng chọn khung khác.');
            }

            $yeuCau = YeuCauSuaChua::create([
                'MaXe'        => $data['MaXe'],
                'NgayHen'     => $data['NgayHen'],
                'KhungGioHen' => $data['KhungGioHen'],
                'MoTa'        => $data['MoTa'] ?? null,
                'HinhAnh'     => $duongDanAnh,
            ]);

            foreach ($data['dich_vu'] ?? [] as $maDV) {
                $yeuCau->chiTietDichVu()->create(['MaDV' => $maDV]);
            }

            return $yeuCau;
        }, 3); // thử lại tối đa 3 lần nếu MySQL báo deadlock
    }

    // Khách đến trực tiếp — cũng phải tuân thủ sức chứa và có KTV rảnh
    public function taoLichTrucTiep(array $data, string $khungGio): YeuCauSuaChua
    {
        return DB::transaction(function () use ($data, $khungGio) {
            $this->khoaKtvDangHoatDong();
            $homNay = now()->toDateString();

            if (!$this->khungGioConCho($homNay, $khungGio)) {
                throw new LichHenException("Khung giờ {$khungGio} đã đủ lịch, hiện không thể nhận thêm xe.");
            }
            if ($this->ktvRanh($homNay, $khungGio)->isEmpty()) {
                throw new LichHenException("Hiện không có KTV rảnh trong khung giờ {$khungGio}.");
            }

            $yeuCau = YeuCauSuaChua::create([
                'MaXe'        => $data['MaXe'],
                'NgayHen'     => $homNay,
                'KhungGioHen' => $khungGio,
                'MoTa'        => $data['MoTa'] ?? null,
                'TrangThai'   => 'CHO_PHAN_CONG',
            ]);

            foreach ($data['dich_vu'] ?? [] as $maDV) {
                $yeuCau->chiTietDichVu()->create(['MaDV' => $maDV]);
            }

            return $yeuCau;
        }, 3);
    }

    // Admin phân công — khóa yêu cầu + KTV, kiểm tra lại bên trong giao dịch
    // $tiepNhanNgay = true (khách tại quầy): phân công xong tự chuyển DA_TIEP_NHAN
    public function phanCongKtv(int $maYC, int $maKTV, bool $tiepNhanNgay = false): TaiKhoan
    {
        return DB::transaction(function () use ($maYC, $maKTV, $tiepNhanNgay) {
            $yeuCau = YeuCauSuaChua::whereKey($maYC)->lockForUpdate()->firstOrFail();

            if (!in_array($yeuCau->TrangThai, ['CHO_PHAN_CONG', 'DA_PHAN_CONG'], true)) {
                throw new LichHenException('Yêu cầu này không ở trạng thái có thể phân công.');
            }
            if ($tiepNhanNgay && !$yeuCau->NgayHen->isToday()) {
                throw new LichHenException('Chỉ tiếp nhận ngay được với yêu cầu của ngày hôm nay.');
            }

            // Khóa đúng KTV được chọn → 2 Admin gán cùng 1 KTV sẽ xếp hàng, người sau bị từ chối
            $ktv = TaiKhoan::where('MaTK', $maKTV)
                ->where('VaiTro', 'TECHNICIAN')
                ->where('TrangThai', 'HOAT_DONG')
                ->lockForUpdate()
                ->first();

            if (!$ktv) {
                throw new LichHenException('KTV không hợp lệ hoặc không hoạt động.');
            }

            if (!$this->ktvConRanh($maKTV, $yeuCau->NgayHen->toDateString(), $yeuCau->KhungGioHen, $maYC)) {
                throw new LichHenException('KTV này đã bận trong khung giờ này, vui lòng chọn KTV khác.');
            }

            PhanCong::updateOrCreate(
                ['MaYC' => $maYC],
                ['MaKTV' => $maKTV, 'TrangThai' => 'DANG_PHU_TRACH']
            );

            $yeuCau->update(['TrangThai' => $tiepNhanNgay ? 'DA_TIEP_NHAN' : 'DA_PHAN_CONG']);

            return $ktv;
        }, 3);
    }
}
?>
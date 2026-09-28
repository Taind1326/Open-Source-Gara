<?php

namespace App\Services;

use App\Models\YeuCauSuaChua;
use App\Models\PhanCong;
use App\Models\TaiKhoan;
use Carbon\Carbon;

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
            return [
                'khung_gio' => $khungGio,
                'da_nhan'   => $daNhan,
                'suc_chua'  => $soKtv,
                'con_cho'   => $daNhan < $soKtv,
            ];
        }, $this->khungGioHopLe);
    }

    // Validate ngày hẹn: không được ở quá khứ
    public function ngayHopLe(string $ngay): bool
    {
        return Carbon::parse($ngay)->startOfDay()->gte(Carbon::today());
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
}
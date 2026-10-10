<?php

namespace Database\Seeders;

use App\Models\TaiKhoan;
use App\Models\Xe;
use Illuminate\Database\Seeder;

class TaiKhoanSeeder extends Seeder
{
    // Mật khẩu mặc định của mọi tài khoản mẫu: 123456
    public function run(): void
    {
        TaiKhoan::updateOrCreate(['SoDienThoai' => '0900000001'], [
            'HoTen' => 'Quản trị viên', 'Email' => 'admin@gara.test',
            'MatKhau' => '123456', 'VaiTro' => TaiKhoan::VAI_TRO_ADMIN, 'TrangThai' => TaiKhoan::HOAT_DONG,
        ]);

        foreach ([['0900000011', 'Kỹ thuật viên A'], ['0900000012', 'Kỹ thuật viên B']] as [$sdt, $ten]) {
            TaiKhoan::updateOrCreate(['SoDienThoai' => $sdt], [
                'HoTen' => $ten, 'MatKhau' => '123456',
                'VaiTro' => TaiKhoan::VAI_TRO_KTV, 'TrangThai' => TaiKhoan::HOAT_DONG,
            ]);
        }

        $khach = TaiKhoan::updateOrCreate(['SoDienThoai' => '0900000101'], [
            'HoTen' => 'Nguyễn Văn Khách', 'Email' => 'khach@gara.test',
            'MatKhau' => '123456', 'VaiTro' => TaiKhoan::VAI_TRO_USER, 'TrangThai' => TaiKhoan::HOAT_DONG,
        ]);

        // DiemTichLuy không fillable → gán bằng forceFill (demo: đủ 500 điểm để thử giảm giá 10%)
        $khach->forceFill(['DiemTichLuy' => 600])->save();

        Xe::updateOrCreate(['BienSo' => '59A-12345'], [
            'MaTK' => $khach->MaTK, 'HangXe' => 'Toyota', 'DongXe' => 'Vios',
            'NamSanXuat' => 2020, 'MauSac' => 'Trắng', 'TrangThai' => Xe::HOAT_DONG,
        ]);
    }
}

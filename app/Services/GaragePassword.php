<?php

namespace App\Services;

use App\Models\TaiKhoan;

class GaragePassword
{
    public function check(
        TaiKhoan $taiKhoan,
        string $matKhau
    ): bool {
        $matKhauDaLuu = (string) $taiKhoan->getAuthPassword();

        $thongTin = password_get_info($matKhauDaLuu);

        // Tài khoản đăng ký mới có mật khẩu đã hash.
        if ($thongTin['algoName'] !== 'unknown') {
            return password_verify(
                $matKhau,
                $matKhauDaLuu
            );
        }

        // Hỗ trợ mật khẩu thường cho đúng ba tài khoản mẫu.
        $choPhepDemo = app()->environment(['local', 'testing'])
            && config('garage.demo_passwords')
            && in_array(
                $taiKhoan->Email,
                config('garage.demo_accounts', []),
                true
            );

        return $choPhepDemo
            && $matKhauDaLuu !== ''
            && hash_equals($matKhauDaLuu, $matKhau);
    }
}
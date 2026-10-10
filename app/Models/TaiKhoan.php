<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TaiKhoan extends Authenticatable
{
    use HasFactory;

    public const VAI_TRO_USER = 'USER';
    public const VAI_TRO_KTV = 'TECHNICIAN';
    public const VAI_TRO_ADMIN = 'ADMIN';
    public const DANH_SACH_VAI_TRO = [self::VAI_TRO_USER, self::VAI_TRO_KTV, self::VAI_TRO_ADMIN];

    public const HOAT_DONG = 'HOAT_DONG';
    public const KHOA = 'KHOA';

    protected $table = 'TAIKHOAN';
    protected $primaryKey = 'MaTK';
    public $timestamps = false;

    // DiemTichLuy KHÔNG nằm trong fillable: chỉ module hóa đơn (M11) được cộng/trừ điểm
    protected $fillable = ['HoTen', 'SoDienThoai', 'Email', 'MatKhau', 'DiaChi', 'VaiTro', 'TrangThai'];
    protected $hidden = ['MatKhau'];

    protected function casts(): array
    {
        return [
            'MatKhau' => 'hashed',
            'NgayTao' => 'datetime',
            'DiemTichLuy' => 'integer',
        ];
    }

    // ---- Tuỳ biến để Laravel Auth dùng cột MatKhau ----
    public function getAuthPasswordName(): string
    {
        return 'MatKhau';
    }

    public function getAuthPassword(): string
    {
        return $this->MatKhau;
    }

    // Bảng không có cột remember_token → tắt tính năng "ghi nhớ đăng nhập"
    public function getRememberTokenName(): string
    {
        return '';
    }

    // ---- Quan hệ ----
    public function xe()
    {
        return $this->hasMany(Xe::class, 'MaTK', 'MaTK');
    }

    // ---- Tiện ích ----
    public function coVaiTro(string ...$vaiTro): bool
    {
        return in_array($this->VaiTro, $vaiTro, true);
    }

    public function dangHoatDong(): bool
    {
        return $this->TrangThai === self::HOAT_DONG;
    }
}

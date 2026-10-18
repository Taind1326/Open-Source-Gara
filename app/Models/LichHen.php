<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LichHen extends Model
{
    protected $table = 'LICHHEN';

    protected $primaryKey = 'MaLH';

    public $timestamps = false;

    public const CHO_PHAN_CONG = 'CHO_PHAN_CONG';
    public const CHO_NHAN_XE = 'CHO_NHAN_XE';
    public const DANG_KIEM_TRA = 'DANG_KIEM_TRA';
    public const CHO_XAC_NHAN = 'CHO_XAC_NHAN';
    public const CHO_SUA_CHUA = 'CHO_SUA_CHUA';
    public const DANG_SUA_CHUA = 'DANG_SUA_CHUA';
    public const CHO_THANH_TOAN = 'CHO_THANH_TOAN';
    public const HOAN_TAT = 'HOAN_TAT';
    public const DA_HUY = 'DA_HUY';

    public const KHUNG_GIO_HEN = [
        '08:00-09:00',
        '09:00-10:00',
        '10:00-11:00',
        '13:00-14:00',
        '14:00-15:00',
        '15:00-16:00',
    ];

    public const NHAN_TRANG_THAI = [
        self::CHO_PHAN_CONG => 'Chờ phân công',
        self::CHO_NHAN_XE => 'Chờ nhận xe',
        self::DANG_KIEM_TRA => 'Đang kiểm tra',
        self::CHO_XAC_NHAN => 'Chờ khách xác nhận',
        self::CHO_SUA_CHUA => 'Chờ sửa chữa',
        self::DANG_SUA_CHUA => 'Đang sửa chữa',
        self::CHO_THANH_TOAN => 'Chờ thanh toán',
        self::HOAN_TAT => 'Hoàn tất',
        self::DA_HUY => 'Đã hủy',
    ];

    protected $fillable = [
        'MaTK',
        'MaXe',
        'MaHDBaoHanh',
        'NgayHen',
        'KhungGioHen',
        'MoTaBanDau',
        'TrangThai',
        'LyDoHuy',
        'NgayHuy',
        'NgayNhanXe',
        'NgayHoanTat',
    ];

    protected $casts = [
        'MaLH' => 'integer',
        'MaTK' => 'integer',
        'MaXe' => 'integer',
        'MaHDBaoHanh' => 'integer',
        'NgayHen' => 'date',
        'NgayHuy' => 'datetime',
        'NgayNhanXe' => 'datetime',
        'NgayHoanTat' => 'datetime',
        'NgayTao' => 'datetime',
    ];

    public function taiKhoan(): BelongsTo
    {
        return $this->belongsTo(
            TaiKhoan::class,
            'MaTK',
            'MaTK'
        );
    }

    public function xe(): BelongsTo
    {
        return $this->belongsTo(
            Xe::class,
            'MaXe',
            'MaXe'
        );
    }

    public function chiTietLichHens(): HasMany
    {
        return $this->hasMany(
            ChiTietLichHen::class,
            'MaLH',
            'MaLH'
        );
    }

    public function getTenTrangThaiAttribute(): string
    {
        return self::NHAN_TRANG_THAI[$this->TrangThai]
            ?? $this->TrangThai;
    }
}
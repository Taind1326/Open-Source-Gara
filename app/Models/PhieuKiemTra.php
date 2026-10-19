<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhieuKiemTra extends Model
{
    protected $table = 'PHIEUKIEMTRA';

    protected $primaryKey = 'MaPKT';

    public $timestamps = false;

    public const NHAP = 'NHAP';
    public const CHO_XAC_NHAN = 'CHO_XAC_NHAN';
    public const DONG_Y = 'DONG_Y';
    public const TU_CHOI = 'TU_CHOI';

    public const UU_DAI_KHONG = 'KHONG';
    public const UU_DAI_DOI_DIEM = 'DOI_DIEM';
    public const UU_DAI_BAO_HANH = 'BAO_HANH';

    public const NHAN_TRANG_THAI = [
        self::NHAP => 'Bản nháp',
        self::CHO_XAC_NHAN => 'Chờ khách xác nhận',
        self::DONG_Y => 'Khách đã đồng ý',
        self::TU_CHOI => 'Khách đã từ chối',
    ];

    protected $fillable = [
        'MaLH',
        'MaPC',
        'KetLuan',
        'GhiChu',
        'TrangThaiXacNhan',
        'NgayKiemTra',
        'NgayGui',
        'NgayPhanHoi',
        'NoiDungPhanHoi',
        'NoiDungDaGui',
        'LoaiUuDai',
        'TyLeGiam',
        'DiemSuDung',
        'TongTruocGiam',
        'GiaTriDuocGiam',
        'TienGiam',
        'TongSauGiam',
    ];

    protected $casts = [
        'MaPKT' => 'integer',
        'MaLH' => 'integer',
        'MaPC' => 'integer',

        'NgayKiemTra' => 'datetime',
        'NgayGui' => 'datetime',
        'NgayPhanHoi' => 'datetime',
        'NgayTao' => 'datetime',

        'NoiDungDaGui' => 'array',

        'TyLeGiam' => 'decimal:2',
        'DiemSuDung' => 'integer',
        'TongTruocGiam' => 'decimal:0',
        'GiaTriDuocGiam' => 'decimal:0',
        'TienGiam' => 'decimal:0',
        'TongSauGiam' => 'decimal:0',
    ];

    public function lichHen(): BelongsTo
    {
        return $this->belongsTo(
            LichHen::class,
            'MaLH',
            'MaLH'
        );
    }

    public function phanCong(): BelongsTo
    {
        return $this->belongsTo(
            PhanCong::class,
            'MaPC',
            'MaPC'
        );
    }

    public function getTenTrangThaiAttribute(): string
    {
        return self::NHAN_TRANG_THAI[$this->TrangThaiXacNhan]
            ?? $this->TrangThaiXacNhan;
    }
}
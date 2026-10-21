<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChiTietLichHen extends Model
{
    protected $table = 'CHITIETLICHHEN';

    protected $primaryKey = 'MaCTLH';

    public $timestamps = false;

    protected $fillable = [
        'MaLH',
        'MaDV',
        'MaPT',
        'MaCTHDBaoHanh',
        'KhachDaChon',
        'KTVDeXuat',
        'SoLuong',
        'DonGia',
        'GhiChu',
    ];

    protected $casts = [
        'MaCTLH' => 'integer',
        'MaLH' => 'integer',
        'MaDV' => 'integer',
        'MaPT' => 'integer',
        'MaCTHDBaoHanh' => 'integer',
        'KhachDaChon' => 'boolean',
        'KTVDeXuat' => 'boolean',
        'SoLuong' => 'integer',
        'DonGia' => 'decimal:0',
    ];

    public function lichHen(): BelongsTo
    {
        return $this->belongsTo(
            LichHen::class,
            'MaLH',
            'MaLH'
        );
    }

    public function dichVu(): BelongsTo
    {
        return $this->belongsTo(
            DichVu::class,
            'MaDV',
            'MaDV'
        );
    }

    public function phuTung(): BelongsTo
    {
        return $this->belongsTo(
            PhuTung::class,
            'MaPT',
            'MaPT'
        );
    }
}
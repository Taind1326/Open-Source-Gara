<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    protected $table = 'HOADON';
    protected $primaryKey = 'MaHD';
    public $timestamps = false;

    protected $fillable = [
        'MaPSC',
        'TongTienGoc',
        'DiemDaDung',
        'PhanTramGiam',
        'SoTienGiam',
        'TongThanhToan',
        'PhuongThucThanhToan',
        'TrangThaiThanhToan',
        'NgayLap',
        'NgayThanhToan',
    ];

    protected $casts = [
        'TongTienGoc' => 'decimal:2',
        'DiemDaDung' => 'integer',
        'PhanTramGiam' => 'decimal:2',
        'SoTienGiam' => 'decimal:2',
        'TongThanhToan' => 'decimal:2',
        'NgayLap' => 'datetime',
        'NgayThanhToan' => 'datetime',
    ];

    public function phieuSuaChua()
    {
        return $this->belongsTo(
            PhieuSuaChua::class,
            'MaPSC',
            'MaPSC'
        );
    }
}
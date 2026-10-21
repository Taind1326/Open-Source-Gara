<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HoaDon extends Model
{
    protected $table = 'HOADON';

    protected $primaryKey = 'MaHD';

    public $timestamps = false;

    protected $fillable = [
        'MaLH',
        'MaPSC',
        'NgayLap',
        'LoaiUuDai',
        'TyLeGiam',
        'DiemSuDung',
        'DiemDuocCong',
        'DaXuLyDiem',
        'TongTruocGiam',
        'GiaTriDuocGiam',
        'TienGiam',
        'TongTien',
        'TrangThai',
    ];

    protected function casts(): array
    {
        return [
            'MaHD' => 'integer',
            'MaLH' => 'integer',
            'MaPSC' => 'integer',

            'NgayLap' => 'datetime',

            'TyLeGiam' => 'decimal:2',
            'DiemSuDung' => 'integer',
            'DiemDuocCong' => 'integer',
            'DaXuLyDiem' => 'boolean',

            'TongTruocGiam' => 'decimal:0',
            'GiaTriDuocGiam' => 'decimal:0',
            'TienGiam' => 'decimal:0',
            'TongTien' => 'decimal:0',
        ];
    }

    public function lichHen(): BelongsTo
    {
        return $this->belongsTo(
            LichHen::class,
            'MaLH',
            'MaLH'
        );
    }

    public function phieuSuaChua(): BelongsTo
    {
        return $this->belongsTo(
            PhieuSuaChua::class,
            'MaPSC',
            'MaPSC'
        );
    }
}
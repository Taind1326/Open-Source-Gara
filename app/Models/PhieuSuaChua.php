<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PhieuSuaChua extends Model
{
    protected $table = 'PHIEUSUACHUA';

    protected $primaryKey = 'MaPSC';

    public $timestamps = false;

    protected $fillable = [
        'MaLH',
        'MaPKT',
        'NgayBatDau',
        'NgayHoanThanh',
        'DaKiemTraCuoi',
        'GhiChu',
        'BaoHanhDen',
    ];

    protected $casts = [
        'MaPSC' => 'integer',
        'MaLH' => 'integer',
        'MaPKT' => 'integer',
        'NgayBatDau' => 'datetime',
        'NgayHoanThanh' => 'datetime',
        'DaKiemTraCuoi' => 'boolean',
        'BaoHanhDen' => 'datetime',
    ];

    public function lichHen(): BelongsTo
    {
        return $this->belongsTo(
            LichHen::class,
            'MaLH',
            'MaLH'
        );
    }

    public function phieuKiemTra(): BelongsTo
    {
        return $this->belongsTo(
            PhieuKiemTra::class,
            'MaPKT',
            'MaPKT'
        );
    }

    public function hoaDon(): HasOne
    {
        return $this->hasOne(
            HoaDon::class,
            'MaPSC',
            'MaPSC'
        );
    }
}
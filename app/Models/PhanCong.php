<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhanCong extends Model
{
    protected $table = 'PHANCONG';

    protected $primaryKey = 'MaPC';

    public $timestamps = false;

    protected $fillable = [
        'MaLH',
        'MaKTV',
        'MaAdmin',
        'NgayPhanCong',
        'GhiChu',
    ];

    protected $casts = [
        'MaPC' => 'integer',
        'MaLH' => 'integer',
        'MaKTV' => 'integer',
        'MaAdmin' => 'integer',
        'NgayPhanCong' => 'datetime',
    ];

    public function lichHen(): BelongsTo
    {
        return $this->belongsTo(
            LichHen::class,
            'MaLH',
            'MaLH'
        );
    }

    public function ktv(): BelongsTo
    {
        return $this->belongsTo(
            TaiKhoan::class,
            'MaKTV',
            'MaTK'
        );
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(
            TaiKhoan::class,
            'MaAdmin',
            'MaTK'
        );
    }
}
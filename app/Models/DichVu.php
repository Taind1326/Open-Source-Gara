<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DichVu extends Model
{
    protected $table = 'DICHVU';

    protected $primaryKey = 'MaDV';

    public $timestamps = false;

    protected $fillable = [
        'MaLoaiDV',
        'TenDV',
        'MoTa',
        'GiaTienCong',
        'HinhAnh',
        'HoatDong',
    ];

    protected function casts(): array
    {
        return [
            'MaLoaiDV' => 'integer',
            'GiaTienCong' => 'decimal:0',
            'HoatDong' => 'boolean',
        ];
    }

    public function loaiDichVu(): BelongsTo
    {
        return $this->belongsTo(
            LoaiDichVu::class,
            'MaLoaiDV',
            'MaLoaiDV'
        );
    }
}
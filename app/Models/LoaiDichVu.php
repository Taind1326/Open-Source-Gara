<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoaiDichVu extends Model
{
    protected $table = 'LOAIDICHVU';

    protected $primaryKey = 'MaLoaiDV';

    public $timestamps = false;

    protected $fillable = [
        'TenLoaiDV',
        'MoTa',
        'HoatDong',
    ];

    protected function casts(): array
    {
        return [
            'HoatDong' => 'boolean',
        ];
    }

    public function dichVus(): HasMany
    {
        return $this->hasMany(
            DichVu::class,
            'MaLoaiDV',
            'MaLoaiDV'
        );
    }
}
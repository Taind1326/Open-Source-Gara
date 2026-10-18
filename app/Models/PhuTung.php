<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhuTung extends Model
{
    protected $table = 'PHUTUNG';

    protected $primaryKey = 'MaPT';

    public $timestamps = false;

    protected $fillable = [
        'TenPT',
        'DonViTinh',
        'GiaBan',
        'SoLuongTon',
        'HoatDong',
    ];

    protected function casts(): array
    {
        return [
            'GiaBan' => 'decimal:0',
            'SoLuongTon' => 'integer',
            'HoatDong' => 'boolean',
        ];
    }
}
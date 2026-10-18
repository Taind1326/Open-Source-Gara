<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Xe extends Model
{
    protected $table = 'XE';

    protected $primaryKey = 'MaXe';

    public $timestamps = false;

    protected $fillable = [
        'MaTK',
        'BienSo',
        'HangXe',
        'DongXe',
        'NamSX',
        'MauSac',
    ];

    protected function casts(): array
    {
        return [
            'MaTK' => 'integer',
            'NamSX' => 'integer',
        ];
    }

    public function taiKhoan(): BelongsTo
    {
        return $this->belongsTo(
            TaiKhoan::class,
            'MaTK',
            'MaTK'
        );
    }
}
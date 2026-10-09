<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietBaoGia extends Model
{
    protected $table = 'CHITIETBAOGIA';
    protected $primaryKey = 'MaCTBG';
    public $timestamps = false;

    protected $fillable = [
        'MaBG',
        'MaDV',
        'MaPT',
        'SoLuong',
        'DonGia',
    ];

    protected $casts = [
        'SoLuong' => 'integer',
        'DonGia' => 'decimal:2',
    ];

    public function baoGia()
    {
        return $this->belongsTo(
            BaoGia::class,
            'MaBG',
            'MaBG'
        );
    }

    public function dichVu()
    {
        return $this->belongsTo(
            DichVu::class,
            'MaDV',
            'MaDV'
        );
    }

    public function phuTung()
    {
        return $this->belongsTo(
            PhuTung::class,
            'MaPT',
            'MaPT'
        );
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietSuaChua extends Model
{
    protected $table = 'CHITIETSUACHUA';
    protected $primaryKey = 'MaCTSC';
    public $timestamps = false;

    protected $fillable = [
        'MaPSC',
        'MaDV',
        'MaPT',
        'SoLuong',
        'DonGia',
    ];

    protected $casts = [
        'SoLuong' => 'integer',
        'DonGia' => 'decimal:2',
    ];

    public function phieuSuaChua()
    {
        return $this->belongsTo(
            PhieuSuaChua::class,
            'MaPSC',
            'MaPSC'
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuSuaChua extends Model
{
    protected $table = 'PHIEUSUACHUA';
    protected $primaryKey = 'MaPSC';
    public $timestamps = false;

    protected $fillable = [
        'MaYC',
        'MaKTV',
        'NgayBatDau',
        'NgayHoanThanh',
        'TrangThai',
        'GhiChu',
    ];

    protected $casts = [
        'NgayBatDau' => 'datetime',
        'NgayHoanThanh' => 'datetime',
    ];

    public function yeuCau()
    {
        return $this->belongsTo(
            YeuCauSuaChua::class,
            'MaYC',
            'MaYC'
        );
    }

    public function chiTiet()
    {
        return $this->hasMany(
            ChiTietSuaChua::class,
            'MaPSC',
            'MaPSC'
        );
    }

    public function hoaDon()
    {
        return $this->hasOne(
            HoaDon::class,
            'MaPSC',
            'MaPSC'
        );
    }
}
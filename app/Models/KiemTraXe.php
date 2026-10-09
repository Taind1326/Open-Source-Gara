<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KiemTraXe extends Model
{
    protected $table = 'KIEMTRAXE';
    protected $primaryKey = 'MaKT';
    public $timestamps = false;

    protected $fillable = [
        'MaYC',
        'MaKTV',
        'TinhTrang',
        'ChanDoan',
        'GhiChu',
        'HinhAnh',
        'NgayKiemTra',
    ];

    protected $casts = [
        'NgayKiemTra' => 'datetime',
    ];

    public function yeuCauSuaChua()
    {
        return $this->belongsTo(
            YeuCauSuaChua::class,
            'MaYC',
            'MaYC'
        );
    }

    public function deXuatDichVus()
    {
        return $this->hasMany(
            DeXuatDichVu::class,
            'MaKT',
            'MaKT'
        );
    }

    public function deXuatPhuTungs()
    {
        return $this->hasMany(
            DeXuatPhuTung::class,
            'MaKT',
            'MaKT'
        );
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeuCauSuaChua extends Model
{
    protected $table = 'YEUCAUSUACHUA';
    protected $primaryKey = 'MaYC';
    public $timestamps = false;

    protected $fillable = [
        'MaXe',
        'NgayHen',
        'KhungGioHen',
        'MoTa',
        'HinhAnh',
        'TrangThai',
    ];

    protected $casts = [
        'NgayHen' => 'date',
        'NgayTao' => 'datetime',
    ];

    public function xe()
    {
        return $this->belongsTo(
            Xe::class,
            'MaXe',
            'MaXe'
        );
    }

    public function chiTietDichVu()
    {
        return $this->hasMany(
            ChiTietYeuCauDichVu::class,
            'MaYC',
            'MaYC'
        );
    }

    public function phanCong()
    {
        return $this->hasOne(
            PhanCong::class,
            'MaYC',
            'MaYC'
        );
    }

    public function dichVu()
    {
        return $this->belongsToMany(
            DichVu::class,
            'CHITIETYEUCAUDICHVU',
            'MaYC',
            'MaDV'
        );
    }

    public function kiemTraXe()
    {
        return $this->hasOne(
            KiemTraXe::class,
            'MaYC',
            'MaYC'
        );
    }

    public function baoGia()
    {
        return $this->hasOne(
            BaoGia::class,
            'MaYC',
            'MaYC'
        );
    }

    public function phieuSuaChua()
    {
        return $this->hasOne(
            PhieuSuaChua::class,
            'MaYC',
            'MaYC'
        );
    }
}
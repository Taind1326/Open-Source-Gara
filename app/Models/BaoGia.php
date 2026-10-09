<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaoGia extends Model
{
    protected $table = 'BAOGIA';
    protected $primaryKey = 'MaBG';
    public $timestamps = false;

    protected $fillable = [
        'MaYC',
        'TongTien',
        'TrangThai',
        'GhiChu',
        'NgayTao',
    ];

    protected $casts = [
        'TongTien' => 'decimal:2',
        'NgayTao' => 'datetime',
    ];

    public function yeuCauSuaChua()
    {
        return $this->belongsTo(
            YeuCauSuaChua::class,
            'MaYC',
            'MaYC'
        );
    }

    public function chiTietBaoGias()
    {
        return $this->hasMany(
            ChiTietBaoGia::class,
            'MaBG',
            'MaBG'
        );
    }
}
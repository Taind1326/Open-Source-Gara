<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanCong extends Model
{
    protected $table = 'PHANCONG';
    protected $primaryKey = 'MaPC';
    public $timestamps = false;

    protected $fillable = [
        'MaYC',
        'MaKTV',
        'TrangThai',
    ];

    protected $casts = [
        'NgayPhanCong' => 'datetime',
    ];

    public function yeuCau()
    {
        return $this->belongsTo(YeuCauSuaChua::class, 'MaYC', 'MaYC');
    }

    public function ktv()
    {
        return $this->belongsTo(TaiKhoan::class, 'MaKTV', 'MaTK');
    }
}

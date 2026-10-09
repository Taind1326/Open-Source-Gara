<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeXuatPhuTung extends Model
{
    protected $table = 'DEXUATPHUTUNG';
    protected $primaryKey = 'MaDXPT';
    public $timestamps = false;

    protected $fillable = [
        'MaKT',
        'MaPT',
        'SoLuong',
    ];

    public function kiemTraXe()
    {
        return $this->belongsTo(KiemTraXe::class, 'MaKT', 'MaKT');
    }

    public function phuTung()
    {
        return $this->belongsTo(PhuTung::class, 'MaPT', 'MaPT');
    }
}
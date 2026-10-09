<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeXuatDichVu extends Model
{
    protected $table = 'DEXUATDICHVU';
    protected $primaryKey = 'MaDXDV';
    public $timestamps = false;

    protected $fillable = [
        'MaKT',
        'MaDV',
    ];

    public function kiemTraXe()
    {
        return $this->belongsTo(KiemTraXe::class, 'MaKT', 'MaKT');
    }

    public function dichVu()
    {
        return $this->belongsTo(DichVu::class, 'MaDV', 'MaDV');
    }
}
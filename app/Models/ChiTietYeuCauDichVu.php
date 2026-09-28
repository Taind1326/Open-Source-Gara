<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietYeuCauDichVu extends Model
{
    protected $table = 'CHITIETYEUCAUDICHVU';
    protected $primaryKey = 'MaCTYC';
    public $timestamps = false;

    protected $fillable = [
        'MaYC',
        'MaDV',
    ];

    public function yeuCau()
    {
        return $this->belongsTo(YeuCauSuaChua::class, 'MaYC', 'MaYC');
    }

    public function dichVu()
    {
        return $this->belongsTo(DichVu::class, 'MaDV', 'MaDV');
    }
}

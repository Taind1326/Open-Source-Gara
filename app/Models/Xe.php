<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Xe extends Model
{
    protected $table = 'XE';
    protected $primaryKey = 'MaXe';
    public $timestamps = false;

    protected $fillable = [
        'MaTK',
        'BienSo',
        'HangXe',
        'DongXe',
        'NamSanXuat',
    ];

    public function yeuCauSuaChuas()
    {
        return $this->hasMany(
            YeuCauSuaChua::class,
            'MaXe',
            'MaXe'
        );
    }
}
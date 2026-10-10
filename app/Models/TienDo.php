<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TienDo extends Model
{
    protected $table = 'TIENDO';
    protected $primaryKey = 'MaTD';
    public $timestamps = false;

    protected $fillable = [
        'MaPSC',
        'MaKTV',
        'NoiDung',
        'TrangThai',
        'NgayCapNhat',
    ];

    protected $casts = [
        'NgayCapNhat' => 'datetime',
    ];

    public function phieuSuaChua()
    {
        return $this->belongsTo(
            PhieuSuaChua::class,
            'MaPSC',
            'MaPSC'
        );
    }
}
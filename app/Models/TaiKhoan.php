<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TaiKhoan extends Authenticatable
{
    protected $table = 'TAIKHOAN';

    protected $primaryKey = 'MaTK';

    public $timestamps = false;

    protected $authPasswordName = 'MatKhau';

    protected $rememberTokenName = '';

    protected $fillable = [
        'HoTen',
        'Email',
        'SoDienThoai',
        'MatKhau',
        'VaiTro',
        'DiemTichLuy',
        'TrangThai',
    ];

    protected $hidden = [
        'MatKhau',
    ];

    protected function casts(): array
    {
        return [
            'DiemTichLuy' => 'integer',
            'NgayTao' => 'datetime',
        ];
    }

    public function xes(): HasMany
    {
        return $this->hasMany(Xe::class, 'MaTK', 'MaTK');
    }
}

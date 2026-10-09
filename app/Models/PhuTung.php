<?php
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class PhuTung extends Model{
        protected $table = 'PHUTUNG';
        protected $primaryKey = 'MaPT';
        public $timestamps = false;
        protected $fillable = ['TenPT', 'DonViTinh', 'Gia', 'SoLuongTon', 'TrangThai'];
    }
<?php
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class LoaiDichVu extends Model{
        protected $table = 'LOAIDICHVU';
        protected $primaryKey = 'MaLoaiDV';
        public $timestamps = false;
        protected $fillable = ['TenLoaiDV', 'MoTa', 'HinhAnh', 'TrangThai'];

        public function dichVus(){
            return $this->hasMany(DichVu::class, 'MaLoaiDV', 'MaLoaiDV');
        }

    }
<?php
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model;

    class DichVu extends Model{
        protected $table = 'DICHVU';
        protected $primaryKey = 'MaDV';
        public $timetamps = false;
        protected $fillable = ['MaLoaiDV', 'TenDV', 'MoTa', 'Gia', 'HinhAnh', 'TrangThai'];

        public function loaiDichVu(){
            return $this->belongsTo(LoaiDichVu::class, 'MaLoaiDV', 'MaLoaiDV');
        }
    }
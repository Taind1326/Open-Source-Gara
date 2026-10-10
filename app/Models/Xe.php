<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class Xe extends Model
{
    use HasFactory;

    public const HOAT_DONG = 'HOAT_DONG';
    public const NGUNG_SU_DUNG = 'NGUNG_SU_DUNG';

    protected $table = 'XE';
    protected $primaryKey = 'MaXe';
    public $timestamps = false;

    protected $fillable = ['MaTK', 'BienSo', 'HangXe', 'DongXe', 'NamSanXuat', 'MauSac', 'TrangThai'];

    protected $casts = ['NgayTao' => 'datetime'];

    public function taiKhoan()
    {
        return $this->belongsTo(TaiKhoan::class, 'MaTK', 'MaTK');
    }

    public function yeuCauSuaChua()
    {
        return $this->hasMany(YeuCauSuaChua::class, 'MaXe', 'MaXe');
    }

    // Tên cũ trong repo chung (bản Xe.php trước đó) - giữ để code cũ không vỡ
    public function yeuCauSuaChuas()
    {
        return $this->yeuCauSuaChua();
    }

    // Các trạng thái yêu cầu đã kết thúc (khớp enum YEUCAUSUACHUA.TrangThai)
    public const TRANG_THAI_YEU_CAU_KET_THUC = ['HOAN_THANH', 'TU_CHOI', 'DA_HUY'];

    // Xe còn yêu cầu sửa chữa/lịch hẹn chưa kết thúc → không được ngừng sử dụng
    // Có Schema::hasTable để TV1 chạy độc lập khi bảng của TV2 chưa được migrate
    public function coYeuCauChuaHoanTat(): bool
    {
        if (!Schema::hasTable('YEUCAUSUACHUA')) {
            return false;
        }

        return $this->yeuCauSuaChua()
            ->whereNotIn('TrangThai', self::TRANG_THAI_YEU_CAU_KET_THUC)
            ->exists();
    }

    // Quy tắc validate dùng chung cho XeController (khách) và Admin\XeController
    public static function quyTacHopLe(?int $boQuaMaXe = null): array
    {
        return [
            'BienSo'     => ['required', 'string', 'max:20', Rule::unique('XE', 'BienSo')->ignore($boQuaMaXe, 'MaXe')],
            'HangXe'     => 'required|string|max:50',
            'DongXe'     => 'nullable|string|max:50',
            'NamSanXuat' => 'nullable|integer|between:1980,' . (now()->year + 1),
            'MauSac'     => 'nullable|string|max:30',
        ];
    }

    public const THONG_BAO_LOI = ['BienSo.unique' => 'Biển số này đã được đăng ký trong hệ thống.'];

    // Chuẩn hoá biển số: "59-a1 123.45" -> "59-A1123.45" (bỏ khoảng trắng, viết hoa)
    public static function chuanHoaBienSo(string $bienSo): string
    {
        return strtoupper(preg_replace('/\s+/', '', trim($bienSo)));
    }
}

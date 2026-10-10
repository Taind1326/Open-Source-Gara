<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nhóm tạo bảng bằng file QUANLY_GARAGE_MySQL.sql → nếu bảng đã có thì bỏ qua
        if (Schema::hasTable('TAIKHOAN')) {
            return;
        }

        Schema::create('TAIKHOAN', function (Blueprint $table) {
            $table->id('MaTK');
            $table->string('HoTen', 100);
            $table->string('SoDienThoai', 15)->unique();
            $table->string('Email', 150)->nullable()->unique();
            $table->string('MatKhau');
            $table->string('DiaChi', 255)->nullable();
            $table->string('VaiTro', 20)->default('USER');          // USER | TECHNICIAN | ADMIN
            $table->unsignedInteger('DiemTichLuy')->default(0);      // điểm tích lũy (M11 hóa đơn dùng)
            $table->string('TrangThai', 20)->default('HOAT_DONG');  // HOAT_DONG | KHOA
            $table->timestamp('NgayTao')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('TAIKHOAN');
    }
};

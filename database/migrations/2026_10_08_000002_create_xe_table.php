<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nhóm tạo bảng bằng file QUANLY_GARAGE_MySQL.sql → nếu bảng đã có thì bỏ qua
        if (Schema::hasTable('XE')) {
            return;
        }

        Schema::create('XE', function (Blueprint $table) {
            $table->id('MaXe');
            $table->unsignedBigInteger('MaTK');
            $table->string('BienSo', 20)->unique();
            $table->string('HangXe', 50);
            $table->string('DongXe', 50)->nullable();
            $table->unsignedSmallInteger('NamSanXuat')->nullable();
            $table->string('MauSac', 30)->nullable();
            $table->string('TrangThai', 20)->default('HOAT_DONG');  // HOAT_DONG | NGUNG_SU_DUNG
            $table->timestamp('NgayTao')->useCurrent();

            $table->foreign('MaTK')->references('MaTK')->on('TAIKHOAN')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('XE');
    }
};

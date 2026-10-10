<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TaiKhoanSeeder::class);
        // Các thành viên khác thêm seeder của module mình vào đây (DichVuSeeder, ...)
    }
}

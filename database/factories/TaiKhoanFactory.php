<?php

namespace Database\Factories;

use App\Models\TaiKhoan;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TaiKhoan> */
class TaiKhoanFactory extends Factory
{
    protected $model = TaiKhoan::class;

    public function definition(): array
    {
        return [
            'HoTen' => fake()->name(),
            'SoDienThoai' => '09' . fake()->unique()->numerify('########'),
            'Email' => fake()->unique()->safeEmail(),
            'MatKhau' => 'password',
            'DiaChi' => fake()->address(),
            'VaiTro' => TaiKhoan::VAI_TRO_USER,
            'DiemTichLuy' => 0,
            'TrangThai' => TaiKhoan::HOAT_DONG,
        ];
    }

    public function admin(): static
    {
        return $this->state(['VaiTro' => TaiKhoan::VAI_TRO_ADMIN]);
    }

    public function ktv(): static
    {
        return $this->state(['VaiTro' => TaiKhoan::VAI_TRO_KTV]);
    }

    public function coDiem(int $diem): static
    {
        return $this->state(['DiemTichLuy' => $diem]);
    }

    public function biKhoa(): static
    {
        return $this->state(['TrangThai' => TaiKhoan::KHOA]);
    }
}

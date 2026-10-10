<?php

namespace Database\Factories;

use App\Models\TaiKhoan;
use App\Models\Xe;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Xe> */
class XeFactory extends Factory
{
    protected $model = Xe::class;

    public function definition(): array
    {
        return [
            'MaTK' => TaiKhoan::factory(),
            'BienSo' => '59A' . fake()->unique()->numerify('-#####'),
            'HangXe' => fake()->randomElement(['Toyota', 'Honda', 'Hyundai', 'Mazda', 'Kia']),
            'DongXe' => fake()->word(),
            'NamSanXuat' => fake()->numberBetween(2010, 2026),
            'MauSac' => fake()->safeColorName(),
            'TrangThai' => Xe::HOAT_DONG,
        ];
    }
}

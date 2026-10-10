<?php

namespace Tests\Feature;

use App\Models\TaiKhoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class XeDanhMucTest extends TestCase
{
    use RefreshDatabase;

    public function test_khach_chua_dang_nhap_khong_goi_duoc_api(): void
    {
        $this->getJson(route('xe-api.hang'))->assertUnauthorized();
    }

    public function test_ktv_khong_goi_duoc_api(): void
    {
        $this->actingAs(TaiKhoan::factory()->ktv()->create())->getJson(route('xe-api.hang'))->assertForbidden();
    }

    public function test_lay_danh_sach_hang(): void
    {
        $this->actingAs(TaiKhoan::factory()->create())->getJson(route('xe-api.hang'))
            ->assertOk()->assertJsonFragment(['Toyota'])->assertJsonFragment(['Honda']);
    }

    public function test_lay_nam_theo_hang(): void
    {
        $nam = $this->actingAs(TaiKhoan::factory()->create())
            ->getJson(route('xe-api.nam', ['hang' => 'Toyota']))->assertOk()->json();

        $this->assertContains(2020, $nam);
        $this->assertContains(1998, $nam);
        $this->assertGreaterThan($nam[count($nam) - 1], $nam[0]); // mới → cũ
    }

    public function test_lay_dong_theo_hang_va_nam(): void
    {
        $khach = TaiKhoan::factory()->create();

        $this->actingAs($khach)->getJson(route('xe-api.dong', ['hang' => 'Toyota', 'nam' => 2020]))
            ->assertOk()->assertJsonFragment(['Vios'])->assertJsonMissing(['Raize']); // Raize mới bán từ 2022

        $this->actingAs($khach)->getJson(route('xe-api.dong', ['hang' => 'Ford', 'nam' => 2024]))
            ->assertOk()->assertJsonMissing(['Focus']); // Focus ngừng bán 2018
    }

    public function test_hang_khong_ton_tai_tra_ve_mang_rong(): void
    {
        $khach = TaiKhoan::factory()->create();

        $this->actingAs($khach)->getJson(route('xe-api.nam', ['hang' => 'Khong-Co']))->assertOk()->assertExactJson([]);
        $this->actingAs($khach)->getJson(route('xe-api.dong', ['hang' => 'Khong-Co', 'nam' => 2020]))->assertOk()->assertExactJson([]);
    }
}

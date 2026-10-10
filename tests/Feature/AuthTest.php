<?php

namespace Tests\Feature;

use App\Models\TaiKhoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_dang_nhap_dung_thong_tin(): void
    {
        $tk = TaiKhoan::factory()->create(['SoDienThoai' => '0911111111']);

        $this->post(route('login.xu-ly'), ['SoDienThoai' => '0911111111', 'password' => 'password'])
            ->assertRedirect();

        $this->assertAuthenticatedAs($tk);
    }

    public function test_dang_nhap_sai_mat_khau(): void
    {
        TaiKhoan::factory()->create(['SoDienThoai' => '0911111111']);

        $this->from(route('login'))
            ->post(route('login.xu-ly'), ['SoDienThoai' => '0911111111', 'password' => 'sai'])
            ->assertSessionHasErrors('SoDienThoai');

        $this->assertGuest();
    }

    public function test_tai_khoan_bi_khoa_khong_dang_nhap_duoc(): void
    {
        TaiKhoan::factory()->biKhoa()->create(['SoDienThoai' => '0922222222']);

        $this->post(route('login.xu-ly'), ['SoDienThoai' => '0922222222', 'password' => 'password'])
            ->assertSessionHasErrors('SoDienThoai');

        $this->assertGuest();
    }

    public function test_dang_ky_tao_khach_hang_va_tu_dang_nhap(): void
    {
        $this->post(route('dang-ky.xu-ly'), [
            'HoTen' => 'Khách Mới',
            'SoDienThoai' => '0933333333',
            'password' => 'matkhau1',
            'password_confirmation' => 'matkhau1',
            'VaiTro' => 'ADMIN', // cố tình gửi lên để leo quyền
        ])->assertRedirect(route('trang-chu'));

        $tk = TaiKhoan::where('SoDienThoai', '0933333333')->firstOrFail();
        $this->assertSame(TaiKhoan::VAI_TRO_USER, $tk->VaiTro);
        $this->assertAuthenticatedAs($tk);
    }

    public function test_dang_ky_trung_so_dien_thoai(): void
    {
        TaiKhoan::factory()->create(['SoDienThoai' => '0944444444']);

        $this->post(route('dang-ky.xu-ly'), [
            'HoTen' => 'A', 'SoDienThoai' => '0944444444',
            'password' => 'matkhau1', 'password_confirmation' => 'matkhau1',
        ])->assertSessionHasErrors('SoDienThoai');
    }

    public function test_dang_xuat(): void
    {
        $this->actingAs(TaiKhoan::factory()->create())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_khach_chua_dang_nhap_bi_chuyen_ve_trang_dang_nhap(): void
    {
        $this->get(route('ho-so.show'))->assertRedirect(route('login'));
    }
}

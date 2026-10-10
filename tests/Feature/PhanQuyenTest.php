<?php

namespace Tests\Feature;

use App\Models\TaiKhoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhanQuyenTest extends TestCase
{
    use RefreshDatabase;

    public function test_khach_hang_khong_vao_duoc_khu_admin(): void
    {
        $this->actingAs(TaiKhoan::factory()->create())
            ->get(route('admin.taikhoan.index'))
            ->assertForbidden();
    }

    public function test_ktv_khong_vao_duoc_khu_admin(): void
    {
        $this->actingAs(TaiKhoan::factory()->ktv()->create())
            ->get(route('admin.taikhoan.index'))
            ->assertForbidden();
    }

    public function test_admin_khong_vao_duoc_khu_xe_khach_hang(): void
    {
        $this->actingAs(TaiKhoan::factory()->admin()->create())
            ->get(route('xe.index'))
            ->assertForbidden();
    }

    public function test_tai_khoan_bi_khoa_giua_chung_bi_dang_xuat(): void
    {
        $tk = TaiKhoan::factory()->create();
        $this->actingAs($tk);

        $tk->update(['TrangThai' => TaiKhoan::KHOA]);

        $this->get(route('ho-so.show'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_admin_khoa_va_mo_khoa_tai_khoan(): void
    {
        $admin = TaiKhoan::factory()->admin()->create();
        $khach = TaiKhoan::factory()->create();

        $this->actingAs($admin)->patch(route('admin.taikhoan.trang-thai', $khach->MaTK));
        $this->assertSame(TaiKhoan::KHOA, $khach->fresh()->TrangThai);

        $this->actingAs($admin)->patch(route('admin.taikhoan.trang-thai', $khach->MaTK));
        $this->assertSame(TaiKhoan::HOAT_DONG, $khach->fresh()->TrangThai);
    }

    public function test_admin_khong_tu_khoa_minh(): void
    {
        $admin = TaiKhoan::factory()->admin()->create();

        $this->actingAs($admin)->patch(route('admin.taikhoan.trang-thai', $admin->MaTK))
            ->assertSessionHasErrors('error');

        $this->assertSame(TaiKhoan::HOAT_DONG, $admin->fresh()->TrangThai);
    }

    public function test_admin_tao_ktv(): void
    {
        $admin = TaiKhoan::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.taikhoan.store'), [
            'HoTen' => 'KTV Mới', 'SoDienThoai' => '0955555555',
            'VaiTro' => 'TECHNICIAN', 'password' => '123456',
        ])->assertRedirect();

        $this->assertDatabaseHas('TAIKHOAN', ['SoDienThoai' => '0955555555', 'VaiTro' => 'TECHNICIAN']);
    }

    public function test_khach_va_ktv_khong_vao_duoc_quan_ly_xe_cua_admin(): void
    {
        $this->actingAs(TaiKhoan::factory()->create())->get(route('admin.xe.index'))->assertForbidden();
        $this->actingAs(TaiKhoan::factory()->ktv()->create())->get(route('admin.xe.create'))->assertForbidden();
    }

    public function test_diem_tich_luy_mac_dinh_bang_0_va_hien_thi_o_ho_so(): void
    {
        $khach = TaiKhoan::factory()->create();
        $this->assertSame(0, $khach->fresh()->DiemTichLuy);

        $khach = TaiKhoan::factory()->coDiem(1200)->create();
        $this->actingAs($khach)->get(route('ho-so.show'))->assertOk()->assertSee('1.200');
    }

    public function test_diem_tich_luy_khong_gan_hang_loat_duoc(): void
    {
        $khach = TaiKhoan::factory()->create();
        $khach->update(['DiemTichLuy' => 99999]);

        $this->assertSame(0, $khach->fresh()->DiemTichLuy);
    }
}

<?php

namespace Tests\Feature;

use App\Models\TaiKhoan;
use App\Models\Xe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminXeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_tim_xe_theo_bien_so_va_so_dien_thoai_chu_xe(): void
    {
        $admin = TaiKhoan::factory()->admin()->create();
        $khach = TaiKhoan::factory()->create(['SoDienThoai' => '0912345678']);
        Xe::factory()->create(['MaTK' => $khach->MaTK, 'BienSo' => '51H-11111']);
        Xe::factory()->create(['BienSo' => '30A-22222']);

        $this->actingAs($admin)->get(route('admin.xe.index', ['tu_khoa' => '51h 11111']))
            ->assertOk()->assertSee('51H-11111')->assertDontSee('30A-22222');

        $this->actingAs($admin)->get(route('admin.xe.index', ['tu_khoa' => '0912345678']))
            ->assertOk()->assertSee('51H-11111')->assertDontSee('30A-22222');
    }

    public function test_admin_them_xe_cho_khach_theo_so_dien_thoai(): void
    {
        $admin = TaiKhoan::factory()->admin()->create();
        $khach = TaiKhoan::factory()->create(['SoDienThoai' => '0933333333']);

        $this->actingAs($admin)->post(route('admin.xe.store'), [
            'SoDienThoaiKhach' => '0933333333', 'BienSo' => ' 59b-99999 ', 'HangXe' => 'Honda', 'DongXe' => 'City', 'NamSanXuat' => 2021,
        ])->assertRedirect(route('admin.taikhoan.show', $khach->MaTK));

        $this->assertDatabaseHas('XE', ['BienSo' => '59B-99999', 'MaTK' => $khach->MaTK, 'TrangThai' => 'HOAT_DONG']);
    }

    public function test_them_xe_cho_so_dien_thoai_chua_co_tai_khoan_bi_tu_choi(): void
    {
        $this->actingAs(TaiKhoan::factory()->admin()->create())->post(route('admin.xe.store'), [
            'SoDienThoaiKhach' => '0944444444', 'BienSo' => '59B-88888', 'HangXe' => 'Honda',
        ])->assertSessionHasErrors('SoDienThoaiKhach');

        $this->assertDatabaseMissing('XE', ['BienSo' => '59B-88888']);
    }

    public function test_khong_them_xe_cho_tai_khoan_ktv(): void
    {
        $ktv = TaiKhoan::factory()->ktv()->create(['SoDienThoai' => '0955555555']);

        $this->actingAs(TaiKhoan::factory()->admin()->create())->post(route('admin.xe.store'), [
            'SoDienThoaiKhach' => $ktv->SoDienThoai, 'BienSo' => '59B-77777', 'HangXe' => 'Kia',
        ])->assertSessionHasErrors('SoDienThoaiKhach');
    }

    public function test_bien_so_trung_bi_tu_choi_khi_admin_them_xe(): void
    {
        Xe::factory()->create(['BienSo' => '59A-12345']);
        $khach = TaiKhoan::factory()->create();

        $this->actingAs(TaiKhoan::factory()->admin()->create())->post(route('admin.xe.store'), [
            'SoDienThoaiKhach' => $khach->SoDienThoai, 'BienSo' => '59a 12345'.'', 'HangXe' => 'Kia',
        ])->assertSessionDoesntHaveErrors('BienSo'); // "59A12345" khác "59A-12345"

        $this->actingAs(TaiKhoan::factory()->admin()->create())->post(route('admin.xe.store'), [
            'SoDienThoaiKhach' => $khach->SoDienThoai, 'BienSo' => '59a-12345', 'HangXe' => 'Kia',
        ])->assertSessionHasErrors('BienSo');
    }

    public function test_admin_sua_xe_va_khong_doi_duoc_chu_xe(): void
    {
        $admin = TaiKhoan::factory()->admin()->create();
        $chu = TaiKhoan::factory()->create();
        $khac = TaiKhoan::factory()->create();
        $xe = Xe::factory()->create(['MaTK' => $chu->MaTK]);

        $this->actingAs($admin)->put(route('admin.xe.update', $xe->MaXe), [
            'BienSo' => '51A-00001', 'HangXe' => 'Mazda', 'MaTK' => $khac->MaTK,
        ])->assertRedirect(route('admin.xe.index'));

        $xe->refresh();
        $this->assertSame('51A-00001', $xe->BienSo);
        $this->assertSame($chu->MaTK, $xe->MaTK);
    }

    public function test_admin_ngung_va_kich_hoat_lai_xe(): void
    {
        $admin = TaiKhoan::factory()->admin()->create();
        $xe = Xe::factory()->create();

        $this->actingAs($admin)->patch(route('admin.xe.trang-thai', $xe->MaXe));
        $this->assertSame(Xe::NGUNG_SU_DUNG, $xe->fresh()->TrangThai);

        $this->actingAs($admin)->patch(route('admin.xe.trang-thai', $xe->MaXe));
        $this->assertSame(Xe::HOAT_DONG, $xe->fresh()->TrangThai);
    }
}

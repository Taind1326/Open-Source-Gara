<?php

namespace Tests\Feature;

use App\Models\TaiKhoan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HoSoTest extends TestCase
{
    use RefreshDatabase;

    public function test_cap_nhat_ho_so_khong_doi_duoc_vai_tro(): void
    {
        $tk = TaiKhoan::factory()->create();

        $this->actingAs($tk)->put(route('ho-so.update'), [
            'HoTen' => 'Tên Mới', 'SoDienThoai' => $tk->SoDienThoai, 'VaiTro' => 'ADMIN',
        ])->assertSessionHasNoErrors();

        $tk->refresh();
        $this->assertSame('Tên Mới', $tk->HoTen);
        $this->assertSame(TaiKhoan::VAI_TRO_USER, $tk->VaiTro);
    }

    public function test_doi_mat_khau(): void
    {
        $tk = TaiKhoan::factory()->create();

        $this->actingAs($tk)->put(route('ho-so.mat-khau'), [
            'mat_khau_cu' => 'password', 'password' => 'moi12345', 'password_confirmation' => 'moi12345',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('moi12345', $tk->fresh()->MatKhau));
    }

    public function test_doi_mat_khau_sai_mat_khau_cu(): void
    {
        $this->actingAs(TaiKhoan::factory()->create())->put(route('ho-so.mat-khau'), [
            'mat_khau_cu' => 'sai', 'password' => 'moi12345', 'password_confirmation' => 'moi12345',
        ])->assertSessionHasErrors('mat_khau_cu');
    }
}

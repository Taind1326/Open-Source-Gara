<?php

namespace Tests\Feature;

use App\Models\TaiKhoan;
use App\Models\Xe;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class XeTest extends TestCase
{
    use RefreshDatabase;

    public function test_them_xe_chuan_hoa_bien_so_va_gan_chu_so_huu(): void
    {
        $khach = TaiKhoan::factory()->create();

        $this->actingAs($khach)->post(route('xe.store'), [
            'BienSo' => ' 59a-12345 ', 'HangXe' => 'Toyota', 'MaTK' => 9999,
        ])->assertRedirect(route('xe.index'));

        $this->assertDatabaseHas('XE', ['BienSo' => '59A-12345', 'MaTK' => $khach->MaTK, 'TrangThai' => 'HOAT_DONG']);
    }

    public function test_bien_so_trung_bi_tu_choi(): void
    {
        Xe::factory()->create(['BienSo' => '59A-12345']);

        $this->actingAs(TaiKhoan::factory()->create())
            ->post(route('xe.store'), ['BienSo' => '59a 12345'.'', 'HangXe' => 'Honda'])
            ->assertSessionDoesntHaveErrors('BienSo'); // "59A12345" khác "59A-12345" → hợp lệ

        $this->actingAs(TaiKhoan::factory()->create())
            ->post(route('xe.store'), ['BienSo' => '59a-12345', 'HangXe' => 'Honda'])
            ->assertSessionHasErrors('BienSo');
    }

    public function test_khong_sua_duoc_xe_cua_nguoi_khac(): void
    {
        $xeNguoiKhac = Xe::factory()->create();

        $this->actingAs(TaiKhoan::factory()->create())
            ->put(route('xe.update', $xeNguoiKhac->MaXe), ['BienSo' => '51A-99999', 'HangXe' => 'Kia'])
            ->assertNotFound();

        $this->assertNotSame('51A-99999', $xeNguoiKhac->fresh()->BienSo);
    }

    public function test_xoa_xe_la_ngung_hoat_dong_khong_xoa_cung(): void
    {
        $khach = TaiKhoan::factory()->create();
        $xe = Xe::factory()->create(['MaTK' => $khach->MaTK]);

        $this->actingAs($khach)->delete(route('xe.destroy', $xe->MaXe))->assertRedirect(route('xe.index'));

        $this->assertDatabaseHas('XE', ['MaXe' => $xe->MaXe, 'TrangThai' => Xe::NGUNG_SU_DUNG]);
    }

    // Bảng YEUCAUSUACHUA thuộc TV2: nếu chưa có thì tạo bảng tối giản chỉ cho test này
    private function dungBangYeuCauTam(callable $kiemTra): void
    {
        $tuTao = !Schema::hasTable('YEUCAUSUACHUA');
        if ($tuTao) {
            Schema::create('YEUCAUSUACHUA', function (Blueprint $t) {
                $t->id('MaYC');
                $t->unsignedBigInteger('MaXe');
                $t->string('TrangThai', 30)->default('CHO_PHAN_CONG');
            });
        }

        try {
            $kiemTra();
        } finally {
            if ($tuTao) {
                Schema::drop('YEUCAUSUACHUA');
            }
        }
    }

    public function test_khong_xoa_duoc_xe_dang_co_yeu_cau_chua_hoan_tat(): void
    {
        $this->dungBangYeuCauTam(function () {
            $khach = TaiKhoan::factory()->create();
            $xe = Xe::factory()->create(['MaTK' => $khach->MaTK]);
            DB::table('YEUCAUSUACHUA')->insert(['MaXe' => $xe->MaXe, 'TrangThai' => 'DANG_SUA']);

            $this->actingAs($khach)->delete(route('xe.destroy', $xe->MaXe))->assertSessionHasErrors('error');

            $this->assertSame(Xe::HOAT_DONG, $xe->fresh()->TrangThai);
        });
    }

    public function test_xoa_duoc_xe_khi_yeu_cau_da_ket_thuc(): void
    {
        $this->dungBangYeuCauTam(function () {
            $khach = TaiKhoan::factory()->create();
            $xe = Xe::factory()->create(['MaTK' => $khach->MaTK]);
            DB::table('YEUCAUSUACHUA')->insert(['MaXe' => $xe->MaXe, 'TrangThai' => 'HOAN_THANH']);

            $this->actingAs($khach)->delete(route('xe.destroy', $xe->MaXe))->assertRedirect(route('xe.index'));

            $this->assertSame(Xe::NGUNG_SU_DUNG, $xe->fresh()->TrangThai);
        });
    }
}

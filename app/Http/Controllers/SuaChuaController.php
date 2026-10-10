<?php

namespace App\Http\Controllers;

use App\Models\{ChiTietSuaChua, PhieuSuaChua, PhuTung, TienDo, YeuCauSuaChua};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SuaChuaController extends Controller
{
    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['error' => $message]);
    }

    private function loadRequest($maYC, bool $lock = false)
    {
        $query = YeuCauSuaChua::query();
        if ($lock) $query->lockForUpdate();
        return $query->with([
            'xe', 'phanCong', 'kiemTraXe',
            'baoGia.chiTietBaoGias.dichVu', 'baoGia.chiTietBaoGias.phuTung',
            'phieuSuaChua.chiTiet.dichVu', 'phieuSuaChua.chiTiet.phuTung',
            'phieuSuaChua.tienDos',
        ])->findOrFail($maYC);
    }

    private function activeTicket($yeuCau)
    {
        $phieu = $yeuCau->phieuSuaChua;
        if (!$phieu || $phieu->TrangThai !== 'DANG_SUA'
            || $yeuCau->TrangThai !== 'DANG_SUA'
            || $yeuCau->baoGia?->TrangThai !== 'DA_DUYET') {
            $this->fail('Phiếu không ở trạng thái đang sửa hoặc báo giá chưa được duyệt.');
        }
        return $phieu;
    }

    public function create($maYC)
    {
        $yeuCau = $this->loadRequest($maYC);
        if ($yeuCau->phieuSuaChua) return redirect()->route('suachua.show', $maYC);
        if ($yeuCau->TrangThai !== 'DANG_SUA' || $yeuCau->baoGia?->TrangThai !== 'DA_DUYET') {
            $this->fail('Cần khách đồng ý báo giá trước khi bắt đầu sửa.');
        }
        return view('suachua.create', compact('yeuCau'));
    }

    public function store($maYC)
    {
        DB::transaction(function () use ($maYC) {
            $yeuCau = $this->loadRequest($maYC, true);
            if ($yeuCau->phieuSuaChua) return;
            if ($yeuCau->TrangThai !== 'DANG_SUA' || $yeuCau->baoGia?->TrangThai !== 'DA_DUYET') {
                $this->fail('Báo giá chưa được đồng ý.');
            }
            $maKTV = $yeuCau->kiemTraXe?->MaKTV;
            if (!$maKTV || (int) $maKTV !== (int) $yeuCau->phanCong?->MaKTV) {
                $this->fail('KTV kiểm tra phải là KTV đã phân công và tiếp tục sửa xe.');
            }
            $phieu = PhieuSuaChua::create([
                'MaYC' => $maYC, 'MaKTV' => $maKTV, 'NgayBatDau' => now(),
                'TrangThai' => 'DANG_SUA',
            ]);
            $this->progress($phieu, 'Bắt đầu sửa chữa xe.', 'DANG_SUA');
        });
        return redirect()->route('suachua.show', $maYC)->with('success', 'Đã bắt đầu sửa chữa.');
    }

    public function show($maYC)
    {
        $yeuCau = $this->loadRequest($maYC);
        if (!$yeuCau->phieuSuaChua) return redirect()->route('suachua.create', $maYC);
        // Chỉ hiển thị các hạng mục đã được khách duyệt.
        $hangMucs = $yeuCau->baoGia->chiTietBaoGias;
        return view('suachua.show', compact('yeuCau', 'hangMucs'));
    }

    public function addService(Request $request, $maYC)
    {
        $data = $request->validate(['MaDV' => 'required|integer|exists:DICHVU,MaDV']);
        DB::transaction(function () use ($maYC, $data) {
            $yeuCau = $this->loadRequest($maYC, true);
            $phieu = $this->activeTicket($yeuCau);
            $hangMuc = $yeuCau->baoGia->chiTietBaoGias->firstWhere('MaDV', $data['MaDV']);
            if (!$hangMuc) $this->fail('Dịch vụ không nằm trong báo giá đã duyệt.');
            if ($phieu->chiTiet->contains('MaDV', $data['MaDV'])) $this->fail('Dịch vụ đã được ghi nhận.');
            ChiTietSuaChua::create([
                'MaPSC' => $phieu->MaPSC, 'MaDV' => $data['MaDV'], 'MaPT' => null,
                'SoLuong' => $hangMuc->SoLuong, 'DonGia' => $hangMuc->DonGia,
            ]);
        });
        return back()->with('success', 'Đã ghi nhận dịch vụ thực tế theo giá đã duyệt.');
    }

    public function addPart(Request $request, $maYC)
    {
        $data = $request->validate([
            'MaPT' => 'required|integer|exists:PHUTUNG,MaPT',
            'SoLuong' => 'required|integer|min:1',
        ]);
        DB::transaction(function () use ($maYC, $data) {
            $yeuCau = $this->loadRequest($maYC, true);
            $phieu = $this->activeTicket($yeuCau);
            $hangMuc = $yeuCau->baoGia->chiTietBaoGias->firstWhere('MaPT', $data['MaPT']);
            if (!$hangMuc) $this->fail('Phụ tùng không nằm trong báo giá đã duyệt.');
            $chiTiet = $phieu->chiTiet->firstWhere('MaPT', $data['MaPT']);
            $tong = ($chiTiet?->SoLuong ?? 0) + $data['SoLuong'];
            if ($tong > $hangMuc->SoLuong) $this->fail('Tổng phụ tùng thực tế vượt số lượng khách đã duyệt.');
            $phuTung = PhuTung::where('MaPT', $data['MaPT'])->lockForUpdate()->firstOrFail();
            if ($phuTung->TrangThai !== 'DANG_SU_DUNG') $this->fail('Phụ tùng đã ngừng sử dụng.');
            if ($phuTung->SoLuongTon < $data['SoLuong']) $this->fail('Số lượng phụ tùng trong kho không đủ.');
            if ($chiTiet) {
                $chiTiet->update(['SoLuong' => $tong]);
            } else {
                ChiTietSuaChua::create([
                    'MaPSC' => $phieu->MaPSC, 'MaPT' => $data['MaPT'], 'MaDV' => null,
                    'SoLuong' => $data['SoLuong'], 'DonGia' => $hangMuc->DonGia,
                ]);
            }
            $phuTung->decrement('SoLuongTon', $data['SoLuong']);
        });
        return back()->with('success', 'Đã ghi nhận phụ tùng thực tế và trừ tồn kho.');
    }

    private function progress($phieu, string $noiDung, string $trangThai): void
    {
        TienDo::create([
            'MaPSC' => $phieu->MaPSC, 'MaKTV' => $phieu->MaKTV,
            'NoiDung' => $noiDung, 'TrangThai' => $trangThai, 'NgayCapNhat' => now(),
        ]);
    }

    public function addProgress(Request $request, $maYC)
    {
        $data = $request->validate(['NoiDung' => 'required|string|max:255']);
        DB::transaction(function () use ($maYC, $data) {
            $yeuCau = $this->loadRequest($maYC, true);
            $this->progress($this->activeTicket($yeuCau), $data['NoiDung'], 'DANG_SUA');
        });
        return back()->with('success', 'Đã cập nhật tiến độ.');
    }

    public function complete($maYC)
    {
        DB::transaction(function () use ($maYC) {
            $yeuCau = $this->loadRequest($maYC, true);
            $phieu = $this->activeTicket($yeuCau);
            if ($phieu->chiTiet->isEmpty()) $this->fail('Cần ghi nhận hạng mục thực tế trước khi hoàn thành.');
            $phieu->update(['TrangThai' => 'HOAN_THANH_KY_THUAT', 'NgayHoanThanh' => now()]);
            $this->progress($phieu, 'Hoàn thành kỹ thuật, chờ lập hóa đơn và thanh toán.', 'HOAN_THANH_KY_THUAT');
            // Yêu cầu vẫn DANG_SUA cho tới khi TV4 xác nhận thanh toán và trả xe.
        });
        return back()->with('success', 'Đã hoàn thành kỹ thuật. Admin có thể lập hóa đơn.');
    }
}

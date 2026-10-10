<?php

namespace App\Http\Controllers;

use App\Models\ChiTietSuaChua;
use App\Models\PhieuSuaChua;
use App\Models\PhuTung;
use App\Models\TienDo;
use App\Models\YeuCauSuaChua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SuaChuaController extends Controller
{
    private function fail(string $message): never
    {
        throw ValidationException::withMessages([
            'error' => $message,
        ]);
    }

    private function loadRequest($maYC, bool $lock = false)
    {
        $query = YeuCauSuaChua::query();

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->with([
            'xe',
            'phanCong',
            'kiemTraXe',
            'baoGia.chiTietBaoGias.dichVu',
            'baoGia.chiTietBaoGias.phuTung',
            'phieuSuaChua.chiTiet.dichVu',
            'phieuSuaChua.chiTiet.phuTung',
            'phieuSuaChua.tienDos',
        ])->findOrFail($maYC);
    }

    private function activeTicket($yeuCau)
    {
        $phieu = $yeuCau->phieuSuaChua;

        if (
            !$phieu
            || $phieu->TrangThai !== 'DANG_SUA'
            || $yeuCau->TrangThai !== 'DANG_SUA'
            || $yeuCau->baoGia?->TrangThai !== 'DA_DUYET'
        ) {
            $this->fail(
                'Phiếu không ở trạng thái đang sửa hoặc báo giá chưa được duyệt.'
            );
        }

        return $phieu;
    }

    public function create($maYC)
    {
        $yeuCau = $this->loadRequest($maYC);

        if ($yeuCau->phieuSuaChua) {
            return redirect()->route('suachua.show', $maYC);
        }

        if (
            $yeuCau->TrangThai !== 'DANG_SUA'
            || $yeuCau->baoGia?->TrangThai !== 'DA_DUYET'
        ) {
            $this->fail(
                'Cần khách đồng ý báo giá trước khi bắt đầu sửa.'
            );
        }

        return view('suachua.create', compact('yeuCau'));
    }

    public function store($maYC)
    {
        DB::transaction(function () use ($maYC) {
            $yeuCau = $this->loadRequest($maYC, true);

            if ($yeuCau->phieuSuaChua) {
                return;
            }

            if (
                $yeuCau->TrangThai !== 'DANG_SUA'
                || $yeuCau->baoGia?->TrangThai !== 'DA_DUYET'
            ) {
                $this->fail('Báo giá chưa được đồng ý.');
            }

            $maKTV = $yeuCau->kiemTraXe?->MaKTV;

            if (
                !$maKTV
                || (int) $maKTV !== (int) $yeuCau->phanCong?->MaKTV
            ) {
                $this->fail(
                    'KTV kiểm tra phải là KTV đã phân công và tiếp tục sửa xe.'
                );
            }

            $phieu = PhieuSuaChua::create([
                'MaYC' => $maYC,
                'MaKTV' => $maKTV,
                'NgayBatDau' => now(),
                'TrangThai' => 'DANG_SUA',
            ]);

            $this->progress(
                $phieu,
                'Bắt đầu sửa chữa xe.',
                'DANG_SUA'
            );
        });

        return redirect()
            ->route('suachua.show', $maYC)
            ->with('success', 'Đã bắt đầu sửa chữa.');
    }

    public function show($maYC)
    {
        $yeuCau = $this->loadRequest($maYC);

        if (!$yeuCau->phieuSuaChua) {
            return redirect()->route('suachua.create', $maYC);
        }

        if (!$yeuCau->baoGia) {
            $this->fail('Không tìm thấy báo giá của yêu cầu.');
        }

        $hangMucs = $yeuCau->baoGia->chiTietBaoGias;

        return view(
            'suachua.show',
            compact('yeuCau', 'hangMucs')
        );
    }

    public function addService(Request $request, $maYC)
    {
        $data = $request->validate([
            'MaDV' => [
                'required',
                'integer',
                'exists:DICHVU,MaDV',
            ],
        ], [
            'MaDV.required' => 'Vui lòng chọn dịch vụ.',
            'MaDV.exists' => 'Dịch vụ không hợp lệ.',
        ]);

        DB::transaction(function () use ($maYC, $data) {
            $yeuCau = $this->loadRequest($maYC, true);
            $phieu = $this->activeTicket($yeuCau);

            $hangMuc = $yeuCau->baoGia
                ->chiTietBaoGias
                ->firstWhere('MaDV', $data['MaDV']);

            if (!$hangMuc) {
                $this->fail(
                    'Dịch vụ không nằm trong báo giá đã duyệt.'
                );
            }

            if ($phieu->chiTiet->contains('MaDV', $data['MaDV'])) {
                $this->fail('Dịch vụ đã được ghi nhận.');
            }

            ChiTietSuaChua::create([
                'MaPSC' => $phieu->MaPSC,
                'MaDV' => $data['MaDV'],
                'MaPT' => null,
                'SoLuong' => $hangMuc->SoLuong,
                'DonGia' => $hangMuc->DonGia,
            ]);
        });

        return back()->with(
            'success',
            'Đã ghi nhận dịch vụ thực tế theo giá đã duyệt.'
        );
    }

    public function addPart(Request $request, $maYC)
    {
        $data = $request->validate([
            'MaPT' => [
                'required',
                'integer',
                'exists:PHUTUNG,MaPT',
            ],
            'SoLuong' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'MaPT.required' => 'Vui lòng chọn phụ tùng.',
            'MaPT.exists' => 'Phụ tùng không hợp lệ.',
            'SoLuong.required' => 'Vui lòng nhập số lượng.',
            'SoLuong.integer' => 'Số lượng phải là số nguyên.',
            'SoLuong.min' => 'Số lượng phải lớn hơn 0.',
        ]);

        DB::transaction(function () use ($maYC, $data) {
            $yeuCau = $this->loadRequest($maYC, true);
            $phieu = $this->activeTicket($yeuCau);

            $hangMuc = $yeuCau->baoGia
                ->chiTietBaoGias
                ->firstWhere('MaPT', $data['MaPT']);

            if (!$hangMuc) {
                $this->fail(
                    'Phụ tùng không nằm trong báo giá đã duyệt.'
                );
            }

            $chiTiet = $phieu->chiTiet
                ->firstWhere('MaPT', $data['MaPT']);

            $tongSoLuong = ($chiTiet?->SoLuong ?? 0)
                + $data['SoLuong'];

            if ($tongSoLuong > $hangMuc->SoLuong) {
                $this->fail(
                    'Tổng phụ tùng thực tế vượt số lượng khách đã duyệt.'
                );
            }

            $phuTung = PhuTung::where('MaPT', $data['MaPT'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($phuTung->TrangThai !== 'DANG_SU_DUNG') {
                $this->fail('Phụ tùng đã ngừng sử dụng.');
            }

            if ($phuTung->SoLuongTon < $data['SoLuong']) {
                $this->fail(
                    'Số lượng phụ tùng trong kho không đủ.'
                );
            }

            if ($chiTiet) {
                $chiTiet->update([
                    'SoLuong' => $tongSoLuong,
                ]);
            } else {
                ChiTietSuaChua::create([
                    'MaPSC' => $phieu->MaPSC,
                    'MaPT' => $data['MaPT'],
                    'MaDV' => null,
                    'SoLuong' => $data['SoLuong'],
                    'DonGia' => $hangMuc->DonGia,
                ]);
            }

            $phuTung->decrement(
                'SoLuongTon',
                $data['SoLuong']
            );
        });

        return back()->with(
            'success',
            'Đã ghi nhận phụ tùng thực tế và trừ tồn kho.'
        );
    }

    private function progress(
        $phieu,
        string $noiDung,
        string $trangThai
    ): void {
        TienDo::create([
            'MaPSC' => $phieu->MaPSC,
            'MaKTV' => $phieu->MaKTV,
            'NoiDung' => $noiDung,
            'TrangThai' => $trangThai,
            'NgayCapNhat' => now(),
        ]);
    }

    public function addProgress(Request $request, $maYC)
    {
        $data = $request->validate([
            'TrangThai' => [
                'required',
                'in:DANG_SUA,KIEM_TRA_SAU_SUA,HOAN_THANH_KY_THUAT',
            ],
            'GhiChu' => [
                'nullable',
                'string',
                'max:180',
            ],
        ], [
            'TrangThai.required' =>
                'Vui lòng chọn trạng thái tiến độ.',

            'TrangThai.in' =>
                'Trạng thái tiến độ không hợp lệ.',

            'GhiChu.max' =>
                'Ghi chú không được vượt quá 180 ký tự.',
        ]);

        DB::transaction(function () use ($maYC, $data) {
            $yeuCau = $this->loadRequest($maYC, true);
            $phieu = $this->activeTicket($yeuCau);

            $trangThai = $data['TrangThai'];

            $noiDung = match ($trangThai) {
                'DANG_SUA' =>
                    'Đang sửa chữa xe.',

                'KIEM_TRA_SAU_SUA' =>
                    'Đang kiểm tra xe sau sửa chữa.',

                'HOAN_THANH_KY_THUAT' =>
                    'Hoàn thành kỹ thuật, chờ lập hóa đơn và thanh toán.',
            };

            if ($trangThai === 'HOAN_THANH_KY_THUAT') {
                $this->finishTicket($phieu);
            }

            $ghiChu = trim($data['GhiChu'] ?? '');

            if ($ghiChu !== '') {
                $noiDung .= ' Ghi chú: ' . $ghiChu;
            }

            $this->progress(
                $phieu,
                $noiDung,
                $trangThai
            );
        });

        return back()->with(
            'success',
            'Đã cập nhật tiến độ sửa chữa.'
        );
    }

    private function finishTicket($phieu): void
    {
        if ($phieu->chiTiet->isEmpty()) {
            $this->fail(
                'Cần ghi nhận hạng mục thực tế trước khi hoàn thành.'
            );
        }

        $phieu->update([
            'TrangThai' => 'HOAN_THANH_KY_THUAT',
            'NgayHoanThanh' => now(),
        ]);
    }

    public function complete($maYC)
    {
        // Giữ tương thích với route hoàn thành hiện có.
        DB::transaction(function () use ($maYC) {
            $yeuCau = $this->loadRequest($maYC, true);
            $phieu = $this->activeTicket($yeuCau);

            $this->finishTicket($phieu);

            $this->progress(
                $phieu,
                'Hoàn thành kỹ thuật, chờ lập hóa đơn và thanh toán.',
                'HOAN_THANH_KY_THUAT'
            );
        });

        return back()->with(
            'success',
            'Đã hoàn thành kỹ thuật. Admin có thể lập hóa đơn.'
        );
    }
}
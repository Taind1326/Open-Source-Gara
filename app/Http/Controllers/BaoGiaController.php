<?php

namespace App\Http\Controllers;

use App\Models\BaoGia;
use App\Models\ChiTietBaoGia;
use App\Models\YeuCauSuaChua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BaoGiaController extends Controller
{
    public function create($maYC)
    {
        $yeuCau = YeuCauSuaChua::with([
            'xe',
            'kiemTraXe.deXuatDichVus.dichVu',
            'kiemTraXe.deXuatPhuTungs.phuTung',
            'baoGia',
        ])->findOrFail($maYC);

        if ($yeuCau->TrangThai !== 'DA_KIEM_TRA') {
            return back()->withErrors([
                'error' => 'Yêu cầu chưa hoàn tất kiểm tra kỹ thuật.'
            ]);
        }

        if (!$yeuCau->kiemTraXe) {
            return back()->withErrors([
                'error' => 'Chưa có kết quả kiểm tra xe.'
            ]);
        }

        if ($yeuCau->baoGia) {
            return redirect()
                ->route('baogia.show', $maYC);
        }

        return view('baogia.create', compact('yeuCau'));
    }


    public function store(Request $request, $maYC)
    {
        $yeuCau = YeuCauSuaChua::with([
            'kiemTraXe.deXuatDichVus.dichVu',
            'kiemTraXe.deXuatPhuTungs.phuTung',
            'baoGia',
        ])->findOrFail($maYC);

        if ($yeuCau->TrangThai !== 'DA_KIEM_TRA') {
            return back()->withErrors([
                'error' => 'Yêu cầu chưa hoàn tất kiểm tra kỹ thuật.'
            ]);
        }

        if (!$yeuCau->kiemTraXe) {
            return back()->withErrors([
                'error' => 'Chưa có kết quả kiểm tra xe.'
            ]);
        }

        if ($yeuCau->baoGia) {
            return back()->withErrors([
                'error' => 'Yêu cầu này đã có báo giá.'
            ]);
        }

        $data = $request->validate([
            'GhiChu' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use ($yeuCau, $data) {

            $baoGia = BaoGia::create([
                'MaYC' => $yeuCau->MaYC,
                'TongTien' => 0,
                'TrangThai' => 'CHO_DUYET',
                'GhiChu' => $data['GhiChu'] ?? null,
                'NgayTao' => now(),
            ]);

            $tongTien = 0;

            foreach ($yeuCau->kiemTraXe->deXuatDichVus as $deXuat) {
                $donGia = $deXuat->dichVu->Gia;

                ChiTietBaoGia::create([
                    'MaBG' => $baoGia->MaBG,
                    'MaDV' => $deXuat->MaDV,
                    'MaPT' => null,
                    'SoLuong' => 1,
                    'DonGia' => $donGia,
                ]);

                $tongTien += $donGia;
            }

            foreach ($yeuCau->kiemTraXe->deXuatPhuTungs as $deXuat) {
                $donGia = $deXuat->phuTung->Gia;
                $soLuong = $deXuat->SoLuong;

                ChiTietBaoGia::create([
                    'MaBG' => $baoGia->MaBG,
                    'MaDV' => null,
                    'MaPT' => $deXuat->MaPT,
                    'SoLuong' => $soLuong,
                    'DonGia' => $donGia,
                ]);

                $tongTien += $donGia * $soLuong;
            }

            $baoGia->update([
                'TongTien' => $tongTien,
            ]);

            $yeuCau->update([
                'TrangThai' => 'CHO_DUYET_BAO_GIA',
            ]);
        });

        return redirect()
            ->route('baogia.show', $maYC)
            ->with(
                'success',
                'Đã lập báo giá thành công.'
            );
    }


    public function show($maYC)
    {
        $yeuCau = YeuCauSuaChua::with([
            'xe',
            'kiemTraXe',
            'baoGia.chiTietBaoGias.dichVu',
            'baoGia.chiTietBaoGias.phuTung',
        ])->findOrFail($maYC);

        if (!$yeuCau->baoGia) {
            return redirect()
                ->route('baogia.create', $maYC)
                ->withErrors([
                    'error' => 'Yêu cầu này chưa có báo giá.'
                ]);
        }

        return view(
            'baogia.show',
            compact('yeuCau')
        );
    }


    public function approve($maYC)
    {
        $yeuCau = YeuCauSuaChua::with('baoGia')
            ->findOrFail($maYC);

        if (!$yeuCau->baoGia) {
            return back()->withErrors([
                'error' => 'Yêu cầu này chưa có báo giá.'
            ]);
        }

        if ($yeuCau->baoGia->TrangThai !== 'CHO_DUYET') {
            return back()->withErrors([
                'error' => 'Báo giá này không còn ở trạng thái chờ duyệt.'
            ]);
        }

        DB::transaction(function () use ($yeuCau) {

            $yeuCau->baoGia->update([
                'TrangThai' => 'DA_DUYET',
            ]);

            $yeuCau->update([
                'TrangThai' => 'DANG_SUA',
            ]);
        });

        return redirect()
            ->route('baogia.show', $maYC)
            ->with(
                'success',
                'Đã đồng ý báo giá. Xe được chuyển sang giai đoạn sửa chữa.'
            );
    }


    public function reject($maYC)
    {
        $yeuCau = YeuCauSuaChua::with('baoGia')
            ->findOrFail($maYC);

        if (!$yeuCau->baoGia) {
            return back()->withErrors([
                'error' => 'Yêu cầu này chưa có báo giá.'
            ]);
        }

        if ($yeuCau->baoGia->TrangThai !== 'CHO_DUYET') {
            return back()->withErrors([
                'error' => 'Báo giá này không còn ở trạng thái chờ duyệt.'
            ]);
        }

        DB::transaction(function () use ($yeuCau) {

            $yeuCau->baoGia->update([
                'TrangThai' => 'TU_CHOI',
            ]);

            $yeuCau->update([
                'TrangThai' => 'TU_CHOI',
            ]);
        });

        return redirect()
            ->route('baogia.show', $maYC)
            ->with(
                'success',
                'Đã từ chối báo giá.'
            );
    }
}
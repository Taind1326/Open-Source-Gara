<?php

namespace App\Http\Controllers;

use App\Models\KiemTraXe;
use App\Models\YeuCauSuaChua;
use App\Models\DichVu;
use App\Models\PhuTung;
use App\Models\DeXuatDichVu;
use App\Models\DeXuatPhuTung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KiemTraXeController extends Controller
{
    /**
     * Hiển thị form kiểm tra và chẩn đoán xe.
     */
    public function create($maYC)
    {
        $yeuCau = YeuCauSuaChua::with([
            'xe',
            'dichVu',
            'phanCong',
            'kiemTraXe',
        ])->findOrFail($maYC);

        // Chỉ kiểm tra xe đã được Admin tiếp nhận
        if ($yeuCau->TrangThai !== 'DA_TIEP_NHAN') {
            return back()->withErrors([
                'error' => 'Xe chưa ở trạng thái cho phép kiểm tra.'
            ]);
        }

        // Một yêu cầu chỉ được kiểm tra một lần
        if ($yeuCau->kiemTraXe) {
            return redirect()
                ->route('kiemtraxe.show', $maYC);
        }

        // Danh sách dịch vụ đang hoạt động
        $dichVus = DichVu::where(
            'TrangThai',
            'HOAT_DONG'
        )
            ->orderBy('TenDV')
            ->get();

        // Danh sách phụ tùng đang sử dụng
        $phuTungs = PhuTung::where(
            'TrangThai',
            'DANG_SU_DUNG'
        )
            ->orderBy('TenPT')
            ->get();

        return view('kiemtraxe.create', compact(
            'yeuCau',
            'dichVus',
            'phuTungs'
        ));
    }

    /**
     * Lưu kết quả kiểm tra và các đề xuất của KTV.
     */
    public function store(Request $request, $maYC)
    {
        $yeuCau = YeuCauSuaChua::with([
            'phanCong',
            'kiemTraXe',
        ])->findOrFail($maYC);

        if ($yeuCau->TrangThai !== 'DA_TIEP_NHAN') {
            return back()->withErrors([
                'error' => 'Xe chưa ở trạng thái cho phép kiểm tra.'
            ]);
        }

        if (!$yeuCau->phanCong) {
            return back()->withErrors([
                'error' => 'Yêu cầu chưa được phân công kỹ thuật viên.'
            ]);
        }

        if ($yeuCau->kiemTraXe) {
            return back()->withErrors([
                'error' => 'Yêu cầu này đã được kiểm tra.'
            ]);
        }

        $data = $request->validate([
            'TinhTrang' => [
                'required',
                'string',
                'max:1000',
            ],

            'ChanDoan' => [
                'required',
                'string',
                'max:1000',
            ],

            'GhiChu' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'HinhAnh' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            // Dịch vụ KTV đề xuất
            'dich_vu_de_xuat' => [
                'nullable',
                'array',
            ],

            'dich_vu_de_xuat.*' => [
                'integer',
                'exists:DICHVU,MaDV',
                'distinct',
            ],

            // Phụ tùng KTV đề xuất
            'phu_tung_de_xuat' => [
                'nullable',
                'array',
            ],

            'phu_tung_de_xuat.*.MaPT' => [
                'required',
                'integer',
                'exists:PHUTUNG,MaPT',
                'distinct',
            ],

            'phu_tung_de_xuat.*.SoLuong' => [
                'required',
                'integer',
                'min:1',
            ],
        ], [
            'TinhTrang.required' =>
                'Vui lòng nhập tình trạng xe.',

            'ChanDoan.required' =>
                'Vui lòng nhập kết quả chẩn đoán.',

            'HinhAnh.image' =>
                'File tải lên phải là hình ảnh.',

            'HinhAnh.mimes' =>
                'Hình ảnh phải có định dạng JPG, JPEG, PNG hoặc WEBP.',

            'HinhAnh.max' =>
                'Hình ảnh không được vượt quá 2MB.',

            'dich_vu_de_xuat.*.exists' =>
                'Dịch vụ được chọn không hợp lệ.',

            'phu_tung_de_xuat.*.MaPT.exists' =>
                'Phụ tùng được chọn không hợp lệ.',

            'phu_tung_de_xuat.*.SoLuong.min' =>
                'Số lượng phụ tùng phải lớn hơn 0.',
        ]);

        /*
         * Upload ảnh trước khi bắt đầu transaction.
         */
        if ($request->hasFile('HinhAnh')) {
            $data['HinhAnh'] = $request
                ->file('HinhAnh')
                ->store('kiem-tra-xe', 'public');
        }

        DB::transaction(function () use ($data, $yeuCau) {

            $yeuCau = YeuCauSuaChua::where('MaYC', $yeuCau->MaYC)
                ->lockForUpdate()->firstOrFail();
            if ($yeuCau->TrangThai !== 'DA_TIEP_NHAN' || $yeuCau->kiemTraXe || !$yeuCau->phanCong) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'error' => 'Yêu cầu đã được xử lý hoặc chưa có phân công.'
                ]);
            }

            // 1. Lưu kết quả kiểm tra
            $kiemTra = KiemTraXe::create([
                'MaYC' => $yeuCau->MaYC,
                'MaKTV' => $yeuCau->phanCong->MaKTV,
                'TinhTrang' => $data['TinhTrang'],
                'ChanDoan' => $data['ChanDoan'],
                'GhiChu' => $data['GhiChu'] ?? null,
                'HinhAnh' => $data['HinhAnh'] ?? null,
                'NgayKiemTra' => now(),
            ]);

            // 2. Lưu các dịch vụ KTV đề xuất
            foreach ($data['dich_vu_de_xuat'] ?? [] as $maDV) {
                DeXuatDichVu::create([
                    'MaKT' => $kiemTra->MaKT,
                    'MaDV' => $maDV,
                ]);
            }

            // 3. Lưu các phụ tùng KTV đề xuất
            foreach ($data['phu_tung_de_xuat'] ?? [] as $phuTung) {
                DeXuatPhuTung::create([
                    'MaKT' => $kiemTra->MaKT,
                    'MaPT' => $phuTung['MaPT'],
                    'SoLuong' => $phuTung['SoLuong'],
                ]);
            }

            // 4. Chuyển trạng thái yêu cầu
            $yeuCau->update([
                'TrangThai' => 'DA_KIEM_TRA',
            ]);
        });

        return redirect()
            ->route('kiemtraxe.show', $maYC)
            ->with(
                'success',
                'Đã lưu kết quả kiểm tra và đề xuất kỹ thuật.'
            );
    }

    /**
     * Xem kết quả kiểm tra.
     */
    public function show($maYC)
    {
        $yeuCau = YeuCauSuaChua::with([
            'xe',
            'dichVu',
            'phanCong',
            'kiemTraXe.deXuatDichVus.dichVu',
            'kiemTraXe.deXuatPhuTungs.phuTung',
        ])->findOrFail($maYC);

        if (!$yeuCau->kiemTraXe) {
            return redirect()->route('kiemtraxe.create', $maYC);
        }

        return view(
            'kiemtraxe.show',
            compact('yeuCau')
        );
    }
}
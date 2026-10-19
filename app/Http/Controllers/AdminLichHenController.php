<?php

namespace App\Http\Controllers;

use App\Models\LichHen;
use App\Models\PhanCong;
use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminLichHenController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'ngay_hen' => ['nullable', 'date_format:Y-m-d'],
            'trang_thai' => [
                'nullable',
                Rule::in(array_keys(LichHen::NHAN_TRANG_THAI)),
            ],
            'page' => ['nullable', 'integer', 'min:1'],
        ], [
            'ngay_hen.date_format' => 'Ngày hẹn không hợp lệ.',
            'trang_thai.in' => 'Trạng thái không hợp lệ.',
        ]);

        $tuKhoa = trim($data['tu_khoa'] ?? '');
        $ngayHen = $data['ngay_hen'] ?? '';
        $trangThai = $data['trang_thai'] ?? '';

        $lichHens = LichHen::query()
            ->with([
                'taiKhoan',
                'xe',
                'phanCong.ktv',
            ])
            ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                $query->where(function ($query) use ($tuKhoa) {
                    $query->whereHas('taiKhoan', function ($query) use ($tuKhoa) {
                        $query->where('HoTen', 'like', "%{$tuKhoa}%")
                            ->orWhere('SoDienThoai', 'like', "%{$tuKhoa}%")
                            ->orWhere('Email', 'like', "%{$tuKhoa}%");
                    })->orWhereHas('xe', function ($query) use ($tuKhoa) {
                        $query->where('BienSo', 'like', "%{$tuKhoa}%");
                    });
                });
            })
            ->when($ngayHen !== '', function ($query) use ($ngayHen) {
                $query->where('NgayHen', $ngayHen);
            })
            ->when($trangThai !== '', function ($query) use ($trangThai) {
                $query->where('TrangThai', $trangThai);
            })
            ->orderByRaw(
                'CASE WHEN TrangThai = ? THEN 0 ELSE 1 END',
                [LichHen::CHO_PHAN_CONG]
            )
            ->orderBy('NgayHen')
            ->orderBy('KhungGioHen')
            ->orderByDesc('MaLH')
            ->paginate(10)
            ->withQueryString();

        if ($lichHens->currentPage() > $lichHens->lastPage()) {
            return redirect()->route(
                'admin.lichhen.index',
                array_merge(
                    $request->only('tu_khoa', 'ngay_hen', 'trang_thai'),
                    ['page' => $lichHens->lastPage()]
                )
            );
        }

        return view('admin.lichhen.index', compact(
            'lichHens',
            'tuKhoa',
            'ngayHen',
            'trangThai'
        ));
    }

    public function show($maLH)
    {
        $lichHen = LichHen::query()
            ->with([
                'taiKhoan',
                'xe',
                'chiTietLichHens.dichVu',
                'phanCong.ktv',
                'phanCong.admin',
            ])
            ->findOrFail($maLH);

        $dichVuBanDau = $lichHen->chiTietLichHens
            ->where('KhachDaChon', true)
            ->whereNotNull('MaDV');

        $tongBanDau = $dichVuBanDau->sum(
            fn ($item) => (int) $item->DonGia * $item->SoLuong
        );

        $trangThaiDangPhuTrach = [
            LichHen::CHO_NHAN_XE,
            LichHen::DANG_KIEM_TRA,
            LichHen::CHO_XAC_NHAN,
            LichHen::CHO_SUA_CHUA,
            LichHen::DANG_SUA_CHUA,
        ];

        $kyThuatViens = TaiKhoan::query()
            ->where('VaiTro', 'TECHNICIAN')
            ->where('TrangThai', 'HOAT_DONG')
            ->select([
                'MaTK',
                'HoTen',
                'SoDienThoai',
            ])
            ->selectSub(
                DB::table('PHANCONG as pc')
                    ->join('LICHHEN as lh', 'lh.MaLH', '=', 'pc.MaLH')
                    ->whereColumn('pc.MaKTV', 'TAIKHOAN.MaTK')
                    ->whereIn('lh.TrangThai', $trangThaiDangPhuTrach)
                    ->selectRaw('COUNT(*)'),
                'SoLichDangPhuTrach'
            )
            ->selectSub(
                DB::table('PHANCONG as pc')
                    ->join('LICHHEN as lh', 'lh.MaLH', '=', 'pc.MaLH')
                    ->whereColumn('pc.MaKTV', 'TAIKHOAN.MaTK')
                    ->where('lh.NgayHen', $lichHen->NgayHen->toDateString())
                    ->where('lh.KhungGioHen', $lichHen->KhungGioHen)
                    ->whereIn('lh.TrangThai', $trangThaiDangPhuTrach)
                    ->selectRaw('COUNT(*)'),
                'SoLichCungKhung'
            )
            ->orderBy('SoLichCungKhung')
            ->orderBy('SoLichDangPhuTrach')
            ->orderBy('HoTen')
            ->get();

        return view('admin.lichhen.show', compact(
            'lichHen',
            'dichVuBanDau',
            'tongBanDau',
            'kyThuatViens'
        ));
    }

    public function assign(Request $request, $maLH)
    {
        if (is_string($request->input('GhiChu'))) {
            $request->merge([
                'GhiChu' => trim($request->input('GhiChu')),
            ]);
        }

        $data = $request->validate([
            'MaKTV' => [
                'bail',
                'required',
                'integer',
                Rule::exists('TAIKHOAN', 'MaTK')
                    ->where('VaiTro', 'TECHNICIAN')
                    ->where('TrangThai', 'HOAT_DONG'),
            ],
            'GhiChu' => ['nullable', 'string', 'max:1000'],
        ], [
            'MaKTV.required' => 'Vui lòng chọn kỹ thuật viên.',
            'MaKTV.integer' => 'Kỹ thuật viên không hợp lệ.',
            'MaKTV.exists' => 'Kỹ thuật viên không tồn tại hoặc đã ngừng hoạt động.',
            'GhiChu.string' => 'Ghi chú phải là văn bản.',
            'GhiChu.max' => 'Ghi chú không được vượt quá 1.000 ký tự.',
        ]);

        DB::transaction(function () use ($request, $data, $maLH) {
            $lichHen = LichHen::query()
                ->where('MaLH', $maLH)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lichHen->TrangThai !== LichHen::CHO_PHAN_CONG
                || PhanCong::where('MaLH', $lichHen->MaLH)->exists()
            ) {
                throw ValidationException::withMessages([
                    'MaKTV' => 'Lịch đã được phân công hoặc đã chuyển trạng thái. '
                        . 'Vui lòng tải lại trang.',
                ]);
            }

            $ktv = TaiKhoan::query()
                ->where('MaTK', $data['MaKTV'])
                ->where('VaiTro', 'TECHNICIAN')
                ->where('TrangThai', 'HOAT_DONG')
                ->lockForUpdate()
                ->first();

            if (! $ktv) {
                throw ValidationException::withMessages([
                    'MaKTV' => 'Kỹ thuật viên không còn hoạt động. '
                        . 'Vui lòng chọn người khác.',
                ]);
            }

            PhanCong::create([
                'MaLH' => $lichHen->MaLH,
                'MaKTV' => $ktv->MaTK,
                'MaAdmin' => $request->user()->MaTK,
                'NgayPhanCong' => now('Asia/Ho_Chi_Minh'),
                'GhiChu' => $data['GhiChu'] ?: null,
            ]);

            $lichHen->update([
                'TrangThai' => LichHen::CHO_NHAN_XE,
            ]);
        }, 3);

        return redirect()
            ->route('admin.lichhen.show', $maLH)
            ->with('success', 'Đã phân công kỹ thuật viên. Lịch đang chờ nhận xe.');
    }
}
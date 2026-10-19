<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Models\LichHen;
use App\Models\PhanCong;
use App\Models\PhieuKiemTra;
use App\Models\PhuTung;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KtvKiemTraController extends Controller
{
    public function edit(Request $request, $maLH)
    {
        $lichHen = $this->assignedTo($request)
            ->with([
                'xe',
                'taiKhoan',
                'phanCong',
                'phieuKiemTra',
                'chiTietLichHens',
            ])
            ->findOrFail($maLH);

        $phieu = $lichHen->phieuKiemTra;

        if (
            $lichHen->TrangThai !== LichHen::DANG_KIEM_TRA
            || (
                $phieu
                && $phieu->TrangThaiXacNhan !== PhieuKiemTra::NHAP
            )
        ) {
            return redirect()
                ->route('ktv.lichhen.show', $lichHen->MaLH)
                ->with(
                    'error',
                    'Lịch hiện không cho phép chỉnh sửa phiếu kiểm tra.'
                );
        }

        $chiTietDichVus = $lichHen->chiTietLichHens
            ->whereNotNull('MaDV')
            ->keyBy('MaDV');

        $chiTietPhuTungs = $lichHen->chiTietLichHens
            ->whereNotNull('MaPT')
            ->keyBy('MaPT');

        // Hiện cả hạng mục đã lưu nhưng hiện đã ngừng hoạt động.
        $dichVus = DichVu::query()
            ->with('loaiDichVu')
            ->where(function ($query) use ($chiTietDichVus) {
                $query->where(function ($query) {
                    $query->where('HoatDong', true)
                        ->whereHas('loaiDichVu', function ($query) {
                            $query->where('HoatDong', true);
                        });
                })->orWhereIn(
                    'MaDV',
                    $chiTietDichVus->keys()->all()
                );
            })
            ->orderBy('TenDV')
            ->get();

        $phuTungs = PhuTung::query()
            ->where(function ($query) use ($chiTietPhuTungs) {
                $query->where('HoatDong', true)
                    ->orWhereIn(
                        'MaPT',
                        $chiTietPhuTungs->keys()->all()
                    );
            })
            ->orderBy('TenPT')
            ->get();

        $dichVuDaChon = $chiTietDichVus
            ->filter(fn ($item) => $phieu
                ? $item->KTVDeXuat
                : $item->KhachDaChon)
            ->keys()
            ->map(fn ($id) => (string) $id)
            ->all();

        return view('ktv.kiemtra.edit', compact(
            'lichHen',
            'phieu',
            'dichVus',
            'phuTungs',
            'chiTietDichVus',
            'chiTietPhuTungs',
            'dichVuDaChon'
        ));
    }

    public function save(Request $request, $maLH)
    {
        // KTV chỉ được thao tác lịch được phân công cho mình.
        $this->assignedTo($request)->findOrFail($maLH);

        foreach (['KetLuan', 'GhiChu'] as $field) {
            if (is_string($request->input($field))) {
                $request->merge([
                    $field => trim($request->input($field)),
                ]);
            }
        }

        $data = $request->validate([
            'KetLuan' => [
                'bail',
                'required',
                'string',
                'max:5000',
            ],

            'GhiChu' => [
                'bail',
                'nullable',
                'string',
                'max:2000',
            ],

            'dich_vus' => [
                'nullable',
                'array',
                'max:50',
            ],

            'dich_vus.*' => [
                'bail',
                'required',
                'integer',
                'min:1',
                'distinct',
            ],

            'phu_tungs' => [
                'nullable',
                'array',
                'max:200',
            ],

            // Giới hạn số lượng thực tế được kiểm tra theo tồn kho bên dưới.
            'phu_tungs.*' => [
                'bail',
                'required',
                'integer',
                'min:0',
            ],
        ], [
            'KetLuan.required' => 'Vui lòng nhập kết luận kiểm tra.',
            'KetLuan.string' => 'Kết luận phải là văn bản.',
            'KetLuan.max' => 'Kết luận không được vượt quá 5.000 ký tự.',

            'GhiChu.string' => 'Ghi chú phải là văn bản.',
            'GhiChu.max' => 'Ghi chú không được vượt quá 2.000 ký tự.',

            'dich_vus.array' => 'Danh sách dịch vụ không hợp lệ.',
            'dich_vus.max' => 'Chỉ được chọn tối đa 50 dịch vụ.',
            'dich_vus.*.required' => 'Dịch vụ được chọn không hợp lệ.',
            'dich_vus.*.integer' => 'Dịch vụ được chọn không hợp lệ.',
            'dich_vus.*.min' => 'Dịch vụ được chọn không hợp lệ.',
            'dich_vus.*.distinct' => 'Danh sách dịch vụ bị trùng.',

            'phu_tungs.array' => 'Danh sách phụ tùng không hợp lệ.',
            'phu_tungs.max' => 'Danh sách phụ tùng vượt giới hạn.',
            'phu_tungs.*.required' =>
                'Vui lòng nhập số lượng, nhập 0 nếu không sử dụng.',
            'phu_tungs.*.integer' =>
                'Số lượng phụ tùng phải là số nguyên.',
            'phu_tungs.*.min' =>
                'Số lượng phụ tùng không được âm.',
        ]);

        $serviceIds = array_map(
            'intval',
            $data['dich_vus'] ?? []
        );

        if (count($serviceIds) !== count(array_unique($serviceIds))) {
            throw ValidationException::withMessages([
                'dich_vus' => 'Danh sách dịch vụ bị trùng.',
            ]);
        }

        $partQuantities = [];
        $seenPartIds = [];

        foreach ($data['phu_tungs'] ?? [] as $id => $quantity) {
            if (! ctype_digit((string) $id) || (int) $id < 1) {
                throw ValidationException::withMessages([
                    'phu_tungs' => 'Mã phụ tùng không hợp lệ.',
                ]);
            }

            $partId = (int) $id;

            if (isset($seenPartIds[$partId])) {
                throw ValidationException::withMessages([
                    'phu_tungs' => 'Danh sách phụ tùng bị trùng.',
                ]);
            }

            $seenPartIds[$partId] = true;

            if ((int) $quantity > 0) {
                $partQuantities[$partId] = (int) $quantity;
            }
        }

        DB::transaction(function () use (
            $request,
            $maLH,
            $data,
            $serviceIds,
            $partQuantities
        ) {
            $lichHen = LichHen::query()
                ->where('MaLH', $maLH)
                ->lockForUpdate()
                ->firstOrFail();

            $phanCong = PhanCong::query()
                ->where('MaLH', $lichHen->MaLH)
                ->where('MaKTV', $request->user()->MaTK)
                ->lockForUpdate()
                ->first();

            abort_unless($phanCong, 404);

            $phieu = PhieuKiemTra::query()
                ->where('MaLH', $lichHen->MaLH)
                ->lockForUpdate()
                ->first();

            if (
                $lichHen->TrangThai !== LichHen::DANG_KIEM_TRA
                || (
                    $phieu
                    && $phieu->TrangThaiXacNhan !== PhieuKiemTra::NHAP
                )
            ) {
                throw ValidationException::withMessages([
                    'KetLuan' => 'Phiếu đã được xử lý hoặc lịch đã chuyển trạng thái.',
                ]);
            }

            $services = DichVu::query()
                ->whereIn('MaDV', $serviceIds)
                ->where('HoatDong', true)
                ->whereHas('loaiDichVu', function ($query) {
                    $query->where('HoatDong', true);
                })
                ->orderBy('MaDV')
                ->lockForUpdate()
                ->get();

            if ($services->count() !== count($serviceIds)) {
                throw ValidationException::withMessages([
                    'dich_vus' => 'Có dịch vụ hoặc nhóm dịch vụ đã ngừng hoạt động. '
                        . 'Vui lòng bỏ chọn hạng mục đó.',
                ]);
            }

            // Khóa các dòng phụ tùng để đọc tồn kho tại thời điểm lưu.
            $parts = PhuTung::query()
                ->whereIn('MaPT', array_keys($partQuantities))
                ->where('HoatDong', true)
                ->orderBy('MaPT')
                ->lockForUpdate()
                ->get();

            $partErrors = [];

            $validPartIds = $parts
                ->pluck('MaPT')
                ->map(fn ($id) => (int) $id)
                ->all();

            foreach (array_keys($partQuantities) as $partId) {
                if (! in_array($partId, $validPartIds, true)) {
                    $partErrors["phu_tungs.{$partId}"] =
                        'Phụ tùng không tồn tại hoặc đã ngừng hoạt động. '
                        . 'Vui lòng bỏ phụ tùng này khỏi đề xuất.';
                }
            }

            foreach ($parts as $part) {
                $quantity = $partQuantities[$part->MaPT];

                if ($quantity > (int) $part->SoLuongTon) {
                    $partErrors["phu_tungs.{$part->MaPT}"] =
                        'Số lượng không được vượt quá tồn kho hiện tại '
                        . "({$part->SoLuongTon} {$part->DonViTinh}).";
                }
            }

            if ($partErrors !== []) {
                throw ValidationException::withMessages($partErrors);
            }

            $existing = $lichHen->chiTietLichHens()
                ->lockForUpdate()
                ->get();

            $existingServices = $existing
                ->whereNotNull('MaDV')
                ->keyBy('MaDV');

            $existingParts = $existing
                ->whereNotNull('MaPT')
                ->keyBy('MaPT');

            $selectedDetailIds = [];
            $tongTruocGiam = 0;

            foreach ($services as $service) {
                $row = $existingServices->get($service->MaDV);

                if ($row) {
                    // Giữ giá, số lượng và dấu vết khách chọn ban đầu.
                    $row->update([
                        'KTVDeXuat' => true,
                    ]);
                } else {
                    $row = $lichHen->chiTietLichHens()->create([
                        'MaDV' => $service->MaDV,
                        'MaPT' => null,
                        'KhachDaChon' => false,
                        'KTVDeXuat' => true,
                        'SoLuong' => 1,
                        'DonGia' => $service->Gia,
                    ]);
                }

                $selectedDetailIds[] = $row->MaCTLH;

                $tongTruocGiam +=
                    (int) $row->DonGia * $row->SoLuong;
            }

            foreach ($parts as $part) {
                $quantity = $partQuantities[$part->MaPT];
                $row = $existingParts->get($part->MaPT);

                if ($row) {
                    $row->update([
                        'KTVDeXuat' => true,
                        'SoLuong' => $quantity,
                    ]);
                } else {
                    $row = $lichHen->chiTietLichHens()->create([
                        'MaDV' => null,
                        'MaPT' => $part->MaPT,
                        'KhachDaChon' => false,
                        'KTVDeXuat' => true,
                        'SoLuong' => $quantity,
                        'DonGia' => $part->GiaBan,
                    ]);
                }

                $selectedDetailIds[] = $row->MaCTLH;

                $tongTruocGiam +=
                    (int) $row->DonGia * $row->SoLuong;
            }

            // Chỉ bỏ đề xuất ở những dòng không còn được chọn.
            // Không xóa lịch sử và không làm mất tick khi lưu nháp nhiều lần.
            $notSelected = $lichHen->chiTietLichHens();

            if ($selectedDetailIds !== []) {
                $notSelected->whereNotIn('MaCTLH', $selectedDetailIds);
            }

            $notSelected->update([
                'KTVDeXuat' => false,
            ]);

            $values = [
                'MaPC' => $phanCong->MaPC,
                'KetLuan' => $data['KetLuan'],
                'GhiChu' => $data['GhiChu'] ?? null,
                'TrangThaiXacNhan' => PhieuKiemTra::NHAP,

                // Bản nháp hiện tính tổng trước ưu đãi.
                'LoaiUuDai' => PhieuKiemTra::UU_DAI_KHONG,
                'TyLeGiam' => 0,
                'DiemSuDung' => 0,
                'TongTruocGiam' => $tongTruocGiam,
                'GiaTriDuocGiam' => 0,
                'TienGiam' => 0,
                'TongSauGiam' => $tongTruocGiam,
            ];

            if ($phieu) {
                $phieu->update($values);
            } else {
                PhieuKiemTra::create([
                    ...$values,
                    'MaLH' => $lichHen->MaLH,
                    'NgayKiemTra' => now('Asia/Ho_Chi_Minh'),
                ]);
            }
        }, 3);

        return redirect()
            ->route('ktv.kiemtra.edit', $maLH)
            ->with('success', 'Đã lưu nháp kết quả kiểm tra.');
    }

    private function assignedTo(Request $request): Builder
    {
        return LichHen::query()
            ->whereHas('phanCong', function ($query) use ($request) {
                $query->where('MaKTV', $request->user()->MaTK);
            });
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\LichHen;
use App\Models\PhanCong;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KtvLichHenController extends Controller
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

        $lichHens = $this->assignedTo($request)
            ->with(['taiKhoan', 'xe'])
            ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                $query->where(function ($query) use ($tuKhoa) {
                    $query->whereHas('xe', function ($query) use ($tuKhoa) {
                        $query->where('BienSo', 'like', "%{$tuKhoa}%");
                    })->orWhereHas('taiKhoan', function ($query) use ($tuKhoa) {
                        $query->where('HoTen', 'like', "%{$tuKhoa}%")
                            ->orWhere('SoDienThoai', 'like', "%{$tuKhoa}%");
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
                'CASE WHEN TrangThai IN (?, ?) THEN 0 '
                    . 'WHEN TrangThai IN (?, ?) THEN 2 ELSE 1 END',
                [
                    LichHen::CHO_NHAN_XE,
                    LichHen::DANG_KIEM_TRA,
                    LichHen::HOAN_TAT,
                    LichHen::DA_HUY,
                ]
            )
            ->orderBy('NgayHen')
            ->orderBy('KhungGioHen')
            ->orderByDesc('MaLH')
            ->paginate(10)
            ->withQueryString();

        if ($lichHens->currentPage() > $lichHens->lastPage()) {
            return redirect()->route(
                'ktv.lichhen.index',
                array_merge(
                    $request->only('tu_khoa', 'ngay_hen', 'trang_thai'),
                    ['page' => $lichHens->lastPage()]
                )
            );
        }

        $soLieu = $this->assignedTo($request)
    ->select('TrangThai')
    ->selectRaw('COUNT(*) AS Tong')
    ->groupBy('TrangThai')
    ->pluck('Tong', 'TrangThai');

return view('ktv.lichhen.index', compact(
    'lichHens',
    'tuKhoa',
    'ngayHen',
    'trangThai',
    'soLieu'
));
    }

    public function show(Request $request, $maLH)
    {
        $lichHen = $this->assignedTo($request)
            ->with([
                'taiKhoan',
                'xe',
                'chiTietLichHens.dichVu',
                'phanCong.admin',
            ])
            ->findOrFail($maLH);

        $dichVuBanDau = $lichHen->chiTietLichHens
            ->where('KhachDaChon', true)
            ->whereNotNull('MaDV');

        return view('ktv.lichhen.show', compact(
            'lichHen',
            'dichVuBanDau'
        ));
    }

    public function receive(Request $request, $maLH)
    {
        DB::transaction(function () use ($request, $maLH) {
            $lichHen = LichHen::query()
                ->where('MaLH', $maLH)
                ->lockForUpdate()
                ->firstOrFail();

            $phanCong = PhanCong::query()
                ->where('MaLH', $lichHen->MaLH)
                ->where('MaKTV', $request->user()->MaTK)
                ->lockForUpdate()
                ->first();

            // Không cho KTV truy cập lịch của người khác.
            abort_unless($phanCong, 404);

            if (
                $lichHen->TrangThai !== LichHen::CHO_NHAN_XE
                || $lichHen->NgayNhanXe !== null
            ) {
                throw ValidationException::withMessages([
                    'nhan_xe' => 'Lịch không còn ở trạng thái chờ nhận xe. '
                        . 'Vui lòng kiểm tra lại.',
                ]);
            }

            $now = CarbonImmutable::now('Asia/Ho_Chi_Minh');

            if (
                $lichHen->NgayHen->format('Y-m-d')
                > $now->toDateString()
            ) {
                throw ValidationException::withMessages([
                    'nhan_xe' => 'Chưa đến ngày hẹn. '
                        . 'Chỉ xác nhận nhận xe khi khách đã mang xe tới gara.',
                ]);
            }

            $lichHen->update([
                'TrangThai' => LichHen::DANG_KIEM_TRA,
                'NgayNhanXe' => $now,
            ]);
        }, 3);

        return redirect()
            ->route('ktv.lichhen.show', $maLH)
            ->with('success', 'Đã nhận xe. Bạn có thể tiến hành kiểm tra.');
    }

    private function assignedTo(Request $request): Builder
    {
        return LichHen::query()
            ->whereHas('phanCong', function ($query) use ($request) {
                $query->where('MaKTV', $request->user()->MaTK);
            });
    }
}
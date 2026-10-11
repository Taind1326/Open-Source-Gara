<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Models\Xe;
use App\Models\YeuCauSuaChua;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DatLichController extends Controller
{
    public function index(Request $request)
    {
        $lichHens = YeuCauSuaChua::with(['xe', 'dichVu'])
            ->whereHas('xe', function ($query) use ($request) {
                $query->where('MaTK', $request->user()->MaTK);
            })
            ->orderByDesc('MaYC')
            ->paginate(10)
            ->withQueryString();

        return view('public.datlich.index', compact('lichHens'));
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'ma_dv' => ['nullable', 'integer', 'min:1'],
        ]);

        $xes = Xe::where('MaTK', $request->user()->MaTK)
            ->where('TrangThai', 'HOAT_DONG')
            ->orderBy('BienSo')
            ->get();

        $dichVus = $this->activeServices()
            ->with('loaiDichVu')
            ->orderBy('TenDV')
            ->get();

        $maDV = $data['ma_dv'] ?? null;

        if ($maDV && ! $dichVus->contains('MaDV', (int) $maDV)) {
            abort(404);
        }

        $today = CarbonImmutable::now(config('booking.timezone'));

        $minDate = $today->toDateString();

        $maxDate = $today
            ->addDays((int) config('booking.max_days_ahead', 60))
            ->toDateString();

        return view('public.datlich.create', compact(
            'xes',
            'dichVus',
            'maDV',
            'minDate',
            'maxDate'
        ));
    }

    public function slots(Request $request)
    {
        $data = $request->validate([
            'NgayHen' => $this->dateRules(),
        ]);

        $counts = YeuCauSuaChua::whereDate('NgayHen', $data['NgayHen'])
            ->whereNotIn(
                'TrangThai',
                config('booking.released_statuses', [])
            )
            ->select('KhungGioHen')
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('KhungGioHen')
            ->pluck('total', 'KhungGioHen');

        $capacity = max(
            1,
            (int) config('booking.capacity_per_slot', 3)
        );

        $slots = collect(config('booking.slots', []))
            ->map(function ($slot) use ($data, $counts, $capacity) {
                $remaining = max(
                    0,
                    $capacity - (int) ($counts[$slot] ?? 0)
                );

                $past = $this->slotHasStarted($data['NgayHen'], $slot);

                return [
                    'value' => $slot,
                    'remaining' => $remaining,
                    'available' => ! $past && $remaining > 0,
                    'label' => $slot . (
                        $past
                            ? ' — Đã qua giờ tiếp nhận'
                            : ($remaining > 0
                                ? " — Còn {$remaining} chỗ"
                                : ' — Hết chỗ')
                    ),
                ];
            })
            ->values();

        return response()->json([
            'data' => $slots,
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'MoTa' => trim((string) $request->input('MoTa')),
        ]);

        $data = $request->validate([
            'MaXe' => ['required', 'integer', 'min:1'],
            'NgayHen' => $this->dateRules(),
            'KhungGioHen' => [
                'required',
                Rule::in(config('booking.slots', [])),
            ],
            'dich_vus' => ['required', 'array', 'min:1', 'max:20'],
            'dich_vus.*' => ['required', 'integer', 'distinct', 'min:1'],
            'MoTa' => ['nullable', 'string', 'max:2000'],
        ], [
            'MaXe.required' => 'Vui lòng chọn xe.',
            'NgayHen.required' => 'Vui lòng chọn ngày hẹn.',
            'NgayHen.date_format' => 'Ngày hẹn không hợp lệ.',
            'NgayHen.after_or_equal' => 'Không được đặt lịch vào ngày quá khứ.',
            'NgayHen.before_or_equal' => 'Ngày hẹn vượt thời gian được phép đặt.',
            'KhungGioHen.required' => 'Vui lòng chọn khung giờ.',
            'KhungGioHen.in' => 'Khung giờ không hợp lệ.',
            'dich_vus.required' => 'Vui lòng chọn ít nhất một dịch vụ.',
            'dich_vus.min' => 'Vui lòng chọn ít nhất một dịch vụ.',
            'dich_vus.max' => 'Mỗi yêu cầu được chọn tối đa 20 dịch vụ.',
            'dich_vus.*.distinct' => 'Danh sách dịch vụ bị trùng.',
            'MoTa.max' => 'Mô tả không được vượt quá 2.000 ký tự.',
        ]);

        $serviceIds = array_map('intval', $data['dich_vus']);

        if ($this->slotHasStarted($data['NgayHen'], $data['KhungGioHen'])) {
            throw ValidationException::withMessages([
                'KhungGioHen' => 'Khung giờ này đã bắt đầu. Vui lòng chọn giờ khác.',
            ]);
        }

        // Tạo hàng khóa nếu khung giờ chưa từng được đặt.
        DB::table('booking_slots')->insertOrIgnore([
            'booking_date' => $data['NgayHen'],
            'time_slot' => $data['KhungGioHen'],
        ]);

        DB::transaction(function () use ($request, $data, $serviceIds) {
            // Các yêu cầu cùng khung giờ phải lần lượt kiểm tra số chỗ.
            DB::table('booking_slots')
                ->where('booking_date', $data['NgayHen'])
                ->where('time_slot', $data['KhungGioHen'])
                ->lockForUpdate()
                ->first();

            $xe = Xe::where('MaTK', $request->user()->MaTK)
                ->where('MaXe', $data['MaXe'])
                ->where('TrangThai', 'HOAT_DONG')
                ->lockForUpdate()
                ->first();

            if (! $xe) {
                throw ValidationException::withMessages([
                    'MaXe' => 'Xe không thuộc tài khoản hoặc đã ngừng sử dụng.',
                ]);
            }

            // Kiểm tra lại thời gian sau khi chờ khóa.
            if ($this->slotHasStarted($data['NgayHen'], $data['KhungGioHen'])) {
                throw ValidationException::withMessages([
                    'KhungGioHen' => 'Khung giờ đã bắt đầu. Vui lòng chọn giờ khác.',
                ]);
            }

            $used = YeuCauSuaChua::whereDate('NgayHen', $data['NgayHen'])
                ->where('KhungGioHen', $data['KhungGioHen'])
                ->whereNotIn(
                    'TrangThai',
                    config('booking.released_statuses', [])
                )
                ->count();

            $capacity = max(
                1,
                (int) config('booking.capacity_per_slot', 3)
            );

            if ($used >= $capacity) {
                throw ValidationException::withMessages([
                    'KhungGioHen' => 'Khung giờ vừa hết chỗ. Vui lòng chọn giờ khác.',
                ]);
            }

            $hasOpenRequest = $xe->yeuCauSuaChuas()
                ->whereNotIn('TrangThai', [
                    'HOAN_THANH',
                    'TU_CHOI',
                    'DA_HUY',
                ])
                ->exists();

            if ($hasOpenRequest) {
                throw ValidationException::withMessages([
                    'MaXe' => 'Xe đang có yêu cầu chưa kết thúc. '
                        . 'Vui lòng hoàn tất hoặc hủy yêu cầu cũ trước.',
                ]);
            }

            $services = $this->activeServices()
                ->whereIn('MaDV', $serviceIds)
                ->lockForUpdate()
                ->get();

            if ($services->count() !== count($serviceIds)) {
                throw ValidationException::withMessages([
                    'dich_vus' => 'Có dịch vụ không còn hoạt động. '
                        . 'Vui lòng tải lại trang và chọn lại.',
                ]);
            }

            $lichHen = YeuCauSuaChua::create([
                'MaXe' => $xe->MaXe,
                'NgayHen' => $data['NgayHen'],
                'KhungGioHen' => $data['KhungGioHen'],
                'MoTa' => $data['MoTa'] ?: null,
                'HinhAnh' => null,
                'TrangThai' => 'CHO_PHAN_CONG',
            ]);

            $lichHen->dichVu()->attach($serviceIds);
        }, 3);

        return redirect()
            ->route('public.datlich.index')
            ->with('success', 'Đã gửi lịch hẹn. Gara sẽ tiếp nhận và phân công kỹ thuật viên.');
    }

    public function cancel(Request $request, $maYC)
    {
        $lichHen = YeuCauSuaChua::whereHas(
            'xe',
            function ($query) use ($request) {
                $query->where('MaTK', $request->user()->MaTK);
            }
        )->findOrFail($maYC);

        DB::transaction(function () use ($request, $lichHen) {
            $xe = Xe::where('MaTK', $request->user()->MaTK)
                ->where('MaXe', $lichHen->MaXe)
                ->lockForUpdate()
                ->firstOrFail();

            $current = YeuCauSuaChua::where('MaYC', $lichHen->MaYC)
                ->where('MaXe', $xe->MaXe)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array(
                $current->TrangThai,
                config('booking.customer_cancel_statuses', []),
                true
            )) {
                throw ValidationException::withMessages([
                    'lich_hen' => 'Yêu cầu đã được xử lý nên không thể tự hủy. '
                        . 'Vui lòng liên hệ gara.',
                ]);
            }

            $current->update([
                'TrangThai' => 'DA_HUY',
            ]);
        }, 3);

        return redirect()
            ->route('public.datlich.index')
            ->with('success', 'Đã hủy lịch hẹn.');
    }

    private function activeServices()
    {
        return DichVu::where('TrangThai', 'HOAT_DONG')
            ->whereHas('loaiDichVu', function ($query) {
                $query->where('TrangThai', 'HOAT_DONG');
            });
    }

    private function dateRules(): array
    {
        $today = CarbonImmutable::now(config('booking.timezone'))
            ->startOfDay();

        return [
            'required',
            'date_format:Y-m-d',
            'after_or_equal:' . $today->toDateString(),
            'before_or_equal:' . $today
                ->addDays((int) config('booking.max_days_ahead', 60))
                ->toDateString(),
        ];
    }

    private function slotHasStarted(string $date, string $slot): bool
    {
        $startTime = explode('-', $slot)[0];

        $start = CarbonImmutable::createFromFormat(
            '!Y-m-d H:i',
            $date . ' ' . $startTime,
            config('booking.timezone')
        );

        return $start->lessThanOrEqualTo(
            CarbonImmutable::now(config('booking.timezone'))
        );
    }
}
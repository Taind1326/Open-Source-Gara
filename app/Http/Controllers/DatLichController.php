<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Models\LichHen;
use App\Models\Xe;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DatLichController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $lichHens = LichHen::query()
            ->with([
                'xe',
                'chiTietLichHens.dichVu',
            ])
            ->where('MaTK', $request->user()->MaTK)
            ->orderByDesc('MaLH')
            ->paginate(10)
            ->withQueryString();

        if ($lichHens->currentPage() > $lichHens->lastPage()) {
            return redirect()->route('public.datlich.index', [
                'page' => $lichHens->lastPage(),
            ]);
        }

        return view('public.datlich.index', compact('lichHens'));
    }

    public function create(Request $request)
    {
        $data = $request->validate([
            'ma_dv' => ['nullable', 'integer', 'min:1'],
        ]);

        $xes = Xe::query()
            ->where('MaTK', $request->user()->MaTK)
            ->orderBy('BienSo')
            ->get();

        $dichVus = $this->activeServices()
            ->with('loaiDichVu')
            ->orderBy('TenDV')
            ->get();

        $maDV = isset($data['ma_dv'])
            ? (int) $data['ma_dv']
            : null;

        if ($maDV && ! $dichVus->contains('MaDV', $maDV)) {
            abort(404);
        }

        $today = CarbonImmutable::now($this->timezone());

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
        ], $this->validationMessages());

        $counts = LichHen::query()
            ->where('NgayHen', $data['NgayHen'])
            ->where('TrangThai', '<>', LichHen::DA_HUY)
            ->select('KhungGioHen')
            ->selectRaw('COUNT(*) AS total')
            ->groupBy('KhungGioHen')
            ->pluck('total', 'KhungGioHen');

        $capacity = $this->capacity();

        $slots = collect(LichHen::KHUNG_GIO_HEN)
            ->map(function ($slot) use ($data, $counts, $capacity) {
                $remaining = max(
                    0,
                    $capacity - (int) ($counts[$slot] ?? 0)
                );

                $past = $this->slotHasStarted(
                    $data['NgayHen'],
                    $slot
                );

                return [
                    'value' => $slot,
                    'remaining' => $remaining,
                    'available' => ! $past && $remaining > 0,
                    'label' => $slot . (
                        $past
                            ? ' — Đã qua giờ tiếp nhận'
                            : (
                                $remaining > 0
                                    ? " — Còn {$remaining} chỗ"
                                    : ' — Hết chỗ'
                            )
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
        if (is_string($request->input('MoTa'))) {
            $request->merge([
                'MoTa' => trim($request->input('MoTa')),
            ]);
        }

        $data = $request->validate([
            'MaXe' => ['bail', 'required', 'integer', 'min:1'],

            'NgayHen' => $this->dateRules(),

            'KhungGioHen' => [
                'required',
                'string',
                Rule::in(LichHen::KHUNG_GIO_HEN),
            ],

            'dich_vus' => [
                'required',
                'array',
                'min:1',
                'max:20',
            ],

            'dich_vus.*' => [
                'bail',
                'required',
                'integer',
                'min:1',
                'distinct',
            ],

            'MoTa' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ], $this->validationMessages());

        $serviceIds = array_map('intval', $data['dich_vus']);

        // Kiểm tra trùng sau khi chuyển ID về số nguyên.
        if (count($serviceIds) !== count(array_unique($serviceIds))) {
            throw ValidationException::withMessages([
                'dich_vus' => 'Danh sách dịch vụ bị trùng.',
            ]);
        }

        $connection = DB::connection();

        $lockName = 'garage.booking.'
            . $data['NgayHen']
            . '.'
            . $data['KhungGioHen'];

        // GET_LOCK và RELEASE_LOCK phải dùng cùng kết nối.
        $lock = $connection->selectOne(
            'SELECT GET_LOCK(?, 5) AS acquired',
            [$lockName],
            false
        );

        if ((int) ($lock->acquired ?? 0) !== 1) {
            throw ValidationException::withMessages([
                'KhungGioHen' => 'Khung giờ đang được cập nhật. '
                    . 'Vui lòng thử gửi lại.',
            ]);
        }

        try {
            $connection->transaction(function () use (
                $request,
                $data,
                $serviceIds
            ) {
                $xe = Xe::query()
                    ->where('MaTK', $request->user()->MaTK)
                    ->where('MaXe', $data['MaXe'])
                    ->lockForUpdate()
                    ->first();

                if (! $xe) {
                    throw ValidationException::withMessages([
                        'MaXe' => 'Xe không thuộc tài khoản của bạn.',
                    ]);
                }

                // Kiểm tra lại sau khi đã chờ khóa.
                if ($this->slotHasStarted(
                    $data['NgayHen'],
                    $data['KhungGioHen']
                )) {
                    throw ValidationException::withMessages([
                        'KhungGioHen' => 'Khung giờ đã bắt đầu. '
                            . 'Vui lòng chọn giờ khác.',
                    ]);
                }

                $used = LichHen::query()
                    ->where('NgayHen', $data['NgayHen'])
                    ->where('KhungGioHen', $data['KhungGioHen'])
                    ->where('TrangThai', '<>', LichHen::DA_HUY)
                    ->count();

                if ($used >= $this->capacity()) {
                    throw ValidationException::withMessages([
                        'KhungGioHen' => 'Khung giờ vừa hết chỗ. '
                            . 'Vui lòng chọn giờ khác.',
                    ]);
                }

                $hasOpenAppointment = LichHen::query()
                    ->where('MaXe', $xe->MaXe)
                    ->whereNotIn('TrangThai', [
                        LichHen::HOAN_TAT,
                        LichHen::DA_HUY,
                    ])
                    ->exists();

                if ($hasOpenAppointment) {
                    throw ValidationException::withMessages([
                        'MaXe' => 'Xe đang có lịch hẹn chưa kết thúc. '
                            . 'Vui lòng hoàn tất hoặc hủy lịch cũ trước.',
                    ]);
                }

                $services = $this->activeServices()
                    ->whereIn('MaDV', $serviceIds)
                    ->orderBy('MaDV')
                    ->lockForUpdate()
                    ->get();

                if ($services->count() !== count($serviceIds)) {
                    throw ValidationException::withMessages([
                        'dich_vus' => 'Có dịch vụ không còn hoạt động. '
                            . 'Vui lòng tải lại trang và chọn lại.',
                    ]);
                }

                $lichHen = LichHen::create([
                    'MaTK' => $request->user()->MaTK,
                    'MaXe' => $xe->MaXe,
                    'NgayHen' => $data['NgayHen'],
                    'KhungGioHen' => $data['KhungGioHen'],
                    'MoTaBanDau' => $data['MoTa'] ?? null,
                    'TrangThai' => LichHen::CHO_PHAN_CONG,
                ]);

                foreach ($services as $service) {
                    $lichHen->chiTietLichHens()->create([
                        'MaDV' => $service->MaDV,
                        'MaPT' => null,
                        'KhachDaChon' => true,
                        'KTVDeXuat' => false,
                        'SoLuong' => 1,
                        'DonGia' => $service->Gia,
                    ]);
                }
            }, 3);
        } finally {
            $connection->selectOne(
                'SELECT RELEASE_LOCK(?) AS released',
                [$lockName],
                false
            );
        }

        return redirect()
            ->route('public.datlich.index')
            ->with(
                'success',
                'Đã gửi lịch hẹn. Gara sẽ phân công kỹ thuật viên kiểm tra xe.'
            );
    }

    public function cancel(Request $request, $maLH)
    {
        $lichHen = LichHen::query()
            ->where('MaTK', $request->user()->MaTK)
            ->findOrFail($maLH);

        DB::transaction(function () use ($request, $lichHen) {
            // Khóa xe trước, cùng thứ tự với thao tác đặt lịch.
            Xe::query()
                ->where('MaTK', $request->user()->MaTK)
                ->where('MaXe', $lichHen->MaXe)
                ->lockForUpdate()
                ->firstOrFail();

            $current = LichHen::query()
                ->where('MaTK', $request->user()->MaTK)
                ->where('MaLH', $lichHen->MaLH)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($current->TrangThai, [
                LichHen::CHO_PHAN_CONG,
                LichHen::CHO_NHAN_XE,
            ], true)) {
                throw ValidationException::withMessages([
                    'lich_hen' => 'Gara đã nhận xe hoặc lịch đã kết thúc, '
                        . 'bạn không thể tự hủy tại đây. '
                        . 'Vui lòng liên hệ gara để được hỗ trợ.',
                ]);
            }

            $current->update([
                'TrangThai' => LichHen::DA_HUY,
                'LyDoHuy' => 'Khách hàng chủ động hủy trước khi gara nhận xe.',
                'NgayHuy' => CarbonImmutable::now($this->timezone()),
            ]);
        }, 3);

        return redirect()
            ->route('public.datlich.index')
            ->with('success', 'Đã hủy lịch hẹn.');
    }

    private function activeServices(): Builder
    {
        return DichVu::query()
            ->where('HoatDong', true)
            ->whereHas('loaiDichVu', function ($query) {
                $query->where('HoatDong', true);
            });
    }

    private function dateRules(): array
    {
        $today = CarbonImmutable::now($this->timezone())
            ->startOfDay();

        return [
            'bail',
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
            $this->timezone()
        );

        return $start->lessThanOrEqualTo(
            CarbonImmutable::now($this->timezone())
        );
    }

    private function capacity(): int
    {
        return max(
            1,
            (int) config('booking.capacity_per_slot', 3)
        );
    }

    private function timezone(): string
    {
        return config('booking.timezone', 'Asia/Ho_Chi_Minh');
    }

    private function validationMessages(): array
    {
        return [
            'MaXe.required' => 'Vui lòng chọn xe.',
            'MaXe.integer' => 'Xe được chọn không hợp lệ.',
            'MaXe.min' => 'Xe được chọn không hợp lệ.',

            'NgayHen.required' => 'Vui lòng chọn ngày hẹn.',
            'NgayHen.date_format' => 'Ngày hẹn không hợp lệ.',
            'NgayHen.after_or_equal' => 'Không được đặt lịch vào ngày quá khứ.',
            'NgayHen.before_or_equal' => 'Chỉ được đặt trước tối đa '
                . config('booking.max_days_ahead', 60) . ' ngày.',

            'KhungGioHen.required' => 'Vui lòng chọn khung giờ.',
            'KhungGioHen.string' => 'Khung giờ không hợp lệ.',
            'KhungGioHen.in' => 'Khung giờ không hợp lệ.',

            'dich_vus.required' => 'Vui lòng chọn ít nhất một dịch vụ.',
            'dich_vus.array' => 'Danh sách dịch vụ không hợp lệ.',
            'dich_vus.min' => 'Vui lòng chọn ít nhất một dịch vụ.',
            'dich_vus.max' => 'Mỗi lịch hẹn được chọn tối đa 20 dịch vụ.',
            'dich_vus.*.required' => 'Dịch vụ được chọn không hợp lệ.',
            'dich_vus.*.integer' => 'Dịch vụ được chọn không hợp lệ.',
            'dich_vus.*.min' => 'Dịch vụ được chọn không hợp lệ.',
            'dich_vus.*.distinct' => 'Danh sách dịch vụ bị trùng.',

            'MoTa.string' => 'Mô tả phải là văn bản.',
            'MoTa.max' => 'Mô tả không được vượt quá 2.000 ký tự.',
        ];
    }
}
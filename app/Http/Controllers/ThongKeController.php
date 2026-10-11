<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThongKeController extends Controller
{
    private function dbDate(CarbonImmutable $date): string
    {
        return $date
            ->setTimezone(config('app.timezone', 'UTC'))
            ->format('Y-m-d H:i:s');
    }

    private function paidInvoices(
        CarbonImmutable $start,
        CarbonImmutable $end
    ) {
        return DB::table('HOADON')
            ->where('TrangThaiThanhToan', 'DA_THANH_TOAN')
            ->whereBetween('NgayThanhToan', [
                $this->dbDate($start),
                $this->dbDate($end),
            ]);
    }

    private function ranking(
        string $type,
        CarbonImmutable $start,
        CarbonImmutable $end
    ) {
        $isService = $type === 'service';
        $table = $isService ? 'DICHVU' : 'PHUTUNG';
        $key = $isService ? 'MaDV' : 'MaPT';
        $name = $isService ? 'TenDV' : 'TenPT';

        return DB::table('CHITIETSUACHUA as ct')
            ->join("$table as dm", "ct.$key", '=', "dm.$key")
            ->join('HOADON as hd', 'ct.MaPSC', '=', 'hd.MaPSC')
            ->where('hd.TrangThaiThanhToan', 'DA_THANH_TOAN')
            ->whereBetween('hd.NgayThanhToan', [
                $this->dbDate($start),
                $this->dbDate($end),
            ])
            ->select("dm.$key as Ma", "dm.$name as Ten")
            ->selectRaw('SUM(ct.SoLuong) AS SoLuong')
            ->selectRaw(
                'SUM(
                    ct.SoLuong * ct.DonGia *
                    CASE
                        WHEN hd.TongTienGoc > 0
                        THEN hd.TongThanhToan / hd.TongTienGoc
                        ELSE 0
                    END
                ) AS DoanhThu'
            )
            ->groupBy("dm.$key", "dm.$name")
            ->orderByDesc('DoanhThu')
            ->orderBy("dm.$key")
            ->get();
    }

    public function index(Request $request)
    {
        $today = CarbonImmutable::now('Asia/Ho_Chi_Minh');

        $request->validate([
            'tu_ngay' => [
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:' . $today->format('Y-m-d'),
            ],
            'den_ngay' => [
                'nullable',
                'date_format:Y-m-d',
                'before_or_equal:' . $today->format('Y-m-d'),
            ],
        ], [
            '*.date_format' => 'Ngày phải có định dạng hợp lệ.',
            '*.before_or_equal' => 'Không chọn ngày trong tương lai.',
        ]);

        $tuNgay = $request->input('tu_ngay')
            ?: $today->startOfMonth()->format('Y-m-d');

        $denNgay = $request->input('den_ngay')
            ?: $today->format('Y-m-d');

        $start = CarbonImmutable::createFromFormat(
            '!Y-m-d',
            $tuNgay,
            'Asia/Ho_Chi_Minh'
        )->startOfDay();

        $end = CarbonImmutable::createFromFormat(
            '!Y-m-d',
            $denNgay,
            'Asia/Ho_Chi_Minh'
        )->endOfDay();

        if ($start->gt($end)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'den_ngay' => 'Ngày kết thúc phải từ ngày bắt đầu trở đi.',
            ]);
        }

        if ($start->addDays(365)->lt($end->startOfDay())) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'den_ngay' => 'Mỗi lần thống kê chọn tối đa 366 ngày.',
            ]);
        }

        $paid = $this->paidInvoices($start, $end)->get();

        $doanhThu = (float) $paid->sum('TongThanhToan');
        $soHoaDonThanhToan = $paid->count();

        $tongYeuCau = DB::table('YEUCAUSUACHUA')
            ->whereBetween('NgayTao', [
                $this->dbDate($start),
                $this->dbDate($end),
            ])
            ->count();

        $soHoaDonChuaThanhToan = DB::table('HOADON')
            ->where('TrangThaiThanhToan', 'CHUA_THANH_TOAN')
            ->whereBetween('NgayLap', [
                $this->dbDate($start),
                $this->dbDate($end),
            ])
            ->count();

        // Khởi tạo cả những ngày không phát sinh doanh thu.
        $daily = [];

        for ($day = $start; $day->lte($end); $day = $day->addDay()) {
            $daily[$day->format('Y-m-d')] = 0;
        }

        foreach ($paid as $invoice) {
            $day = CarbonImmutable::parse(
                $invoice->NgayThanhToan,
                config('app.timezone', 'UTC')
            )
                ->setTimezone('Asia/Ho_Chi_Minh')
                ->format('Y-m-d');

            if (array_key_exists($day, $daily)) {
                $daily[$day] += (float) $invoice->TongThanhToan;
            }
        }

        $dailyLabels = array_map(
            fn ($day) => CarbonImmutable::parse($day)->format('d/m'),
            array_keys($daily)
        );

        $dailyValues = array_values($daily);

        // So sánh hai tháng theo cùng số ngày, giới hạn theo tháng ngắn hơn.
        $monthStart = $end->startOfMonth();
        $previousStart = $monthStart->subMonth();

        $comparisonDays = min(
            $end->day,
            $previousStart->daysInMonth
        );

        $currentCutoff = $monthStart
            ->addDays($comparisonDays - 1)
            ->endOfDay();

        $previousCutoff = $previousStart
            ->addDays($comparisonDays - 1)
            ->endOfDay();

        $currentRevenue = (float) $this
            ->paidInvoices($monthStart, $currentCutoff)
            ->sum('TongThanhToan');

        $previousRevenue = (float) $this
            ->paidInvoices($previousStart, $previousCutoff)
            ->sum('TongThanhToan');

        $difference = $currentRevenue - $previousRevenue;

        $growth = $previousRevenue > 0
            ? round($difference / $previousRevenue * 100, 1)
            : null;

        $currentMonthLabel = $monthStart->format('m/Y');
        $previousMonthLabel = $previousStart->format('m/Y');

        // Sáu tháng kết thúc ở tháng chứa ngày kết thúc bộ lọc.
        $sixMonthStart = $monthStart->subMonths(5);
        $monthly = [];

        for ($i = 0; $i < 6; $i++) {
            $monthly[$sixMonthStart->addMonths($i)->format('Y-m')] = 0;
        }

        $monthlyInvoices = $this
            ->paidInvoices($sixMonthStart, $end)
            ->get(['NgayThanhToan', 'TongThanhToan']);

        foreach ($monthlyInvoices as $invoice) {
            $month = CarbonImmutable::parse(
                $invoice->NgayThanhToan,
                config('app.timezone', 'UTC')
            )
                ->setTimezone('Asia/Ho_Chi_Minh')
                ->format('Y-m');

            if (array_key_exists($month, $monthly)) {
                $monthly[$month] += (float) $invoice->TongThanhToan;
            }
        }

        $monthlyLabels = array_map(
            fn ($month) => CarbonImmutable::parse($month . '-01')->format('m/Y'),
            array_keys($monthly)
        );

        $monthlyValues = array_values($monthly);

        $allServices = $this->ranking('service', $start, $end);
        $allParts = $this->ranking('part', $start, $end);

        $topServices = $allServices->take(10)->values();
        $topParts = $allParts->take(10)->values();

        $tongLuotDichVu = $allServices->sum('SoLuong');
        $tongLuotPhuTung = $allParts->sum('SoLuong');

        $chartData = [
            'dailyLabels' => $dailyLabels,
            'dailyValues' => $dailyValues,
            'monthlyLabels' => $monthlyLabels,
            'monthlyValues' => $monthlyValues,
            'comparisonLabels' => [
                $previousMonthLabel,
                $currentMonthLabel,
            ],
            'comparisonValues' => [
                $previousRevenue,
                $currentRevenue,
            ],
        ];

        return view('thongke.index', compact(
            'tuNgay',
            'denNgay',
            'doanhThu',
            'tongYeuCau',
            'soHoaDonThanhToan',
            'soHoaDonChuaThanhToan',
            'tongLuotDichVu',
            'tongLuotPhuTung',
            'topServices',
            'topParts',
            'currentRevenue',
            'previousRevenue',
            'difference',
            'growth',
            'comparisonDays',
            'currentMonthLabel',
            'previousMonthLabel',
            'chartData'
        ));
    }
}
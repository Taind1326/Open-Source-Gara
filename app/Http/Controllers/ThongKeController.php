<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThongKeController extends Controller
{
    public function index(Request $request)
    {
        $now = CarbonImmutable::now('Asia/Ho_Chi_Minh');

        $data = $request->validate([
            'nam' => [
                'nullable',
                'integer',
                'min:1900',
                'max:' . $now->year,
            ],
        ], [
            'nam.integer' => 'Năm thống kê phải là số nguyên.',
            'nam.min' => 'Năm thống kê phải từ 1900.',
            'nam.max' => 'Không chọn năm lớn hơn năm hiện tại.',
        ]);

        $nam = (int) ($data['nam'] ?? $now->year);

        $start = CarbonImmutable::create(
            $nam,
            1,
            1,
            0,
            0,
            0,
            'Asia/Ho_Chi_Minh'
        );

        $end = $nam === $now->year
            ? $now
            : $start->addYear();

        $summary = $this->paidIn($start, $end)
            ->selectRaw(
                'COUNT(*) AS SoHoaDon, '
                . 'COALESCE(SUM(hd.TongTien), 0) AS DoanhThu'
            )
            ->first();

        $doanhThu = $summary->DoanhThu;
        $soHoaDonThanhToan = (int) $summary->SoHoaDon;

        // Hóa đơn lập trong năm chọn và hiện vẫn chưa thanh toán.
        $soHoaDonChuaThanhToan = DB::table('HOADON as hd')
            ->join('LICHHEN as lh', 'lh.MaLH', '=', 'hd.MaLH')
            ->where('hd.TrangThai', 'CHUA_THANH_TOAN')
            ->where('lh.TrangThai', 'CHO_THANH_TOAN')
            ->where('hd.NgayLap', '>=', $start->format('Y-m-d H:i:s'))
            ->where('hd.NgayLap', '<', $end->format('Y-m-d H:i:s'))
            ->count();

        $tongLichHen = DB::table('LICHHEN')
            ->where('NgayTao', '>=', $start->format('Y-m-d H:i:s'))
            ->where('NgayTao', '<', $end->format('Y-m-d H:i:s'))
            ->count();

        // Đủ 12 tháng, kể cả tháng không có doanh thu.
        $monthlyValues = array_fill(0, 12, 0);

        $monthlyRows = $this->paidIn($start, $end)
            ->selectRaw(
                'MONTH(COALESCE(tt.NgayThanhToan, lh.NgayHoanTat)) AS Thang'
            )
            ->selectRaw('SUM(hd.TongTien) AS DoanhThu')
            ->groupByRaw(
                'MONTH(COALESCE(tt.NgayThanhToan, lh.NgayHoanTat))'
            )
            ->get();

        foreach ($monthlyRows as $row) {
            $monthlyValues[(int) $row->Thang - 1] = (float) $row->DoanhThu;
        }

        $monthlyLabels = array_map(
            fn ($month) => 'Tháng ' . $month,
            range(1, 12)
        );

        // Năm hiện tại: so sánh tháng hiện tại với tháng trước.
        // Năm cũ: so sánh tháng 12 với tháng 11.
        $anchor = $nam === $now->year
            ? $now
            : $start->addYear()->subDay();

        $currentStart = $anchor->startOfMonth();
        $previousStart = $currentStart->subMonth();

        // So sánh cùng số ngày để tháng dài không có lợi thế.
        $comparisonDays = min(
            $anchor->day,
            $previousStart->daysInMonth
        );

        $currentEnd = $currentStart->addDays($comparisonDays);
        $previousEnd = $previousStart->addDays($comparisonDays);

        // Nếu là ngày hiện tại, chỉ tính đến thời điểm hiện tại.
        $currentEnd = $currentEnd->gt($now) ? $now : $currentEnd;
        $previousEnd = $previousEnd->gt($now) ? $now : $previousEnd;

        $currentRevenue = $this->paidIn($currentStart, $currentEnd)
            ->sum('hd.TongTien');

        $previousRevenue = $this->paidIn($previousStart, $previousEnd)
            ->sum('hd.TongTien');

        $difference = (float) $currentRevenue - (float) $previousRevenue;

        $growth = (float) $previousRevenue > 0
            ? round($difference / (float) $previousRevenue * 100, 1)
            : null;

        $currentMonthLabel = $currentStart->format('m/Y');
        $previousMonthLabel = $previousStart->format('m/Y');

        $topServices = $this->ranking(
            'DICH_VU',
            $start,
            $end
        );

        $topParts = $this->ranking(
            'PHU_TUNG',
            $start,
            $end
        );

        $chartData = [
            'monthlyLabels' => $monthlyLabels,
            'monthlyValues' => $monthlyValues,
        ];

        return view('thongke.index', compact(
            'nam',
            'doanhThu',
            'soHoaDonThanhToan',
            'soHoaDonChuaThanhToan',
            'tongLichHen',
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

    private function paidIn(
        CarbonImmutable $start,
        CarbonImmutable $end
    ) {
        $successfulPayments = DB::table('THANHTOAN')
            ->where('TrangThai', 'THANH_CONG')
            ->select('MaHD', 'NgayThanhToan', 'SoTien');

        return DB::table('HOADON as hd')
            ->join('LICHHEN as lh', 'lh.MaLH', '=', 'hd.MaLH')
            ->leftJoinSub(
                $successfulPayments,
                'tt',
                function ($join) {
                    $join->on('tt.MaHD', '=', 'hd.MaHD');
                }
            )
            ->where('hd.TrangThai', 'DA_THANH_TOAN')
            ->where('hd.DaXuLyDiem', true)
            ->where('lh.TrangThai', 'HOAN_TAT')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->where('hd.TongTien', '>', 0)
                        ->whereNotNull('tt.NgayThanhToan')
                        ->whereColumn('tt.SoTien', 'hd.TongTien');
                })->orWhere(function ($query) {
                    // Hóa đơn 0đ không có giao dịch thu tiền.
                    $query->where('hd.TongTien', 0)
                        ->whereNotNull('lh.NgayHoanTat');
                });
            })
            ->whereRaw(
                'COALESCE(tt.NgayThanhToan, lh.NgayHoanTat) >= ?',
                [$start->format('Y-m-d H:i:s')]
            )
            ->whereRaw(
                'COALESCE(tt.NgayThanhToan, lh.NgayHoanTat) < ?',
                [$end->format('Y-m-d H:i:s')]
            );
    }

    private function ranking(
        string $type,
        CarbonImmutable $start,
        CarbonImmutable $end
    ) {
        $key = $type === 'DICH_VU' ? 'item.MaDV' : 'item.MaPT';

        $paidInvoices = $this->paidIn($start, $end)
            ->select('hd.MaHD');

        return DB::table('CTHOADON as ct')
            ->joinSub($paidInvoices, 'paid', function ($join) {
                $join->on('paid.MaHD', '=', 'ct.MaHD');
            })
            ->join('CHITIETLICHHEN as item', function ($join) {
                $join->on('item.MaCTLH', '=', 'ct.MaCTLH')
                    ->on('item.MaLH', '=', 'ct.MaLH');
            })
            ->where('ct.LoaiHangMuc', $type)
            ->whereNotNull($key)
            ->select($key . ' as Ma')
            ->selectRaw('MAX(ct.TenHangMuc) AS Ten')
            ->selectRaw('SUM(ct.SoLuong) AS SoLuong')
            ->selectRaw('SUM(ct.ThanhTien) AS DoanhThu')
            ->groupBy($key)
            ->orderByDesc('SoLuong')
            ->orderByDesc('DoanhThu')
            ->orderBy($key)
            ->limit(10)
            ->get();
    }
}
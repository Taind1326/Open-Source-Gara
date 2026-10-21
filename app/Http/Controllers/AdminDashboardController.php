<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $now = CarbonImmutable::now('Asia/Ho_Chi_Minh');

        $counts = DB::table('LICHHEN')
            ->select('TrangThai')
            ->selectRaw('COUNT(*) AS SoLuong')
            ->groupBy('TrangThai')
            ->pluck('SoLuong', 'TrangThai');

        $summary = [
            'CHO_PHAN_CONG' => (int) ($counts['CHO_PHAN_CONG'] ?? 0),
            'DANG_KIEM_TRA' => (int) ($counts['DANG_KIEM_TRA'] ?? 0),
            'DANG_SUA_CHUA' => (int) ($counts['DANG_SUA_CHUA'] ?? 0),
            'CHO_THANH_TOAN' => (int) ($counts['CHO_THANH_TOAN'] ?? 0),
        ];

        $lichChoPhanCong = DB::table('LICHHEN as lh')
            ->join('XE as xe', 'xe.MaXe', '=', 'lh.MaXe')
            ->join('TAIKHOAN as tk', 'tk.MaTK', '=', 'lh.MaTK')
            ->where('lh.TrangThai', 'CHO_PHAN_CONG')
            ->orderBy('lh.NgayHen')
            ->orderBy('lh.KhungGioHen')
            ->orderBy('lh.MaLH')
            ->limit(5)
            ->get([
                'lh.MaLH',
                'lh.NgayHen',
                'lh.KhungGioHen',
                'xe.BienSo',
                'tk.HoTen',
            ]);

        // Mỗi hóa đơn chỉ lấy yêu cầu tiền mặt đang chờ mới nhất.
        $pendingCash = DB::table('THANHTOAN')
            ->where('PhuongThuc', 'TIEN_MAT')
            ->where('TrangThai', 'CHO_XU_LY')
            ->select('MaHD')
            ->selectRaw('MAX(MaTT) AS MaTT')
            ->groupBy('MaHD');

        $cashQuery = DB::table('HOADON as hd')
            ->joinSub($pendingCash, 'pending', function ($join) {
                $join->on('pending.MaHD', '=', 'hd.MaHD');
            })
            ->join('THANHTOAN as tt', 'tt.MaTT', '=', 'pending.MaTT')
            ->join('LICHHEN as lh', 'lh.MaLH', '=', 'hd.MaLH')
            ->join('TAIKHOAN as tk', 'tk.MaTK', '=', 'lh.MaTK')
            ->where('hd.TrangThai', 'CHUA_THANH_TOAN')
            ->where('lh.TrangThai', 'CHO_THANH_TOAN')
            ->whereColumn('tt.SoTien', 'hd.TongTien');

        $soYeuCauTienMat = (clone $cashQuery)->count();

        $thanhToanTienMat = (clone $cashQuery)
            ->orderBy('tt.NgayTao')
            ->orderBy('tt.MaTT')
            ->limit(5)
            ->get([
                'hd.MaHD',
                'hd.TongTien',
                'tk.HoTen',
            ]);

        // Đây là tổng tin nhắn, không phải số tin chưa đọc.
        $soTinNhan = DB::table('contact_messages')->count();

        // Doanh thu theo ngày nhận tiền, cùng quy tắc trang thống kê.
        $paidQuery = DB::table('HOADON as hd')
            ->join('LICHHEN as lh', 'lh.MaLH', '=', 'hd.MaLH')
            ->join('THANHTOAN as tt', 'tt.MaHD', '=', 'hd.MaHD')
            ->where('hd.TrangThai', 'DA_THANH_TOAN')
            ->where('hd.DaXuLyDiem', true)
            ->where('lh.TrangThai', 'HOAN_TAT')
            ->where('tt.TrangThai', 'THANH_CONG')
            ->whereColumn('tt.SoTien', 'hd.TongTien')
            ->where(
                'tt.NgayThanhToan',
                '>=',
                $now->startOfMonth()->format('Y-m-d H:i:s')
            )
            ->where(
                'tt.NgayThanhToan',
                '<=',
                $now->format('Y-m-d H:i:s')
            );

        $dailyRevenue = $paidQuery
            ->selectRaw('DAY(tt.NgayThanhToan) AS Ngay')
            ->selectRaw('SUM(hd.TongTien) AS DoanhThu')
            ->groupByRaw('DAY(tt.NgayThanhToan)')
            ->pluck('DoanhThu', 'Ngay');

        $chartLabels = [];
        $chartValues = [];

        for ($day = 1; $day <= $now->day; $day++) {
            $chartLabels[] = str_pad((string) $day, 2, '0', STR_PAD_LEFT);
            $chartValues[] = (int) ($dailyRevenue[$day] ?? 0);
        }

        $doanhThuThang = array_sum($chartValues);

        return view('admin.dashboard', compact(
            'now',
            'summary',
            'lichChoPhanCong',
            'soYeuCauTienMat',
            'thanhToanTienMat',
            'soTinNhan',
            'chartLabels',
            'chartValues',
            'doanhThuThang'
        ));
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\YeuCauSuaChua;
use App\Models\PhanCong;
use App\Models\Xe;
use App\Models\DichVu;
use App\Models\TaiKhoan;
use App\Services\LichHenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LichHenController extends Controller
{
    protected LichHenService $service;

    public function __construct(LichHenService $service)
    {
        $this->service = $service;
    }

    // ================= PHÍA USER (M06) =================

    // Danh sách lịch của chính user đang login
    public function index()
    {
        $danhSach = YeuCauSuaChua::whereHas('xe', function ($q) {
            $q->where('MaTK', Auth::id());
        })
            ->with(['xe', 'dichVu', 'phanCong.ktv'])
            ->orderByDesc('NgayTao')
            ->paginate(10);

        return view('lichhen.index', compact('danhSach'));
    }

    // Form đặt lịch mới
    public function create(Request $request)
    {
        $ngay = $request->input('ngay', now()->toDateString());

        $xeList = Xe::where('MaTK', Auth::id())
            ->where('TrangThai', 'HOAT_DONG')
            ->get();

        $dichVuList = DichVu::where('TrangThai', 'HOAT_DONG')->get();
        $khungGioList = $this->service->trangThaiTatCaKhungGio($ngay);

        return view('lichhen.create', compact('xeList', 'dichVuList', 'khungGioList', 'ngay'));
    }

    // Xử lý lưu lịch hẹn
    public function store(Request $request)
    {
        $data = $request->validate([
            'MaXe'        => 'required|exists:XE,MaXe',
            'NgayHen'     => 'required|date',
            'KhungGioHen' => 'required|string',
            'dich_vu'     => 'nullable|array',
            'dich_vu.*'   => 'exists:DICHVU,MaDV',
            'MoTa'        => 'nullable|string|max:1000',
        ]);

        // Đảm bảo xe thuộc đúng user đang login
        $xe = Xe::where('MaXe', $data['MaXe'])->where('MaTK', Auth::id())->first();
        if (!$xe) {
            return back()->withErrors(['MaXe' => 'Xe không hợp lệ.']);
        }

        if (!$this->service->thoiGianKhungGioHopLe($data['NgayHen'], $data['KhungGioHen'])) {
            return back()->withErrors(['NgayHen' => 'Thời gian đã chọn không còn hợp lệ (đã qua hoặc ở quá khứ).'])->withInput();
        }

        if (!$this->service->laKhungGioHopLe($data['KhungGioHen'])) {
            return back()->withErrors(['KhungGioHen' => 'Khung giờ không hợp lệ.'])->withInput();
        }

        if (!$this->service->khungGioConCho($data['NgayHen'], $data['KhungGioHen'])) {
            return back()->withErrors(['KhungGioHen' => 'Khung giờ này đã đầy, vui lòng chọn khung khác.'])->withInput();
        }

        $yeuCau = YeuCauSuaChua::create([
            'MaXe'        => $data['MaXe'],
            'NgayHen'     => $data['NgayHen'],
            'KhungGioHen' => $data['KhungGioHen'],
            'MoTa'        => $data['MoTa'] ?? null,
        ]);

        foreach ($data['dich_vu'] ?? [] as $maDV) {
            $yeuCau->chiTietDichVu()->create(['MaDV' => $maDV]);
        }

        return redirect()->route('lichhen.index')->with('success', 'Đặt lịch thành công! Vui lòng chờ xác nhận.');
    }

    // Xem chi tiết 1 lịch (dùng chung cho User xem lại)
    public function show(YeuCauSuaChua $yeuCau)
    {
        if ($yeuCau->xe->MaTK !== Auth::id() && Auth::user()->VaiTro === 'USER') {
            abort(403);
        }

        $yeuCau->load(['xe', 'dichVu', 'phanCong.ktv']);
        return view('lichhen.show', compact('yeuCau'));
    }

    // User hủy lịch (chỉ khi chưa phân công)
    public function huy(YeuCauSuaChua $yeuCau)
    {
        if ($yeuCau->xe->MaTK !== Auth::id()) {
            abort(403);
        }

        if ($yeuCau->TrangThai !== 'CHO_PHAN_CONG') {
            return back()->withErrors(['error' => 'Không thể hủy lịch đã được xử lý.']);
        }

        $yeuCau->update(['TrangThai' => 'DA_HUY']);
        return back()->with('success', 'Đã hủy lịch hẹn.');
    }

    // ================= PHÍA ADMIN (M07) =================

    // Danh sách lịch chờ phân công
    public function danhSachChoPhanCong()
    {
        $danhSach = YeuCauSuaChua::where('TrangThai', 'CHO_PHAN_CONG')
            ->with(['xe.taiKhoan', 'dichVu'])
            ->orderBy('NgayHen')
            ->orderBy('KhungGioHen')
            ->paginate(15);

        return view('admin.lichhen.cho-phan-cong', compact('danhSach'));
    }

    // Trang chi tiết để chọn KTV
    public function formPhanCong(YeuCauSuaChua $yeuCau)
    {
        $ktvRanh = $this->service->ktvRanh($yeuCau->NgayHen->toDateString(), $yeuCau->KhungGioHen);
        $yeuCau->load(['xe.taiKhoan', 'dichVu']);

        return view('admin.lichhen.phan-cong', compact('yeuCau', 'ktvRanh'));
    }

    // Xử lý lưu phân công
    public function luuPhanCong(Request $request, YeuCauSuaChua $yeuCau)
    {
        $data = $request->validate([
            'MaKTV' => 'required|exists:TAIKHOAN,MaTK',
        ]);

        if ($yeuCau->TrangThai !== 'CHO_PHAN_CONG' && $yeuCau->TrangThai !== 'DA_PHAN_CONG') {
            return back()->withErrors(['error' => 'Yêu cầu này không ở trạng thái có thể phân công.']);
        }

        // Kiểm tra lại server-side, không tin dropdown phía client
        $conRanh = $this->service->ktvConRanh(
            $data['MaKTV'],
            $yeuCau->NgayHen->toDateString(),
            $yeuCau->KhungGioHen,
            $yeuCau->MaYC // bỏ qua chính yêu cầu này khi đang sửa lại phân công
        );

        if (!$conRanh) {
            return back()->withErrors(['MaKTV' => 'KTV này đã bận trong khung giờ này, vui lòng chọn KTV khác.']);
        }

        $ktv = TaiKhoan::where('MaTK', $data['MaKTV'])
            ->where('VaiTro', 'TECHNICIAN')
            ->where('TrangThai', 'HOAT_DONG')
            ->first();

        if (!$ktv) {
            return back()->withErrors(['MaKTV' => 'KTV không hợp lệ hoặc không hoạt động.']);
        }

        PhanCong::updateOrCreate(
            ['MaYC' => $yeuCau->MaYC],
            ['MaKTV' => $data['MaKTV'], 'TrangThai' => 'DANG_PHU_TRACH']
        );

        $yeuCau->update(['TrangThai' => 'DA_PHAN_CONG']);

        return redirect()->route('admin.lichhen.cho-phan-cong')
            ->with('success', "Đã phân công KTV {$ktv->HoTen} cho yêu cầu #{$yeuCau->MaYC}.");
    }

    // ================= ADMIN TIẾP NHẬN XE (đầu M08) =================

    // Form tìm kiếm lịch để tiếp nhận
    public function timTiepNhan(Request $request)
    {
        $tuKhoa = $request->input('tu_khoa');
        $ketQua = collect();

        if ($tuKhoa) {
            $ketQua = YeuCauSuaChua::where('TrangThai', 'DA_PHAN_CONG')
                ->where(function ($q) use ($tuKhoa) {
                    $q->where('MaYC', 'like', "%{$tuKhoa}%")
                        ->orWhereHas('xe', function ($q2) use ($tuKhoa) {
                            $q2->where('BienSo', 'like', "%{$tuKhoa}%");
                        })
                        ->orWhereHas('xe.taiKhoan', function ($q2) use ($tuKhoa) {
                            $q2->where('SoDienThoai', 'like', "%{$tuKhoa}%");
                        });
                })
                ->with(['xe.taiKhoan', 'phanCong.ktv'])
                ->get();
        }

        return view('admin.lichhen.tiep-nhan', compact('ketQua', 'tuKhoa'));
    }

    // Xử lý tiếp nhận xe
    public function tiepNhan(YeuCauSuaChua $yeuCau)
    {
        if ($yeuCau->TrangThai !== 'DA_PHAN_CONG') {
            return back()->withErrors(['error' => 'Yêu cầu chưa được phân công KTV hoặc đã được tiếp nhận.']);
        }

        $yeuCau->update(['TrangThai' => 'DA_TIEP_NHAN']);

        return redirect()->route('admin.lichhen.tiep-nhan')
            ->with('success', "Đã tiếp nhận xe cho yêu cầu #{$yeuCau->MaYC}. Chuyển cho KTV kiểm tra.");
    }

    // Khách đến trực tiếp không đặt trước — tạo yêu cầu + tiếp nhận ngay
    public function taoTiepNhanTrucTiep(Request $request)
    {
        $data = $request->validate([
            'MaXe'        => 'required|exists:XE,MaXe',
            'MoTa'        => 'nullable|string|max:1000',
            'dich_vu'     => 'nullable|array',
            'dich_vu.*'   => 'exists:DICHVU,MaDV',
        ]);

        $khungGioHienTai = $this->khungGioHienTai();

        if ($khungGioHienTai === null) {
            return back()->withErrors([
                'error' => 'Garage hiện ngoài giờ hoạt động (07:30–20:00), không thể tiếp nhận xe lúc này.'
            ]);
        }

        $yeuCau = YeuCauSuaChua::create([
            'MaXe'        => $data['MaXe'],
            'NgayHen'     => now()->toDateString(),
            'KhungGioHen' => $khungGioHienTai,
            'MoTa'        => $data['MoTa'] ?? null,
            'TrangThai'   => 'CHO_PHAN_CONG',
        ]);

        foreach ($data['dich_vu'] ?? [] as $maDV) {
            $yeuCau->chiTietDichVu()->create(['MaDV' => $maDV]);
        }

        return redirect()->route('admin.lichhen.phan-cong', $yeuCau->MaYC)
            ->with('success', 'Đã tạo yêu cầu tại chỗ. Vui lòng phân công KTV.');
    }

    // Xác định khung giờ hiện tại — trả về null nếu ngoài giờ hoạt động
    private function khungGioHienTai(): ?string
    {
        $gioHienTai = now()->format('H:i');
        $danhSachKhungGio = $this->service->layDanhSachKhungGio();

        foreach ($danhSachKhungGio as $khungGio) {
            [$batDau, $ketThuc] = explode('-', $khungGio);
            if ($gioHienTai >= $batDau && $gioHienTai < $ketThuc) {
                return $khungGio;
            }
        }

        return null; // ngoài giờ hoạt động 07:30-20:00
    }
}

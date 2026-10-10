<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaiKhoan;
use App\Models\Xe;
use Illuminate\Http\Request;

// M03 - Admin quản lý xe / khách hàng (tìm xe, thêm xe cho khách đến trực tiếp)
class XeController extends Controller
{
    public function index(Request $request)
    {
        $tuKhoa = trim((string) $request->input('tu_khoa'));
        $trangThai = $request->input('trang_thai');

        $dsXe = Xe::with('taiKhoan')
            ->when($tuKhoa !== '', function ($q) use ($tuKhoa) {
                $bienSo = Xe::chuanHoaBienSo($tuKhoa);
                $q->where(function ($q2) use ($tuKhoa, $bienSo) {
                    $q2->where('BienSo', 'like', "%{$bienSo}%")
                       ->orWhere('HangXe', 'like', "%{$tuKhoa}%")
                       ->orWhere('DongXe', 'like', "%{$tuKhoa}%")
                       ->orWhereHas('taiKhoan', fn ($q3) => $q3
                           ->where('HoTen', 'like', "%{$tuKhoa}%")
                           ->orWhere('SoDienThoai', 'like', "%{$tuKhoa}%"));
                });
            })
            ->when(in_array($trangThai, [Xe::HOAT_DONG, Xe::NGUNG_SU_DUNG], true), fn ($q) => $q->where('TrangThai', $trangThai))
            ->orderByDesc('MaXe')
            ->paginate(15)
            ->withQueryString();

        return view('admin.xe.index', compact('dsXe', 'tuKhoa', 'trangThai'));
    }

    // ?khach=MaTK để điền sẵn SĐT khi bấm "Thêm xe cho khách" từ trang chi tiết tài khoản
    public function create(Request $request)
    {
        $khach = $request->filled('khach') ? TaiKhoan::find($request->input('khach')) : null;

        return view('admin.xe.form', [
            'xe' => new Xe(),
            'soDienThoaiKhach' => $khach?->SoDienThoai,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['SoDienThoaiKhach' => 'required|string'], [], ['SoDienThoaiKhach' => 'số điện thoại khách']);

        $khach = TaiKhoan::where('SoDienThoai', trim($request->input('SoDienThoaiKhach')))
            ->where('VaiTro', TaiKhoan::VAI_TRO_USER)
            ->first();

        if (!$khach) {
            return back()->withInput()->withErrors([
                'SoDienThoaiKhach' => 'Chưa có tài khoản khách hàng với số điện thoại này. Hãy tạo tài khoản khách trước.',
            ]);
        }

        if (!$khach->dangHoatDong()) {
            return back()->withInput()->withErrors(['SoDienThoaiKhach' => 'Tài khoản khách hàng này đang bị khóa.']);
        }

        $this->chuanHoa($request);
        $data = $request->validate(Xe::quyTacHopLe(), Xe::THONG_BAO_LOI);

        Xe::create($data + ['MaTK' => $khach->MaTK, 'TrangThai' => Xe::HOAT_DONG]);

        return redirect()->route('admin.taikhoan.show', $khach->MaTK)->with('success', 'Đã thêm xe cho khách hàng.');
    }

    public function edit(int $xe)
    {
        $xe = Xe::with('taiKhoan')->findOrFail($xe);

        return view('admin.xe.form', ['xe' => $xe, 'soDienThoaiKhach' => $xe->taiKhoan->SoDienThoai]);
    }

    // Không đổi chủ sở hữu xe ở đây → chỉ nhận thông tin xe
    public function update(Request $request, int $xe)
    {
        $xe = Xe::findOrFail($xe);

        $this->chuanHoa($request);
        $data = $request->validate(Xe::quyTacHopLe($xe->MaXe), Xe::THONG_BAO_LOI);

        $xe->update($data);

        return redirect()->route('admin.xe.index')->with('success', 'Đã cập nhật xe.');
    }

    // Ngừng sử dụng / kích hoạt lại xe (không xóa cứng)
    public function doiTrangThai(int $xe)
    {
        $xe = Xe::findOrFail($xe);

        if ($xe->TrangThai === Xe::HOAT_DONG) {
            if ($xe->coYeuCauChuaHoanTat()) {
                return back()->withErrors(['error' => "Xe {$xe->BienSo} đang có lịch hẹn/yêu cầu sửa chữa chưa hoàn tất nên chưa thể ngừng sử dụng."]);
            }
            $xe->update(['TrangThai' => Xe::NGUNG_SU_DUNG]);

            return back()->with('success', 'Đã ngừng sử dụng xe.');
        }

        $xe->update(['TrangThai' => Xe::HOAT_DONG]);

        return back()->with('success', 'Đã kích hoạt lại xe.');
    }

    private function chuanHoa(Request $request): void
    {
        if ($request->filled('BienSo')) {
            $request->merge(['BienSo' => Xe::chuanHoaBienSo($request->input('BienSo'))]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Xe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

// M03 - Xe của khách hàng (chỉ thao tác trên xe của chính mình)
class XeController extends Controller
{
    private function xeCuaToi()
    {
        return Xe::where('MaTK', Auth::id());
    }

    private function timXe(int $id): Xe
    {
        // 404 nếu xe không tồn tại HOẶC không phải của user → không lộ sự tồn tại xe người khác
        return $this->xeCuaToi()->where('MaXe', $id)->firstOrFail();
    }

    private function rules(?int $ignoreId = null): array
    {
        return Xe::quyTacHopLe($ignoreId);
    }

    private const THONG_BAO = Xe::THONG_BAO_LOI;

    // Chuẩn hoá biển số trước khi validate để so sánh unique chính xác
    private function chuanHoa(Request $request): void
    {
        if ($request->filled('BienSo')) {
            $request->merge(['BienSo' => Xe::chuanHoaBienSo($request->input('BienSo'))]);
        }
    }

    public function index()
    {
        $dsXe = $this->xeCuaToi()->where('TrangThai', Xe::HOAT_DONG)->orderByDesc('MaXe')->get();

        return view('xe.index', compact('dsXe'));
    }

    public function create()
    {
        return view('xe.form', ['xe' => new Xe()]);
    }

    public function store(Request $request)
    {
        $this->chuanHoa($request);
        $data = $request->validate($this->rules(), self::THONG_BAO);

        Xe::create($data + ['MaTK' => Auth::id(), 'TrangThai' => Xe::HOAT_DONG]);

        return redirect()->route('xe.index')->with('success', 'Đã thêm xe.');
    }

    public function edit(int $xe)
    {
        return view('xe.form', ['xe' => $this->timXe($xe)]);
    }

    public function update(Request $request, int $xe)
    {
        $xe = $this->timXe($xe);

        $this->chuanHoa($request);
        $data = $request->validate($this->rules($xe->MaXe), self::THONG_BAO);

        $xe->update($data);

        return redirect()->route('xe.index')->with('success', 'Đã cập nhật xe.');
    }

    // Xe có thể đã gắn với lịch hẹn/hồ sơ sửa chữa → không xoá cứng, chỉ ngừng hoạt động
    // (LichHenController chỉ lấy xe TrangThai = HOAT_DONG nên xe này sẽ biến mất khỏi form đặt lịch)
    public function destroy(int $xe)
    {
        $xe = $this->timXe($xe);

        if ($xe->coYeuCauChuaHoanTat()) {
            return back()->withErrors(['error' => "Xe {$xe->BienSo} đang có lịch hẹn/yêu cầu sửa chữa chưa hoàn tất nên chưa thể xóa."]);
        }

        $xe->update(['TrangThai' => Xe::NGUNG_SU_DUNG]);

        return redirect()->route('xe.index')->with('success', 'Đã xóa xe khỏi danh sách.');
    }
}

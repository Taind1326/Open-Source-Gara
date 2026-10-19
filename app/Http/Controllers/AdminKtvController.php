<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminKtvController extends Controller
{
    private const DEFAULT_PASSWORD = 'Ktv@123';

    private const OPEN_STATUSES = [
        'CHO_NHAN_XE',
        'DANG_KIEM_TRA',
        'CHO_XAC_NHAN',
        'CHO_SUA_CHUA',
        'DANG_SUA_CHUA',
        'CHO_THANH_TOAN',
    ];

    public function index(Request $request)
    {
        $data = $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ], [
            'tu_khoa.string' => 'Từ khóa phải là văn bản.',
            'tu_khoa.max' => 'Từ khóa không được vượt quá 100 ký tự.',
        ]);

        $tuKhoa = trim($data['tu_khoa'] ?? '');

        $workload = DB::table('PHANCONG as pc')
            ->join('LICHHEN as lh', 'lh.MaLH', '=', 'pc.MaLH')
            ->whereIn('lh.TrangThai', self::OPEN_STATUSES)
            ->select('pc.MaKTV')
            ->selectRaw('COUNT(*) AS SoCongViec')
            ->groupBy('pc.MaKTV');

        $ktvs = TaiKhoan::query()
            ->leftJoinSub($workload, 'workload', function ($join) {
                $join->on('workload.MaKTV', '=', 'TAIKHOAN.MaTK');
            })
            ->where('TAIKHOAN.VaiTro', 'TECHNICIAN')
            ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                $query->where(function ($query) use ($tuKhoa) {
                    $query->where('TAIKHOAN.HoTen', 'like', "%{$tuKhoa}%")
                        ->orWhere('TAIKHOAN.Email', 'like', "%{$tuKhoa}%")
                        ->orWhere(
                            'TAIKHOAN.SoDienThoai',
                            'like',
                            "%{$tuKhoa}%"
                        );
                });
            })
            ->select('TAIKHOAN.*')
            ->selectRaw('COALESCE(workload.SoCongViec, 0) AS SoCongViec')
            ->orderByDesc('TAIKHOAN.MaTK')
            ->paginate(10)
            ->withQueryString();

        if ($ktvs->currentPage() > $ktvs->lastPage()) {
            return redirect()->route('admin.ktv.index', [
                'tu_khoa' => $tuKhoa,
                'page' => $ktvs->lastPage(),
            ]);
        }

        return view('admin.ktv.index', compact('ktvs', 'tuKhoa'));
    }

    public function create()
    {
        $ktv = new TaiKhoan();

        return view('admin.ktv.form', compact('ktv'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTechnician($request);

        TaiKhoan::create([
            ...$data,
            'VaiTro' => 'TECHNICIAN',
            'TrangThai' => 'HOAT_DONG',
            'MatKhau' => Hash::make(self::DEFAULT_PASSWORD),
        ]);

        return redirect()
            ->route('admin.ktv.index')
            ->with(
                'success',
                'Đã tạo tài khoản kỹ thuật viên. '
                . 'Mật khẩu mặc định: ' . self::DEFAULT_PASSWORD
            );
    }

    public function edit($maTK)
    {
        $ktv = $this->technician($maTK)->firstOrFail();

        return view('admin.ktv.form', compact('ktv'));
    }

    public function update(Request $request, $maTK)
    {
        $ktv = $this->technician($maTK)->firstOrFail();
        $data = $this->validateTechnician($request, $ktv);

        DB::transaction(function () use ($maTK, $data) {
            $ktv = $this->technician($maTK)
                ->lockForUpdate()
                ->firstOrFail();

            // Chỉ cập nhật thông tin, không nhận vai trò/mật khẩu từ form.
            $ktv->update($data);
        }, 3);

        return redirect()
            ->route('admin.ktv.index')
            ->with('success', 'Đã cập nhật thông tin kỹ thuật viên.');
    }

    public function updateStatus(Request $request, $maTK)
    {
        $this->technician($maTK)->firstOrFail();

        $data = $request->validate([
            'TrangThai' => [
                'required',
                Rule::in(['HOAT_DONG', 'KHOA']),
            ],
        ], [
            'TrangThai.required' => 'Vui lòng chọn trạng thái tài khoản.',
            'TrangThai.in' => 'Trạng thái tài khoản không hợp lệ.',
        ]);

        DB::transaction(function () use ($maTK, $data) {
            $ktv = $this->technician($maTK)
                ->lockForUpdate()
                ->firstOrFail();

            if ($ktv->TrangThai === $data['TrangThai']) {
                return;
            }

            if ($data['TrangThai'] === 'KHOA') {
                $hasOpenWork = DB::table('PHANCONG as pc')
                    ->join('LICHHEN as lh', 'lh.MaLH', '=', 'pc.MaLH')
                    ->where('pc.MaKTV', $ktv->MaTK)
                    ->whereIn('lh.TrangThai', self::OPEN_STATUSES)
                    ->exists();

                if ($hasOpenWork) {
                    throw ValidationException::withMessages([
                        'ktv_' . $maTK =>
                            'KTV còn lịch chưa kết thúc. '
                            . 'Vui lòng xử lý xong trước khi khóa.',
                    ]);
                }
            }

            $ktv->update([
                'TrangThai' => $data['TrangThai'],
            ]);
        }, 3);

        return redirect()
            ->route('admin.ktv.index')
            ->with(
                'success',
                $data['TrangThai'] === 'KHOA'
                    ? 'Đã khóa tài khoản kỹ thuật viên.'
                    : 'Đã mở khóa tài khoản kỹ thuật viên.'
            );
    }

    public function resetPassword($maTK)
    {
        DB::transaction(function () use ($maTK) {
            $ktv = $this->technician($maTK)
                ->lockForUpdate()
                ->firstOrFail();

            $ktv->update([
                'MatKhau' => Hash::make(self::DEFAULT_PASSWORD),
            ]);
        }, 3);

        return redirect()
            ->route('admin.ktv.index')
            ->with(
                'success',
                'Đã đặt lại mật khẩu kỹ thuật viên thành '
                . self::DEFAULT_PASSWORD
                . '. Trạng thái khóa/mở tài khoản được giữ nguyên.'
            );
    }

    private function technician($maTK)
    {
        return TaiKhoan::query()
            ->where('VaiTro', 'TECHNICIAN')
            ->where('MaTK', $maTK);
    }

    private function validateTechnician(
        Request $request,
        ?TaiKhoan $ktv = null
    ): array {
        $normalized = [];

        foreach (['HoTen', 'Email', 'SoDienThoai'] as $field) {
            $value = $request->input($field);

            if (is_string($value)) {
                $normalized[$field] = trim($value);
            }
        }

        if (isset($normalized['Email'])) {
            $normalized['Email'] = mb_strtolower($normalized['Email']);
        }

        $request->merge($normalized);

        $uniqueEmail = Rule::unique('TAIKHOAN', 'Email');
        $uniquePhone = Rule::unique('TAIKHOAN', 'SoDienThoai');

        if ($ktv) {
            $uniqueEmail->ignore($ktv->MaTK, 'MaTK');
            $uniquePhone->ignore($ktv->MaTK, 'MaTK');
        }

        return $request->validate([
            'HoTen' => ['bail', 'required', 'string', 'max:100'],
            'Email' => [
                'bail',
                'required',
                'string',
                'email',
                'max:100',
                $uniqueEmail,
            ],
            'SoDienThoai' => [
                'bail',
                'required',
                'string',
                'regex:/^0[0-9]{9}$/',
                $uniquePhone,
            ],
        ], [
            'HoTen.required' => 'Vui lòng nhập họ tên kỹ thuật viên.',
            'HoTen.string' => 'Họ tên phải là văn bản.',
            'HoTen.max' => 'Họ tên không được vượt quá 100 ký tự.',

            'Email.required' => 'Vui lòng nhập email.',
            'Email.string' => 'Email không hợp lệ.',
            'Email.email' => 'Email không đúng định dạng.',
            'Email.max' => 'Email không được vượt quá 100 ký tự.',
            'Email.unique' => 'Email này đã được sử dụng.',

            'SoDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'SoDienThoai.string' => 'Số điện thoại không hợp lệ.',
            'SoDienThoai.regex' =>
                'Số điện thoại phải có 10 chữ số, bắt đầu bằng số 0.',
            'SoDienThoai.unique' =>
                'Số điện thoại này đã được sử dụng.',
        ]);
    }
}
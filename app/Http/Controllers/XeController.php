<?php

namespace App\Http\Controllers;

use App\Models\Xe;
use App\Services\VehicleCatalogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class XeController extends Controller
{
    public function __construct(
        private VehicleCatalogService $catalog
    ) {
    }

    public function index(Request $request)
    {
        $data = $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'trang_thai' => [
                'nullable',
                Rule::in(['HOAT_DONG', 'NGUNG_SU_DUNG']),
            ],
        ]);

        $tuKhoa = trim($data['tu_khoa'] ?? '');
        $trangThai = $data['trang_thai'] ?? null;

        $xes = Xe::where('MaTK', $request->user()->MaTK)
            ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                $query->where(function ($query) use ($tuKhoa) {
                    $query->where('BienSo', 'like', "%{$tuKhoa}%")
                        ->orWhere('HangXe', 'like', "%{$tuKhoa}%")
                        ->orWhere('DongXe', 'like', "%{$tuKhoa}%");
                });
            })
            ->when($trangThai, function ($query) use ($trangThai) {
                $query->where('TrangThai', $trangThai);
            })
            ->orderByDesc('MaXe')
            ->paginate(9)
            ->withQueryString();

        return view('public.xe.index', compact(
            'xes',
            'tuKhoa',
            'trangThai'
        ));
    }

    public function create()
    {
        return view('public.xe.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateVehicle($request);

        Xe::create([
            'MaTK' => $request->user()->MaTK,
            'BienSo' => $data['BienSo'],
            'HangXe' => $data['HangXe'],
            'DongXe' => $data['DongXe'],
            'NamSanXuat' => $data['NamSanXuat'],
            'TrangThai' => 'HOAT_DONG',
        ]);

        return redirect()
            ->route('public.xe.index')
            ->with('success', 'Đã thêm xe vào tài khoản của bạn.');
    }

    public function edit(Request $request, $maXe)
    {
        $xe = Xe::where('MaTK', $request->user()->MaTK)
            ->findOrFail($maXe);

        return view('public.xe.edit', compact('xe'));
    }

    public function update(Request $request, $maXe)
    {
        $xe = Xe::where('MaTK', $request->user()->MaTK)
            ->findOrFail($maXe);

        $data = $this->validateVehicle($request, $xe);

        DB::transaction(function () use ($request, $maXe, $data) {
            $xe = Xe::where('MaTK', $request->user()->MaTK)
                ->where('MaXe', $maXe)
                ->lockForUpdate()
                ->firstOrFail();

            $xe->fill($data);

            if ($xe->isDirty() && $this->hasOpenRequest($xe)) {
                throw ValidationException::withMessages([
                    'BienSo' => 'Xe đang có yêu cầu chưa kết thúc. '
                        . 'Vui lòng hoàn tất hoặc hủy yêu cầu trước khi sửa thông tin.',
                ]);
            }

            $xe->save();
        });

        return redirect()
            ->route('public.xe.index')
            ->with('success', 'Đã cập nhật thông tin xe.');
    }

    public function updateStatus(Request $request, $maXe)
    {
        $data = $request->validate([
            'TrangThai' => [
                'required',
                Rule::in(['HOAT_DONG', 'NGUNG_SU_DUNG']),
            ],
        ]);

        DB::transaction(function () use ($request, $maXe, $data) {
            $xe = Xe::where('MaTK', $request->user()->MaTK)
                ->where('MaXe', $maXe)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $data['TrangThai'] === 'NGUNG_SU_DUNG'
                && $this->hasOpenRequest($xe)
            ) {
                throw ValidationException::withMessages([
                    'TrangThai' => 'Xe đang có yêu cầu chưa kết thúc '
                        . 'nên chưa thể ngừng sử dụng.',
                ]);
            }

            $xe->update([
                'TrangThai' => $data['TrangThai'],
            ]);
        });

        return redirect()
            ->route('public.xe.index')
            ->with(
                'success',
                $data['TrangThai'] === 'HOAT_DONG'
                    ? 'Đã kích hoạt lại xe.'
                    : 'Đã ngừng sử dụng xe. Lịch sử sửa chữa vẫn được giữ lại.'
            );
    }

    public function makes()
    {
        try {
            return response()->json([
                'data' => $this->catalog->makes(),
            ]);
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 503);
        }
    }

    public function models(Request $request)
    {
        $data = $request->validate([
            'make_id' => ['required', 'integer', 'min:1'],
            'year' => [
                'required',
                'integer',
                'min:1990',
                'max:' . now('Asia/Ho_Chi_Minh')->year,
            ],
        ]);

        try {
            return response()->json(
                $this->catalog->models(
                    (int) $data['make_id'],
                    (int) $data['year']
                )
            );
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 503);
        }
    }

    private function hasOpenRequest(Xe $xe): bool
    {
        return $xe->yeuCauSuaChuas()
            ->whereNotIn('TrangThai', [
                'HOAN_THANH',
                'TU_CHOI',
                'DA_HUY',
            ])
            ->exists();
    }

    private function validateVehicle(
        Request $request,
        ?Xe $xe = null
    ): array {
        $request->merge([
            'BienSo' => mb_strtoupper(
                trim((string) $request->input('BienSo')),
                'UTF-8'
            ),
        ]);

        $uniquePlate = Rule::unique('XE', 'BienSo');

        if ($xe) {
            $uniquePlate->ignore($xe->MaXe, 'MaXe');
        }

        $data = $request->validate([
            'BienSo' => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Z0-9.-]+$/',
                $uniquePlate,
            ],
            'NamSanXuat' => [
                'required',
                'integer',
                'min:1990',
                'max:' . now('Asia/Ho_Chi_Minh')->year,
            ],
            'MakeId' => ['required', 'integer', 'min:1'],
            'ModelId' => ['required', 'integer', 'min:1'],
        ], [
            'BienSo.required' => 'Vui lòng nhập biển số xe.',
            'BienSo.max' => 'Biển số không được vượt quá 15 ký tự.',
            'BienSo.regex' => 'Biển số chỉ gồm chữ, số, dấu chấm và dấu gạch ngang.',
            'BienSo.unique' => 'Biển số này đã được đăng ký trong hệ thống.',

            'NamSanXuat.required' => 'Vui lòng chọn năm sản xuất.',
            'NamSanXuat.integer' => 'Năm sản xuất không hợp lệ.',
            'NamSanXuat.min' => 'Năm sản xuất phải từ năm 1990.',
            'NamSanXuat.max' => 'Năm sản xuất không được lớn hơn năm hiện tại.',

            'MakeId.required' => 'Vui lòng chọn hãng xe.',
            'MakeId.integer' => 'Hãng xe không hợp lệ.',
            'MakeId.min' => 'Hãng xe không hợp lệ.',

            'ModelId.required' => 'Vui lòng chọn dòng xe.',
            'ModelId.integer' => 'Dòng xe không hợp lệ.',
            'ModelId.min' => 'Dòng xe không hợp lệ.',
        ]);

        try {
            $catalog = $this->catalog->models(
                (int) $data['MakeId'],
                (int) $data['NamSanXuat']
            );
        } catch (RuntimeException $exception) {
            throw ValidationException::withMessages([
                'MakeId' => $exception->getMessage(),
            ]);
        }

        $model = collect($catalog['models'])
            ->firstWhere('id', (int) $data['ModelId']);

        if (! $model) {
            throw ValidationException::withMessages([
                'ModelId' => 'Dòng xe không thuộc danh sách hãng và năm đã chọn. '
                    . 'Vui lòng tải lại danh sách.',
            ]);
        }

        $makeName = $catalog['make']['name'];
        $modelName = $model['name'];

        if (
            mb_strlen($makeName, 'UTF-8') > 50
            || mb_strlen($modelName, 'UTF-8') > 50
        ) {
            throw ValidationException::withMessages([
                'ModelId' => 'Tên hãng hoặc dòng xe vượt giới hạn lưu trữ 50 ký tự.',
            ]);
        }

        return [
            'BienSo' => $data['BienSo'],
            'HangXe' => $makeName,
            'DongXe' => $modelName,
            'NamSanXuat' => (int) $data['NamSanXuat'],
        ];
    }
}
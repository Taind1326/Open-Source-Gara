<?php

namespace App\Http\Controllers;

use App\Models\Xe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class XeController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $tuKhoa = trim($data['tu_khoa'] ?? '');

        $xes = Xe::query()
            ->where('MaTK', $request->user()->MaTK)
            ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                $query->where(function ($query) use ($tuKhoa) {
                    $query->where('BienSo', 'like', "%{$tuKhoa}%")
                        ->orWhere('HangXe', 'like', "%{$tuKhoa}%")
                        ->orWhere('DongXe', 'like', "%{$tuKhoa}%");
                });
            })
            ->orderByDesc('MaXe')
            ->paginate(9)
            ->withQueryString();

        if ($xes->currentPage() > $xes->lastPage()) {
            return redirect()->route(
                'public.xe.index',
                array_merge(
                    $request->only('tu_khoa'),
                    ['page' => $xes->lastPage()]
                )
            );
        }

        return view('public.xe.index', compact('xes', 'tuKhoa'));
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
            ...$data,
        ]);

        return redirect()
            ->route('public.xe.index')
            ->with('success', 'Đã thêm xe vào tài khoản của bạn.');
    }

    public function edit(Request $request, $maXe)
    {
        $xe = Xe::query()
            ->where('MaTK', $request->user()->MaTK)
            ->findOrFail($maXe);

        return view('public.xe.edit', compact('xe'));
    }

    public function update(Request $request, $maXe)
    {
        $xe = Xe::query()
            ->where('MaTK', $request->user()->MaTK)
            ->findOrFail($maXe);

        $data = $this->validateVehicle($request, $xe);

        DB::transaction(function () use ($request, $maXe, $data) {
            $xe = Xe::query()
                ->where('MaTK', $request->user()->MaTK)
                ->where('MaXe', $maXe)
                ->lockForUpdate()
                ->firstOrFail();

            $xe->fill($data);

            if ($xe->isDirty() && $this->hasOpenAppointment($xe)) {
                throw ValidationException::withMessages([
                    'BienSo' => 'Xe đang có lịch hẹn chưa kết thúc. '
                        . 'Vui lòng hoàn tất hoặc hủy lịch trước khi sửa thông tin.',
                ]);
            }

            $xe->save();
        });

        return redirect()
            ->route('public.xe.index')
            ->with('success', 'Đã cập nhật thông tin xe.');
    }

    public function destroy(Request $request, $maXe)
{
    try {
        $daXoa = DB::transaction(function () use ($request, $maXe) {
            $xe = Xe::query()
                ->where('MaTK', $request->user()->MaTK)
                ->where('MaXe', $maXe)
                ->lockForUpdate()
                ->firstOrFail();

            // Giữ lịch sử của mọi lịch hẹn, kể cả đã hủy hoặc hoàn tất.
            $daCoLichHen = DB::table('LICHHEN')
                ->where('MaXe', $xe->MaXe)
                ->exists();

            if ($daCoLichHen) {
                return false;
            }

            $xe->delete();

            return true;
        }, 3);
    } catch (QueryException $exception) {
        if ((int) ($exception->errorInfo[1] ?? 0) !== 1451) {
            throw $exception;
        }

        $daXoa = false;
    }

    if (!$daXoa) {
        return redirect()
            ->route('public.xe.index')
            ->with(
                'error',
                'Xe đã có lịch hẹn nên không thể xóa. '
                . 'Thông tin xe được giữ lại để bảo toàn lịch sử sửa chữa, '
                . 'hóa đơn và bảo hành.'
            );
    }

    return redirect()
        ->route('public.xe.index')
        ->with('success', 'Đã xóa xe khỏi tài khoản của bạn.');
}

    private function hasOpenAppointment(Xe $xe): bool
    {
        return DB::table('LICHHEN')
            ->where('MaXe', $xe->MaXe)
            ->whereNotIn('TrangThai', [
                'HOAN_TAT',
                'DA_HUY',
            ])
            ->exists();
    }

    private function validateVehicle(
        Request $request,
        ?Xe $xe = null
    ): array {
        $normalized = [];

        foreach (['BienSo', 'HangXe', 'DongXe', 'MauSac'] as $field) {
            $value = $request->input($field);

            if (is_string($value)) {
                $normalized[$field] = trim($value);
            }
        }

        if (isset($normalized['BienSo'])) {
            $normalized['BienSo'] = mb_strtoupper(
                $normalized['BienSo'],
                'UTF-8'
            );
        }

        $request->merge($normalized);

        $uniquePlate = Rule::unique('XE', 'BienSo');

        if ($xe) {
            $uniquePlate->ignore($xe->MaXe, 'MaXe');
        }

        $data = $request->validate([
            'BienSo' => [
                'bail',
                'required',
                'string',
                'regex:/\A[0-9]{2}[A-Z][A-Z0-9]?-[0-9]{4,5}\z/',
                $uniquePlate,
            ],

            'HangXe' => [
                'bail',
                'required',
                'string',
                'max:50',
            ],

            'DongXe' => [
                'bail',
                'required',
                'string',
                'max:70',
            ],

            'NamSX' => [
                'bail',
                'nullable',
                'integer',
                'min:1900',
                'max:' . now('Asia/Ho_Chi_Minh')->year,
            ],

            'MauSac' => [
                'bail',
                'nullable',
                'string',
                'max:40',
            ],
        ], [
            'BienSo.required' => 'Vui lòng nhập biển số xe.',
            'BienSo.string' => 'Biển số xe không hợp lệ.',
            'BienSo.regex' => 'Biển số phải gồm 2 số đầu, tiếp theo là '
                . '1 chữ và có thể thêm 1 chữ hoặc 1 số, dấu gạch ngang, '
                . 'cuối cùng là 4 hoặc 5 số. '
                . 'Ví dụ: 51A-12345, 51AB-12345 hoặc 51A1-1234.',
            'BienSo.unique' => 'Biển số này đã được đăng ký trong hệ thống.',

            'HangXe.required' => 'Vui lòng chọn hoặc nhập hãng xe.',
            'HangXe.string' => 'Hãng xe không hợp lệ.',
            'HangXe.max' => 'Tên hãng xe không được vượt quá 50 ký tự.',

            'DongXe.required' => 'Vui lòng chọn hoặc nhập dòng xe.',
            'DongXe.string' => 'Dòng xe không hợp lệ.',
            'DongXe.max' => 'Tên dòng xe không được vượt quá 70 ký tự.',

            'NamSX.integer' => 'Năm sản xuất phải là số nguyên.',
            'NamSX.min' => 'Năm sản xuất phải từ năm 1900.',
            'NamSX.max' => 'Năm sản xuất không được lớn hơn năm hiện tại.',

            'MauSac.string' => 'Màu sắc không hợp lệ.',
            'MauSac.max' => 'Màu sắc không được vượt quá 40 ký tự.',
        ]);

        // Chuẩn hóa tên trùng với danh mục.
        // Vẫn cho phép nhập hãng và dòng xe chưa có.
        foreach (config('vehicle_catalog', []) as $make => $models) {
            if (
                mb_strtolower($make, 'UTF-8')
                !== mb_strtolower($data['HangXe'], 'UTF-8')
            ) {
                continue;
            }

            $data['HangXe'] = $make;

            foreach ($models as $model) {
                if (
                    mb_strtolower($model, 'UTF-8')
                    === mb_strtolower($data['DongXe'], 'UTF-8')
                ) {
                    $data['DongXe'] = $model;
                    break;
                }
            }

            break;
        }

        return [
            'BienSo' => $data['BienSo'],
            'HangXe' => $data['HangXe'],
            'DongXe' => $data['DongXe'],
            'NamSX' => isset($data['NamSX'])
                ? (int) $data['NamSX']
                : null,
            'MauSac' => ($data['MauSac'] ?? '') !== ''
                ? $data['MauSac']
                : null,
        ];
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\PhuTung;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhuTungController extends Controller
{
    public function index()
    {
        $danhSach = PhuTung::query()
            ->orderByDesc('MaPT')
            ->get();

        return view('phutung.index', compact('danhSach'));
    }

    public function create()
    {
        return view('phutung.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatePart($request);

        PhuTung::create($data);

        return redirect()
            ->route('phutung.index')
            ->with('success', 'Đã thêm phụ tùng.');
    }

    public function edit($id)
    {
        $phuTung = PhuTung::findOrFail($id);

        return view('phutung.edit', compact('phuTung'));
    }

    public function update(Request $request, $id)
    {
        PhuTung::findOrFail($id);

        $data = $this->validatePart($request);

        DB::transaction(function () use ($id, $data) {
            $phuTung = PhuTung::query()
                ->where('MaPT', $id)
                ->lockForUpdate()
                ->firstOrFail();

            $phuTung->update($data);
        });

        return redirect()
            ->route('phutung.index')
            ->with('success', 'Đã cập nhật phụ tùng.');
    }

    public function destroy($id)
    {
        try {
            $daXoa = DB::transaction(function () use ($id) {
                $phuTung = PhuTung::query()
                    ->where('MaPT', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $daDuocSuDung = DB::table('CHITIETLICHHEN')
                    ->where('MaPT', $phuTung->MaPT)
                    ->exists();

                if ($daDuocSuDung) {
                    return false;
                }

                $phuTung->delete();

                return true;
            });
        } catch (QueryException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) !== 1451) {
                throw $exception;
            }

            $daXoa = false;
        }

        if (!$daXoa) {
            return redirect()
                ->route('phutung.index')
                ->with(
                    'error',
                    'Phụ tùng đã có trong lịch hẹn nên không thể xóa. '
                    . 'Hãy chuyển sang ngừng sử dụng để giữ lịch sử.'
                );
        }

        return redirect()
            ->route('phutung.index')
            ->with('success', 'Đã xóa phụ tùng.');
    }

    private function validatePart(Request $request): array
    {
        $normalized = [];

        foreach (['TenPT', 'DonViTinh'] as $field) {
            $value = $request->input($field);

            if (is_string($value)) {
                $normalized[$field] = trim($value);
            }
        }

        $request->merge($normalized);

        $data = $request->validate([
            'TenPT' => ['bail', 'required', 'string', 'max:150'],
            'DonViTinh' => ['bail', 'required', 'string', 'max:30'],
            'GiaBan' => [
                'bail',
                'required',
                'integer',
                'min:0',
                'max:999999999999',
            ],
            'SoLuongTon' => [
                'bail',
                'required',
                'integer',
                'min:0',
                'max:2147483647',
            ],
            'HoatDong' => ['bail', 'required', 'boolean'],
        ], [
            'TenPT.required' => 'Vui lòng nhập tên phụ tùng.',
            'TenPT.string' => 'Tên phụ tùng không hợp lệ.',
            'TenPT.max' => 'Tên phụ tùng không được vượt quá 150 ký tự.',

            'DonViTinh.required' => 'Vui lòng nhập đơn vị tính.',
            'DonViTinh.string' => 'Đơn vị tính không hợp lệ.',
            'DonViTinh.max' => 'Đơn vị tính không được vượt quá 30 ký tự.',

            'GiaBan.required' => 'Vui lòng nhập giá bán.',
            'GiaBan.integer' => 'Giá bán phải là số nguyên.',
            'GiaBan.min' => 'Giá bán không được nhỏ hơn 0.',
            'GiaBan.max' => 'Giá bán vượt quá giới hạn cho phép.',

            'SoLuongTon.required' => 'Vui lòng nhập số lượng tồn.',
            'SoLuongTon.integer' => 'Số lượng tồn phải là số nguyên.',
            'SoLuongTon.min' => 'Số lượng tồn không được nhỏ hơn 0.',
            'SoLuongTon.max' => 'Số lượng tồn vượt quá giới hạn cho phép.',

            'HoatDong.required' => 'Vui lòng chọn trạng thái.',
            'HoatDong.boolean' => 'Trạng thái không hợp lệ.',
        ]);

        return [
            'TenPT' => $data['TenPT'],
            'DonViTinh' => $data['DonViTinh'],
            'GiaBan' => (int) $data['GiaBan'],
            'SoLuongTon' => (int) $data['SoLuongTon'],
            'HoatDong' => (bool) $data['HoatDong'],
        ];
    }
}
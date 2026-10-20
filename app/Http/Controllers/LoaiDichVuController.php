<?php

namespace App\Http\Controllers;

use App\Models\LoaiDichVu;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LoaiDichVuController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ], [
            'tu_khoa.string' => 'Từ khóa không hợp lệ.',
            'tu_khoa.max' => 'Từ khóa không được vượt quá 100 ký tự.',
            'page.integer' => 'Trang không hợp lệ.',
            'page.min' => 'Trang phải từ 1.',
        ]);

        $tuKhoa = trim($data['tu_khoa'] ?? '');

        $danhSach = LoaiDichVu::query()
            ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                $query->where('TenLoaiDV', 'like', "%{$tuKhoa}%");
            })
            ->orderByDesc('MaLoaiDV')
            ->paginate(10)
            ->withQueryString();

        if ($danhSach->currentPage() > $danhSach->lastPage()) {
            return redirect()->route('loaidichvu.index', [
                'tu_khoa' => $tuKhoa,
                'page' => $danhSach->lastPage(),
            ]);
        }

        return view('loaidichvu.index', compact('danhSach', 'tuKhoa'));
    }

    public function create()
    {
        return view('loaidichvu.create');
    }

    public function store(Request $request)
    {
        LoaiDichVu::create($this->validateCategory($request));

        return redirect()
            ->route('loaidichvu.index')
            ->with('success', 'Đã thêm loại dịch vụ.');
    }

    public function edit(LoaiDichVu $loaidichvu)
    {
        return view('loaidichvu.edit', compact('loaidichvu'));
    }

    public function update(Request $request, LoaiDichVu $loaidichvu)
    {
        $loaidichvu->update(
            $this->validateCategory($request, $loaidichvu)
        );

        return redirect()
            ->route('loaidichvu.index')
            ->with('success', 'Đã cập nhật loại dịch vụ.');
    }

    private function validateCategory(
        Request $request,
        ?LoaiDichVu $loai = null
    ): array {
        $normalized = [];

        foreach (['TenLoaiDV', 'MoTa'] as $field) {
            $value = $request->input($field);

            if (is_string($value)) {
                $normalized[$field] = trim($value);
            }
        }

        $request->merge($normalized);

        $uniqueName = Rule::unique('LOAIDICHVU', 'TenLoaiDV');

        if ($loai) {
            $uniqueName->ignore($loai->MaLoaiDV, 'MaLoaiDV');
        }

        $data = $request->validate([
            'TenLoaiDV' => [
                'bail',
                'required',
                'string',
                'max:100',
                $uniqueName,
            ],
            'MoTa' => ['nullable', 'string', 'max:5000'],
            'HoatDong' => ['bail', 'required', 'boolean'],
        ], [
            'TenLoaiDV.required' => 'Vui lòng nhập tên loại dịch vụ.',
            'TenLoaiDV.string' => 'Tên loại dịch vụ không hợp lệ.',
            'TenLoaiDV.max' => 'Tên loại dịch vụ không được vượt quá 100 ký tự.',
            'TenLoaiDV.unique' => 'Tên loại dịch vụ đã tồn tại.',

            'MoTa.string' => 'Mô tả không hợp lệ.',
            'MoTa.max' => 'Mô tả không được vượt quá 5.000 ký tự.',

            'HoatDong.required' => 'Vui lòng chọn trạng thái.',
            'HoatDong.boolean' => 'Trạng thái không hợp lệ.',
        ]);

        return [
            'TenLoaiDV' => $data['TenLoaiDV'],
            'MoTa' => $data['MoTa'] ?? null,
            'HoatDong' => (bool) $data['HoatDong'],
        ];
    }
}
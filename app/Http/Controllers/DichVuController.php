<?php

namespace App\Http\Controllers;

use App\Models\DichVu;
use App\Models\LoaiDichVu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

class DichVuController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'tu_khoa' => ['nullable', 'string', 'max:100'],
            'ma_loai' => [
                'nullable',
                'integer',
                Rule::exists('LOAIDICHVU', 'MaLoaiDV'),
            ],
            'page' => ['nullable', 'integer', 'min:1'],
        ], [
            'tu_khoa.string' => 'Từ khóa không hợp lệ.',
            'tu_khoa.max' => 'Từ khóa không được vượt quá 100 ký tự.',
            'ma_loai.integer' => 'Loại dịch vụ không hợp lệ.',
            'ma_loai.exists' => 'Loại dịch vụ không tồn tại.',
            'page.integer' => 'Trang không hợp lệ.',
            'page.min' => 'Trang phải từ 1.',
        ]);

        $tuKhoa = trim($data['tu_khoa'] ?? '');
        $maLoai = $data['ma_loai'] ?? null;

        $danhSach = DichVu::query()
            ->with('loaiDichVu')
            ->when($tuKhoa !== '', function ($query) use ($tuKhoa) {
                $query->where('TenDV', 'like', "%{$tuKhoa}%");
            })
            ->when($maLoai !== null, function ($query) use ($maLoai) {
                $query->where('MaLoaiDV', $maLoai);
            })
            ->orderByDesc('MaDV')
            ->paginate(10)
            ->withQueryString();

        if ($danhSach->currentPage() > $danhSach->lastPage()) {
            return redirect()->route('dichvu.index', [
                'tu_khoa' => $tuKhoa,
                'ma_loai' => $maLoai,
                'page' => $danhSach->lastPage(),
            ]);
        }

        $loaiDichVuList = LoaiDichVu::orderBy('TenLoaiDV')->get();

        return view('dichvu.index', compact(
            'danhSach',
            'loaiDichVuList',
            'tuKhoa',
            'maLoai'
        ));
    }

    public function create()
    {
        $loaiDichVuList = LoaiDichVu::orderBy('TenLoaiDV')->get();

        return view('dichvu.create', compact('loaiDichVuList'));
    }

    public function store(Request $request)
    {
        $data = $this->validateService($request);
        $newImage = null;

        try {
            if ($request->hasFile('HinhAnh')) {
                $newImage = $request->file('HinhAnh')
                    ->store('dich-vu', 'public');

                $data['HinhAnh'] = $newImage;
            }

            DichVu::create($data);
        } catch (Throwable $exception) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        return redirect()
            ->route('dichvu.index')
            ->with('success', 'Đã thêm dịch vụ.');
    }

    public function edit(DichVu $dichvu)
    {
        $loaiDichVuList = LoaiDichVu::orderBy('TenLoaiDV')->get();

        return view('dichvu.edit', compact(
            'dichvu',
            'loaiDichVuList'
        ));
    }

    public function update(Request $request, DichVu $dichvu)
    {
        $data = $this->validateService($request);
        $newImage = null;

        try {
            if ($request->hasFile('HinhAnh')) {
                $newImage = $request->file('HinhAnh')
                    ->store('dich-vu', 'public');

                $data['HinhAnh'] = $newImage;
            }

            // Không chọn ảnh mới thì giữ nguyên ảnh hiện tại.
            $dichvu->update($data);
        } catch (Throwable $exception) {
            if ($newImage) {
                Storage::disk('public')->delete($newImage);
            }

            throw $exception;
        }

        return redirect()
            ->route('dichvu.index')
            ->with('success', 'Đã cập nhật dịch vụ.');
    }

    public function destroy(DichVu $dichvu)
{
    try {
        $daXoa = DB::transaction(function () use ($dichvu) {
            $service = DichVu::query()
                ->where('MaDV', $dichvu->MaDV)
                ->lockForUpdate()
                ->firstOrFail();

            $daDuocSuDung = DB::table('CHITIETLICHHEN')
                ->where('MaDV', $service->MaDV)
                ->exists();

            if ($daDuocSuDung) {
                return false;
            }

            $service->delete();

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
            ->route('dichvu.index')
            ->with(
                'error',
                'Dịch vụ đã có trong lịch hẹn nên không thể xóa. '
                . 'Hãy chuyển sang ngừng hoạt động để giữ lịch sử.'
            );
    }

    return redirect()
        ->route('dichvu.index')
        ->with('success', 'Đã xóa dịch vụ.');
}

    private function validateService(Request $request): array
    {
        $normalized = [];

        foreach (['TenDV', 'MoTa'] as $field) {
            $value = $request->input($field);

            if (is_string($value)) {
                $normalized[$field] = trim($value);
            }
        }

        $request->merge($normalized);

        $data = $request->validate([
            'MaLoaiDV' => [
                'bail',
                'required',
                'integer',
                Rule::exists('LOAIDICHVU', 'MaLoaiDV'),
            ],
            'TenDV' => ['bail', 'required', 'string', 'max:150'],
            'MoTa' => ['nullable', 'string', 'max:10000'],
            'Gia' => [
                'bail',
                'required',
                'integer',
                'min:0',
                'max:999999999999',
            ],
            'HinhAnh' => [
                'bail',
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'HoatDong' => ['bail', 'required', 'boolean'],
        ], [
            'MaLoaiDV.required' => 'Vui lòng chọn loại dịch vụ.',
            'MaLoaiDV.integer' => 'Loại dịch vụ không hợp lệ.',
            'MaLoaiDV.exists' => 'Loại dịch vụ không tồn tại.',

            'TenDV.required' => 'Vui lòng nhập tên dịch vụ.',
            'TenDV.string' => 'Tên dịch vụ không hợp lệ.',
            'TenDV.max' => 'Tên dịch vụ không được vượt quá 150 ký tự.',

            'MoTa.string' => 'Mô tả không hợp lệ.',
            'MoTa.max' => 'Mô tả không được vượt quá 10.000 ký tự.',

            'Gia.required' => 'Vui lòng nhập giá dịch vụ.',
            'Gia.integer' => 'Giá dịch vụ phải là số nguyên.',
            'Gia.min' => 'Giá dịch vụ không được nhỏ hơn 0.',
            'Gia.max' => 'Giá dịch vụ vượt quá giới hạn cho phép.',

            'HinhAnh.uploaded' => 'Không tải được ảnh. Vui lòng kiểm tra dung lượng file.',
            'HinhAnh.image' => 'Vui lòng chọn file hình ảnh.',
            'HinhAnh.mimes' => 'Ảnh phải có định dạng JPG, JPEG, PNG hoặc WEBP.',
            'HinhAnh.max' => 'Ảnh không được vượt quá 2 MB.',

            'HoatDong.required' => 'Vui lòng chọn trạng thái.',
            'HoatDong.boolean' => 'Trạng thái không hợp lệ.',
        ]);

        // File ảnh được xử lý riêng, không đưa UploadedFile vào model.
        unset($data['HinhAnh']);

        $data['MaLoaiDV'] = (int) $data['MaLoaiDV'];
        $data['Gia'] = (int) $data['Gia'];
        $data['HoatDong'] = (bool) $data['HoatDong'];

        return $data;
    }
}
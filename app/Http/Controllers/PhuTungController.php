<?php

namespace App\Http\Controllers;

use App\Models\PhuTung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhuTungController extends Controller
{
    public function index()
    {
        $danhSach = PhuTung::orderBy('MaPT', 'desc')->get();

        return view('phutung.index', compact('danhSach'));
    }

    public function create()
    {
        return view('phutung.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'TenPT' => 'required|string|max:100',
                'DonViTinh' => 'required|string|max:30',
                'Gia' => 'required|numeric|min:0|max:9999999999.99',
                'SoLuongTon' => 'required|integer|min:0',
                'TrangThai' => 'required|in:DANG_SU_DUNG,NGUNG_SU_DUNG'
            ],
            [
                'TenPT.required' => 'Vui lòng nhập tên phụ tùng.',
                'TenPT.string' => 'Tên phụ tùng phải là chuỗi.',
                'TenPT.max' => 'Tên phụ tùng không được vượt quá 100 ký tự.',

                'DonViTinh.required' => 'Vui lòng nhập đơn vị tính.',
                'DonViTinh.string' => 'Đơn vị tính phải là chuỗi.',
                'DonViTinh.max' => 'Đơn vị tính không được vượt quá 30 ký tự.',

                'Gia.required' => 'Vui lòng nhập giá hợp lệ.',
                'Gia.numeric' => 'Vui lòng nhập giá hợp lệ.',
                'Gia.min' => 'Vui lòng nhập giá hợp lệ.',
                'Gia.max' => 'Vui lòng nhập giá hợp lệ.',

                'SoLuongTon.required' => 'Vui lòng nhập số lượng tồn.',
                'SoLuongTon.integer' => 'Số lượng tồn phải là số nguyên.',
                'SoLuongTon.min' => 'Số lượng tồn không được nhỏ hơn 0.',

                'TrangThai.required' => 'Vui lòng chọn trạng thái.',
                'TrangThai.in' => 'Trạng thái không hợp lệ.'
            ]
        );

        PhuTung::create($data);

        return redirect()
            ->route('phutung.index')
            ->with('success', 'Thêm phụ tùng thành công.');
    }

    public function edit($id)
    {
        $phuTung = PhuTung::findOrFail($id);

        return view('phutung.edit', compact('phuTung'));
    }

    public function update(Request $request, $id)
    {
        $phuTung = PhuTung::findOrFail($id);

        $data = $request->validate(
            [
                'TenPT' => 'required|string|max:100',
                'DonViTinh' => 'required|string|max:30',
                'Gia' => 'required|numeric|min:0|max:9999999999.99',
                'SoLuongTon' => 'required|integer|min:0',
                'TrangThai' => 'required|in:DANG_SU_DUNG,NGUNG_SU_DUNG'
            ],
            [
                'TenPT.required' => 'Vui lòng nhập tên phụ tùng.',
                'TenPT.string' => 'Tên phụ tùng phải là chuỗi.',
                'TenPT.max' => 'Tên phụ tùng không được vượt quá 100 ký tự.',

                'DonViTinh.required' => 'Vui lòng nhập đơn vị tính.',
                'DonViTinh.string' => 'Đơn vị tính phải là chuỗi.',
                'DonViTinh.max' => 'Đơn vị tính không được vượt quá 30 ký tự.',

                'Gia.required' => 'Vui lòng nhập giá hợp lệ.',
                'Gia.numeric' => 'Vui lòng nhập giá hợp lệ.',
                'Gia.min' => 'Vui lòng nhập giá hợp lệ.',
                'Gia.max' => 'Vui lòng nhập giá hợp lệ.',

                'SoLuongTon.required' => 'Vui lòng nhập số lượng tồn.',
                'SoLuongTon.integer' => 'Số lượng tồn phải là số nguyên.',
                'SoLuongTon.min' => 'Số lượng tồn không được nhỏ hơn 0.',

                'TrangThai.required' => 'Vui lòng chọn trạng thái.',
                'TrangThai.in' => 'Trạng thái không hợp lệ.'
            ]
        );

        $phuTung->update($data);

        return redirect()
            ->route('phutung.index')
            ->with('success', 'Cập nhật phụ tùng thành công.');
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
    } catch (\Illuminate\Database\QueryException $exception) {
        // Trường hợp phát sinh liên kết mới ngay lúc xóa.
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
                'Không thể xóa phụ tùng đã có trong lịch hẹn. '
                . 'Bạn có thể sửa và chuyển sang ngừng sử dụng '
                . 'để giữ lại lịch sử.'
            );
    }

    return redirect()
        ->route('phutung.index')
        ->with('success', 'Đã xóa phụ tùng.');
}
}
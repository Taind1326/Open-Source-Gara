<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhuTungController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\ThongKeController;

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| M05 - PHỤ TÙNG
|--------------------------------------------------------------------------
*/

Route::resource(
    'phutung',
    PhuTungController::class
)->except(['show']);


/*
|--------------------------------------------------------------------------
| M11 - HÓA ĐƠN & THANH TOÁN
|--------------------------------------------------------------------------
*/

// Admin xem danh sách hóa đơn
Route::get(
    '/admin/hoadon',
    [HoaDonController::class, 'adminIndex']
)->name('hoadon.admin.index');

// Admin lập hóa đơn
Route::post(
    '/admin/hoadon/tao/{maPSC}',
    [HoaDonController::class, 'taoHoaDon']
)->name('hoadon.admin.tao');

// User xem danh sách hóa đơn
Route::get(
    '/hoadon',
    [HoaDonController::class, 'index']
)->name('hoadon.index');

// User xem chi tiết hóa đơn
Route::get(
    '/hoadon/{maHD}',
    [HoaDonController::class, 'show']
)->name('hoadon.show');

// User áp dụng điểm
Route::post(
    '/hoadon/{maHD}/ap-dung-diem',
    [HoaDonController::class, 'apDungDiem']
)->name('hoadon.ap-dung-diem');

// User bỏ sử dụng điểm
Route::post(
    '/hoadon/{maHD}/bo-diem',
    [HoaDonController::class, 'boDiem']
)->name('hoadon.bo-diem');

// User thanh toán
Route::post(
    '/hoadon/{maHD}/thanh-toan',
    [HoaDonController::class, 'thanhToan']
)->name('hoadon.thanh-toan');

/*
|--------------------------------------------------------------------------
| M12 - THỐNG KÊ
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/thongke',
    [ThongKeController::class, 'index']
)->name('thongke.admin.index');
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhuTungController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\ThongKeController;
use App\Http\Controllers\LoaiDichVuController;
use App\Http\Controllers\DichVuController;
use App\Http\Controllers\SuaChuaController;

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

// M04 - Loại dịch vụ
Route::resource('loaidichvu', LoaiDichVuController::class)
    ->except(['show', 'destroy']);

// M04 - Dịch vụ
Route::resource('dichvu', DichVuController::class)
    ->except(['show', 'destroy']);

use App\Http\Controllers\KiemTraXeController;

Route::get('/kiem-tra-xe/{maYC}',
    [KiemTraXeController::class, 'create'])
    ->name('kiemtraxe.create');

Route::post('/kiem-tra-xe/{maYC}',
    [KiemTraXeController::class, 'store'])
    ->name('kiemtraxe.store');

Route::get('/kiem-tra-xe/{maYC}/ket-qua',
    [KiemTraXeController::class, 'show'])
    ->name('kiemtraxe.show');

    use App\Http\Controllers\BaoGiaController;

Route::get(
    '/bao-gia/{maYC}/lap',
    [BaoGiaController::class, 'create']
)->name('baogia.create');

Route::post(
    '/bao-gia/{maYC}',
    [BaoGiaController::class, 'store']
)->name('baogia.store');

Route::get(
    '/bao-gia/{maYC}',
    [BaoGiaController::class, 'show']
)->name('baogia.show');

Route::post(
    '/bao-gia/{maYC}/dong-y',
    [BaoGiaController::class, 'approve']
)->name('baogia.approve');

Route::post(
    '/bao-gia/{maYC}/tu-choi',
    [BaoGiaController::class, 'reject']
)->name('baogia.reject');

/*
|--------------------------------------------------------------------------
| M10 - SỬA CHỮA & TIẾN ĐỘ
|--------------------------------------------------------------------------
*/

Route::get(
    '/sua-chua/{maYC}/bat-dau',
    [SuaChuaController::class, 'create']
)->name('suachua.create');

Route::post(
    '/sua-chua/{maYC}/bat-dau',
    [SuaChuaController::class, 'store']
)->name('suachua.store');

Route::get(
    '/sua-chua/{maYC}',
    [SuaChuaController::class, 'show']
)->name('suachua.show');

Route::post(
    '/sua-chua/{maYC}/dich-vu',
    [SuaChuaController::class, 'addService']
)->name('suachua.service');

Route::post(
    '/sua-chua/{maYC}/phu-tung',
    [SuaChuaController::class, 'addPart']
)->name('suachua.part');

Route::post(
    '/sua-chua/{maYC}/tien-do',
    [SuaChuaController::class, 'addProgress']
)->name('suachua.progress');

Route::post(
    '/sua-chua/{maYC}/hoan-thanh',
    [SuaChuaController::class, 'complete']
)->name('suachua.complete');

/*
|--------------------------------------------------------------------------
| DỊCH VỤ PUBLIC
|--------------------------------------------------------------------------
*/

Route::get(
    '/dich-vu',
    [\App\Http\Controllers\DichVuPublicController::class, 'index']
)->name('public.dichvu.index');

Route::get(
    '/dich-vu/{maDV}',
    [\App\Http\Controllers\DichVuPublicController::class, 'show']
)
    ->whereNumber('maDV')
    ->name('public.dichvu.show');

Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [
        \App\Http\Controllers\AuthController::class,
        'showLogin',
    ])->name('login');

    Route::post('/dang-nhap', [
        \App\Http\Controllers\AuthController::class,
        'login',
    ])->middleware('throttle:5,1')->name('login.store');

    Route::get('/dang-ky', [
        \App\Http\Controllers\AuthController::class,
        'showRegister',
    ])->name('register');

    Route::post('/dang-ky', [
        \App\Http\Controllers\AuthController::class,
        'register',
    ])->middleware('throttle:5,1')->name('register.store');
});

Route::post('/dang-xuat', [
    \App\Http\Controllers\AuthController::class,
    'logout',
])->middleware('auth')->name('logout');
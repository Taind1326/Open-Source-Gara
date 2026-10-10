<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaoGiaController;
use App\Http\Controllers\DichVuController;
use App\Http\Controllers\DichVuPublicController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\KiemTraXeController;
use App\Http\Controllers\LoaiDichVuController;
use App\Http\Controllers\PhuTungController;
use App\Http\Controllers\SuaChuaController;
use App\Http\Controllers\ThanhToanController;
use App\Http\Controllers\ThongKeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TRANG PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('public.dichvu.index');
});

Route::get(
    '/dich-vu',
    [DichVuPublicController::class, 'index']
)->name('public.dichvu.index');

Route::get(
    '/dich-vu/{maDV}',
    [DichVuPublicController::class, 'show']
)
    ->whereNumber('maDV')
    ->name('public.dichvu.show');

/*
|--------------------------------------------------------------------------
| ĐĂNG NHẬP & ĐĂNG KÝ
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get(
        '/dang-nhap',
        [AuthController::class, 'showLogin']
    )->name('login');

    Route::post(
        '/dang-nhap',
        [AuthController::class, 'login']
    )
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::get(
        '/dang-ky',
        [AuthController::class, 'showRegister']
    )->name('register');

    Route::post(
        '/dang-ky',
        [AuthController::class, 'register']
    )
        ->middleware('throttle:5,1')
        ->name('register.store');
});

Route::post(
    '/dang-xuat',
    [AuthController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| QUẢN TRỊ
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::view(
        '/admin',
        'admin.dashboard'
    )->name('admin.dashboard');

    Route::resource(
        'loaidichvu',
        LoaiDichVuController::class
    )->except(['show', 'destroy']);

    Route::resource(
        'dichvu',
        DichVuController::class
    )->except(['show', 'destroy']);

    Route::resource(
        'phutung',
        PhuTungController::class
    )->except(['show']);

    Route::get(
        '/admin/hoadon',
        [HoaDonController::class, 'adminIndex']
    )->name('hoadon.admin.index');

    Route::post(
        '/admin/hoadon/tao/{maPSC}',
        [HoaDonController::class, 'taoHoaDon']
    )
        ->whereNumber('maPSC')
        ->name('hoadon.admin.tao');

    Route::get(
        '/admin/hoadon/{maHD}',
        [ThanhToanController::class, 'adminShow']
    )
        ->whereNumber('maHD')
        ->name('hoadon.admin.show');

    Route::post(
        '/admin/hoadon/{maHD}/xac-nhan-thanh-toan',
        [ThanhToanController::class, 'confirmPayment']
    )
        ->whereNumber('maHD')
        ->name('hoadon.admin.confirm');

    Route::get(
        '/admin/thongke',
        [ThongKeController::class, 'index']
    )->name('thongke.admin.index');
});

/*
|--------------------------------------------------------------------------
| NGHIỆP VỤ: TÀI KHOẢN ĐANG HOẠT ĐỘNG
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:ADMIN,TECHNICIAN,USER',
])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | HÓA ĐƠN KHÁCH HÀNG
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:USER')->group(function () {
        Route::get(
            '/hoadon',
            [HoaDonController::class, 'index']
        )->name('hoadon.index');

        Route::get(
            '/hoadon/{maHD}',
            [HoaDonController::class, 'show']
        )
            ->whereNumber('maHD')
            ->name('hoadon.show');

        Route::post(
            '/hoadon/{maHD}/ap-dung-diem',
            [HoaDonController::class, 'apDungDiem']
        )
            ->whereNumber('maHD')
            ->name('hoadon.ap-dung-diem');

        Route::post(
            '/hoadon/{maHD}/bo-diem',
            [HoaDonController::class, 'boDiem']
        )
            ->whereNumber('maHD')
            ->name('hoadon.bo-diem');

        Route::post(
            '/hoadon/{maHD}/thanh-toan',
            [ThanhToanController::class, 'requestPayment']
        )
            ->whereNumber('maHD')
            ->name('hoadon.thanh-toan');
    });

    /*
    |--------------------------------------------------------------------------
    | KIỂM TRA XE
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:ADMIN,TECHNICIAN')->group(function () {
        Route::get(
            '/kiem-tra-xe/{maYC}',
            [KiemTraXeController::class, 'create']
        )
            ->whereNumber('maYC')
            ->name('kiemtraxe.create');

        Route::post(
            '/kiem-tra-xe/{maYC}',
            [KiemTraXeController::class, 'store']
        )
            ->whereNumber('maYC')
            ->name('kiemtraxe.store');
    });

    Route::get(
        '/kiem-tra-xe/{maYC}/ket-qua',
        [KiemTraXeController::class, 'show']
    )
        ->whereNumber('maYC')
        ->name('kiemtraxe.show');

    /*
    |--------------------------------------------------------------------------
    | LẬP BÁO GIÁ
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:ADMIN')->group(function () {
        Route::get(
            '/bao-gia/{maYC}/lap',
            [BaoGiaController::class, 'create']
        )
            ->whereNumber('maYC')
            ->name('baogia.create');

        Route::post(
            '/bao-gia/{maYC}',
            [BaoGiaController::class, 'store']
        )
            ->whereNumber('maYC')
            ->name('baogia.store');
    });

    Route::get(
        '/bao-gia/{maYC}',
        [BaoGiaController::class, 'show']
    )
        ->whereNumber('maYC')
        ->name('baogia.show');

    /*
    |--------------------------------------------------------------------------
    | KHÁCH DUYỆT BÁO GIÁ
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:USER')->group(function () {
        Route::post(
            '/bao-gia/{maYC}/dong-y',
            [BaoGiaController::class, 'approve']
        )
            ->whereNumber('maYC')
            ->name('baogia.approve');

        Route::post(
            '/bao-gia/{maYC}/tu-choi',
            [BaoGiaController::class, 'reject']
        )
            ->whereNumber('maYC')
            ->name('baogia.reject');
    });

    /*
    |--------------------------------------------------------------------------
    | XEM SỬA CHỮA & TIẾN ĐỘ
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/sua-chua/{maYC}',
        [SuaChuaController::class, 'show']
    )
        ->whereNumber('maYC')
        ->name('suachua.show');

    /*
    |--------------------------------------------------------------------------
    | THAO TÁC SỬA CHỮA
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:ADMIN,TECHNICIAN')->group(function () {
        Route::get(
            '/sua-chua/{maYC}/bat-dau',
            [SuaChuaController::class, 'create']
        )
            ->whereNumber('maYC')
            ->name('suachua.create');

        Route::post(
            '/sua-chua/{maYC}/bat-dau',
            [SuaChuaController::class, 'store']
        )
            ->whereNumber('maYC')
            ->name('suachua.store');

        Route::post(
            '/sua-chua/{maYC}/dich-vu',
            [SuaChuaController::class, 'addService']
        )
            ->whereNumber('maYC')
            ->name('suachua.service');

        Route::post(
            '/sua-chua/{maYC}/phu-tung',
            [SuaChuaController::class, 'addPart']
        )
            ->whereNumber('maYC')
            ->name('suachua.part');

        Route::post(
            '/sua-chua/{maYC}/tien-do',
            [SuaChuaController::class, 'addProgress']
        )
            ->whereNumber('maYC')
            ->name('suachua.progress');

        Route::post(
            '/sua-chua/{maYC}/hoan-thanh',
            [SuaChuaController::class, 'complete']
        )
            ->whereNumber('maYC')
            ->name('suachua.complete');
    });
});
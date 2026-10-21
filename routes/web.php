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
use App\Http\Controllers\TaiKhoanController;
use App\Http\Controllers\XeController;
use App\Http\Controllers\DatLichController;
use App\Http\Controllers\DemoPasswordResetController;

/*
|--------------------------------------------------------------------------
| TRANG PUBLIC
|--------------------------------------------------------------------------
*/

Route::view('/', 'public.home')->name('public.home');
Route::view('/gioi-thieu', 'public.about')->name('public.about');
Route::view('/lien-he', 'public.contact')->name('public.contact');

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

    Route::middleware(['auth', 'role:ADMIN,TECHNICIAN,USER'])
    ->group(function () {
        Route::get('/tai-khoan', [TaiKhoanController::class, 'edit'])
            ->name('public.taikhoan.edit');

        Route::patch('/tai-khoan', [TaiKhoanController::class, 'update'])
            ->name('public.taikhoan.update');

        Route::patch('/tai-khoan/mat-khau', [TaiKhoanController::class, 'updatePassword'])
            ->middleware('throttle:5,1')
            ->name('public.taikhoan.password');
    });

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
| XE CỦA KHÁCH HÀNG
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:USER'])->group(function () {
    Route::get('/xe-cua-toi', [XeController::class, 'index'])
        ->name('public.xe.index');

    Route::get('/xe-cua-toi/them', [XeController::class, 'create'])
        ->name('public.xe.create');

    Route::post('/xe-cua-toi', [XeController::class, 'store'])
        ->name('public.xe.store');

    Route::get('/xe-cua-toi/{maXe}/sua', [XeController::class, 'edit'])
        ->whereNumber('maXe')
        ->name('public.xe.edit');

    Route::patch('/xe-cua-toi/{maXe}', [XeController::class, 'update'])
        ->whereNumber('maXe')
        ->name('public.xe.update');

    Route::patch('/xe-cua-toi/{maXe}/trang-thai', [XeController::class, 'updateStatus'])
        ->whereNumber('maXe')
        ->name('public.xe.status');

    Route::get('/danh-muc-xe/hang', [XeController::class, 'makes'])
        ->middleware('throttle:30,1')
        ->name('public.xe.catalog.makes');

    Route::get('/danh-muc-xe/dong', [XeController::class, 'models'])
        ->middleware('throttle:30,1')
        ->name('public.xe.catalog.models');
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

    /*
|--------------------------------------------------------------------------
| ĐẶT LỊCH KHÁCH HÀNG
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:USER'])->group(function () {
    Route::get(
        '/lich-hen',
        [DatLichController::class, 'index']
    )->name('public.datlich.index');

    Route::get(
        '/dat-lich',
        [DatLichController::class, 'create']
    )->name('public.datlich.create');

    Route::get(
        '/dat-lich/khung-gio',
        [DatLichController::class, 'slots']
    )
        ->middleware('throttle:30,1')
        ->name('public.datlich.slots');

    Route::post(
        '/dat-lich',
        [DatLichController::class, 'store']
    )
        ->middleware('throttle:5,1')
        ->name('public.datlich.store');

    Route::patch(
        '/lich-hen/{maYC}/huy',
        [DatLichController::class, 'cancel']
    )
        ->whereNumber('maYC')
        ->name('public.datlich.cancel');
});
});
// Form liên hệ Public và hộp thư dành cho Admin.
Route::post('/lien-he', [\App\Http\Controllers\ContactController::class, 'store'])
    ->middleware('throttle:5,1')->name('public.contact.store');
Route::get('/admin/tin-nhan-lien-he', [\App\Http\Controllers\ContactController::class, 'index'])
    ->middleware(['auth', 'role:ADMIN'])->name('admin.contact.index');

    Route::middleware('guest')->group(function () {
    Route::get('/quen-mat-khau', [DemoPasswordResetController::class, 'show'])->name('password.demo');
    Route::post('/quen-mat-khau', [DemoPasswordResetController::class, 'start'])->middleware('throttle:5,1')->name('password.demo.start');
    Route::post('/quen-mat-khau/xac-nhan', [DemoPasswordResetController::class, 'verify'])->middleware('throttle:10,1')->name('password.demo.verify');
    Route::post('/quen-mat-khau/gui-lai', [DemoPasswordResetController::class, 'resend'])->middleware('throttle:3,1')->name('password.demo.resend');
    Route::post('/quen-mat-khau/dat-lai', [DemoPasswordResetController::class, 'reset'])->middleware('throttle:5,1')->name('password.demo.reset');
    Route::post('/quen-mat-khau/bat-dau-lai', [DemoPasswordResetController::class, 'restart'])->middleware('throttle:5,1')->name('password.demo.restart');
});

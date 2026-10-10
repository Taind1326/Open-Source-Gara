<?php

// Route của TV1 (M01 Auth, M02 Tài khoản, M03 Hồ sơ/Xe). Được nạp từ routes/web.php bằng 1 dòng:
//     require __DIR__ . '/tv1.php';

use App\Http\Controllers\Admin\TaiKhoanController;
use App\Http\Controllers\Admin\XeController as AdminXeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HoSoController;
use App\Http\Controllers\XeController;
use App\Http\Controllers\XeDanhMucController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| TV1 - M01 Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.xu-ly');
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('dang-ky');
    Route::post('/dang-ky', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('dang-ky.xu-ly');
});

Route::post('/dang-xuat', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Khu vực đã đăng nhập (tài khoản đang HOẠT ĐỘNG)
| Nhóm khác thêm route của mình vào trong các group tương ứng bên dưới:
|   - khách hàng : group role:USER
|   - admin      : group role:ADMIN (prefix admin, name admin.)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'hoat-dong'])->group(function () {
    Route::get('/trang-chu', [AuthController::class, 'trangChu'])->name('trang-chu');

    // M03 - Hồ sơ (mọi vai trò)
    Route::get('/ho-so', [HoSoController::class, 'show'])->name('ho-so.show');
    Route::put('/ho-so', [HoSoController::class, 'update'])->name('ho-so.update');
    Route::put('/ho-so/mat-khau', [HoSoController::class, 'doiMatKhau'])->name('ho-so.mat-khau');

    // M03 - API gợi ý Hãng → Năm → Dòng (khách và admin cùng dùng khi nhập xe)
    Route::middleware('role:USER,ADMIN')->prefix('xe-api')->name('xe-api.')->group(function () {
        Route::get('hang', [XeDanhMucController::class, 'hang'])->name('hang');
        Route::get('nam', [XeDanhMucController::class, 'nam'])->name('nam');
        Route::get('dong', [XeDanhMucController::class, 'dong'])->name('dong');
    });

    // M02 - Khách hàng
    Route::middleware('role:USER')->group(function () {
        // M03 - Xe của tôi
        Route::resource('xe', XeController::class)->except('show');

        // M06 (nhóm đặt lịch) thêm route lichhen.* vào đây
    });

    // M02 - Admin
    Route::middleware('role:ADMIN')->prefix('admin')->name('admin.')->group(function () {
        // M03 - Quản lý tài khoản
        Route::get('tai-khoan', [TaiKhoanController::class, 'index'])->name('taikhoan.index');
        Route::get('tai-khoan/tao', [TaiKhoanController::class, 'create'])->name('taikhoan.create');
        Route::post('tai-khoan', [TaiKhoanController::class, 'store'])->name('taikhoan.store');
        Route::get('tai-khoan/{taiKhoan}', [TaiKhoanController::class, 'show'])->whereNumber('taiKhoan')->name('taikhoan.show');
        Route::get('tai-khoan/{taiKhoan}/sua', [TaiKhoanController::class, 'edit'])->whereNumber('taiKhoan')->name('taikhoan.edit');
        Route::put('tai-khoan/{taiKhoan}', [TaiKhoanController::class, 'update'])->whereNumber('taiKhoan')->name('taikhoan.update');
        Route::patch('tai-khoan/{taiKhoan}/trang-thai', [TaiKhoanController::class, 'doiTrangThai'])->whereNumber('taiKhoan')->name('taikhoan.trang-thai');

        // M03 - Quản lý xe / khách hàng (khách đến trực tiếp: tìm theo SĐT/biển số, thêm xe cho khách)
        Route::get('xe', [AdminXeController::class, 'index'])->name('xe.index');
        Route::get('xe/tao', [AdminXeController::class, 'create'])->name('xe.create');
        Route::post('xe', [AdminXeController::class, 'store'])->name('xe.store');
        Route::get('xe/{xe}/sua', [AdminXeController::class, 'edit'])->whereNumber('xe')->name('xe.edit');
        Route::put('xe/{xe}', [AdminXeController::class, 'update'])->whereNumber('xe')->name('xe.update');
        Route::patch('xe/{xe}/trang-thai', [AdminXeController::class, 'doiTrangThai'])->whereNumber('xe')->name('xe.trang-thai');

        // M07, M08 (nhóm lịch hẹn) thêm route admin.lichhen.* vào đây
    });
});

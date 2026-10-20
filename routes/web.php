<?php

use App\Http\Controllers\AdminLichHenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DatLichController;
use App\Http\Controllers\DemoPasswordResetController;
use App\Http\Controllers\DichVuController;
use App\Http\Controllers\DichVuPublicController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\KhachHoaDonController;
use App\Http\Controllers\KhachThanhToanController;
use App\Http\Controllers\KtvKiemTraController;
use App\Http\Controllers\KtvLichHenController;
use App\Http\Controllers\KtvSuaChuaController;
use App\Http\Controllers\LoaiDichVuController;
use App\Http\Controllers\PhuTungController;
use App\Http\Controllers\TaiKhoanController;
use App\Http\Controllers\ThongKeController;
use App\Http\Controllers\XacNhanSuaChuaController;
use App\Http\Controllers\XeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminKtvController;
use App\Http\Controllers\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| TRANG PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->user()?->VaiTro === 'TECHNICIAN') {
        return redirect()->route('ktv.lichhen.index');
    }

    return view('public.home');
})->name('public.home');

// Cho KTV chủ động xem trang giới thiệu website.
Route::view('/trang-chu', 'public.home')
    ->name('public.website');

Route::view('/gioi-thieu', 'public.about')
    ->name('public.about');

Route::view('/lien-he', 'public.contact')
    ->name('public.contact');

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

Route::post(
    '/lien-he',
    [ContactController::class, 'store']
)
    ->middleware('throttle:5,1')
    ->name('public.contact.store');

/*
|--------------------------------------------------------------------------
| ĐĂNG NHẬP, ĐĂNG KÝ VÀ QUÊN MẬT KHẨU
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

    Route::get(
        '/quen-mat-khau',
        [DemoPasswordResetController::class, 'show']
    )->name('password.demo');

    Route::post(
        '/quen-mat-khau',
        [DemoPasswordResetController::class, 'start']
    )
        ->middleware('throttle:5,1')
        ->name('password.demo.start');

    Route::post(
        '/quen-mat-khau/xac-nhan',
        [DemoPasswordResetController::class, 'verify']
    )
        ->middleware('throttle:10,1')
        ->name('password.demo.verify');

    Route::post(
        '/quen-mat-khau/gui-lai',
        [DemoPasswordResetController::class, 'resend']
    )
        ->middleware('throttle:3,1')
        ->name('password.demo.resend');

    Route::post(
        '/quen-mat-khau/dat-lai',
        [DemoPasswordResetController::class, 'reset']
    )
        ->middleware('throttle:5,1')
        ->name('password.demo.reset');

    Route::post(
        '/quen-mat-khau/bat-dau-lai',
        [DemoPasswordResetController::class, 'restart']
    )
        ->middleware('throttle:5,1')
        ->name('password.demo.restart');
});

Route::post(
    '/dang-xuat',
    [AuthController::class, 'logout']
)
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| TÀI KHOẢN CÁ NHÂN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:ADMIN,TECHNICIAN,USER',
])->group(function () {
    Route::get(
        '/tai-khoan',
        [TaiKhoanController::class, 'edit']
    )->name('public.taikhoan.edit');

    Route::patch(
        '/tai-khoan',
        [TaiKhoanController::class, 'update']
    )->name('public.taikhoan.update');

    Route::patch(
        '/tai-khoan/mat-khau',
        [TaiKhoanController::class, 'updatePassword']
    )
        ->middleware('throttle:5,1')
        ->name('public.taikhoan.password');
});

/*
|--------------------------------------------------------------------------
| QUẢN TRỊ
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:ADMIN'])->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])
    ->name('admin.dashboard');

    Route::resource(
    'loaidichvu',
    LoaiDichVuController::class
)->except(['show']);

Route::resource(
    'dichvu',
    DichVuController::class
)->except(['show']);

    Route::resource(
        'phutung',
        PhuTungController::class
    )->except(['show']);

    /*
    | Hóa đơn được tự tạo khi KTV hoàn thành sửa chữa.
    */

    Route::get(
        '/admin/hoadon',
        [HoaDonController::class, 'adminIndex']
    )->name('hoadon.admin.index');

    Route::get(
        '/admin/hoadon/{maHD}',
        [HoaDonController::class, 'adminShow']
    )
        ->whereNumber('maHD')
        ->name('hoadon.admin.show');

    Route::post(
        '/admin/hoadon/{maHD}/xac-nhan-thanh-toan',
        [KhachThanhToanController::class, 'confirmCash']
    )
        ->whereNumber('maHD')
        ->name('hoadon.admin.confirm');

    Route::get(
        '/admin/thongke',
        [ThongKeController::class, 'index']
    )->name('thongke.admin.index');

    Route::get(
        '/admin/tin-nhan-lien-he',
        [ContactController::class, 'index']
    )->name('admin.contact.index');

    /*
    | Quản lý lịch hẹn và phân công kỹ thuật viên.
    */

    Route::prefix('admin/lich-hen')
        ->name('admin.lichhen.')
        ->group(function () {
            Route::get(
                '/',
                [AdminLichHenController::class, 'index']
            )->name('index');

            Route::get(
                '/{maLH}',
                [AdminLichHenController::class, 'show']
            )
                ->whereNumber('maLH')
                ->name('show');

            Route::post(
                '/{maLH}/phan-cong',
                [AdminLichHenController::class, 'assign']
            )
                ->whereNumber('maLH')
                ->name('assign');
        });
});

/*
|--------------------------------------------------------------------------
| KHÁCH HÀNG
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:USER'])->group(function () {
    /*
    | Xe của khách hàng.
    */

    Route::get(
        '/xe-cua-toi',
        [XeController::class, 'index']
    )->name('public.xe.index');

    Route::get(
        '/xe-cua-toi/them',
        [XeController::class, 'create']
    )->name('public.xe.create');

    Route::post(
        '/xe-cua-toi',
        [XeController::class, 'store']
    )->name('public.xe.store');

    Route::get(
        '/xe-cua-toi/{maXe}/sua',
        [XeController::class, 'edit']
    )
        ->whereNumber('maXe')
        ->name('public.xe.edit');

    Route::patch(
        '/xe-cua-toi/{maXe}',
        [XeController::class, 'update']
    )
        ->whereNumber('maXe')
        ->name('public.xe.update');

    Route::delete(
    '/xe-cua-toi/{maXe}',
    [XeController::class, 'destroy']
)
    ->whereNumber('maXe')
    ->name('public.xe.destroy');

    /*
    | Đặt lịch và xem lịch hẹn.
    */

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
        '/lich-hen/{maLH}/huy',
        [DatLichController::class, 'cancel']
    )
        ->whereNumber('maLH')
        ->name('public.datlich.cancel');

    /*
    | Xác nhận các hạng mục và chi phí sửa chữa.
    */

    Route::get(
        '/lich-hen/{maLH}/xac-nhan',
        [XacNhanSuaChuaController::class, 'show']
    )
        ->whereNumber('maLH')
        ->name('public.xacnhan.show');

    Route::post(
        '/lich-hen/{maLH}/xac-nhan/dong-y',
        [XacNhanSuaChuaController::class, 'approve']
    )
        ->whereNumber('maLH')
        ->name('public.xacnhan.approve');

    Route::post(
        '/lich-hen/{maLH}/xac-nhan/tu-choi',
        [XacNhanSuaChuaController::class, 'reject']
    )
        ->whereNumber('maLH')
        ->name('public.xacnhan.reject');

    /*
    | Hóa đơn và in hóa đơn.
    */

    Route::get(
        '/hoadon',
        [KhachHoaDonController::class, 'index']
    )->name('hoadon.index');

    Route::get(
        '/hoadon/{maHD}',
        [KhachHoaDonController::class, 'show']
    )
        ->whereNumber('maHD')
        ->name('hoadon.show');

    Route::get(
        '/hoadon/{maHD}/in',
        [KhachHoaDonController::class, 'printInvoice']
    )
        ->whereNumber('maHD')
        ->name('hoadon.print');

    /*
    | Tiền mặt và chuyển khoản mô phỏng.
    */

    Route::post(
        '/hoadon/{maHD}/chon-thanh-toan',
        [KhachThanhToanController::class, 'choose']
    )
        ->whereNumber('maHD')
        ->name('hoadon.payment.choose');

    Route::get(
        '/hoadon/{maHD}/qr',
        [KhachThanhToanController::class, 'qr']
    )
        ->whereNumber('maHD')
        ->name('hoadon.payment.qr');

    Route::post(
        '/hoadon/{maHD}/mo-phong-thanh-cong',
        [KhachThanhToanController::class, 'demo']
    )
        ->whereNumber('maHD')
        ->name('hoadon.payment.demo');
});

/*
|--------------------------------------------------------------------------
| KỸ THUẬT VIÊN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:TECHNICIAN'])->group(function () {
    /*
    | Công việc được phân công và nhận xe.
    */

    Route::prefix('ktv/lich-hen')
        ->name('ktv.lichhen.')
        ->group(function () {
            Route::get(
                '/',
                [KtvLichHenController::class, 'index']
            )->name('index');

            Route::get(
                '/{maLH}',
                [KtvLichHenController::class, 'show']
            )
                ->whereNumber('maLH')
                ->name('show');

            Route::post(
                '/{maLH}/nhan-xe',
                [KtvLichHenController::class, 'receive']
            )
                ->whereNumber('maLH')
                ->name('receive');
        });

    /*
    | Lưu nháp và gửi phiếu kiểm tra.
    */

    Route::get(
        '/ktv/lich-hen/{maLH}/kiem-tra',
        [KtvKiemTraController::class, 'edit']
    )
        ->whereNumber('maLH')
        ->name('ktv.kiemtra.edit');

    Route::post(
        '/ktv/lich-hen/{maLH}/kiem-tra',
        [KtvKiemTraController::class, 'save']
    )
        ->whereNumber('maLH')
        ->name('ktv.kiemtra.save');

    Route::get(
        '/ktv/lich-hen/{maLH}/xac-nhan',
        [XacNhanSuaChuaController::class, 'preview']
    )
        ->whereNumber('maLH')
        ->name('ktv.kiemtra.preview');

    Route::post(
        '/ktv/lich-hen/{maLH}/gui-xac-nhan',
        [XacNhanSuaChuaController::class, 'send']
    )
        ->whereNumber('maLH')
        ->name('ktv.kiemtra.send');

    /*
    | Bắt đầu và hoàn thành sửa chữa.
    */

    Route::post(
        '/ktv/lich-hen/{maLH}/bat-dau-sua',
        [KtvSuaChuaController::class, 'start']
    )
        ->whereNumber('maLH')
        ->name('ktv.suachua.start');

    Route::post(
        '/ktv/lich-hen/{maLH}/hoan-thanh-sua',
        [KtvSuaChuaController::class, 'finish']
    )
        ->whereNumber('maLH')
        ->name('ktv.suachua.finish');
});

Route::middleware(['auth', 'role:ADMIN'])
    ->prefix('admin/ky-thuat-vien')
    ->name('admin.ktv.')
    ->group(function () {
        Route::get('/', [AdminKtvController::class, 'index'])
            ->name('index');

        Route::get('/them', [AdminKtvController::class, 'create'])
            ->name('create');

        Route::post('/', [AdminKtvController::class, 'store'])
            ->name('store');

        Route::get('/{maTK}/sua', [AdminKtvController::class, 'edit'])
            ->whereNumber('maTK')
            ->name('edit');

        Route::patch('/{maTK}', [AdminKtvController::class, 'update'])
            ->whereNumber('maTK')
            ->name('update');

        Route::patch(
            '/{maTK}/trang-thai',
            [AdminKtvController::class, 'updateStatus']
        )
            ->whereNumber('maTK')
            ->name('status');

        Route::post(
            '/{maTK}/dat-lai-mat-khau',
            [AdminKtvController::class, 'resetPassword']
        )
            ->whereNumber('maTK')
            ->name('reset-password');
    });
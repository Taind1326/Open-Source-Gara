<?php

use App\Http\Controllers\AdminLichHenController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaoGiaController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DatLichController;
use App\Http\Controllers\DemoPasswordResetController;
use App\Http\Controllers\DichVuController;
use App\Http\Controllers\DichVuPublicController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\KhachHoaDonController;
use App\Http\Controllers\KhachThanhToanController;
use App\Http\Controllers\KiemTraXeController;
use App\Http\Controllers\KtvKiemTraController;
use App\Http\Controllers\KtvLichHenController;
use App\Http\Controllers\KtvSuaChuaController;
use App\Http\Controllers\LoaiDichVuController;
use App\Http\Controllers\PhuTungController;
use App\Http\Controllers\SuaChuaController;
use App\Http\Controllers\TaiKhoanController;
use App\Http\Controllers\ThongKeController;
use App\Http\Controllers\XacNhanSuaChuaController;
use App\Http\Controllers\XeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminKtvController;

/*
|--------------------------------------------------------------------------
| TRANG PUBLIC
|--------------------------------------------------------------------------
*/

Route::view('/', 'public.home')
    ->name('public.home');

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
    Route::view('/admin', 'admin.dashboard')
        ->name('admin.dashboard');

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

    Route::patch(
        '/xe-cua-toi/{maXe}/trang-thai',
        [XeController::class, 'updateStatus']
    )
        ->whereNumber('maXe')
        ->name('public.xe.status');

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

/*
|--------------------------------------------------------------------------
| CÁC ROUTE NGHIỆP VỤ CŨ ĐANG CÒN TRONG PROJECT
|--------------------------------------------------------------------------
| Giữ nguyên để không làm mất các liên kết cũ.
| Luồng mới sử dụng lịch hẹn, phiếu kiểm tra và KtvSuaChuaController.
*/

Route::middleware([
    'auth',
    'role:ADMIN,TECHNICIAN,USER',
])->group(function () {
    /*
    | Kiểm tra xe theo luồng cũ.
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
    | Báo giá theo luồng cũ.
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
    | Sửa chữa và tiến độ theo luồng cũ.
    */

    Route::get(
        '/sua-chua/{maYC}',
        [SuaChuaController::class, 'show']
    )
        ->whereNumber('maYC')
        ->name('suachua.show');

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
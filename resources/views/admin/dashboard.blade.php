@extends('admin.layout')

@section('title', 'Tổng quan')

@section('content')
    <div class="dashboard-banner mb-4">
        <div class="small text-white-50 mb-2">
            TRANG QUẢN TRỊ
        </div>

        <h1 class="h3 fw-bold mb-2">
            Xin chào, {{ auth()->user()->HoTen }}
        </h1>

        <p class="mb-0">
            Quản lý dịch vụ, phụ tùng và hoạt động kinh doanh của gara.
        </p>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 fw-bold mb-0">Truy cập nhanh</h2>

        <span class="small text-secondary">
            {{ now('Asia/Ho_Chi_Minh')->format('d/m/Y') }}
        </span>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <a href="{{ route('loaidichvu.index') }}" class="shortcut-card">
                <span class="shortcut-number">01</span>
                <h3 class="h5 fw-bold">Loại dịch vụ</h3>
                <p class="text-secondary mb-0">
                    Quản lý nhóm dịch vụ và trạng thái hoạt động.
                </p>
            </a>
        </div>

        <div class="col-md-6 col-xl-4">
            <a href="{{ route('dichvu.index') }}" class="shortcut-card">
                <span class="shortcut-number">02</span>
                <h3 class="h5 fw-bold">Dịch vụ</h3>
                <p class="text-secondary mb-0">
                    Cập nhật thông tin, hình ảnh và giá dịch vụ.
                </p>
            </a>
        </div>

        <div class="col-md-6 col-xl-4">
            <a href="{{ route('phutung.index') }}" class="shortcut-card">
                <span class="shortcut-number">03</span>
                <h3 class="h5 fw-bold">Phụ tùng</h3>
                <p class="text-secondary mb-0">
                    Theo dõi phụ tùng, đơn giá và số lượng tồn kho.
                </p>
            </a>
        </div>

        <div class="col-md-6 col-xl-4">
            <a href="{{ route('hoadon.admin.index') }}" class="shortcut-card">
                <span class="shortcut-number">04</span>
                <h3 class="h5 fw-bold">Hóa đơn</h3>
                <p class="text-secondary mb-0">
                    Xem hóa đơn và tình trạng thanh toán.
                </p>
            </a>
        </div>

        <div class="col-md-6 col-xl-4">
            <a href="{{ route('thongke.admin.index') }}" class="shortcut-card">
                <span class="shortcut-number">05</span>
                <h3 class="h5 fw-bold">Thống kê</h3>
                <p class="text-secondary mb-0">
                    Xem báo cáo hoạt động và doanh thu gara.
                </p>
            </a>
        </div>

        <div class="col-md-6 col-xl-4">
            <a href="{{ route('public.dichvu.index') }}" class="shortcut-card">
                <span class="shortcut-number">06</span>
                <h3 class="h5 fw-bold">Website khách hàng</h3>
                <p class="text-secondary mb-0">
                    Xem danh sách dịch vụ đang hiển thị công khai.
                </p>
            </a>
        </div>
    </div>
@endsection
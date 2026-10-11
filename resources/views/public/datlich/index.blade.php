@extends('public.layout')

@section('title', 'Lịch hẹn của tôi')

@section('content')
    @php
        $statusLabels = [
            'CHO_PHAN_CONG' => 'Chờ phân công',
            'DA_PHAN_CONG' => 'Đã phân công',
            'DA_TIEP_NHAN' => 'Đã tiếp nhận',
            'DA_KIEM_TRA' => 'Đã kiểm tra',
            'CHO_DUYET_BAO_GIA' => 'Chờ duyệt báo giá',
            'DANG_SUA' => 'Đang sửa chữa',
            'HOAN_THANH' => 'Hoàn thành',
            'TU_CHOI' => 'Đã từ chối',
            'DA_HUY' => 'Đã hủy',
        ];

        $statusClasses = [
            'CHO_PHAN_CONG' => 'text-warning-emphasis bg-warning-subtle',
            'DA_PHAN_CONG' => 'text-primary bg-primary-subtle',
            'DA_TIEP_NHAN' => 'text-primary bg-primary-subtle',
            'DA_KIEM_TRA' => 'text-info-emphasis bg-info-subtle',
            'CHO_DUYET_BAO_GIA' => 'text-warning-emphasis bg-warning-subtle',
            'DANG_SUA' => 'text-primary bg-primary-subtle',
            'HOAN_THANH' => 'text-success bg-success-subtle',
            'TU_CHOI' => 'text-danger bg-danger-subtle',
            'DA_HUY' => 'text-secondary bg-secondary-subtle',
        ];
    @endphp

    <section class="hero">
        <div class="container">
            <nav aria-label="Đường dẫn">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}" class="text-white">
                            Trang chủ
                        </a>
                    </li>

                    <li class="breadcrumb-item active text-white"
                        aria-current="page">
                        Lịch hẹn của tôi
                    </li>
                </ol>
            </nav>

            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">
                        Lịch hẹn của tôi
                    </h1>

                    <p class="hero-description mb-0">
                        Theo dõi lịch tiếp nhận xe và trạng thái
                        xử lý yêu cầu của bạn.
                    </p>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('public.datlich.create') }}"
                       class="btn btn-primary btn-lg">
                        <i class="bi bi-calendar-plus me-2"
                           aria-hidden="true"></i>
                        Đặt lịch mới
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="feature-card h-auto">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <p class="section-eyebrow mb-2">
                            Yêu cầu của bạn
                        </p>

                        <h2 class="h4 fw-bold mb-1">
                            Danh sách lịch hẹn
                        </h2>

                        <p class="text-muted small mb-0">
                            @if($lichHens->total() > 0)
                                Hiển thị
                                {{ $lichHens->firstItem() }}–{{ $lichHens->lastItem() }}
                                trong {{ $lichHens->total() }} lịch hẹn
                            @else
                                Bạn chưa có lịch hẹn
                            @endif
                        </p>
                    </div>

                    <a href="{{ route('public.xe.index') }}"
                       class="btn btn-outline-primary">
                        <i class="bi bi-car-front me-1"
                           aria-hidden="true"></i>
                        Xe của tôi
                    </a>
                </div>

                @if($lichHens->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Yêu cầu</th>
                                    <th scope="col">Xe</th>
                                    <th scope="col">Lịch tiếp nhận</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col" class="text-end">
                                        Thao tác
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($lichHens as $lichHen)
                                    <tr>
                                        <td class="fw-semibold text-nowrap">
                                            #{{ $lichHen->MaYC }}
                                        </td>

                                        <td>
                                            <div class="fw-semibold text-nowrap">
                                                {{ $lichHen->xe->BienSo }}
                                            </div>

                                            <small class="text-muted">
                                                {{ $lichHen->xe->HangXe }}
                                                {{ $lichHen->xe->DongXe }}
                                            </small>
                                        </td>

                                        <td class="text-nowrap">
                                            <div class="fw-semibold">
                                                {{ $lichHen->NgayHen->format('d/m/Y') }}
                                            </div>

                                            <small class="text-muted">
                                                {{ $lichHen->KhungGioHen }}
                                            </small>
                                        </td>

                                        <td class="text-nowrap">
                                            <span class="badge rounded-pill px-3 py-2 {{ $statusClasses[$lichHen->TrangThai] ?? 'text-secondary bg-secondary-subtle' }}">
                                                {{ $statusLabels[$lichHen->TrangThai] ?? $lichHen->TrangThai }}
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <button type="button"
                                                    class="btn btn-outline-primary btn-sm text-nowrap"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#bookingDetails{{ $lichHen->MaYC }}">
                                                Xem lịch hẹn
                                            </button>

                                            @if(in_array(
                                                $lichHen->TrangThai,
                                                config('booking.customer_cancel_statuses', []),
                                                true
                                            ))
                                                <button type="button"
                                                        class="btn btn-outline-danger btn-sm text-nowrap mt-1 mt-xl-0"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cancelBooking{{ $lichHen->MaYC }}">
                                                    Hủy lịch
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($lichHens->hasPages())
                        <div class="mt-4">
                            {{ $lichHens->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <span class="feature-icon">
                            <i class="bi bi-calendar-check"
                               aria-hidden="true"></i>
                        </span>

                        <h3 class="h5 fw-bold">
                            Bắt đầu bằng một lịch hẹn
                        </h3>

                        <p class="text-muted mb-4">
                            Chọn xe, dịch vụ và thời gian
                            bạn muốn gara tiếp nhận.
                        </p>

                        <a href="{{ route('public.datlich.create') }}"
                           class="btn btn-primary">
                            Đặt lịch sửa chữa
                        </a>
                    </div>
                @endif
            </div>

            <div class="alert alert-info d-flex gap-3 mt-4 mb-0">
                <i class="bi bi-info-circle" aria-hidden="true"></i>

                <div>
                    Bạn có thể tự hủy khi yêu cầu còn chờ phân công.
                    Nếu gara đã xử lý, vui lòng liên hệ để được hỗ trợ.
                </div>
            </div>
        </div>
    </section>

    @foreach($lichHens as $lichHen)
        <div class="modal fade"
             id="bookingDetails{{ $lichHen->MaYC }}"
             tabindex="-1"
             aria-labelledby="bookingDetailsTitle{{ $lichHen->MaYC }}"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5 fw-bold"
                            id="bookingDetailsTitle{{ $lichHen->MaYC }}">
                            Lịch hẹn #{{ $lichHen->MaYC }}
                        </h2>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Đóng"></button>
                    </div>

                    <div class="modal-body">
                        <dl class="row mb-4">
                            <dt class="col-sm-4 text-muted fw-normal">
                                Xe
                            </dt>
                            <dd class="col-sm-8 fw-semibold">
                                {{ $lichHen->xe->BienSo }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Ngày tiếp nhận
                            </dt>
                            <dd class="col-sm-8">
                                {{ $lichHen->NgayHen->format('d/m/Y') }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Khung giờ
                            </dt>
                            <dd class="col-sm-8">
                                {{ $lichHen->KhungGioHen }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Trạng thái
                            </dt>
                            <dd class="col-sm-8 mb-0">
                                {{ $statusLabels[$lichHen->TrangThai] ?? $lichHen->TrangThai }}
                            </dd>
                        </dl>

                        <h3 class="h6 fw-bold">Dịch vụ đã chọn</h3>

                        <ul class="mb-4">
                            @forelse($lichHen->dichVu as $dichVu)
                                <li>{{ $dichVu->TenDV }}</li>
                            @empty
                                <li class="text-muted">
                                    Chưa có thông tin dịch vụ.
                                </li>
                            @endforelse
                        </ul>

                        <h3 class="h6 fw-bold">Mô tả nhu cầu</h3>

                        @forelse(
                            array_filter(
                                preg_split('/\r\n|\r|\n/', $lichHen->MoTa ?? ''),
                                fn ($line) => trim($line) !== ''
                            ) as $paragraph
                        )
                            <p class="text-muted text-break">
                                {{ $paragraph }}
                            </p>
                        @empty
                            <p class="text-muted mb-0">
                                Không có mô tả thêm.
                            </p>
                        @endforelse
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-light"
                                data-bs-dismiss="modal">
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if(in_array(
            $lichHen->TrangThai,
            config('booking.customer_cancel_statuses', []),
            true
        ))
            <div class="modal fade"
                 id="cancelBooking{{ $lichHen->MaYC }}"
                 tabindex="-1"
                 aria-labelledby="cancelBookingTitle{{ $lichHen->MaYC }}"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title fs-5 fw-bold"
                                id="cancelBookingTitle{{ $lichHen->MaYC }}">
                                Hủy lịch hẹn
                            </h2>

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Đóng"></button>
                        </div>

                        <div class="modal-body">
                            <p class="mb-0">
                                Bạn muốn hủy lịch hẹn
                                <strong>#{{ $lichHen->MaYC }}</strong>
                                của xe
                                <strong>{{ $lichHen->xe->BienSo }}</strong>,
                                ngày {{ $lichHen->NgayHen->format('d/m/Y') }},
                                khung {{ $lichHen->KhungGioHen }}?
                            </p>
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-light"
                                    data-bs-dismiss="modal">
                                Giữ lịch hẹn
                            </button>

                            <form method="POST"
                                  action="{{ route('public.datlich.cancel', $lichHen->MaYC) }}">
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn btn-danger">
                                    Xác nhận hủy
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
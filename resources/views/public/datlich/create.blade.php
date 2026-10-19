@extends('public.layout')

@section('title', 'Đặt lịch sửa chữa')

@section('content')
    @php
        $oldServices = old('dich_vus', $maDV ? [$maDV] : []);

        $selectedServices = collect(
            is_array($oldServices) ? $oldServices : []
        )
            ->filter(fn ($value) => is_scalar($value))
            ->map(fn ($value) => (string) $value)
            ->all();
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

                    <li
                        class="breadcrumb-item active text-white"
                        aria-current="page"
                    >
                        Đặt lịch sửa chữa
                    </li>
                </ol>
            </nav>

            <h1 class="display-5 fw-bold mb-3">
                Đặt lịch cho chiếc xe của bạn
            </h1>

            <p class="hero-description mb-0">
                Chọn xe, dịch vụ và thời gian tiếp nhận phù hợp.
                Kỹ thuật viên kiểm tra xe và gửi các hạng mục để bạn
                xác nhận trước khi sửa chữa.
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <p class="fw-semibold mb-2">
                        Vui lòng kiểm tra lại thông tin:
                    </p>

                    <ul class="mb-0">
                        @foreach($errors->all() as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($xes->isEmpty())
                <div class="feature-card h-auto text-center py-5">
                    <span class="feature-icon">
                        <i class="bi bi-car-front" aria-hidden="true"></i>
                    </span>

                    <h2 class="h4 fw-bold">
                        Bạn chưa thêm xe
                    </h2>

                    <p class="text-muted mb-4">
                        Thêm thông tin xe trước khi đặt lịch.
                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-2">
                        <a
                            href="{{ route('public.xe.create') }}"
                            class="btn btn-primary"
                        >
                            Thêm xe
                        </a>

                        <a
                            href="{{ route('public.xe.index') }}"
                            class="btn btn-outline-primary"
                        >
                            Xe của tôi
                        </a>
                    </div>
                </div>
            @elseif($dichVus->isEmpty())
                <div class="feature-card h-auto text-center py-5">
                    <span class="feature-icon">
                        <i class="bi bi-tools" aria-hidden="true"></i>
                    </span>

                    <h2 class="h4 fw-bold">
                        Chưa có dịch vụ nhận đặt lịch
                    </h2>

                    <p class="text-muted">
                        Vui lòng liên hệ gara để được hỗ trợ.
                    </p>

                    <a
                        href="{{ route('public.contact') }}"
                        class="btn btn-primary"
                    >
                        Liên hệ gara
                    </a>
                </div>
            @else
                <form
                    method="POST"
                    action="{{ route('public.datlich.store') }}"
                    data-booking-form
                    data-slots-url="{{ route('public.datlich.slots') }}"
                    data-selected-slot="{{ old('KhungGioHen', '') }}"
                >
                    @csrf

                    <div class="row g-4 align-items-start">
                        <div class="col-lg-8">
                            <section class="feature-card h-auto mb-4">
                                <p class="section-eyebrow mb-2">
                                    Bước 1
                                </p>

                                <h2 class="h4 fw-bold mb-4">
                                    Xe và thời gian tiếp nhận
                                </h2>

                                <div class="mb-4">
                                    <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                                        <label
                                            for="bookingVehicle"
                                            class="form-label fw-semibold mb-0"
                                        >
                                            Chọn xe
                                        </label>

                                        <a
                                            href="{{ route('public.xe.create') }}"
                                            class="small fw-semibold"
                                        >
                                            <i
                                                class="bi bi-plus-circle me-1"
                                                aria-hidden="true"
                                            ></i>
                                            Thêm xe
                                        </a>
                                    </div>

                                    <select
                                        id="bookingVehicle"
                                        name="MaXe"
                                        class="form-select @error('MaXe') is-invalid @enderror"
                                        required
                                    >
                                        <option value="">
                                            Chọn xe của bạn
                                        </option>

                                        @foreach($xes as $xe)
                                            <option
                                                value="{{ $xe->MaXe }}"
                                                @selected((string) old('MaXe') === (string) $xe->MaXe)
                                            >
                                                {{ $xe->BienSo }}
                                                — {{ $xe->HangXe }}
                                                {{ $xe->DongXe }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('MaXe')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label
                                            for="bookingDate"
                                            class="form-label fw-semibold"
                                        >
                                            Ngày hẹn
                                        </label>

                                        <input
                                            type="date"
                                            id="bookingDate"
                                            name="NgayHen"
                                            data-booking-date
                                            class="form-control @error('NgayHen') is-invalid @enderror"
                                            value="{{ old('NgayHen', $minDate) }}"
                                            min="{{ $minDate }}"
                                            max="{{ $maxDate }}"
                                            required
                                        >

                                        @error('NgayHen')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label
                                            for="bookingSlot"
                                            class="form-label fw-semibold"
                                        >
                                            Khung giờ tiếp nhận
                                        </label>

                                        <select
                                            id="bookingSlot"
                                            name="KhungGioHen"
                                            data-booking-slot
                                            class="form-select @error('KhungGioHen') is-invalid @enderror"
                                            required
                                            disabled
                                        >
                                            <option value="">
                                                Đang tải khung giờ…
                                            </option>
                                        </select>

                                        @error('KhungGioHen')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-text mt-3">
                                    Khung giờ là thời gian tiếp nhận xe,
                                    không phải thời gian hoàn thành sửa chữa.
                                </div>

                                <div
                                    data-booking-error
                                    class="alert alert-warning mt-3 mb-0 d-none"
                                    role="alert"
                                ></div>

                                <p
                                    data-booking-notice
                                    class="text-muted small mt-3 mb-0"
                                    aria-live="polite"
                                ></p>

                                <button
                                    type="button"
                                    data-booking-retry
                                    class="btn btn-outline-primary btn-sm mt-3"
                                >
                                    <i
                                        class="bi bi-arrow-repeat me-1"
                                        aria-hidden="true"
                                    ></i>
                                    Tải lại khung giờ
                                </button>
                            </section>

                            <section class="feature-card h-auto mb-4">
                                <p class="section-eyebrow mb-2">
                                    Bước 2
                                </p>

                                <h2 class="h4 fw-bold mb-2">
                                    Dịch vụ bạn quan tâm
                                </h2>

                                <p class="text-muted mb-4">
                                    Chọn từ 1 đến 20 dịch vụ.
                                    Các hạng mục thực hiện sẽ được thống nhất
                                    với bạn sau khi kiểm tra xe.
                                </p>

                                @error('dich_vus')
                                    <div class="alert alert-danger" role="alert">
                                        {{ $message }}
                                    </div>
                                @enderror

                                @error('dich_vus.*')
                                    <div class="alert alert-danger" role="alert">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div class="row g-3">
                                    @foreach($dichVus as $dichVu)
                                        <div class="col-md-6">
                                            <div class="border rounded-3 p-3 h-100">
                                                <div class="form-check">
                                                    <input
                                                        type="checkbox"
                                                        id="bookingService{{ $dichVu->MaDV }}"
                                                        name="dich_vus[]"
                                                        value="{{ $dichVu->MaDV }}"
                                                        data-booking-service
                                                        class="form-check-input"
                                                        @checked(in_array(
                                                            (string) $dichVu->MaDV,
                                                            $selectedServices,
                                                            true
                                                        ))
                                                    >

                                                    <label
                                                        for="bookingService{{ $dichVu->MaDV }}"
                                                        class="form-check-label"
                                                    >
                                                        <span class="d-block fw-semibold">
                                                            {{ $dichVu->TenDV }}
                                                        </span>

                                                        <span class="d-block text-muted small mt-1">
                                                            {{ $dichVu->loaiDichVu->TenLoaiDV }}
                                                        </span>

                                                        <span class="d-block text-primary small fw-semibold mt-2">
                                                            Giá dịch vụ:
                                                            {{ number_format($dichVu->Gia, 0, ',', '.') }} ₫
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </section>

                            <section class="feature-card h-auto">
                                <p class="section-eyebrow mb-2">
                                    Bước 3
                                </p>

                                <h2 class="h4 fw-bold mb-4">
                                    Mô tả nhu cầu
                                </h2>

                                <label
                                    for="bookingDescription"
                                    class="form-label fw-semibold"
                                >
                                    Tình trạng xe hoặc yêu cầu thêm

                                    <span class="text-muted fw-normal">
                                        (không bắt buộc)
                                    </span>
                                </label>

                                <textarea
                                    id="bookingDescription"
                                    name="MoTa"
                                    class="form-control @error('MoTa') is-invalid @enderror"
                                    rows="5"
                                    maxlength="2000"
                                    aria-describedby="bookingDescriptionHelp"
                                    placeholder="Mô tả dấu hiệu bất thường hoặc nhu cầu chăm sóc xe…"
                                >{{ old('MoTa') }}</textarea>

                                @error('MoTa')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div
                                    id="bookingDescriptionHelp"
                                    class="form-text"
                                >
                                    Tối đa 2.000 ký tự.
                                </div>
                            </section>
                        </div>

                        <div class="col-lg-4">
                            <aside class="feature-card h-auto">
                                <span class="feature-icon">
                                    <i
                                        class="bi bi-calendar-check"
                                        aria-hidden="true"
                                    ></i>
                                </span>

                                <h2 class="h5 fw-bold mb-3">
                                    Trước khi gửi lịch
                                </h2>

                                <ul class="list-unstyled text-muted mb-4">
                                    <li class="d-flex gap-2 mb-3">
                                        <i
                                            class="bi bi-check-circle text-primary"
                                            aria-hidden="true"
                                        ></i>

                                        <span>
                                            Kiểm tra đúng xe và ngày hẹn.
                                        </span>
                                    </li>

                                    <li class="d-flex gap-2 mb-3">
                                        <i
                                            class="bi bi-check-circle text-primary"
                                            aria-hidden="true"
                                        ></i>

                                        <span>
                                            Chọn khung giờ còn chỗ.
                                        </span>
                                    </li>

                                    <li class="d-flex gap-2">
                                        <i
                                            class="bi bi-check-circle text-primary"
                                            aria-hidden="true"
                                        ></i>

                                        <span>
                                            Chọn từ 1 đến 20 dịch vụ.
                                        </span>
                                    </li>
                                </ul>

                                <div class="alert alert-info small">
                                    Gửi lịch chưa phát sinh thanh toán.
                                    Kỹ thuật viên kiểm tra xe và gửi các
                                    hạng mục cùng tổng chi phí để bạn
                                    xác nhận trước khi sửa chữa.
                                </div>

                                <button
                                    type="submit"
                                    data-booking-submit
                                    class="btn btn-primary w-100"
                                    disabled
                                >
                                    Gửi lịch hẹn

                                    <i
                                        class="bi bi-arrow-right ms-2"
                                        aria-hidden="true"
                                    ></i>
                                </button>

                                <a
                                    href="{{ route('public.datlich.index') }}"
                                    class="btn btn-light w-100 mt-2"
                                >
                                    Lịch hẹn của tôi
                                </a>

                                <noscript>
                                    <p class="text-danger small mt-3 mb-0">
                                        Vui lòng bật JavaScript
                                        để tải khung giờ đặt lịch.
                                    </p>
                                </noscript>
                            </aside>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/booking-form.js') }}" defer></script>
@endpush
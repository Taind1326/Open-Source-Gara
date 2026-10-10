@extends('public.layout')

@section('title', 'Liên hệ')

@section('description', 'Liên hệ AutoCare Garage để trao đổi nhu cầu chăm sóc, bảo dưỡng và sửa chữa ô tô.')

@section('content')
    @php
        $address = config('garage.address');
        $phone = config('garage.phone');
        $email = config('garage.email');
        $openingHours = config('garage.opening_hours');

        $phoneLink = preg_replace('/[^0-9+]/', '', $phone ?? '');

        $mapUrl = $address
            ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address)
            : null;
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
                        Liên hệ
                    </li>
                </ol>
            </nav>

            <h1 class="display-5 fw-bold mb-3">
                Kết nối với AutoCare Garage
            </h1>

            <p class="hero-description fs-5 mb-0">
                Trao đổi tình trạng xe và nhu cầu của bạn
                để gara hỗ trợ chuẩn bị các bước tiếp theo.
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                        </span>

                        <h2 class="h5 fw-bold">Địa chỉ gara</h2>

                        <p class="text-muted mb-3">
                            {{ $address ?: 'Địa chỉ đang được cập nhật.' }}
                        </p>

                        @if($mapUrl)
                            <a href="{{ $mapUrl }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="fw-semibold">
                                Xem đường đi
                                <i class="bi bi-arrow-up-right ms-1"
                                   aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-telephone" aria-hidden="true"></i>
                        </span>

                        <h2 class="h5 fw-bold">Điện thoại</h2>

                        <p class="text-muted mb-3">
                            {{ $phone ?: 'Số điện thoại đang được cập nhật.' }}
                        </p>

                        @if($phoneLink)
                            <a href="tel:{{ $phoneLink }}" class="fw-semibold">
                                Gọi cho gara
                                <i class="bi bi-arrow-right ms-1"
                                   aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-envelope" aria-hidden="true"></i>
                        </span>

                        <h2 class="h5 fw-bold">Email</h2>

                        <p class="text-muted text-break mb-3">
                            {{ $email ?: 'Email đang được cập nhật.' }}
                        </p>

                        @if($email)
                            <a href="mailto:{{ $email }}" class="fw-semibold">
                                Gửi email
                                <i class="bi bi-arrow-right ms-1"
                                   aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-clock" aria-hidden="true"></i>
                        </span>

                        <h2 class="h5 fw-bold">Giờ làm việc</h2>

                        <p class="text-muted mb-0">
                            {{ $openingHours ?: 'Giờ làm việc đang được cập nhật.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space bg-white">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <p class="section-eyebrow mb-2">Đến gara</p>

                    <h2 class="section-title mb-4">
                        Tìm đường đến AutoCare Garage
                    </h2>

                    <p class="text-muted">
                        Liên hệ trước khi đến để trao đổi nhu cầu
                        và thời gian tiếp nhận xe.
                    </p>

                    <div class="d-flex gap-3 mb-4">
                        <i class="bi bi-car-front text-primary fs-4"
                           aria-hidden="true"></i>

                        <div>
                            <h3 class="h6 fw-bold">Thông tin chiếc xe</h3>

                            <p class="text-muted mb-0">
                                Chuẩn bị biển số, hãng xe và dòng xe
                                để thuận tiện khi tiếp nhận.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <i class="bi bi-chat-square-text text-primary fs-4"
                           aria-hidden="true"></i>

                        <div>
                            <h3 class="h6 fw-bold">Tình trạng đang gặp phải</h3>

                            <p class="text-muted mb-0">
                                Mô tả dấu hiệu bất thường hoặc hạng mục
                                bạn muốn kiểm tra, bảo dưỡng.
                            </p>
                        </div>
                    </div>

                    @if($mapUrl)
                        <a href="{{ $mapUrl }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-primary">
                            Mở Google Maps
                            <i class="bi bi-arrow-up-right ms-2"
                               aria-hidden="true"></i>
                        </a>
                    @endif
                </div>

                <div class="col-lg-7">
                    @if($address)
                        <div class="ratio ratio-4x3 rounded-4 overflow-hidden border">
                            <iframe
                                src="https://maps.google.com/maps?q={{ rawurlencode($address) }}&output=embed"
                                title="Bản đồ vị trí AutoCare Garage"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @else
                        <div class="feature-card d-flex flex-column align-items-center justify-content-center text-center">
                            <span class="feature-icon">
                                <i class="bi bi-map" aria-hidden="true"></i>
                            </span>

                            <h3 class="h5 fw-bold">Vị trí gara đang được cập nhật</h3>

                            <p class="text-muted mb-0">
                                Bản đồ sẽ hiển thị khi có thông tin địa chỉ.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="text-center mb-5">
                <p class="section-eyebrow mb-2">Thông tin hữu ích</p>
                <h2 class="section-title">Trước khi sửa chữa</h2>
            </div>

            <div class="accordion" id="contactFaq">
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faqQuotation"
                                aria-expanded="true"
                                aria-controls="faqQuotation">
                            Tôi có được xem báo giá trước khi sửa không?
                        </button>
                    </h3>

                    <div id="faqQuotation"
                         class="accordion-collapse collapse show"
                         data-bs-parent="#contactFaq">
                        <div class="accordion-body text-muted">
                            Sau khi kiểm tra xe, gara lập báo giá các hạng mục
                            đề xuất. Bạn xem và đồng ý hoặc từ chối báo giá
                            trước khi thực hiện sửa chữa.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faqProgress"
                                aria-expanded="false"
                                aria-controls="faqProgress">
                            Tôi theo dõi tiến độ sửa chữa như thế nào?
                        </button>
                    </h3>

                    <div id="faqProgress"
                         class="accordion-collapse collapse"
                         data-bs-parent="#contactFaq">
                        <div class="accordion-body text-muted">
                            Các cập nhật được ghi nhận trong hồ sơ sửa chữa.
                            Đăng nhập bằng tài khoản gắn với yêu cầu để xem
                            thông tin tiến độ của chiếc xe.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button collapsed"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#faqPayment"
                                aria-expanded="false"
                                aria-controls="faqPayment">
                            Gara hỗ trợ phương thức thanh toán nào?
                        </button>
                    </h3>

                    <div id="faqPayment"
                         class="accordion-collapse collapse"
                         data-bs-parent="#contactFaq">
                        <div class="accordion-body text-muted">
                            Bạn có thể chọn tiền mặt hoặc chuyển khoản.
                            Hóa đơn được cập nhật đã thanh toán sau khi
                            gara kiểm tra và xác nhận đã nhận tiền.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
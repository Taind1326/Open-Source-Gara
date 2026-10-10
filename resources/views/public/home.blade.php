@extends('public.layout')

@section('title', 'Trang chủ')

@section('description', 'Khám phá dịch vụ chăm sóc, bảo dưỡng và sửa chữa ô tô tại AutoCare Garage. Xem báo giá và theo dõi tiến độ sửa chữa.')

@section('content')
    <section class="home-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <div class="hero-label mb-4">
                        <i class="bi bi-tools" aria-hidden="true"></i>
                        AutoCare Garage
                    </div>

                    <h1 class="mb-4">
                        Chăm sóc chiếc xe.<br>
                        <span class="hero-highlight">An tâm hành trình.</span>
                    </h1>

                    <p class="hero-description fs-5 mb-4">
                        Tìm hiểu dịch vụ, xem báo giá và theo dõi tiến độ sửa chữa.
                        Mọi thông tin cần thiết cho chiếc xe của bạn được kết nối
                        tại một nơi.
                    </p>

                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('public.dichvu.index') }}"
                           class="btn btn-primary btn-lg">
                            Khám phá dịch vụ
                            <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                        </a>

                        <a href="{{ url('/lien-he') }}"
                           class="btn btn-outline-light btn-lg">
                            Liên hệ gara
                        </a>
                    </div>

                    <div class="hero-benefits d-flex flex-wrap gap-4 mt-4 small">
                        <span>
                            <i class="bi bi-check-circle me-2" aria-hidden="true"></i>
                            Báo giá trước khi sửa
                        </span>

                        <span>
                            <i class="bi bi-check-circle me-2" aria-hidden="true"></i>
                            Theo dõi tiến độ
                        </span>

                        <span>
                            <i class="bi bi-check-circle me-2" aria-hidden="true"></i>
                            Lưu thông tin hóa đơn
                        </span>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="hero-panel">
                        <div class="hero-car" aria-hidden="true">
                            <i class="bi bi-car-front"></i>
                        </div>

                        <h2 class="h5 fw-bold mb-2">
                            Đồng hành cùng chiếc xe của bạn
                        </h2>

                        <p class="hero-panel-description small mb-3">
                            Thông tin rõ ràng từ tiếp nhận đến thanh toán.
                        </p>

                        <div class="hero-step">
                            <i class="bi bi-clipboard-check" aria-hidden="true"></i>

                            <div>
                                <div class="fw-semibold">Kiểm tra và chẩn đoán</div>
                                <small>
                                    Ghi nhận tình trạng và nhu cầu sửa chữa.
                                </small>
                            </div>
                        </div>

                        <div class="hero-step">
                            <i class="bi bi-receipt" aria-hidden="true"></i>

                            <div>
                                <div class="fw-semibold">Xem và xác nhận báo giá</div>
                                <small>
                                    Thống nhất dịch vụ trước khi thực hiện.
                                </small>
                            </div>
                        </div>

                        <div class="hero-step">
                            <i class="bi bi-wrench-adjustable" aria-hidden="true"></i>

                            <div>
                                <div class="fw-semibold">Theo dõi quá trình sửa chữa</div>
                                <small>
                                    Cập nhật tiến độ theo từng giai đoạn.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                <div>
                    <p class="section-eyebrow mb-2">Dịch vụ gara</p>

                    <h2 class="section-title mb-2">
                        Chăm sóc xe theo nhu cầu
                    </h2>

                    <p class="text-muted mb-0">
                        Khám phá danh mục và xem thông tin chi tiết từng dịch vụ.
                    </p>
                </div>

                <a href="{{ route('public.dichvu.index') }}"
                   class="btn btn-outline-primary">
                    Xem danh mục
                    <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                </a>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-gear" aria-hidden="true"></i>
                        </span>

                        <h3 class="h5 fw-bold">Bảo dưỡng ô tô</h3>

                        <p class="text-muted">
                            Kiểm tra và chăm sóc các bộ phận để duy trì
                            khả năng vận hành của chiếc xe.
                        </p>

                        <a href="{{ route('public.dichvu.index') }}"
                           class="fw-semibold">
                            Khám phá dịch vụ
                            <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </a>
                    </article>
                </div>

                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-tools" aria-hidden="true"></i>
                        </span>

                        <h3 class="h5 fw-bold">Sửa chữa ô tô</h3>

                        <p class="text-muted">
                            Kiểm tra tình trạng, chẩn đoán và đề xuất
                            phương án sửa chữa phù hợp.
                        </p>

                        <a href="{{ route('public.dichvu.index') }}"
                           class="fw-semibold">
                            Khám phá dịch vụ
                            <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </a>
                    </article>
                </div>

                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-stars" aria-hidden="true"></i>
                        </span>

                        <h3 class="h5 fw-bold">Chăm sóc và vệ sinh</h3>

                        <p class="text-muted">
                            Tìm hiểu các dịch vụ vệ sinh, chăm sóc
                            nội thất và ngoại thất ô tô.
                        </p>

                        <a href="{{ route('public.dichvu.index') }}"
                           class="fw-semibold">
                            Khám phá dịch vụ
                            <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                        </a>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <p class="section-eyebrow mb-2">Quy trình sửa chữa</p>

                <h2 class="section-title">Rõ ràng trong từng bước</h2>

                <p class="text-muted mb-0">
                    Từ tiếp nhận yêu cầu đến hoàn thành và bàn giao xe.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <span class="step-number" aria-hidden="true">1</span>

                    <h3 class="h5 fw-bold">Tiếp nhận xe</h3>

                    <p class="text-muted mb-0">
                        Ghi nhận nhu cầu và tình trạng ban đầu của xe.
                    </p>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <span class="step-number" aria-hidden="true">2</span>

                    <h3 class="h5 fw-bold">Kiểm tra và báo giá</h3>

                    <p class="text-muted mb-0">
                        Chẩn đoán, lập báo giá và chờ khách hàng xác nhận.
                    </p>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <span class="step-number" aria-hidden="true">3</span>

                    <h3 class="h5 fw-bold">Thực hiện sửa chữa</h3>

                    <p class="text-muted mb-0">
                        Kỹ thuật viên thực hiện công việc và cập nhật tiến độ.
                    </p>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <span class="step-number" aria-hidden="true">4</span>

                    <h3 class="h5 fw-bold">Thanh toán và bàn giao</h3>

                    <p class="text-muted mb-0">
                        Kiểm tra sau sửa chữa, xác nhận thanh toán
                        và hoàn thành hồ sơ.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="cta-panel">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2 class="fw-bold mb-3">
                            Bạn đang cần chăm sóc chiếc xe?
                        </h2>

                        <p class="cta-description mb-0">
                            Xem các dịch vụ hiện có hoặc liên hệ gara
                            để trao đổi nhu cầu.
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('public.dichvu.index') }}"
                           class="btn btn-light btn-lg">
                            Xem dịch vụ
                            <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
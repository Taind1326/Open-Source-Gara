@extends('public.layout')

@section('title', 'Giới thiệu')

@section('description', 'Tìm hiểu AutoCare Garage và quy trình tiếp nhận, kiểm tra, báo giá, sửa chữa và bàn giao ô tô.')

@section('content')
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
                        Giới thiệu
                    </li>
                </ol>
            </nav>

            <h1 class="display-5 fw-bold mb-3">
                Chăm sóc chiếc xe,<br>
                đồng hành cùng bạn.
            </h1>

            <p class="hero-description fs-5 mb-0">
                AutoCare Garage kết nối khách hàng với quá trình chăm sóc
                và sửa chữa ô tô qua thông tin rõ ràng trong từng giai đoạn.
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <p class="section-eyebrow mb-2">Về AutoCare Garage</p>

                    <h2 class="section-title mb-4">
                        Hiểu chiếc xe.<br>
                        Hiểu nhu cầu của bạn.
                    </h2>

                    <p class="text-muted">
                        Mỗi chiếc xe có tình trạng vận hành và nhu cầu chăm sóc
                        khác nhau. Việc tiếp nhận thông tin và kiểm tra thực tế
                        giúp xác định những hạng mục cần thực hiện.
                    </p>

                    <p class="text-muted">
                        Tại AutoCare Garage, thông tin kiểm tra được ghi nhận
                        trong hồ sơ sửa chữa. Khách hàng xem báo giá và xác nhận
                        trước khi gara tiến hành công việc.
                    </p>

                    <p class="text-muted mb-4">
                        Trong quá trình sửa chữa, tiến độ được cập nhật để
                        khách hàng theo dõi. Các hạng mục thực hiện và số tiền
                        thanh toán được thể hiện trên hóa đơn.
                    </p>

                    <a href="{{ route('public.dichvu.index') }}"
                       class="btn btn-primary">
                        Tìm hiểu dịch vụ
                        <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="col-lg-6">
                    <div class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-car-front" aria-hidden="true"></i>
                        </span>

                        <h3 class="h4 fw-bold mb-4">
                            Thông tin bạn có thể theo dõi
                        </h3>

                        <div class="d-flex gap-3 mb-4">
                            <i class="bi bi-clipboard-check text-primary fs-4"
                               aria-hidden="true"></i>

                            <div>
                                <h4 class="h6 fw-bold">Kết quả kiểm tra</h4>
                                <p class="text-muted mb-0">
                                    Tình trạng xe, chẩn đoán và ghi chú
                                    của kỹ thuật viên.
                                </p>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mb-4">
                            <i class="bi bi-receipt text-primary fs-4"
                               aria-hidden="true"></i>

                            <div>
                                <h4 class="h6 fw-bold">Báo giá sửa chữa</h4>
                                <p class="text-muted mb-0">
                                    Các dịch vụ, phụ tùng và chi phí dự kiến
                                    để bạn xem xét trước khi xác nhận.
                                </p>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mb-4">
                            <i class="bi bi-wrench-adjustable text-primary fs-4"
                               aria-hidden="true"></i>

                            <div>
                                <h4 class="h6 fw-bold">Tiến độ thực hiện</h4>
                                <p class="text-muted mb-0">
                                    Những cập nhật được ghi nhận trong
                                    quá trình sửa chữa.
                                </p>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <i class="bi bi-wallet2 text-primary fs-4"
                               aria-hidden="true"></i>

                            <div>
                                <h4 class="h6 fw-bold">Hóa đơn và thanh toán</h4>
                                <p class="text-muted mb-0">
                                    Chi tiết hạng mục, điểm đã sử dụng
                                    và trạng thái thanh toán.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <p class="section-eyebrow mb-2">Cách chúng tôi làm việc</p>

                <h2 class="section-title">Rõ ràng để bạn an tâm</h2>

                <p class="text-muted mb-0">
                    Khách hàng được cung cấp thông tin để quyết định
                    và theo dõi quá trình sửa chữa.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-chat-square-text" aria-hidden="true"></i>
                        </span>

                        <h3 class="h5 fw-bold">Lắng nghe nhu cầu</h3>

                        <p class="text-muted mb-0">
                            Ghi nhận vấn đề bạn gặp phải và những yêu cầu
                            chăm sóc xe để phục vụ việc kiểm tra.
                        </p>
                    </article>
                </div>

                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-file-earmark-check" aria-hidden="true"></i>
                        </span>

                        <h3 class="h5 fw-bold">Thống nhất trước khi sửa</h3>

                        <p class="text-muted mb-0">
                            Khách hàng xem các hạng mục và chi phí trên báo giá,
                            sau đó đồng ý hoặc từ chối.
                        </p>
                    </article>
                </div>

                <div class="col-md-4">
                    <article class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
                        </span>

                        <h3 class="h5 fw-bold">Cập nhật quá trình</h3>

                        <p class="text-muted mb-0">
                            Ghi nhận tiến độ và kết quả thực hiện trong hồ sơ
                            để thuận tiện theo dõi.
                        </p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="text-center mb-5">
                <p class="section-eyebrow mb-2">Quy trình tại gara</p>

                <h2 class="section-title">
                    Từ tiếp nhận đến bàn giao
                </h2>
            </div>

            <div class="row g-4">
                <div class="col-sm-6 col-lg-3">
                    <span class="step-number" aria-hidden="true">1</span>

                    <h3 class="h5 fw-bold">Tiếp nhận</h3>

                    <p class="text-muted mb-0">
                        Ghi nhận thông tin xe, nhu cầu và phân công
                        kỹ thuật viên phụ trách.
                    </p>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <span class="step-number" aria-hidden="true">2</span>

                    <h3 class="h5 fw-bold">Kiểm tra và báo giá</h3>

                    <p class="text-muted mb-0">
                        Kiểm tra tình trạng, đề xuất hạng mục
                        và gửi báo giá để khách hàng xác nhận.
                    </p>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <span class="step-number" aria-hidden="true">3</span>

                    <h3 class="h5 fw-bold">Sửa chữa</h3>

                    <p class="text-muted mb-0">
                        Thực hiện các hạng mục đã thống nhất,
                        cập nhật tiến độ và kiểm tra sau sửa chữa.
                    </p>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <span class="step-number" aria-hidden="true">4</span>

                    <h3 class="h5 fw-bold">Hoàn thành</h3>

                    <p class="text-muted mb-0">
                        Lập hóa đơn, xác nhận thanh toán
                        và hoàn thành hồ sơ sửa chữa.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space bg-white">
        <div class="container">
            <div class="cta-panel">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2 class="fw-bold mb-3">
                            Tìm dịch vụ phù hợp cho chiếc xe
                        </h2>

                        <p class="cta-description mb-0">
                            Xem thông tin dịch vụ hoặc liên hệ gara
                            để trao đổi thêm về nhu cầu của bạn.
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('public.dichvu.index') }}"
                           class="btn btn-light btn-lg">
                            Khám phá dịch vụ
                            <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
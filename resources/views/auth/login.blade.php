@extends('public.layout')

@section('title', 'Đăng nhập')

@section('description', 'Đăng nhập AutoCare Garage để theo dõi thông tin sửa chữa và hóa đơn của bạn.')

@section('content')
    <section class="section-space">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="card border rounded-4 overflow-hidden">
                        <div class="row g-0">
                            <div class="col-lg-5">
                                <div class="hero h-100 p-4 p-lg-5">
                                    <div class="hero-label mb-4">
                                        <i class="bi bi-tools" aria-hidden="true"></i>
                                        AutoCare Garage
                                    </div>

                                    <h1 class="h2 fw-bold mb-3">
                                        Chào mừng bạn<br>
                                        quay trở lại.
                                    </h1>

                                    <p class="hero-panel-description mb-5">
                                        Đăng nhập để kết nối với thông tin
                                        chăm sóc và sửa chữa chiếc xe của bạn.
                                    </p>

                                    <div class="hero-step">
                                        <i class="bi bi-receipt" aria-hidden="true"></i>

                                        <div>
                                            <div class="fw-semibold">
                                                Xem báo giá
                                            </div>

                                            <small>
                                                Xem các hạng mục trước khi
                                                xác nhận sửa chữa.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="hero-step">
                                        <i class="bi bi-wrench-adjustable"
                                           aria-hidden="true"></i>

                                        <div>
                                            <div class="fw-semibold">
                                                Theo dõi tiến độ
                                            </div>

                                            <small>
                                                Xem các cập nhật trong
                                                hồ sơ sửa chữa.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="hero-step">
                                        <i class="bi bi-wallet2" aria-hidden="true"></i>

                                        <div>
                                            <div class="fw-semibold">
                                                Quản lý hóa đơn
                                            </div>

                                            <small>
                                                Theo dõi chi phí và
                                                trạng thái thanh toán.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="p-4 p-lg-5">
                                    <p class="section-eyebrow mb-2">
                                        Tài khoản của bạn
                                    </p>

                                    <h2 class="h3 fw-bold mb-2">
                                        Đăng nhập
                                    </h2>

                                    <p class="text-muted mb-4">
                                        Nhập email và mật khẩu để tiếp tục.
                                    </p>

                                    <form method="POST"
                                          action="{{ route('login.store') }}">
                                        @csrf

                                        <div class="mb-4">
                                            <label for="loginEmail"
                                                   class="form-label fw-semibold">
                                                Email
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="bi bi-envelope"
                                                       aria-hidden="true"></i>
                                                </span>

                                                <input type="email"
                                                       id="loginEmail"
                                                       name="Email"
                                                       class="form-control form-control-lg @error('Email') is-invalid @enderror"
                                                       value="{{ old('Email') }}"
                                                       placeholder="Nhập email của bạn"
                                                       maxlength="100"
                                                       autocomplete="username"
                                                       required
                                                       autofocus>

                                                @error('Email')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="loginPassword"
                                                   class="form-label fw-semibold">
                                                Mật khẩu
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-text bg-light">
                                                    <i class="bi bi-lock"
                                                       aria-hidden="true"></i>
                                                </span>

                                                <input type="password"
                                                       id="loginPassword"
                                                       name="MatKhau"
                                                       class="form-control form-control-lg @error('MatKhau') is-invalid @enderror"
                                                       placeholder="Nhập mật khẩu"
                                                       autocomplete="current-password"
                                                       required>

                                                @error('MatKhau')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <button type="submit"
                                                class="btn btn-primary btn-lg w-100">
                                            Đăng nhập
                                            <i class="bi bi-arrow-right ms-2"
                                               aria-hidden="true"></i>
                                        </button>
                                    </form>

                                    <div class="border-top mt-4 pt-4 text-center">
                                        <p class="text-muted mb-2">
                                            Bạn chưa có tài khoản?
                                        </p>

                                        <a href="{{ route('register') }}"
                                           class="fw-semibold">
                                            Đăng ký tài khoản
                                            <i class="bi bi-arrow-right ms-1"
                                               aria-hidden="true"></i>
                                        </a>
                                    </div>

                                    <div class="text-center mt-4">
                                        <a href="{{ url('/') }}"
                                           class="text-muted small">
                                            <i class="bi bi-arrow-left me-1"
                                               aria-hidden="true"></i>
                                            Quay về trang chủ
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@extends('public.layout')

@section('title', 'Đăng ký')

@section('description', 'Tạo tài khoản AutoCare Garage để theo dõi báo giá, tiến độ sửa chữa và hóa đơn.')

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
                                        Bắt đầu hành trình<br>
                                        chăm sóc chiếc xe.
                                    </h1>

                                    <p class="hero-panel-description mb-5">
                                        Tạo tài khoản để theo dõi thông tin
                                        sửa chữa và thanh toán của bạn.
                                    </p>

                                    <div class="hero-step">
                                        <i class="bi bi-file-earmark-check"
                                           aria-hidden="true"></i>

                                        <div>
                                            <div class="fw-semibold">
                                                Xác nhận báo giá
                                            </div>

                                            <small>
                                                Xem hạng mục và chi phí
                                                trước khi quyết định sửa chữa.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="hero-step">
                                        <i class="bi bi-arrow-repeat"
                                           aria-hidden="true"></i>

                                        <div>
                                            <div class="fw-semibold">
                                                Theo dõi quá trình
                                            </div>

                                            <small>
                                                Xem những cập nhật tiến độ
                                                trong hồ sơ của chiếc xe.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="hero-step">
                                        <i class="bi bi-gift" aria-hidden="true"></i>

                                        <div>
                                            <div class="fw-semibold">
                                                Tích lũy điểm
                                            </div>

                                            <small>
                                                Nhận điểm khi hóa đơn được
                                                xác nhận thanh toán và sử dụng
                                                khi đủ điều kiện.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="p-4 p-lg-5">
                                    <p class="section-eyebrow mb-2">
                                        Khách hàng mới
                                    </p>

                                    <h2 class="h3 fw-bold mb-2">
                                        Tạo tài khoản
                                    </h2>

                                    <p class="text-muted mb-4">
                                        Điền thông tin bên dưới để đăng ký.
                                    </p>

                                    <form method="POST"
                                          action="{{ route('register.store') }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="registerName"
                                                   class="form-label fw-semibold">
                                                Họ và tên
                                            </label>

                                            <input type="text"
                                                   id="registerName"
                                                   name="HoTen"
                                                   class="form-control @error('HoTen') is-invalid @enderror"
                                                   value="{{ old('HoTen') }}"
                                                   placeholder="Nhập họ và tên"
                                                   maxlength="100"
                                                   autocomplete="name"
                                                   required
                                                   autofocus>

                                            @error('HoTen')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="registerEmail"
                                                   class="form-label fw-semibold">
                                                Email
                                            </label>

                                            <input type="email"
                                                   id="registerEmail"
                                                   name="Email"
                                                   class="form-control @error('Email') is-invalid @enderror"
                                                   value="{{ old('Email') }}"
                                                   placeholder="Nhập email của bạn"
                                                   maxlength="100"
                                                   autocomplete="email"
                                                   required>

                                            @error('Email')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="registerPhone"
                                                   class="form-label fw-semibold">
                                                Số điện thoại
                                            </label>

                                            <input type="tel"
                                                   id="registerPhone"
                                                   name="SoDienThoai"
                                                   class="form-control @error('SoDienThoai') is-invalid @enderror"
                                                   value="{{ old('SoDienThoai') }}"
                                                   placeholder="Nhập số điện thoại"
                                                   inputmode="numeric"
                                                   pattern="0[0-9]{9}"
                                                   minlength="10"
                                                   maxlength="10"
                                                   autocomplete="tel"
                                                   aria-describedby="phoneHelp"
                                                   required>

                                            @error('SoDienThoai')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                            <div id="phoneHelp" class="form-text">
                                                Gồm 10 chữ số, bắt đầu bằng số 0.
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="registerPassword"
                                                   class="form-label fw-semibold">
                                                Mật khẩu
                                            </label>

                                            <input type="password"
                                                   id="registerPassword"
                                                   name="MatKhau"
                                                   class="form-control @error('MatKhau') is-invalid @enderror"
                                                   placeholder="Tạo mật khẩu"
                                                   minlength="8"
                                                   autocomplete="new-password"
                                                   aria-describedby="passwordHelp"
                                                   required>

                                            @error('MatKhau')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                            <div id="passwordHelp" class="form-text">
                                                Ít nhất 8 ký tự, gồm chữ và số.
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label for="registerPasswordConfirmation"
                                                   class="form-label fw-semibold">
                                                Xác nhận mật khẩu
                                            </label>

                                            <input type="password"
                                                   id="registerPasswordConfirmation"
                                                   name="MatKhau_confirmation"
                                                   class="form-control @error('MatKhau_confirmation') is-invalid @enderror"
                                                   placeholder="Nhập lại mật khẩu"
                                                   minlength="8"
                                                   autocomplete="new-password"
                                                   required>

                                            @error('MatKhau_confirmation')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <button type="submit"
                                                class="btn btn-primary btn-lg w-100">
                                            Tạo tài khoản
                                            <i class="bi bi-arrow-right ms-2"
                                               aria-hidden="true"></i>
                                        </button>
                                    </form>

                                    <div class="border-top mt-4 pt-4 text-center">
                                        <p class="text-muted mb-2">
                                            Bạn đã có tài khoản?
                                        </p>

                                        <a href="{{ route('login') }}"
                                           class="fw-semibold">
                                            Đăng nhập
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
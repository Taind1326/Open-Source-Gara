@extends(auth()->user()->VaiTro === 'ADMIN' ? 'admin.layout' : 'public.layout')
@section('title', 'Tài khoản của tôi')

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
                        Tài khoản
                    </li>
                </ol>
            </nav>

            <h1 class="display-5 fw-bold mb-3">
                Tài khoản của tôi
            </h1>

            <p class="hero-description mb-0">
                Cập nhật thông tin liên hệ và quản lý mật khẩu của bạn.
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="row g-4 align-items-start">
                <div class="col-lg-4">
                    <aside class="feature-card h-auto">
                        <span class="feature-icon">
                            <i class="bi bi-person-circle" aria-hidden="true"></i>
                        </span>

                        <h2 class="h4 fw-bold text-break">
                            {{ $taiKhoan->HoTen }}
                        </h2>

                        <p class="text-muted text-break">
                            {{ $taiKhoan->Email }}
                        </p>

                        <span class="badge rounded-pill text-primary bg-primary-subtle px-3 py-2">
                            @switch($taiKhoan->VaiTro)
                                @case('ADMIN')
                                    Quản trị viên
                                    @break

                                @case('TECHNICIAN')
                                    Kỹ thuật viên
                                    @break

                                @default
                                    Khách hàng
                            @endswitch
                        </span>

                        @if($taiKhoan->VaiTro === 'USER')
                            <div class="border-top mt-4 pt-4">
                                <p class="text-muted small mb-1">
                                    Điểm tích lũy
                                </p>

                                <div class="fs-3 fw-bold text-primary">
                                    {{ number_format($taiKhoan->DiemTichLuy, 0, ',', '.') }}
                                    <span class="fs-6">điểm</span>
                                </div>
                            </div>

                            <a href="{{ route('hoadon.index') }}"
                               class="btn btn-outline-primary w-100 mt-4">
                                <i class="bi bi-receipt me-2" aria-hidden="true"></i>
                                Hóa đơn của tôi
                            </a>
                        @endif
                    </aside>
                </div>

                <div class="col-lg-8">
                    <section class="feature-card h-auto mb-4">
                        <p class="section-eyebrow mb-2">
                            Thông tin cá nhân
                        </p>

                        <h2 class="h4 fw-bold mb-2">
                            Thông tin liên hệ
                        </h2>

                        <p class="text-muted mb-4">
                            Sử dụng email và số điện thoại bạn đang dùng.
                        </p>

                        <form novalidate method="POST"
                              action="{{ route('public.taikhoan.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="profileName"
                                       class="form-label fw-semibold">
                                    Họ và tên
                                </label>

                                <input type="text"
                                       id="profileName"
                                       name="HoTen"
                                       class="form-control @error('HoTen') is-invalid @enderror"
                                       value="{{ old('HoTen', $taiKhoan->HoTen) }}"
                                       maxlength="100"
                                       autocomplete="name"
                                       required>

                                @error('HoTen')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="profileEmail"
                                       class="form-label fw-semibold">
                                    Email
                                </label>

                                <input type="email"
                                       id="profileEmail"
                                       name="Email"
                                       class="form-control @error('Email') is-invalid @enderror"
                                       value="{{ old('Email', $taiKhoan->Email) }}"
                                       maxlength="100"
                                       autocomplete="email"
                                       required>

                                @error('Email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="profilePhone"
                                       class="form-label fw-semibold">
                                    Số điện thoại
                                </label>

                                <input type="tel"
                                       id="profilePhone"
                                       name="SoDienThoai"
                                       class="form-control @error('SoDienThoai') is-invalid @enderror"
                                       value="{{ old('SoDienThoai', $taiKhoan->SoDienThoai) }}"
                                       inputmode="numeric"
                                       pattern="0[0-9]{9}"
                                       minlength="10"
                                       maxlength="10"
                                       autocomplete="tel"
                                       aria-describedby="profilePhoneHelp"
                                       required>

                                @error('SoDienThoai')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div id="profilePhoneHelp" class="form-text">
                                    Gồm 10 chữ số, bắt đầu bằng số 0.
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-2" aria-hidden="true"></i>
                                Lưu thông tin
                            </button>
                        </form>
                    </section>

                    <section class="feature-card h-auto">
                        <p class="section-eyebrow mb-2">
                            Bảo mật
                        </p>

                        <h2 class="h4 fw-bold mb-2">
                            Đổi mật khẩu
                        </h2>

                        <p class="text-muted mb-4">
                            Mật khẩu mới cần có ít nhất 8 ký tự, gồm chữ và số.
                        </p>

                        @if($errors->password->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->password->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form novalidate method="POST"
                              action="{{ route('public.taikhoan.password') }}" data-error-bag="password">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="currentPassword"
                                       class="form-label fw-semibold">
                                    Mật khẩu hiện tại
                                </label>

                                <input type="password"
                                       id="currentPassword"
                                       name="MatKhauHienTai"
                                       class="form-control @error('MatKhauHienTai', 'password') is-invalid @enderror"
                                       autocomplete="current-password"
                                       required>

                                @error('MatKhauHienTai', 'password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="newPassword"
                                       class="form-label fw-semibold">
                                    Mật khẩu mới
                                </label>

                                <input type="password"
                                       id="newPassword"
                                       name="MatKhau"
                                       class="form-control @error('MatKhau', 'password') is-invalid @enderror"
                                       minlength="8"
                                       autocomplete="new-password"
                                       aria-describedby="newPasswordHelp"
                                       required>

                                @error('MatKhau', 'password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div id="newPasswordHelp" class="form-text">
                                    Chọn mật khẩu khác mật khẩu hiện tại.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="confirmNewPassword"
                                       class="form-label fw-semibold">
                                    Xác nhận mật khẩu mới
                                </label>

                                <input type="password"
                                       id="confirmNewPassword"
                                       name="MatKhau_confirmation"
                                       class="form-control @error('MatKhau_confirmation', 'password') is-invalid @enderror"
                                       minlength="8"
                                       autocomplete="new-password"
                                       required>

                                @error('MatKhau_confirmation', 'password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-shield-lock me-2" aria-hidden="true"></i>
                                Đổi mật khẩu
                            </button>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </section>
@endsection
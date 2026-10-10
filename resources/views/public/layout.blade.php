<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description"
          content="@yield('description', 'Dịch vụ chăm sóc, bảo dưỡng và sửa chữa ô tô tại AutoCare Garage.')">

    <title>@yield('title', 'Trang chủ') | AutoCare Garage</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('css/public.css') }}">

    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg site-navbar sticky-top"
         aria-label="Điều hướng chính">
        <div class="container py-2">
            <a class="navbar-brand d-flex align-items-center gap-2"
               href="{{ url('/') }}">
                <span class="brand-icon">
                    <i class="bi bi-tools" aria-hidden="true"></i>
                </span>

                <span>
                    AutoCare<span class="text-primary">Garage</span>
                </span>
            </a>

            <button class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#mainNavigation"
                    aria-controls="mainNavigation"
                    aria-expanded="false"
                    aria-label="Mở hoặc đóng menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavigation">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                           href="{{ url('/') }}"
                           @if(request()->is('/')) aria-current="page" @endif>
                            Trang chủ
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('gioi-thieu') ? 'active' : '' }}"
                           href="{{ url('/gioi-thieu') }}"
                           @if(request()->is('gioi-thieu')) aria-current="page" @endif>
                            Giới thiệu
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.dichvu.*') ? 'active' : '' }}"
                           href="{{ route('public.dichvu.index') }}"
                           @if(request()->routeIs('public.dichvu.*')) aria-current="page" @endif>
                            Dịch vụ
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('lien-he') ? 'active' : '' }}"
                           href="{{ url('/lien-he') }}"
                           @if(request()->is('lien-he')) aria-current="page" @endif>
                            Liên hệ
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle account-button"
                                    type="button"
                                    id="accountMenu"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                <i class="bi bi-person-circle me-1" aria-hidden="true"></i>
                                {{ auth()->user()->HoTen }}
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="accountMenu">
                                @if(auth()->user()->VaiTro === 'ADMIN')
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-grid me-2" aria-hidden="true"></i>
                                            Trang quản trị
                                        </a>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>
                                @endif

                                @if(auth()->user()->VaiTro === 'USER')
                                    <li>
                                        <a class="dropdown-item"
                                           href="{{ route('hoadon.index') }}">
                                            <i class="bi bi-receipt me-2" aria-hidden="true"></i>
                                            Hóa đơn của tôi
                                        </a>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>
                                @endif

                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <button class="dropdown-item text-danger"
                                                type="submit">
                                            <i class="bi bi-box-arrow-right me-2"
                                               aria-hidden="true"></i>
                                            Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a class="btn btn-light" href="{{ route('login') }}">
                            Đăng nhập
                        </a>

                        <a class="btn btn-primary" href="{{ route('register') }}">
                            Đăng ký
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @if(session('success') || session('error') || $errors->any())
            <div class="container pt-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show"
                         role="status">
                        {{ session('success') }}

                        <button class="btn-close"
                                type="button"
                                data-bs-dismiss="alert"
                                aria-label="Đóng thông báo"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show"
                         role="alert">
                        {{ session('error') }}

                        <button class="btn-close"
                                type="button"
                                data-bs-dismiss="alert"
                                aria-label="Đóng thông báo"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <div class="fw-semibold mb-2">
                            Vui lòng kiểm tra lại thông tin:
                        </div>

                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer pt-5 pb-3">
        <div class="container">
            <div class="row g-4 pb-4">
                <div class="col-lg-5">
                    <a class="d-inline-flex align-items-center gap-2 text-white fs-5 fw-bold mb-3"
                       href="{{ url('/') }}">
                        <span class="brand-icon">
                            <i class="bi bi-tools" aria-hidden="true"></i>
                        </span>

                        AutoCare Garage
                    </a>

                    <p class="footer-description mb-0">
                        Chăm sóc, bảo dưỡng và sửa chữa ô tô.
                        Theo dõi quá trình sửa chữa và thông tin thanh toán
                        ngay trên website.
                    </p>
                </div>

                <div class="col-6 col-lg-3">
                    <h6 class="footer-title mb-3">Khám phá</h6>

                    <div class="d-flex flex-column gap-2">
                        <a href="{{ url('/') }}">Trang chủ</a>
                        <a href="{{ url('/gioi-thieu') }}">Giới thiệu gara</a>
                        <a href="{{ route('public.dichvu.index') }}">Dịch vụ</a>
                        <a href="{{ url('/lien-he') }}">Liên hệ</a>
                    </div>
                </div>

                <div class="col-6 col-lg-4">
                    <h6 class="footer-title mb-3">Dành cho khách hàng</h6>

                    <div class="d-flex flex-column gap-2">
                        @guest
                            <a href="{{ route('login') }}">Đăng nhập</a>
                            <a href="{{ route('register') }}">Tạo tài khoản</a>
                        @endguest

                        @auth
                            @if(auth()->user()->VaiTro === 'USER')
                                <a href="{{ route('hoadon.index') }}">
                                    Hóa đơn của tôi
                                </a>
                            @endif
                        @endauth

                        <span>Thông tin rõ ràng trong từng bước sửa chữa.</span>
                    </div>
                </div>
            </div>

            <div class="border-top border-secondary pt-3 small d-flex flex-wrap justify-content-between gap-2">
                <span>© {{ date('Y') }} AutoCare Garage.</span>
                <span>Chăm sóc chiếc xe, an tâm hành trình.</span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Dịch vụ') | Garage Auto</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        :root {
            --garage-navy: #14263d;
            --garage-blue: #2563eb;
            --garage-background: #f4f7fb;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--garage-background);
            color: var(--garage-navy);
        }

        .garage-navbar {
            background: var(--garage-navy);
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: .5px;
        }

        .navbar .nav-link {
            color: rgba(255, 255, 255, .8);
        }

        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            color: #fff;
        }

        main {
            flex: 1;
        }

        .hero {
            padding: 40px;
            border-radius: 24px;
            background: linear-gradient(135deg, #14263d, #2563eb);
            color: #fff;
        }

        .hero p {
            color: rgba(255, 255, 255, .85);
        }

        .service-card {
            height: 100%;
            overflow: hidden;
            border: 0;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(20, 38, 61, .06);
            transition: transform .2s, box-shadow .2s;
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(20, 38, 61, .12);
        }

        .service-image,
        .service-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .service-placeholder,
        .image-placeholder {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: linear-gradient(135deg, #e7efff, #d9e5f8);
            color: #335785;
            text-align: center;
            font-weight: 700;
        }

        .detail-image {
            width: 100%;
            max-height: 440px;
            object-fit: cover;
            border-radius: 18px;
        }

        .price {
            color: var(--garage-blue);
            font-weight: 800;
        }

        .btn-primary {
            background-color: var(--garage-blue);
            border-color: var(--garage-blue);
        }

        .form-control,
        .form-select,
        .btn {
            border-radius: 10px;
        }

        .garage-footer {
            background: #fff;
            border-top: 1px solid #e5eaf1;
        }

        .account-name {
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #fff;
        }

        @media (max-width: 575.98px) {
            .hero {
                padding: 28px 22px;
                border-radius: 18px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark garage-navbar">
        <div class="container py-2">
            <a
                class="navbar-brand"
                href="{{ route('public.dichvu.index') }}"
            >
                GARAGE AUTO
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#publicNavbar"
                aria-controls="publicNavbar"
                aria-expanded="false"
                aria-label="Mở menu"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="publicNavbar">
                <ul class="navbar-nav me-auto mb-3 mb-lg-0">
                    <li class="nav-item">
                        <a
                            class="nav-link {{ request()->routeIs('public.dichvu.*') ? 'active' : '' }}"
                            href="{{ route('public.dichvu.index') }}"
                        >
                            Dịch vụ
                        </a>
                    </li>
                </ul>

                <div class="d-flex flex-wrap align-items-center gap-3">
                    @auth
                        <span
                            class="account-name"
                            title="{{ auth()->user()->HoTen }}"
                        >
                            {{ auth()->user()->HoTen }}
                        </span>

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                            class="m-0"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="btn btn-outline-light btn-sm"
                            >
                                Đăng xuất
                            </button>
                        </form>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="btn btn-outline-light btn-sm"
                        >
                            Đăng nhập
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="btn btn-light btn-sm"
                        >
                            Đăng ký
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="container py-4 py-lg-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Đóng"
                ></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="fw-semibold mb-2">
                    Vui lòng kiểm tra lại thông tin:
                </div>

                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="garage-footer">
        <div class="container py-4">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                <div>
                    <div class="fw-bold">GARAGE AUTO</div>

                    <div class="small text-secondary">
                        Chăm sóc và sửa chữa ô tô.
                    </div>
                </div>

                <div class="small text-secondary">
                    Giờ làm việc: 07:30 – 20:00
                </div>
            </div>
        </div>
    </footer>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

    @stack('scripts')
</body>
</html>
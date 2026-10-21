<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Công việc của tôi') | AutoCare Garage</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="{{ asset('css/ktv.css') }}">

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/garage-theme.css') }}">
<script src="{{ asset('js/garage-theme.js') }}"></script>
</head>

<body class="ktv-app">
    <header class="ktv-header">
        <nav class="navbar navbar-expand-lg" aria-label="Menu kỹ thuật viên">
            <div class="ktv-container">
                <a
                    class="ktv-brand"
                    href="{{ route('ktv.lichhen.index') }}"
                    aria-label="AutoCare Garage — Công việc của tôi"
                >
                    <span class="ktv-brand-icon">
                        <i class="bi bi-tools" aria-hidden="true"></i>
                    </span>

                    <span>AutoCare<span class="ktv-brand-accent">Garage</span></span>
                </a>

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#ktvNavigation"
                    aria-controls="ktvNavigation"
                    aria-expanded="false"
                    aria-label="Mở menu"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="ktvNavigation">
                    <div class="ktv-navigation">
                        <a
                            href="{{ route('ktv.lichhen.index') }}"
                            class="ktv-nav-link {{ request()->routeIs('ktv.*') ? 'is-active' : '' }}"
                            @if(request()->routeIs('ktv.*')) aria-current="page" @endif
                        >
                            Công việc của tôi
                        </a>
                    </div>

                    <div class="ktv-header-actions">
                        <a href="{{ route('public.home') }}" class="ktv-website-link">
                            <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                            Xem website
                        </a>

                        <div class="dropdown">
                            <button
                                class="ktv-account-toggle dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                <span class="ktv-account-avatar">
                                    <i class="bi bi-person-fill" aria-hidden="true"></i>
                                </span>

                                <span class="ktv-account-info">
                                    <span class="ktv-account-name">
                                        {{ auth()->user()->HoTen }}
                                    </span>

                                    <span class="ktv-account-role">
                                        Kỹ thuật viên
                                    </span>
                                </span>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a
                                        class="dropdown-item"
                                        href="{{ route('public.taikhoan.edit') }}"
                                    >
                                        <i class="bi bi-person-gear" aria-hidden="true"></i>
                                        Tài khoản của tôi
                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <form
                                        method="POST"
                                        action="{{ route('logout') }}"
                                        data-confirm-title="Đăng xuất"
                                        data-confirm="Bạn có muốn đăng xuất khỏi tài khoản?"
                                        data-confirm-accept="Đăng xuất"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="dropdown-item text-danger"
                                        >
                                            <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                                            Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main class="ktv-main">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @include('shared.form-ui')
    @include('shared.flash-popup')

    @stack('scripts')
</body>
</html>
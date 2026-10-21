<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Quản trị') | AutoCareGarage</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/garage-theme.css') }}">
<script src="{{ asset('js/garage-theme.js') }}"></script>
</head>

<body class="admin-app">
    @php
        $adminMenu = [
            [
                'label' => 'Tổng quan',
                'route' => 'admin.dashboard',
                'match' => 'admin.dashboard',
                'icon' => 'bi-grid-1x2',
            ],
            [
                'label' => 'Lịch hẹn & Phân công',
                'route' => 'admin.lichhen.index',
                'match' => 'admin.lichhen.*',
                'icon' => 'bi-calendar-check',
            ],
            [
                'caption' => 'Quản lý',
            ],
            [
                'label' => 'Loại dịch vụ',
                'route' => 'loaidichvu.index',
                'match' => 'loaidichvu.*',
                'icon' => 'bi-collection',
            ],
            [
                'label' => 'Dịch vụ',
                'route' => 'dichvu.index',
                'match' => 'dichvu.*',
                'icon' => 'bi-tools',
            ],
            [
                'label' => 'Phụ tùng',
                'route' => 'phutung.index',
                'match' => 'phutung.*',
                'icon' => 'bi-box-seam',
            ],
            [
                'label' => 'Kỹ thuật viên',
                'route' => 'admin.ktv.index',
                'match' => 'admin.ktv.*',
                'icon' => 'bi-people',
            ],
            [
                'caption' => 'Kinh doanh',
            ],
            [
                'label' => 'Hóa đơn',
                'route' => 'hoadon.admin.index',
                'match' => 'hoadon.admin.*',
                'icon' => 'bi-receipt',
            ],
            [
                'label' => 'Thống kê',
                'route' => 'thongke.admin.index',
                'match' => 'thongke.admin.*',
                'icon' => 'bi-bar-chart',
            ],
            [
                'label' => 'Tin nhắn liên hệ',
                'route' => 'admin.contact.index',
                'match' => 'admin.contact.*',
                'icon' => 'bi-envelope',
            ],
        ];
    @endphp

    <aside
        class="admin-sidebar offcanvas-lg offcanvas-start"
        tabindex="-1"
        id="adminSidebar"
        aria-labelledby="adminSidebarTitle"
    >
        <a href="{{ route('admin.dashboard') }}" class="admin-brand">
            <span class="admin-brand-icon" aria-hidden="true">
                <i class="bi bi-tools"></i>
            </span>

            <span>
                <span class="admin-brand-name d-block" id="adminSidebarTitle">
                    AutoCareGarage
                </span>

                <span class="admin-brand-label d-block">
                    QUẢN TRỊ
                </span>
            </span>
        </a>

        <div class="d-lg-none text-end px-3 pt-3">
            <button
                type="button"
                class="btn btn-outline-light btn-sm"
                data-bs-dismiss="offcanvas"
                data-bs-target="#adminSidebar"
                aria-label="Đóng menu quản trị"
            >
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
        </div>

        <nav class="admin-sidebar-nav" aria-label="Menu quản trị">
            @foreach($adminMenu as $menu)
                @if(isset($menu['caption']))
                    <div class="admin-menu-caption">
                        {{ $menu['caption'] }}
                    </div>
                @else
                    @php
                        $active = request()->routeIs($menu['match']);
                    @endphp

                    <a
                        href="{{ route($menu['route']) }}"
                        class="admin-nav-link {{ $active ? 'active' : '' }}"
                        @if($active) aria-current="page" @endif
                    >
                        <i class="bi {{ $menu['icon'] }}" aria-hidden="true"></i>
                        <span>{{ $menu['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="admin-sidebar-footer">
            Hệ thống quản lý garage
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button
                    type="button"
                    class="btn btn-outline-secondary admin-mobile-toggle"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#adminSidebar"
                    aria-controls="adminSidebar"
                    aria-label="Mở menu quản trị"
                >
                    <i class="bi bi-list fs-5" aria-hidden="true"></i>
                </button>

                <div class="admin-topbar-title">
                    @yield('title', 'Quản trị')
                </div>
            </div>

            <div class="admin-topbar-actions">
                <a href="{{ route('public.website') }}" class="admin-website-link">
                    Xem website
                    <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                </a>

                <div class="dropdown">
                    <button
                        type="button"
                        class="admin-account-toggle dropdown-toggle"
                        id="adminAccountMenu"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <span class="admin-account-avatar" aria-hidden="true">
                            <i class="bi bi-person"></i>
                        </span>

                        <span>
                            <span class="admin-account-name">
                                {{ auth()->user()->HoTen }}
                            </span>

                            <span class="admin-account-role">
                                Quản trị viên
                            </span>
                        </span>
                    </button>

                    <ul
                        class="dropdown-menu dropdown-menu-end"
                        aria-labelledby="adminAccountMenu"
                    >
                        <li>
                            <a
                                href="{{ route('public.taikhoan.edit') }}"
                                class="dropdown-item"
                            >
                                <i class="bi bi-person-circle" aria-hidden="true"></i>
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
                                class="m-0"
                                novalidate
                                data-confirm-title="Đăng xuất"
                                data-confirm="Bạn muốn đăng xuất khỏi tài khoản?"
                                data-confirm-accept="Đăng xuất"
                                data-confirm-cancel="Ở lại"
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
        </header>

        <main class="admin-content" id="adminMainContent">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @include('shared.form-ui')
    @include('shared.flash-popup')

    @stack('scripts')
</body>
</html>
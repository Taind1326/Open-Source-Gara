@php
    $dangQuanLyXe = request()->routeIs(
        'public.xe.*',
        'public.datlich.index',
        'public.xacnhan.*',
        'hoadon.*'
    );
@endphp

<nav
    class="navbar navbar-expand-lg site-navbar sticky-top"
    aria-label="Điều hướng chính"
>
    <div class="container py-2">
        <a
            class="navbar-brand d-flex align-items-center gap-2"
            href="{{ route('public.home') }}"
        >
            <span class="brand-icon">
                <i class="bi bi-tools" aria-hidden="true"></i>
            </span>

            <span>
                AutoCare<span class="text-primary">Garage</span>
            </span>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#customerMainNavigation"
            aria-controls="customerMainNavigation"
            aria-expanded="false"
            aria-label="Mở hoặc đóng menu"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="customerMainNavigation">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('public.home') ? 'active' : '' }}"
                        href="{{ route('public.home') }}"
                        @if(request()->routeIs('public.home')) aria-current="page" @endif
                    >
                        Trang chủ
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->is('gioi-thieu') ? 'active' : '' }}"
                        href="{{ url('/gioi-thieu') }}"
                        @if(request()->is('gioi-thieu')) aria-current="page" @endif
                    >
                        Giới thiệu
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->routeIs('public.dichvu.*') ? 'active' : '' }}"
                        href="{{ route('public.dichvu.index') }}"
                        @if(request()->routeIs('public.dichvu.*')) aria-current="page" @endif
                    >
                        Dịch vụ
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link {{ request()->is('lien-he') ? 'active' : '' }}"
                        href="{{ url('/lien-he') }}"
                        @if(request()->is('lien-he')) aria-current="page" @endif
                    >
                        Liên hệ
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a
                        class="nav-link dropdown-toggle {{ $dangQuanLyXe ? 'active' : '' }}"
                        href="#"
                        id="customerVehicleMenu"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        Quản lý xe
                    </a>

                    <ul
                        class="dropdown-menu"
                        aria-labelledby="customerVehicleMenu"
                    >
                        <li>
                            <a
                                class="dropdown-item {{ request()->routeIs('public.xe.*') ? 'active' : '' }}"
                                href="{{ route('public.xe.index') }}"
                            >
                                <i class="bi bi-car-front me-2" aria-hidden="true"></i>
                                Xe của tôi
                            </a>
                        </li>

                        <li>
                            <a
                                class="dropdown-item {{ request()->routeIs('public.datlich.index', 'public.xacnhan.*') ? 'active' : '' }}"
                                href="{{ route('public.datlich.index') }}"
                            >
                                <i class="bi bi-calendar-check me-2" aria-hidden="true"></i>
                                Lịch hẹn của tôi
                            </a>
                        </li>

                        <li>
                            <a
                                class="dropdown-item {{ request()->routeIs('hoadon.*') ? 'active' : '' }}"
                                href="{{ route('hoadon.index') }}"
                            >
                                <i class="bi bi-receipt me-2" aria-hidden="true"></i>
                                Hóa đơn và thanh toán
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mt-lg-0">
                <a
                    class="btn btn-primary"
                    href="{{ route('public.datlich.create') }}"
                >
                    <i class="bi bi-calendar-plus me-1" aria-hidden="true"></i>
                    Đặt lịch
                </a>

                <div class="dropdown">
                    <button
                        class="btn btn-light dropdown-toggle account-button"
                        type="button"
                        id="customerAccountMenu"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <i class="bi bi-person-circle me-1" aria-hidden="true"></i>
                        {{ auth()->user()->HoTen }}
                    </button>

                    <ul
                        class="dropdown-menu dropdown-menu-end"
                        aria-labelledby="customerAccountMenu"
                    >
                        <li>
                            <a
                                class="dropdown-item"
                                href="{{ route('public.taikhoan.edit') }}"
                            >
                                <i class="bi bi-person me-2" aria-hidden="true"></i>
                                Tài khoản của tôi
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <x-public.logout-button />
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
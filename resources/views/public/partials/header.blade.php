@php
    $vaiTro = auth()->user()?->VaiTro;
    $laKhachHang = $vaiTro === 'USER';

    $dangQuanLyXe = request()->routeIs(
        'public.xe.*',
        'public.datlich.index',
        'public.xacnhan.*'
    );

    $dangThanhToan = request()->routeIs('hoadon.*');
@endphp

<nav
    id="customerNavigation"
    class="navbar sticky-top"
    aria-label="Điều hướng chính"
>
    <div class="customer-header-container">
        <a
            class="customer-brand"
            href="{{ route('public.home') }}"
            aria-label="AutoCare Garage — Trang chủ"
        >
            <span class="customer-brand-icon">
                <i class="bi bi-tools" aria-hidden="true"></i>
            </span>

            <span>
                AutoCare<span class="customer-brand-accent">Garage</span>
            </span>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#customerMenu"
            aria-controls="customerMenu"
            aria-expanded="false"
            aria-label="Mở hoặc đóng menu"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="customerMenu">
            <ul class="navbar-nav customer-main-menu">
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

                @if($laKhachHang)
                    <li class="nav-item dropdown">
                        <button
                            class="nav-link dropdown-toggle {{ $dangQuanLyXe ? 'active' : '' }}"
                            id="customerVehicleMenu"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            Quản lý xe
                        </button>

                        <ul
                            class="dropdown-menu"
                            aria-labelledby="customerVehicleMenu"
                        >
                            <li>
                                <a
                                    class="dropdown-item {{ request()->routeIs('public.xe.*') ? 'active' : '' }}"
                                    href="{{ route('public.xe.index') }}"
                                >
                                    <i class="bi bi-car-front" aria-hidden="true"></i>
                                    Xe của tôi
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item {{ request()->routeIs('public.datlich.index', 'public.xacnhan.*') ? 'active' : '' }}"
                                    href="{{ route('public.datlich.index') }}"
                                >
                                    <i class="bi bi-calendar-check" aria-hidden="true"></i>
                                    Lịch hẹn của tôi
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a
                            class="nav-link {{ $dangThanhToan ? 'active' : '' }}"
                            href="{{ route('hoadon.index') }}"
                            @if($dangThanhToan) aria-current="page" @endif
                        >
                            Thanh toán
                        </a>
                    </li>
                @elseif($vaiTro === 'TECHNICIAN')
                    <li class="nav-item">
                        <a
                            class="nav-link {{ request()->routeIs('ktv.*') ? 'active' : '' }}"
                            href="{{ route('ktv.lichhen.index') }}"
                        >
                            Công việc của tôi
                        </a>
                    </li>
                @elseif($vaiTro === 'ADMIN')
                    <li class="nav-item">
                        <a
                            class="nav-link"
                            href="{{ route('admin.dashboard') }}"
                        >
                            Trang quản trị
                        </a>
                    </li>
                @endif
            </ul>

            <div class="customer-header-actions">
                @if(!auth()->check() || $laKhachHang)
                    <a
                        class="btn customer-booking-button"
                        href="{{ route('public.datlich.create') }}"
                    >
                        Đặt lịch
                    </a>
                @endif

                @auth
                    <div class="dropdown customer-account">
                        <button
                            class="customer-account-toggle dropdown-toggle"
                            id="customerAccountMenu"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <span class="customer-account-avatar">
                                <i class="bi bi-person-fill" aria-hidden="true"></i>
                            </span>

                            <span class="customer-account-name">
                                {{ auth()->user()->HoTen }}
                            </span>
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
                                        class="dropdown-item text-danger"
                                        type="submit"
                                    >
                                        <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
                                        Đăng xuất
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a
                        class="customer-login-link"
                        href="{{ route('login') }}"
                    >
                        Đăng nhập
                    </a>

                    <a
                        class="btn customer-register-button"
                        href="{{ route('register') }}"
                    >
                        Đăng ký
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
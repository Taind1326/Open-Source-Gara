<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Quản trị') | Garage Auto</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        :root {
            --navy: #14263d;
            --blue: #2563eb;
            --background: #f4f7fb;
        }

        body {
            background: var(--background);
            color: var(--navy);
        }

        .admin-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: 250px;
            padding: 28px 18px;
            background: var(--navy);
            color: white;
            overflow-y: auto;
            z-index: 1045;
        }

        .brand {
            display: block;
            padding: 0 14px;
            margin-bottom: 32px;
            color: white;
            text-decoration: none;
            font-size: 22px;
            font-weight: 800;
        }

        .menu-caption {
            padding: 0 14px;
            margin: 24px 0 10px;
            font-size: 11px;
            color: #9eafc6;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .sidebar-link {
            display: block;
            margin-bottom: 6px;
            padding: 12px 14px;
            border-radius: 12px;
            color: #cfdaea;
            text-decoration: none;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, .08);
            color: white;
        }

        .sidebar-link.active {
            background: var(--blue);
            color: white;
            font-weight: 600;
        }

        .admin-main {
            margin-left: 250px;
            min-height: 100vh;
        }

        .admin-topbar {
            background: white;
            border-bottom: 1px solid #e5eaf1;
        }

        .admin-content {
            padding: 32px;
        }

        .card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(20, 38, 61, .05);
        }

        .btn,
        .form-control,
        .form-select {
            border-radius: 10px;
        }

        .btn-primary {
            background: var(--blue);
            border-color: var(--blue);
        }

        .dashboard-banner {
            padding: 30px;
            border-radius: 20px;
            background: linear-gradient(135deg, #14263d, #2563eb);
            color: white;
        }

        .shortcut-card {
            display: block;
            height: 100%;
            padding: 26px;
            background: white;
            border: 1px solid #e5eaf1;
            border-radius: 18px;
            color: var(--navy);
            text-decoration: none;
            transition: border-color .2s, transform .2s;
        }

        .shortcut-card:hover {
            border-color: var(--blue);
            transform: translateY(-3px);
        }

        .shortcut-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            margin-bottom: 20px;
            border-radius: 12px;
            background: #eaf0ff;
            color: var(--blue);
            font-weight: 800;
        }

        @media (max-width: 991.98px) {
            .admin-main {
                margin-left: 0;
            }

            .admin-sidebar {
                position: static;
                width: 100%;
            }

            .admin-content {
                padding: 20px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <aside class="admin-sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            GARAGE AUTO
            <span class="d-block small fw-normal text-white-50 mt-1">
                Quản trị hệ thống
            </span>
        </a>

        <nav aria-label="Menu quản trị">
            <a
                href="{{ route('admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
            >
                Tổng quan
            </a>

            <div class="menu-caption">Danh mục</div>

            <a
                href="{{ route('loaidichvu.index') }}"
                class="sidebar-link {{ request()->routeIs('loaidichvu.*') ? 'active' : '' }}"
            >
                Loại dịch vụ
            </a>

            <a
                href="{{ route('dichvu.index') }}"
                class="sidebar-link {{ request()->routeIs('dichvu.*') ? 'active' : '' }}"
            >
                Dịch vụ
            </a>

            <a
                href="{{ route('phutung.index') }}"
                class="sidebar-link {{ request()->routeIs('phutung.*') ? 'active' : '' }}"
            >
                Phụ tùng
            </a>

            <div class="menu-caption">Kinh doanh</div>

            <a
                href="{{ route('hoadon.admin.index') }}"
                class="sidebar-link {{ request()->routeIs('hoadon.admin.*') ? 'active' : '' }}"
            >
                Hóa đơn
            </a>

            <a
                href="{{ route('thongke.admin.index') }}"
                class="sidebar-link {{ request()->routeIs('thongke.admin.*') ? 'active' : '' }}"
            >
                Thống kê
            </a>

            <div class="menu-caption">Website</div>

            <a
                href="{{ route('public.dichvu.index') }}"
                class="sidebar-link"
            >
                Xem trang khách hàng
            </a>
        </nav>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 px-4 py-3">
                <div>
                    <div class="fw-bold">@yield('title', 'Quản trị')</div>
                    <div class="small text-secondary">
                        Hệ thống quản lý gara ô tô
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="text-end">
                        <div class="fw-semibold">
                            {{ auth()->user()->HoTen }}
                        </div>

                        <div class="small text-secondary">
                            Quản trị viên
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf

                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="admin-content">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
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
    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    ></script>

    @stack('scripts')
</body>
</html>
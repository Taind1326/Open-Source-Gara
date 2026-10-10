<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gara')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@auth
    @php($user = auth()->user())
    <header class="topbar">
        <div class="in">
            <a class="brand" href="{{ route('trang-chu') }}">Gara<b>Care</b></a>

            <nav class="nav" aria-label="Điều hướng chính">
                @if($user->coVaiTro(\App\Models\TaiKhoan::VAI_TRO_USER))
                    <a href="{{ route('xe.index') }}" @if(request()->routeIs('xe.*')) aria-current="page" @endif>Xe của tôi</a>
                    @if(Route::has('lichhen.index'))
                        <a href="{{ route('lichhen.index') }}" @if(request()->routeIs('lichhen.*')) aria-current="page" @endif>Lịch hẹn</a>
                    @endif
                @endif

                @if($user->coVaiTro(\App\Models\TaiKhoan::VAI_TRO_ADMIN))
                    <a href="{{ route('admin.taikhoan.index') }}" @if(request()->routeIs('admin.taikhoan.*')) aria-current="page" @endif>Tài khoản</a>
                    <a href="{{ route('admin.xe.index') }}" @if(request()->routeIs('admin.xe.*')) aria-current="page" @endif>Xe khách hàng</a>
                @endif

                <a href="{{ route('ho-so.show') }}" @if(request()->routeIs('ho-so.*')) aria-current="page" @endif>Hồ sơ</a>
            </nav>

            <div class="who">
                <span>{{ $user->HoTen }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="linkbtn">Đăng xuất</button>
                </form>
            </div>
        </div>
    </header>
@endauth

<main class="wrap @yield('wrap')">
    @include('layouts.thong-bao')
    @yield('content')
</main>
</body>
</html>

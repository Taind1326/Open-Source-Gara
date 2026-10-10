<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gara')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="auth">
    <a class="logo" href="{{ url('/') }}">Gara<b>Care</b></a>
    @include('layouts.thong-bao')
    @yield('content')
</div>
</body>
</html>

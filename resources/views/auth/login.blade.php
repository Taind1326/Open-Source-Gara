@extends('layouts.guest')
@section('title', 'Đăng nhập')

@section('content')
<div class="card">
    <h2>Đăng nhập</h2>

    <form method="POST" action="{{ route('login.xu-ly') }}" novalidate>
        @csrf

        <div class="field">
            <label for="SoDienThoai">Số điện thoại</label>
            <input type="tel" id="SoDienThoai" name="SoDienThoai" value="{{ old('SoDienThoai') }}"
                   inputmode="numeric" autocomplete="username" required autofocus
                   class="@error('SoDienThoai') is-invalid @enderror">
            @error('SoDienThoai')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required
                   class="@error('password') is-invalid @enderror">
            @error('password')<div class="err">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-block">Đăng nhập</button>
    </form>

    <p class="alt small">Chưa có tài khoản? <a href="{{ route('dang-ky') }}">Đăng ký</a></p>
</div>
@endsection

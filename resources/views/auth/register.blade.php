@extends('layouts.guest')
@section('title', 'Đăng ký')

@section('content')
<div class="card">
    <h2>Đăng ký khách hàng</h2>

    <form method="POST" action="{{ route('dang-ky.xu-ly') }}" novalidate>
        @csrf

        <div class="field">
            <label for="HoTen">Họ và tên</label>
            <input type="text" id="HoTen" name="HoTen" value="{{ old('HoTen') }}" maxlength="100" required autofocus
                   autocomplete="name" class="@error('HoTen') is-invalid @enderror">
            @error('HoTen')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="SoDienThoai">Số điện thoại</label>
            <input type="tel" id="SoDienThoai" name="SoDienThoai" value="{{ old('SoDienThoai') }}" inputmode="numeric"
                   maxlength="10" required autocomplete="tel" class="@error('SoDienThoai') is-invalid @enderror">
            <div class="hint">10 chữ số, bắt đầu bằng 0. Dùng để đăng nhập.</div>
            @error('SoDienThoai')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="Email">Email <span class="opt">(không bắt buộc)</span></label>
            <input type="email" id="Email" name="Email" value="{{ old('Email') }}" maxlength="150" autocomplete="email"
                   class="@error('Email') is-invalid @enderror">
            @error('Email')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="DiaChi">Địa chỉ <span class="opt">(không bắt buộc)</span></label>
            <input type="text" id="DiaChi" name="DiaChi" value="{{ old('DiaChi') }}" maxlength="255"
                   autocomplete="street-address" class="@error('DiaChi') is-invalid @enderror">
            @error('DiaChi')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" autocomplete="new-password" required
                   class="@error('password') is-invalid @enderror">
            <div class="hint">Tối thiểu 6 ký tự.</div>
            @error('password')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Nhập lại mật khẩu</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
        </div>

        <button type="submit" class="btn btn-block">Tạo tài khoản</button>
    </form>

    <p class="alt small">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
</div>
@endsection

@extends('layouts.app')
@section('title', 'Hồ sơ cá nhân')
@section('wrap', 'mid')

@section('content')
@php
    $nhanVaiTro = ['USER' => 'Khách hàng', 'TECHNICIAN' => 'Kỹ thuật viên', 'ADMIN' => 'Quản trị viên'];
@endphp

<div class="head">
    <h1>Hồ sơ cá nhân</h1>
    <span class="tag tag-role">{{ $nhanVaiTro[$taiKhoan->VaiTro] ?? $taiKhoan->VaiTro }}</span>
</div>

@if($taiKhoan->VaiTro === \App\Models\TaiKhoan::VAI_TRO_USER)
<section class="card">
    <h2>Điểm tích lũy</h2>
    <p><strong>{{ number_format($taiKhoan->DiemTichLuy, 0, ',', '.') }}</strong> điểm</p>
    <div class="hint">Cứ 10.000đ thanh toán được 1 điểm. 500 điểm giảm 10%, 1.000 điểm giảm 15% cho hóa đơn kế tiếp.</div>
</section>
@endif

<section class="card">
    <h2>Thông tin liên hệ</h2>
    <form method="POST" action="{{ route('ho-so.update') }}" novalidate>
        @csrf
        @method('PUT')

        <div class="field">
            <label for="HoTen">Họ và tên</label>
            <input type="text" id="HoTen" name="HoTen" value="{{ old('HoTen', $taiKhoan->HoTen) }}" maxlength="100" required
                   class="@error('HoTen') is-invalid @enderror">
            @error('HoTen')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="grid2">
            <div class="field">
                <label for="SoDienThoai">Số điện thoại</label>
                <input type="tel" id="SoDienThoai" name="SoDienThoai" value="{{ old('SoDienThoai', $taiKhoan->SoDienThoai) }}"
                       inputmode="numeric" maxlength="10" required class="@error('SoDienThoai') is-invalid @enderror">
                @error('SoDienThoai')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="Email">Email <span class="opt">(không bắt buộc)</span></label>
                <input type="email" id="Email" name="Email" value="{{ old('Email', $taiKhoan->Email) }}" maxlength="150"
                       class="@error('Email') is-invalid @enderror">
                @error('Email')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="field">
            <label for="DiaChi">Địa chỉ <span class="opt">(không bắt buộc)</span></label>
            <input type="text" id="DiaChi" name="DiaChi" value="{{ old('DiaChi', $taiKhoan->DiaChi) }}" maxlength="255"
                   class="@error('DiaChi') is-invalid @enderror">
            @error('DiaChi')<div class="err">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn">Lưu thay đổi</button>
    </form>
</section>

<section class="card">
    <h2>Đổi mật khẩu</h2>
    <form method="POST" action="{{ route('ho-so.mat-khau') }}" novalidate>
        @csrf
        @method('PUT')

        <div class="field">
            <label for="mat_khau_cu">Mật khẩu hiện tại</label>
            <input type="password" id="mat_khau_cu" name="mat_khau_cu" autocomplete="current-password" required
                   class="@error('mat_khau_cu') is-invalid @enderror">
            @error('mat_khau_cu')<div class="err">{{ $message }}</div>@enderror
        </div>

        <div class="grid2">
            <div class="field">
                <label for="password">Mật khẩu mới</label>
                <input type="password" id="password" name="password" autocomplete="new-password" required
                       class="@error('password') is-invalid @enderror">
                <div class="hint">Tối thiểu 6 ký tự.</div>
                @error('password')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Nhập lại mật khẩu mới</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
            </div>
        </div>

        <button type="submit" class="btn">Đổi mật khẩu</button>
    </form>
</section>
@endsection

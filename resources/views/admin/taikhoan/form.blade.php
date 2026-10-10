@extends('layouts.app')
@section('title', $taiKhoan->exists ? 'Sửa tài khoản' : 'Tạo tài khoản')
@section('wrap', 'mid')

@section('content')
@php
    $nhanVaiTro = ['USER' => 'Khách hàng', 'TECHNICIAN' => 'Kỹ thuật viên', 'ADMIN' => 'Quản trị viên'];
    $tuSua = $taiKhoan->exists && $taiKhoan->MaTK === auth()->id();
@endphp

<div class="head">
    <h1>{{ $taiKhoan->exists ? 'Sửa tài khoản' : 'Tạo tài khoản mới' }}</h1>
</div>

<section class="card">
    <form method="POST"
          action="{{ $taiKhoan->exists ? route('admin.taikhoan.update', $taiKhoan->MaTK) : route('admin.taikhoan.store') }}" novalidate>
        @csrf
        @if($taiKhoan->exists) @method('PUT') @endif

        <div class="field">
            <label for="HoTen">Họ và tên</label>
            <input type="text" id="HoTen" name="HoTen" value="{{ old('HoTen', $taiKhoan->HoTen) }}" maxlength="100" required autofocus
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

        <div class="grid2">
            <div class="field">
                <label for="VaiTro">Vai trò</label>
                <select id="VaiTro" name="VaiTro" required class="@error('VaiTro') is-invalid @enderror">
                    @foreach($nhanVaiTro as $ma => $ten)
                        <option value="{{ $ma }}" @selected(old('VaiTro', $taiKhoan->VaiTro ?? 'USER') === $ma)>{{ $ten }}</option>
                    @endforeach
                </select>
                @if($tuSua)<div class="hint">Bạn không thể tự đổi vai trò của chính mình.</div>@endif
                @error('VaiTro')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div class="field">
                <label for="password">
                    {{ $taiKhoan->exists ? 'Mật khẩu mới' : 'Mật khẩu' }}
                    @if($taiKhoan->exists)<span class="opt">(để trống nếu không đổi)</span>@endif
                </label>
                <input type="password" id="password" name="password" autocomplete="new-password"
                       @unless($taiKhoan->exists) required @endunless class="@error('password') is-invalid @enderror">
                <div class="hint">Tối thiểu 6 ký tự.</div>
                @error('password')<div class="err">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn">{{ $taiKhoan->exists ? 'Lưu thay đổi' : 'Tạo tài khoản' }}</button>
            <a class="btn btn-line"
               href="{{ $taiKhoan->exists ? route('admin.taikhoan.show', $taiKhoan->MaTK) : route('admin.taikhoan.index') }}">Hủy</a>
        </div>
    </form>
</section>
@endsection

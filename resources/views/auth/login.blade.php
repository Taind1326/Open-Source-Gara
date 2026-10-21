@extends('public.layout')
@section('title', 'Đăng nhập')
@section('description', 'Đăng nhập AutoCare Garage để đặt lịch và theo dõi quá trình chăm sóc xe.')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth-orange.css') }}">
@endpush
@section('content')
<section class="garage-auth">
    <div class="container">
        <div class="garage-auth-card">
            <aside class="garage-auth-story">
                <span class="garage-auth-brand"><i class="bi bi-tools" aria-hidden="true"></i> AutoCare Garage</span>
                <div>
                    <p class="garage-auth-eyebrow">Đồng hành cùng chiếc xe của bạn</p>
                    <h1>An tâm chăm sóc xe,<br>thuận tiện mỗi ngày.</h1>
                    <p>Đặt lịch, xem báo giá và theo dõi tiến độ sửa chữa ngay trong tài khoản.</p>
                </div>
                <ul class="garage-auth-benefits">
                    <li><i class="bi bi-calendar-check" aria-hidden="true"></i><div><strong>Đặt lịch thuận tiện</strong><span>Chọn xe và khung giờ phù hợp.</span></div></li>
                    <li><i class="bi bi-file-earmark-check" aria-hidden="true"></i><div><strong>Báo giá rõ ràng</strong><span>Xem hạng mục trước khi đồng ý sửa.</span></div></li>
                    <li><i class="bi bi-wrench-adjustable" aria-hidden="true"></i><div><strong>Theo dõi quá trình</strong><span>Xem tiến độ và hóa đơn của bạn.</span></div></li>
                </ul>
            </aside>
            <div class="garage-auth-form-panel">
                <p class="garage-auth-eyebrow">Chào mừng bạn quay trở lại</p>
                <h2>Đăng nhập</h2>
                <p class="garage-auth-intro">Nhập email và mật khẩu để tiếp tục.</p>
                <form action="{{ route('login.store') }}" method="POST">
                    @csrf
                    <div class="garage-auth-field">
                        <label for="loginEmail">Email</label>
                        <input class="form-control @error('Email') is-invalid @enderror" type="email" id="loginEmail" name="Email" value="{{ old('Email') }}" placeholder="Nhập email của bạn" maxlength="100" autocomplete="username" required autofocus @error('Email') aria-invalid="true" aria-describedby="loginEmailError" @enderror>
                        @error('Email')<div id="loginEmailError" class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="garage-auth-field">
                        <label for="loginPassword">Mật khẩu</label>
                        <div class="garage-auth-password">
                            <input class="form-control @error('MatKhau') is-invalid @enderror" type="password" id="loginPassword" name="MatKhau" placeholder="Nhập mật khẩu" maxlength="255" autocomplete="current-password" required @error('MatKhau') aria-invalid="true" aria-describedby="loginPasswordError" @enderror>
                            <button class="garage-auth-toggle" type="button" data-password-toggle aria-controls="loginPassword" aria-label="Hiện mật khẩu" aria-pressed="false" hidden><i class="bi bi-eye" aria-hidden="true"></i></button>
                        </div>
                        @error('MatKhau')<p id="loginPasswordError" class="garage-auth-error">{{ $message }}</p>@enderror
                    </div>
                    @if(app()->environment(['local', 'testing']) && config('password_demo.enabled'))
                        <div class="text-end mb-3">
                            <a href="{{ route('password.demo') }}"
                               class="garage-auth-forgot"
                               style="color: #b94305; font-weight: 650; display: inline-block; padding: 8px 0;">
                                Quên mật khẩu?
                            </a>
                        </div>
                    @endif
                    <button class="btn btn-primary garage-auth-submit" type="submit">Đăng nhập <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></button>
                </form>
                <div class="garage-auth-register">Bạn chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></div>
                <a class="garage-auth-back" href="{{ route('public.home') }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Quay về trang chủ</a>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script src="{{ asset('js/auth-orange.js') }}" defer></script>
@endpush

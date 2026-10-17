@extends('public.layout')
@section('title', 'Quên mật khẩu')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/password-demo.css') }}">
@endpush
@section('content')
<section class="password-demo-page">
    <div class="container">
        <div class="password-demo-card">
            <div class="password-demo-mark"><i class="bi bi-shield-lock" aria-hidden="true"></i></div>
            <h1>Khôi phục mật khẩu</h1>
            <ol class="password-demo-steps" aria-label="Các bước khôi phục">
                @foreach(['Email', 'Xác nhận OTP', 'Mật khẩu mới'] as $label)
                    <li class="{{ $loop->iteration <= $step ? 'is-active' : '' }}" @if($loop->iteration === $step) aria-current="step" @endif><span>{{ $loop->iteration }}</span>{{ $label }}</li>
                @endforeach
            </ol>
            <p class="password-demo-notice">Chế độ demo — OTP: <strong>123456</strong>. Không gửi email thật.</p>
            @if($step === 1)
                <h2>Nhập email của bạn</h2>
                <p>Nhập email đã đăng ký tài khoản tại AutoCare Garage.</p>
                <form method="POST" action="{{ route('password.demo.start') }}">
                    @csrf
                    <label for="reset-email">Email</label>
                    <input class="form-control @error('Email') is-invalid @enderror" id="reset-email" name="Email" type="email" value="{{ old('Email') }}" autocomplete="email" maxlength="100" placeholder="Nhập email của bạn" required autofocus>
                    @error('Email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <button class="btn btn-primary w-100 mt-4" type="submit">Tiếp tục <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></button>
                </form>
            @elseif($step === 2)
                @php
                    $expires = \Carbon\Carbon::parse($flow->expires_at)->getTimestampMs();
                    $remaining = max(0, (int) ceil(($expires - now()->getTimestampMs()) / 1000));
                @endphp
                <h2>Xác nhận mã OTP</h2>
                <p>Email khôi phục: <strong class="password-demo-email">{{ $flow->email }}</strong></p>
                <form method="POST" action="{{ route('password.demo.verify') }}">
                    @csrf
                    <label for="reset-otp">Mã xác nhận 6 chữ số</label>
                    <input class="form-control password-demo-otp @error('otp') is-invalid @enderror" id="reset-otp" name="otp" inputmode="numeric" pattern="[0-9]{6}" minlength="6" maxlength="6" autocomplete="one-time-code" placeholder="000000" required autofocus>
                    @error('otp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <p class="password-demo-timer mt-3" data-countdown data-expires="{{ $expires }}" data-server-now="{{ now()->getTimestampMs() }}">Mã hết hạn sau <strong>{{ sprintf('%02d:%02d', intdiv($remaining, 60), $remaining % 60) }}</strong></p>
                    <button class="btn btn-primary w-100" type="submit" data-verify @disabled($remaining === 0)>Xác nhận OTP</button>
                </form>
                <div class="password-demo-resend">
                    <form method="POST" action="{{ route('password.demo.resend') }}">
                        @csrf
                        <button class="password-demo-link" type="submit" data-resend @disabled($remaining > 0)>Gửi lại mã</button>
                    </form>
                    <noscript><p>Hết 2 phút, tải lại trang để bật nút gửi lại.</p></noscript>
                </div>
            @else
                <h2>Tạo mật khẩu mới</h2>
                <p>Mật khẩu có ít nhất 8 ký tự, gồm chữ và số.</p>
                <form method="POST" action="{{ route('password.demo.reset') }}">
                    @csrf
                    @foreach(['MatKhau' => 'Mật khẩu mới', 'MatKhau_confirmation' => 'Xác nhận mật khẩu mới'] as $name => $label)
                        <div class="mt-3">
                            <label for="reset-{{ $name }}">{{ $label }}</label>
                            <div class="password-demo-input-wrap">
                                <input class="form-control @error($name) is-invalid @enderror" type="password" id="reset-{{ $name }}" name="{{ $name }}" minlength="8" maxlength="255" autocomplete="new-password" required>
                                <button class="password-demo-eye" type="button" data-reset-toggle aria-controls="reset-{{ $name }}" aria-label="Hiện mật khẩu" aria-pressed="false" hidden><i class="bi bi-eye" aria-hidden="true"></i></button>
                            </div>
                            @error($name)<p class="text-danger small mt-2">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                    <button class="btn btn-primary w-100 mt-4" type="submit">Đổi mật khẩu</button>
                </form>
            @endif
            @if($step > 1)
                <form method="POST" action="{{ route('password.demo.restart') }}" class="mt-3">
                    @csrf
                    <button class="password-demo-link" type="submit">Dùng email khác / Bắt đầu lại</button>
                </form>
            @endif
            <a class="password-demo-back" href="{{ route('login') }}"><i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Quay lại đăng nhập</a>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script src="{{ asset('js/password-demo.js') }}" defer></script>
@endpush

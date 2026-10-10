@extends('public.layout')

@section('title', 'Đăng nhập')

@section('content')
    <div class="row justify-content-center py-4">
        <div class="col-md-8 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 fw-bold mb-2">Đăng nhập</h1>

                    <p class="text-secondary mb-4">
                        Đăng nhập để đặt lịch và theo dõi quá trình sửa xe.
                    </p>

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="Email" class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                id="Email"
                                name="Email"
                                class="form-control @error('Email') is-invalid @enderror"
                                value="{{ old('Email') }}"
                                maxlength="100"
                                autocomplete="username"
                                required
                                autofocus
                            >

                            @error('Email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="MatKhau" class="form-label">
                                Mật khẩu
                            </label>

                            <input
                                type="password"
                                id="MatKhau"
                                name="MatKhau"
                                class="form-control @error('MatKhau') is-invalid @enderror"
                                autocomplete="current-password"
                                required
                            >

                            @error('MatKhau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Đăng nhập
                        </button>
                    </form>

                    <p class="text-center text-secondary mt-4 mb-0">
                        Chưa có tài khoản?
                        <a href="{{ route('register') }}">Đăng ký ngay</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

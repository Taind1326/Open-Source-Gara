@extends('public.layout')

@section('title', 'Đăng ký tài khoản')

@section('content')
    <div class="row justify-content-center py-4">
        <div class="col-md-9 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 fw-bold mb-2">Tạo tài khoản</h1>

                    <p class="text-secondary mb-4">
                        Quản lý xe và theo dõi lịch sửa chữa của m.
                    </p>

                    <form method="POST" action="{{ route('register.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="HoTen" class="form-label">Họ tên</label>

                            <input
                                type="text"
                                id="HoTen"
                                name="HoTen"
                                class="form-control @error('HoTen') is-invalid @enderror"
                                value="{{ old('HoTen') }}"
                                maxlength="100"
                                autocomplete="name"
                                required
                                autofocus
                            >

                            @error('HoTen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="Email" class="form-label">Email</label>

                            <input
                                type="email"
                                id="Email"
                                name="Email"
                                class="form-control @error('Email') is-invalid @enderror"
                                value="{{ old('Email') }}"
                                maxlength="100"
                                autocomplete="email"
                                required
                            >

                            @error('Email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="SoDienThoai" class="form-label">
                                Số điện thoại
                            </label>

                            <input
                                type="tel"
                                id="SoDienThoai"
                                name="SoDienThoai"
                                class="form-control @error('SoDienThoai') is-invalid @enderror"
                                value="{{ old('SoDienThoai') }}"
                                maxlength="10"
                                pattern="0[0-9]{9}"
                                autocomplete="tel"
                                required
                            >

                            @error('SoDienThoai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="MatKhau" class="form-label">
                                Mật khẩu
                            </label>

                            <input
                                type="password"
                                id="MatKhau"
                                name="MatKhau"
                                class="form-control @error('MatKhau') is-invalid @enderror"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >

                            <div class="form-text">
                                Ít nhất 8 ký tự, có chữ cái và chữ số.
                            </div>

                            @error('MatKhau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="MatKhau_confirmation" class="form-label">
                                Xác nhận mật khẩu
                            </label>

                            <input
                                type="password"
                                id="MatKhau_confirmation"
                                name="MatKhau_confirmation"
                                class="form-control"
                                minlength="8"
                                autocomplete="new-password"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Đăng ký
                        </button>
                    </form>

                    <p class="text-center text-secondary mt-4 mb-0">
                        Đã có tài khoản?
                        <a href="{{ route('login') }}">Đăng nhập</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

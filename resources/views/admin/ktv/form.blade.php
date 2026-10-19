@extends('admin.layout')

@section('title', $ktv->exists ? 'Sửa kỹ thuật viên' : 'Thêm kỹ thuật viên')

@section('content')
    @php
        $isEdit = $ktv->exists;
    @endphp

    <a
        href="{{ route('admin.ktv.index') }}"
        class="btn btn-outline-primary mb-4"
    >
        Quay lại danh sách
    </a>

    <div class="row">
        <div class="col-lg-8 col-xl-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h4 fw-bold mb-4">
                        {{ $isEdit ? 'Sửa thông tin kỹ thuật viên' : 'Thêm kỹ thuật viên' }}
                    </h1>

                    <form
                        method="POST"
                        action="{{ $isEdit
                            ? route('admin.ktv.update', $ktv->MaTK)
                            : route('admin.ktv.store') }}"
                        novalidate
                    >
                        @csrf

                        @if($isEdit)
                            @method('PATCH')
                        @endif

                        <div class="mb-3">
                            <label for="technician-name" class="form-label">
                                Họ tên
                            </label>

                            <input
                                type="text"
                                id="technician-name"
                                name="HoTen"
                                value="{{ old('HoTen', $ktv->HoTen) }}"
                                class="form-control @error('HoTen') is-invalid @enderror"
                                maxlength="100"
                                autocomplete="name"
                                required
                            >

                            @error('HoTen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="technician-email" class="form-label">
                                Email đăng nhập
                            </label>

                            <input
                                type="email"
                                id="technician-email"
                                name="Email"
                                value="{{ old('Email', $ktv->Email) }}"
                                class="form-control @error('Email') is-invalid @enderror"
                                maxlength="100"
                                autocomplete="email"
                                required
                            >

                            @error('Email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="technician-phone" class="form-label">
                                Số điện thoại
                            </label>

                            <input
                                type="tel"
                                id="technician-phone"
                                name="SoDienThoai"
                                value="{{ old('SoDienThoai', $ktv->SoDienThoai) }}"
                                class="form-control @error('SoDienThoai') is-invalid @enderror"
                                maxlength="10"
                                pattern="0[0-9]{9}"
                                inputmode="numeric"
                                autocomplete="tel"
                                data-pattern-message="Số điện thoại phải có 10 chữ số, bắt đầu bằng số 0."
                                required
                            >

                            @error('SoDienThoai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(!$isEdit)
                            <p class="small text-secondary mb-4">
                                Tài khoản được tạo với vai trò kỹ thuật viên.
                                Mật khẩu mặc định:
                                <strong>Ktv@123</strong>.
                                KTV có thể đổi mật khẩu sau khi đăng nhập.
                            </p>
                        @else
                            <p class="small text-secondary mb-4">
                                Cập nhật thông tin không thay đổi mật khẩu
                                hoặc trạng thái tài khoản.
                            </p>
                        @endif

                        <div class="d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ $isEdit ? 'Lưu thay đổi' : 'Tạo tài khoản KTV' }}
                            </button>

                            <a
                                href="{{ route('admin.ktv.index') }}"
                                class="btn btn-light"
                            >
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
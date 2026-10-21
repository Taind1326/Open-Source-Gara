@extends('admin.layout')

@section('title', 'Quản lý kỹ thuật viên')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">
                Quản lý kỹ thuật viên
            </h1>

            <p class="text-secondary mb-0">
                Tạo tài khoản, cập nhật thông tin và quản lý quyền đăng nhập.
            </p>
        </div>

        <a href="{{ route('admin.ktv.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-2" aria-hidden="true"></i>
            Thêm kỹ thuật viên
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form
                method="GET"
                action="{{ route('admin.ktv.index') }}"
                novalidate
            >
                <label for="technician-search" class="form-label">
                    Tìm kỹ thuật viên
                </label>

                <div class="d-flex flex-wrap gap-2 align-items-start">
                    <div class="flex-grow-1">
                        <input
                            type="search"
                            id="technician-search"
                            name="tu_khoa"
                            value="{{ old('tu_khoa', $tuKhoa) }}"
                            class="form-control @error('tu_khoa') is-invalid @enderror"
                            placeholder="Họ tên, email hoặc số điện thoại..."
                            maxlength="100"
                        >

                        @error('tu_khoa')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Tìm kiếm
                    </button>

                    <a
                        href="{{ route('admin.ktv.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <p class="text-secondary small mb-3">
                {{ $ktvs->total() }} kỹ thuật viên
            </p>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="text-nowrap">
                                Kỹ thuật viên
                            </th>

                            <th scope="col">
                                Liên hệ
                            </th>

                            <th scope="col" class="text-nowrap">
                                Công việc chưa kết thúc
                            </th>

                            <th scope="col" class="text-nowrap">
                                Trạng thái
                            </th>

                            <th scope="col" class="text-end">
                                Thao tác
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($ktvs as $ktv)
                            @php
                                $hoatDong = $ktv->TrangThai === 'HOAT_DONG';
                                $loiKtv = 'ktv_' . $ktv->MaTK;
                                $statusFormId = 'ktv-status-' . $ktv->MaTK;
                            @endphp

                            <tr>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $ktv->HoTen }}
                                    </div>

                                    <small class="text-secondary">
                                        #{{ $ktv->MaTK }}
                                    </small>
                                </td>

                                <td>
                                    <div>{{ $ktv->Email }}</div>

                                    <small class="text-secondary">
                                        {{ $ktv->SoDienThoai }}
                                    </small>
                                </td>

                                <td class="text-nowrap">
                                    {{ $ktv->SoCongViec }} lịch
                                </td>

                                <td class="text-nowrap">
                                    <span class="badge {{ $hoatDong ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $hoatDong ? 'Hoạt động' : 'Đã khóa' }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    <div class="d-flex flex-nowrap justify-content-end align-items-start gap-2">
                                        <a
                                            href="{{ route('admin.ktv.edit', $ktv->MaTK) }}"
                                            class="btn btn-outline-primary btn-sm text-nowrap"
                                        >
                                            Sửa
                                        </a>

                                        <button
                                            type="submit"
                                            form="{{ $statusFormId }}"
                                            class="btn {{ $hoatDong ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm text-nowrap"
                                        >
                                            {{ $hoatDong ? 'Khóa' : 'Mở khóa' }}
                                        </button>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.ktv.reset-password', $ktv->MaTK) }}"
                                            class="m-0"
                                            novalidate
                                            data-confirm-title="Đặt lại mật khẩu KTV"
                                            data-confirm="Đặt lại mật khẩu của {{ $ktv->HoTen }} thành Ktv@123?"
                                            data-confirm-accept="Đặt lại mật khẩu"
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn btn-outline-secondary btn-sm text-nowrap"
                                            >
                                                Reset mật khẩu
                                            </button>
                                        </form>
                                    </div>

                                    <form
                                        id="{{ $statusFormId }}"
                                        method="POST"
                                        action="{{ route('admin.ktv.status', $ktv->MaTK) }}"
                                        class="m-0 text-start"
                                        novalidate
                                        data-confirm-title="{{ $hoatDong ? 'Khóa tài khoản KTV' : 'Mở khóa tài khoản KTV' }}"
                                        data-confirm="{{ $hoatDong
                                            ? 'Khóa tài khoản của ' . $ktv->HoTen . '? KTV sẽ không thể đăng nhập và nhận phân công mới.'
                                            : 'Mở lại tài khoản của ' . $ktv->HoTen . '?' }}"
                                        data-confirm-accept="{{ $hoatDong ? 'Khóa tài khoản' : 'Mở khóa' }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="TrangThai"
                                            value="{{ $hoatDong ? 'KHOA' : 'HOAT_DONG' }}"
                                        >

                                        @error($loiKtv)
                                            <div
                                                class="garage-field-error text-danger small mt-2"
                                                data-form-error
                                                tabindex="-1"
                                                role="alert"
                                            >
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">
                                    Chưa có kỹ thuật viên phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ktvs->hasPages())
                <div class="mt-4">
                    {{ $ktvs->links('shared.pagination') }}
                </div>
            @endif
        </div>
    </div>

    <p class="small text-secondary mt-3 mb-0">
        Không khóa tài khoản khi còn lịch chưa kết thúc,
        kể cả lịch đang chờ thanh toán.
        Tài khoản được giữ lại để bảo toàn lịch sử phân công.
    </p>
@endsection
@extends('public.layout')

@section('title', 'Công việc của tôi')

@section('content')
    <section class="hero">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">
                Công việc của tôi
            </h1>

            <p class="hero-description mb-0">
                Theo dõi các xe được phân công kiểm tra và sửa chữa.
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="feature-card h-auto mb-4">
                <form
                    method="GET"
                    action="{{ route('ktv.lichhen.index') }}"
                    novalidate
                >
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4">
                            <label for="jobKeyword" class="form-label fw-semibold">
                                Tìm kiếm
                            </label>

                            <input
                                type="search"
                                id="jobKeyword"
                                name="tu_khoa"
                                value="{{ $tuKhoa }}"
                                maxlength="100"
                                placeholder="Biển số, tên khách, số điện thoại..."
                                class="form-control @error('tu_khoa') is-invalid @enderror"
                            >

                            @error('tu_khoa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-3">
                            <label for="jobDate" class="form-label fw-semibold">
                                Ngày hẹn
                            </label>

                            <input
                                type="date"
                                id="jobDate"
                                name="ngay_hen"
                                value="{{ $ngayHen }}"
                                class="form-control @error('ngay_hen') is-invalid @enderror"
                            >

                            @error('ngay_hen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-3">
                            <label for="jobStatus" class="form-label fw-semibold">
                                Trạng thái
                            </label>

                            <select
                                id="jobStatus"
                                name="trang_thai"
                                class="form-select @error('trang_thai') is-invalid @enderror"
                            >
                                <option value="">Tất cả trạng thái</option>

                                @foreach(\App\Models\LichHen::NHAN_TRANG_THAI as $value => $label)
                                    <option
                                        value="{{ $value }}"
                                        @selected($trangThai === $value)
                                    >
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            @error('trang_thai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-lg-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    Lọc
                                </button>

                                <a
                                    href="{{ route('ktv.lichhen.index') }}"
                                    class="btn btn-light"
                                >
                                    Đặt lại
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="feature-card h-auto">
                <h2 class="h4 fw-bold mb-4">
                    Lịch được phân công
                    <span class="text-muted fw-normal">
                        ({{ $lichHens->total() }})
                    </span>
                </h2>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mã lịch</th>
                                <th>Xe</th>
                                <th>Khách hàng</th>
                                <th>Lịch tiếp nhận</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($lichHens as $lichHen)
                                <tr>
                                    <td class="fw-semibold">
                                        #{{ $lichHen->MaLH }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold text-nowrap">
                                            {{ $lichHen->xe->BienSo }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $lichHen->xe->HangXe }}
                                            {{ $lichHen->xe->DongXe }}
                                        </small>
                                    </td>

                                    <td>
                                        <div>{{ $lichHen->taiKhoan->HoTen }}</div>

                                        <small class="text-muted">
                                            {{ $lichHen->taiKhoan->SoDienThoai }}
                                        </small>
                                    </td>

                                    <td class="text-nowrap">
                                        <div>
                                            {{ $lichHen->NgayHen->format('d/m/Y') }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $lichHen->KhungGioHen }}
                                        </small>
                                    </td>

                                    <td class="text-nowrap">
                                        @include('admin.lichhen._status')
                                    </td>

                                    <td class="text-end">
                                        <a
                                            href="{{ route('ktv.lichhen.show', $lichHen->MaLH) }}"
                                            class="btn btn-outline-primary btn-sm text-nowrap"
                                        >
                                            Xem công việc
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        Chưa có công việc phù hợp.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($lichHens->hasPages())
                    <div class="mt-4">
                        {{ $lichHens->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
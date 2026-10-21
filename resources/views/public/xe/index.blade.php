@extends('public.layout')

@section('title', 'Xe của tôi')

@section('content')
    <section class="hero">
        <div class="container">
            <nav aria-label="Đường dẫn">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}" class="text-white">
                            Trang chủ
                        </a>
                    </li>

                    <li
                        class="breadcrumb-item active text-white"
                        aria-current="page"
                    >
                        Xe của tôi
                    </li>
                </ol>
            </nav>

            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h1 class="display-5 fw-bold mb-3">
                        Xe của tôi
                    </h1>

                    <p class="hero-description mb-0">
                        Quản lý thông tin các xe của bạn để sử dụng
                        khi đặt lịch và theo dõi sửa chữa.
                    </p>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <a
                        href="{{ route('public.xe.create') }}"
                        class="btn btn-primary btn-lg"
                    >
                        <i class="bi bi-plus-circle me-2" aria-hidden="true"></i>
                        Thêm xe
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="feature-card h-auto mb-4">
                <form
                    method="GET"
                    action="{{ route('public.xe.index') }}"
                    novalidate
                >
                    <div class="row align-items-start g-3">
                        <div class="col-lg-9">
                            <label
                                for="vehicleKeyword"
                                class="form-label fw-semibold"
                            >
                                Tìm xe
                            </label>

                            <input
                                type="search"
                                id="vehicleKeyword"
                                name="tu_khoa"
                                class="form-control @error('tu_khoa') is-invalid @enderror"
                                value="{{ old('tu_khoa', $tuKhoa) }}"
                                maxlength="100"
                                placeholder="Biển số, hãng hoặc dòng xe..."
                            >

                            @error('tu_khoa')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-lg-3">
                            <div class="d-flex gap-2 mt-lg-4 pt-lg-2">
                                <button
                                    type="submit"
                                    class="btn btn-primary flex-grow-1"
                                >
                                    <i class="bi bi-search me-1" aria-hidden="true"></i>
                                    Tìm kiếm
                                </button>

                                <a
                                    href="{{ route('public.xe.index') }}"
                                    class="btn btn-light"
                                    title="Xóa bộ lọc"
                                    aria-label="Xóa bộ lọc"
                                >
                                    <i
                                        class="bi bi-arrow-counterclockwise"
                                        aria-hidden="true"
                                    ></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <div>
                    <h2 class="h4 fw-bold mb-1">
                        Danh sách xe
                    </h2>

                    <p class="text-muted small mb-0">
                        {{ $xes->total() }} xe
                    </p>
                </div>

                <a
                    href="{{ route('public.taikhoan.edit') }}"
                    class="fw-semibold"
                >
                    <i class="bi bi-person me-1" aria-hidden="true"></i>
                    Tài khoản của tôi
                </a>
            </div>

            <div class="row g-4">
                @forelse($xes as $xe)
                    <div class="col-md-6 col-lg-4">
                        <article class="feature-card d-flex flex-column h-100">
                            <div class="mb-3">
                                <span class="feature-icon mb-0">
                                    <i class="bi bi-car-front" aria-hidden="true"></i>
                                </span>
                            </div>

                            <h3 class="h4 fw-bold text-break mb-2">
                                {{ $xe->BienSo }}
                            </h3>

                            <p class="text-muted text-break mb-4">
                                {{ $xe->HangXe }} · {{ $xe->DongXe }}
                            </p>

                            <dl class="row small mb-4">
                                <dt class="col-5 text-muted fw-normal">
                                    Hãng xe
                                </dt>

                                <dd class="col-7 text-break fw-semibold">
                                    {{ $xe->HangXe }}
                                </dd>

                                <dt class="col-5 text-muted fw-normal">
                                    Dòng xe
                                </dt>

                                <dd class="col-7 text-break fw-semibold">
                                    {{ $xe->DongXe }}
                                </dd>

                                <dt class="col-5 text-muted fw-normal">
                                    Năm sản xuất
                                </dt>

                                <dd class="col-7 fw-semibold">
                                    {{ $xe->NamSX ?? 'Chưa cập nhật' }}
                                </dd>

                                <dt class="col-5 text-muted fw-normal">
                                    Màu sắc
                                </dt>

                                <dd class="col-7 text-break fw-semibold">
                                    {{ $xe->MauSac ?: 'Chưa cập nhật' }}
                                </dd>
                            </dl>

                            <div class="d-flex flex-wrap gap-2 border-top pt-3 mt-auto">
                                <a
                                    href="{{ route('public.xe.edit', $xe->MaXe) }}"
                                    class="btn btn-outline-primary flex-grow-1"
                                >
                                    <i
                                        class="bi bi-pencil-square me-1"
                                        aria-hidden="true"
                                    ></i>
                                    Sửa thông tin
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('public.xe.destroy', $xe->MaXe) }}"
                                    novalidate
                                    data-confirm-title="Xóa xe"
                                    data-confirm="Bạn muốn xóa xe {{ $xe->BienSo }} khỏi tài khoản? Chỉ xe chưa có lịch hẹn mới có thể xóa."
                                    data-confirm-accept="Xóa xe"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-outline-danger"
                                        aria-label="Xóa xe {{ $xe->BienSo }}"
                                    >
                                        <i
                                            class="bi bi-trash me-1"
                                            aria-hidden="true"
                                        ></i>
                                        Xóa xe
                                    </button>
                                </form>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="feature-card h-auto text-center py-5">
                            <span class="feature-icon">
                                <i class="bi bi-car-front" aria-hidden="true"></i>
                            </span>

                            @if($tuKhoa !== '')
                                <h3 class="h5 fw-bold">
                                    Không tìm thấy xe phù hợp
                                </h3>

                                <p class="text-muted">
                                    Thử tìm bằng biển số, hãng hoặc dòng xe khác.
                                </p>

                                <a
                                    href="{{ route('public.xe.index') }}"
                                    class="btn btn-primary"
                                >
                                    Xem tất cả xe
                                </a>
                            @else
                                <h3 class="h5 fw-bold">
                                    Bạn chưa thêm xe
                                </h3>

                                <p class="text-muted">
                                    Thêm thông tin chiếc xe đầu tiên
                                    để sử dụng khi đặt lịch sửa chữa.
                                </p>

                                <a
                                    href="{{ route('public.xe.create') }}"
                                    class="btn btn-primary"
                                >
                                    <i
                                        class="bi bi-plus-circle me-2"
                                        aria-hidden="true"
                                    ></i>
                                    Thêm xe đầu tiên
                                </a>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            @if($xes->hasPages())
                <div class="mt-5">
                    {{ $xes->links('shared.pagination') }}
                </div>
            @endif

            <div class="alert alert-info d-flex gap-3 mt-4 mb-0">
                <i class="bi bi-info-circle" aria-hidden="true"></i>

                <div>
                    Xe đang có lịch hẹn chưa kết thúc cần hoàn tất
                    hoặc hủy lịch trước khi sửa thông tin.
                    Chỉ có thể xóa xe chưa từng có lịch hẹn.
                </div>
            </div>
        </div>
    </section>
@endsection
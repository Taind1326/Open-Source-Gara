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

                    <li class="breadcrumb-item active text-white"
                        aria-current="page">
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
                    <a href="{{ route('public.xe.create') }}"
                       class="btn btn-primary btn-lg">
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
                <form novalidate method="GET" action="{{ route('public.xe.index') }}">
                    <div class="row align-items-end g-3">
                        <div class="col-lg-5">
                            <label for="vehicleKeyword"
                                   class="form-label fw-semibold">
                                Tìm xe
                            </label>

                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search" aria-hidden="true"></i>
                                </span>

                                <input type="search"
                                       id="vehicleKeyword"
                                       name="tu_khoa"
                                       class="form-control"
                                       value="{{ $tuKhoa }}"
                                       maxlength="100"
                                       placeholder="Biển số, hãng hoặc dòng xe…">
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <label for="vehicleStatus"
                                   class="form-label fw-semibold">
                                Trạng thái
                            </label>

                            <select id="vehicleStatus"
                                    name="trang_thai"
                                    class="form-select">
                                <option value="">Tất cả trạng thái</option>

                                <option value="HOAT_DONG"
                                        @selected($trangThai === 'HOAT_DONG')>
                                    Đang sử dụng
                                </option>

                                <option value="NGUNG_SU_DUNG"
                                        @selected($trangThai === 'NGUNG_SU_DUNG')>
                                    Ngừng sử dụng
                                </option>
                            </select>
                        </div>

                        <div class="col-lg-3">
                            <div class="d-flex gap-2">
                                <button type="submit"
                                        class="btn btn-primary flex-grow-1">
                                    Tìm kiếm
                                </button>

                                <a href="{{ route('public.xe.index') }}"
                                   class="btn btn-light"
                                   title="Xóa bộ lọc"
                                   aria-label="Xóa bộ lọc">
                                    <i class="bi bi-arrow-counterclockwise"
                                       aria-hidden="true"></i>
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
                        @if($xes->total() > 0)
                            Hiển thị {{ $xes->firstItem() }}–{{ $xes->lastItem() }}
                            trong {{ $xes->total() }} xe
                        @else
                            Chưa có xe phù hợp
                        @endif
                    </p>
                </div>

                <a href="{{ route('public.taikhoan.edit') }}"
                   class="fw-semibold">
                    <i class="bi bi-person me-1" aria-hidden="true"></i>
                    Tài khoản của tôi
                </a>
            </div>

            <div class="row g-4">
                @forelse($xes as $xe)
                    <div class="col-md-6 col-lg-4">
                        <article class="feature-card d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                <span class="feature-icon mb-0">
                                    <i class="bi bi-car-front" aria-hidden="true"></i>
                                </span>

                                @if($xe->TrangThai === 'HOAT_DONG')
                                    <span class="badge rounded-pill text-success bg-success-subtle px-3 py-2">
                                        Đang sử dụng
                                    </span>
                                @else
                                    <span class="badge rounded-pill text-secondary bg-secondary-subtle px-3 py-2">
                                        Ngừng sử dụng
                                    </span>
                                @endif
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
                                    {{ $xe->NamSanXuat }}
                                </dd>
                            </dl>

                            <div class="border-top pt-3 mt-auto">
                                <a href="{{ route('public.xe.edit', $xe->MaXe) }}"
                                   class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-pencil-square me-1"
                                       aria-hidden="true"></i>
                                    Sửa thông tin
                                </a>

                                @if($xe->TrangThai === 'HOAT_DONG')
                                    <button type="button"
                                            class="btn btn-light w-100 text-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#stopVehicle{{ $xe->MaXe }}">
                                        Ngừng sử dụng
                                    </button>
                                @else
                                    <form novalidate method="POST"
                                          action="{{ route('public.xe.status', $xe->MaXe) }}">
                                        @csrf
                                        @method('PATCH')

                                        <input type="hidden"
                                               name="TrangThai"
                                               value="HOAT_DONG">

                                        <button type="submit"
                                                class="btn btn-light w-100 text-primary">
                                            <i class="bi bi-arrow-repeat me-1"
                                               aria-hidden="true"></i>
                                            Sử dụng lại
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="feature-card h-auto text-center py-5">
                            <span class="feature-icon">
                                <i class="bi bi-car-front" aria-hidden="true"></i>
                            </span>

                            @if($tuKhoa !== '' || $trangThai)
                                <h3 class="h5 fw-bold">
                                    Không tìm thấy xe phù hợp
                                </h3>

                                <p class="text-muted">
                                    Thử từ khóa khác hoặc bỏ bộ lọc trạng thái.
                                </p>

                                <a href="{{ route('public.xe.index') }}"
                                   class="btn btn-primary">
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

                                <a href="{{ route('public.xe.create') }}"
                                   class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-2"
                                       aria-hidden="true"></i>
                                    Thêm xe đầu tiên
                                </a>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            @if($xes->hasPages())
                <div class="mt-5">
                    {{ $xes->links('pagination::bootstrap-5') }}
                </div>
            @endif

            <div class="alert alert-info d-flex gap-3 mt-4 mb-0">
                <i class="bi bi-info-circle" aria-hidden="true"></i>

                <div>
                    Ngừng sử dụng xe không xóa lịch sử sửa chữa.
                    Xe có yêu cầu chưa kết thúc cần hoàn tất hoặc hủy
                    yêu cầu trước khi sửa thông tin hay ngừng sử dụng.
                </div>
            </div>
        </div>
    </section>

    @foreach($xes as $xe)
        @if($xe->TrangThai === 'HOAT_DONG')
            <div class="modal fade"
                 id="stopVehicle{{ $xe->MaXe }}"
                 tabindex="-1"
                 aria-labelledby="stopVehicleTitle{{ $xe->MaXe }}"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title fs-5 fw-bold"
                                id="stopVehicleTitle{{ $xe->MaXe }}">
                                Ngừng sử dụng xe
                            </h2>

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal"
                                    aria-label="Đóng"></button>
                        </div>

                        <div class="modal-body">
                            <p>
                                Bạn muốn ngừng sử dụng xe
                                <strong>{{ $xe->BienSo }}</strong>?
                            </p>

                            <p class="text-muted mb-0">
                                Lịch sử sửa chữa vẫn được giữ lại.
                                Bạn có thể kích hoạt lại xe khi cần.
                            </p>
                        </div>

                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-light"
                                    data-bs-dismiss="modal">
                                Hủy
                            </button>

                            <form novalidate method="POST"
                                  action="{{ route('public.xe.status', $xe->MaXe) }}">
                                @csrf
                                @method('PATCH')

                                <input type="hidden"
                                       name="TrangThai"
                                       value="NGUNG_SU_DUNG">

                                <button type="submit" class="btn btn-primary">
                                    Xác nhận ngừng sử dụng
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
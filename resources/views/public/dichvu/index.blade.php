@extends('public.layout')

@section('title', 'Dịch vụ')

@section('description', 'Khám phá các dịch vụ chăm sóc, bảo dưỡng và sửa chữa ô tô tại AutoCare Garage.')

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
                        Dịch vụ
                    </li>
                </ol>
            </nav>

            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="hero-label mb-3">
                        <i class="bi bi-tools" aria-hidden="true"></i>
                        Dịch vụ tại gara
                    </div>

                    <h1 class="display-5 fw-bold mb-3">
                        Chăm sóc xe theo nhu cầu
                    </h1>

                    <p class="hero-description fs-5 mb-0">
                        Tìm hiểu từng dịch vụ và giá niêm yết.
                        Các hạng mục sửa chữa được kiểm tra, báo giá
                        và thống nhất trước khi thực hiện.
                    </p>
                </div>

                <div class="col-lg-4 text-lg-end">
                    <a href="{{ url('/lien-he') }}"
                       class="btn btn-outline-light">
                        Liên hệ tư vấn
                        <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="feature-card mb-4">
                <form method="GET" action="{{ route('public.dichvu.index') }}">
                    <div class="row align-items-end g-3">
                        <div class="col-lg-5">
                            <label for="serviceKeyword"
                                   class="form-label fw-semibold">
                                Tìm dịch vụ
                            </label>

                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search" aria-hidden="true"></i>
                                </span>

                                <input type="search"
                                       id="serviceKeyword"
                                       name="tu_khoa"
                                       class="form-control"
                                       value="{{ $tuKhoa }}"
                                       maxlength="100"
                                       placeholder="Nhập tên dịch vụ…">
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <label for="serviceCategory"
                                   class="form-label fw-semibold">
                                Loại dịch vụ
                            </label>

                            <select id="serviceCategory"
                                    name="ma_loai"
                                    class="form-select">
                                <option value="">Tất cả loại dịch vụ</option>

                                @foreach($loaiDichVus as $loai)
                                    <option value="{{ $loai->MaLoaiDV }}"
                                            @selected((string) $maLoai === (string) $loai->MaLoaiDV)>
                                        {{ $loai->TenLoaiDV }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3">
                            <div class="d-flex gap-2">
                                <button type="submit"
                                        class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-funnel me-1" aria-hidden="true"></i>
                                    Tìm kiếm
                                </button>

                                <a href="{{ route('public.dichvu.index') }}"
                                   class="btn btn-light"
                                   aria-label="Xóa bộ lọc"
                                   title="Xóa bộ lọc">
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
                    <h2 class="h4 fw-bold mb-1">Danh sách dịch vụ</h2>

                    <p class="text-muted small mb-0">
                        @if($dichVus->total() > 0)
                            Hiển thị {{ $dichVus->firstItem() }}–{{ $dichVus->lastItem() }}
                            trong {{ $dichVus->total() }} dịch vụ
                        @else
                            Không có dịch vụ phù hợp
                        @endif
                    </p>
                </div>

                @if($tuKhoa !== '')
                    <span class="badge rounded-pill text-bg-light border px-3 py-2">
                        Từ khóa: {{ $tuKhoa }}
                    </span>
                @endif
            </div>

            <div class="row g-4">
                @forelse($dichVus as $dichVu)
                    <div class="col-md-6 col-lg-4">
                        <article class="service-card d-flex flex-column">
                            <a href="{{ route('public.dichvu.show', $dichVu->MaDV) }}"
                               aria-label="Xem dịch vụ {{ $dichVu->TenDV }}">
                                @if($dichVu->HinhAnh)
                                    <img src="{{ asset('storage/' . $dichVu->HinhAnh) }}"
                                         alt="{{ $dichVu->TenDV }}"
                                         loading="lazy">
                                @else
                                    <div class="ratio ratio-16x9 bg-light">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="bi bi-tools display-3 text-primary"
                                               aria-hidden="true"></i>
                                        </div>
                                    </div>
                                @endif
                            </a>

                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <div class="mb-3">
                                    <span class="badge rounded-pill text-primary bg-primary-subtle px-3 py-2">
                                        {{ $dichVu->loaiDichVu->TenLoaiDV }}
                                    </span>
                                </div>

                                <h3 class="h5 fw-bold mb-3">
                                    <a href="{{ route('public.dichvu.show', $dichVu->MaDV) }}"
                                       class="text-dark">
                                        {{ $dichVu->TenDV }}
                                    </a>
                                </h3>

                                <p class="text-muted mb-4">
                                    {{ \Illuminate\Support\Str::limit(
                                        strip_tags($dichVu->MoTa ?: 'Xem thông tin chi tiết và trao đổi với gara về nhu cầu của bạn.'),
                                        120
                                    ) }}
                                </p>

                                <div class="mt-auto">
                                    <div class="border-top pt-3 mb-3">
                                        <span class="text-muted small">
                                            Giá niêm yết
                                        </span>

                                        <div class="fs-5 fw-bold text-primary">
                                            {{ number_format($dichVu->Gia, 0, ',', '.') }} ₫
                                        </div>
                                    </div>

                                    <a href="{{ route('public.dichvu.show', $dichVu->MaDV) }}"
                                       class="btn btn-outline-primary w-100">
                                        Xem chi tiết
                                        <i class="bi bi-arrow-right ms-2"
                                           aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="feature-card text-center py-5">
                            <span class="feature-icon">
                                <i class="bi bi-search" aria-hidden="true"></i>
                            </span>

                            <h3 class="h5 fw-bold">
                                Chưa tìm thấy dịch vụ phù hợp
                            </h3>

                            <p class="text-muted">
                                Thử từ khóa khác hoặc bỏ bộ lọc loại dịch vụ.
                            </p>

                            <a href="{{ route('public.dichvu.index') }}"
                               class="btn btn-primary">
                                Xem tất cả dịch vụ
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($dichVus->hasPages())
                <div class="mt-5">
                    {{ $dichVus->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>

    <section class="pb-5">
        <div class="container">
            <div class="cta-panel">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2 class="h3 fw-bold mb-3">
                            Chưa biết chiếc xe cần dịch vụ nào?
                        </h2>

                        <p class="cta-description mb-0">
                            Liên hệ gara và mô tả tình trạng xe
                            để trao đổi về bước kiểm tra phù hợp.
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ url('/lien-he') }}"
                           class="btn btn-light btn-lg">
                            Liên hệ gara
                            <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
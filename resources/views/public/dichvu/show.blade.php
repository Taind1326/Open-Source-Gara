@extends('public.layout')

@section('title', $dichVu->TenDV)

@section('description', \Illuminate\Support\Str::limit(
    strip_tags($dichVu->MoTa ?: 'Tìm hiểu dịch vụ ' . $dichVu->TenDV . ' tại AutoCare Garage.'),
    160
))

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

                    <li class="breadcrumb-item">
                        <a href="{{ route('public.dichvu.index') }}"
                           class="text-white">
                            Dịch vụ
                        </a>
                    </li>

                    <li class="breadcrumb-item active text-white"
                        aria-current="page">
                        Chi tiết dịch vụ
                    </li>
                </ol>
            </nav>

            <div class="hero-label mb-3">
                <i class="bi bi-tools" aria-hidden="true"></i>
                {{ $dichVu->loaiDichVu->TenLoaiDV }}
            </div>

            <h1 class="display-5 fw-bold mb-0">
                {{ $dichVu->TenDV }}
            </h1>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <a href="{{ route('public.dichvu.index') }}"
               class="d-inline-flex align-items-center gap-2 fw-semibold mb-4">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Quay lại danh sách dịch vụ
            </a>

            <div class="card border rounded-4 overflow-hidden mb-5">
                <div class="row g-0">
                    <div class="col-lg-6 bg-light">
                        <div class="ratio ratio-4x3">
                            @if($dichVu->HinhAnh)
                                <img src="{{ asset('storage/' . $dichVu->HinhAnh) }}"
                                     alt="{{ $dichVu->TenDV }}"
                                     class="w-100 h-100 object-fit-cover">
                            @else
                                <div class="d-flex flex-column align-items-center justify-content-center p-4 text-center">
                                    <span class="feature-icon">
                                        <i class="bi bi-tools" aria-hidden="true"></i>
                                    </span>

                                    <span class="text-muted">
                                        {{ $dichVu->loaiDichVu->TenLoaiDV }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="p-4 p-lg-5">
                            <span class="badge rounded-pill text-primary bg-primary-subtle px-3 py-2 mb-3">
                                {{ $dichVu->loaiDichVu->TenLoaiDV }}
                            </span>

                            <h2 class="h3 fw-bold mb-3">
                                {{ $dichVu->TenDV }}
                            </h2>

                            <p class="text-muted small mb-1">
                                Giá niêm yết
                            </p>

                            <div class="fs-2 fw-bold text-primary mb-4">
                                {{ number_format($dichVu->Gia, 0, ',', '.') }} ₫
                            </div>

                            <div class="alert alert-primary d-flex gap-3 mb-4">
                                <i class="bi bi-info-circle fs-5"
                                   aria-hidden="true"></i>

                                <div>
                                    Gara kiểm tra tình trạng xe và lập báo giá
                                    các hạng mục cần thực hiện. Việc sửa chữa
                                    bắt đầu sau khi khách hàng đồng ý.
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ url('/lien-he') }}"
                                   class="btn btn-primary">
                                    Liên hệ tư vấn
                                    <i class="bi bi-arrow-right ms-2"
                                       aria-hidden="true"></i>
                                </a>

                                <a href="{{ route('public.dichvu.index', [
                                    'ma_loai' => $dichVu->MaLoaiDV,
                                ]) }}"
                                   class="btn btn-outline-primary">
                                    Dịch vụ cùng nhóm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <article class="feature-card">
                        <p class="section-eyebrow mb-2">
                            Thông tin dịch vụ
                        </p>

                        <h2 class="h4 fw-bold mb-4">
                            Giới thiệu dịch vụ
                        </h2>

                        @foreach(
                            preg_split('/\r\n|\r|\n/', $dichVu->MoTa ?: 'Liên hệ AutoCare Garage để trao đổi chi tiết về dịch vụ và tình trạng xe của bạn.')
                            as $paragraph
                        )
                            @if(trim($paragraph) !== '')
                                <p class="text-muted text-break">
                                    {{ $paragraph }}
                                </p>
                            @endif
                        @endforeach
                    </article>
                </div>

                <div class="col-lg-4">
                    <aside class="feature-card">
                        <span class="feature-icon">
                            <i class="bi bi-clipboard-check"
                               aria-hidden="true"></i>
                        </span>

                        <h2 class="h5 fw-bold mb-3">
                            Trước khi thực hiện
                        </h2>

                        <ul class="list-unstyled text-muted mb-4">
                            <li class="d-flex gap-2 mb-3">
                                <i class="bi bi-check-circle text-primary"
                                   aria-hidden="true"></i>
                                <span>Ghi nhận nhu cầu và kiểm tra xe.</span>
                            </li>

                            <li class="d-flex gap-2 mb-3">
                                <i class="bi bi-check-circle text-primary"
                                   aria-hidden="true"></i>
                                <span>Xem hạng mục và chi phí trên báo giá.</span>
                            </li>

                            <li class="d-flex gap-2">
                                <i class="bi bi-check-circle text-primary"
                                   aria-hidden="true"></i>
                                <span>Xác nhận báo giá trước khi sửa chữa.</span>
                            </li>
                        </ul>

                        <a href="{{ url('/lien-he') }}"
                           class="btn btn-outline-primary w-100">
                            Trao đổi với gara
                        </a>
                    </aside>
                </div>
            </div>
        </div>
    </section>

    @if($dichVuLienQuan->isNotEmpty())
        <section class="section-space bg-white">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
                    <div>
                        <p class="section-eyebrow mb-2">
                            Khám phá thêm
                        </p>

                        <h2 class="section-title mb-0">
                            Dịch vụ cùng nhóm
                        </h2>
                    </div>

                    <a href="{{ route('public.dichvu.index', [
                        'ma_loai' => $dichVu->MaLoaiDV,
                    ]) }}"
                       class="btn btn-outline-primary">
                        Xem tất cả
                        <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="row g-4">
                    @foreach($dichVuLienQuan as $item)
                        <div class="col-md-4">
                            <article class="service-card d-flex flex-column">
                                <a href="{{ route('public.dichvu.show', $item->MaDV) }}"
                                   aria-label="Xem dịch vụ {{ $item->TenDV }}">
                                    @if($item->HinhAnh)
                                        <img src="{{ asset('storage/' . $item->HinhAnh) }}"
                                             alt="{{ $item->TenDV }}"
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
                                    <h3 class="h5 fw-bold mb-3">
                                        <a href="{{ route('public.dichvu.show', $item->MaDV) }}"
                                           class="text-dark">
                                            {{ $item->TenDV }}
                                        </a>
                                    </h3>

                                    <p class="text-muted">
                                        {{ \Illuminate\Support\Str::limit(
                                            strip_tags($item->MoTa ?: 'Xem chi tiết dịch vụ và trao đổi nhu cầu với gara.'),
                                            100
                                        ) }}
                                    </p>

                                    <div class="mt-auto">
                                        <p class="fs-5 fw-bold text-primary mb-3">
                                            {{ number_format($item->Gia, 0, ',', '.') }} ₫
                                        </p>

                                        <a href="{{ route('public.dichvu.show', $item->MaDV) }}"
                                           class="btn btn-outline-primary w-100">
                                            Xem chi tiết
                                            <i class="bi bi-arrow-right ms-2"
                                               aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
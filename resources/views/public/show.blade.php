@extends('public.layout')

@section('title', $dichVu->TenDV . ' — Garage')

@section('content')
    <a
        href="{{ route('public.dichvu.index') }}"
        class="btn btn-outline-secondary mb-4"
    >
        Quay lại danh sách
    </a>

    <section class="card border-0 shadow-sm overflow-hidden mb-4">
        <div class="row g-0">
            <div class="col-md-5">
                @if ($dichVu->HinhAnh)
                    <img
                        src="{{ asset('storage/' . $dichVu->HinhAnh) }}"
                        alt="{{ $dichVu->TenDV }}"
                        class="w-100 h-100"
                        style="object-fit: cover; min-height: 300px;"
                    >
                @else
                    <div class="service-placeholder h-100" style="min-height: 300px;">
                        {{ $dichVu->loaiDichVu->TenLoaiDV }}
                    </div>
                @endif
            </div>

            <div class="col-md-7">
                <div class="p-4 p-lg-5">
                    <span class="badge text-bg-primary mb-3">
                        {{ $dichVu->loaiDichVu->TenLoaiDV }}
                    </span>

                    <h1 class="h2 fw-bold">
                        {{ $dichVu->TenDV }}
                    </h1>

                    <div class="text-muted mt-3">
                        Giá tham khảo
                    </div>

                    <div class="service-price mb-3">
                        {{ number_format($dichVu->Gia, 0, ',', '.') }}đ
                    </div>

                    <p class="service-description">
                        {{ $dichVu->MoTa ?: 'Liên hệ Garage để được tư vấn chi tiết về dịch vụ.' }}
                    </p>

                    <div class="alert alert-info mb-0">
                        Garage kiểm tra xe thực tế và gửi báo giá.
                        Việc sửa chữa bắt đầu sau khi khách đồng ý.
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($dichVuLienQuan->isNotEmpty())
        <h4 class="fw-bold mb-3">
            Dịch vụ cùng nhóm
        </h4>

        <div class="row g-3">
            @foreach ($dichVuLienQuan as $item)
                <div class="col-md-4">
                    <article class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <h5>{{ $item->TenDV }}</h5>

                            <div class="service-price mb-3">
                                {{ number_format($item->Gia, 0, ',', '.') }}đ
                            </div>

                            <a
                                href="{{ route(
                                    'public.dichvu.show',
                                    $item->MaDV
                                ) }}"
                                class="btn btn-outline-primary mt-auto"
                            >
                                Xem chi tiết
                            </a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    @endif
@endsection
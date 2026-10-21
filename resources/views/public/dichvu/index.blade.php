@extends('public.layout')
@section('title', 'Dịch vụ')
@section('description', 'Khám phá dịch vụ sửa chữa, bảo dưỡng và chăm sóc ô tô tại AutoCare Garage.')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/services-orange.css') }}">
@endpush
@section('content')
<div class="services-page">
    <section class="services-hero">
        @if(file_exists(public_path('images/services/banner.jpg')))
            <img class="services-hero-image" src="{{ asset('images/services/banner.jpg') }}" alt="" fetchpriority="high">
        @endif
        <div class="container services-hero-content" data-reveal>
            <nav aria-label="Đường dẫn"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('public.home') }}">Trang chủ</a></li><li class="breadcrumb-item active" aria-current="page">Dịch vụ</li></ol></nav>
            <h1>Chọn dịch vụ phù hợp<br>cho chiếc xe</h1>
            <p>Khám phá dịch vụ sửa chữa, bảo dưỡng và chăm sóc ô tô.</p>
        </div>
    </section>
    <section class="services-catalog">
        <div class="container">
            <form method="GET" action="{{ route('public.dichvu.index') }}" class="services-search" role="search">
                @if($maLoai)<input type="hidden" name="ma_loai" value="{{ $maLoai }}">@endif
                <input type="hidden" name="sap_xep" value="{{ $sapXep }}">
                <div class="services-search-field"><i class="bi bi-search" aria-hidden="true"></i><label class="visually-hidden" for="service-keyword">Tìm tên dịch vụ</label><input class="form-control" id="service-keyword" type="search" name="tu_khoa" value="{{ $tuKhoa }}" maxlength="100" placeholder="Tìm tên dịch vụ bạn cần..."></div>
                <button class="btn btn-primary" type="submit">Tìm kiếm</button>
            </form>
            <nav class="services-filters" aria-label="Lọc nhóm dịch vụ">
                <a class="services-filter {{ !$maLoai ? 'is-active' : '' }}" href="{{ route('public.dichvu.index', array_filter(['tu_khoa' => $tuKhoa, 'sap_xep' => $sapXep])) }}" @if(!$maLoai) aria-current="true" @endif>Tất cả</a>
                @foreach($loaiDichVus as $loai)
                    <a class="services-filter {{ (string) $maLoai === (string) $loai->MaLoaiDV ? 'is-active' : '' }}" href="{{ route('public.dichvu.index', ['tu_khoa' => $tuKhoa, 'ma_loai' => $loai->MaLoaiDV, 'sap_xep' => $sapXep]) }}" @if((string) $maLoai === (string) $loai->MaLoaiDV) aria-current="true" @endif>{{ $loai->TenLoaiDV }}</a>
                @endforeach
            </nav>
            <div class="services-toolbar">
                <strong>{{ $dichVus->total() }} dịch vụ</strong>
                <form method="GET" action="{{ route('public.dichvu.index') }}" class="services-sort">
                    <input type="hidden" name="tu_khoa" value="{{ $tuKhoa }}">
                    @if($maLoai)<input type="hidden" name="ma_loai" value="{{ $maLoai }}">@endif
                    <label for="service-sort">Sắp xếp:</label>
                    <select class="form-select" id="service-sort" name="sap_xep" onchange="this.form.requestSubmit()">
                        @foreach(['mac_dinh' => 'Mặc định', 'gia_tang' => 'Giá tăng dần', 'gia_giam' => 'Giá giảm dần', 'ten' => 'Tên A–Z'] as $value => $label)<option value="{{ $value }}" @selected($sapXep === $value)>{{ $label }}</option>@endforeach
                    </select>
                    <noscript><button class="btn btn-outline-primary" type="submit">Áp dụng</button></noscript>
                </form>
            </div>
            <div class="row g-4">
                @forelse($dichVus as $dichVu)
                    <div class="col-md-6 col-lg-4" data-reveal>
                        <article class="catalog-card">
                            <a class="catalog-photo" href="{{ route('public.dichvu.show', $dichVu->MaDV) }}" aria-label="Xem {{ $dichVu->TenDV }}">
                                @if($dichVu->HinhAnh)
                                    <img src="{{ asset('storage/' . $dichVu->HinhAnh) }}" alt="{{ $dichVu->TenDV }}" loading="lazy" width="600" height="360">
                                @else
                                    <span class="catalog-photo-placeholder"><i class="bi bi-tools" aria-hidden="true"></i></span>
                                @endif
                            </a>
                            <div class="catalog-card-body">
                                <span class="catalog-category">{{ $dichVu->loaiDichVu->TenLoaiDV }}</span>
                                <h2><a href="{{ route('public.dichvu.show', $dichVu->MaDV) }}">{{ $dichVu->TenDV }}</a></h2>
                                <p class="catalog-description">{{ \Illuminate\Support\Str::limit(strip_tags($dichVu->MoTa ?: 'Xem thông tin dịch vụ và trao đổi nhu cầu với garage.'), 110) }}</p>
                                <div class="catalog-bottom">
                                    <p class="catalog-price"><span>Giá</span> <strong>{{ number_format($dichVu->Gia, 0, ',', '.') }}đ</strong></p>
                                    <div class="catalog-actions">
                                        <a class="btn btn-outline-primary" href="{{ route('public.dichvu.show', $dichVu->MaDV) }}">Xem chi tiết<span class="visually-hidden"> {{ $dichVu->TenDV }}</span></a>
                                        <a class="btn btn-primary" href="{{ route('public.datlich.create', ['ma_dv' => $dichVu->MaDV]) }}"><i class="bi bi-calendar-plus me-2" aria-hidden="true"></i>Đặt lịch<span class="visually-hidden"> {{ $dichVu->TenDV }}</span></a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12"><div class="services-empty"><i class="bi bi-search" aria-hidden="true"></i><h2>Chưa có dịch vụ phù hợp</h2><p>Thử từ khóa khác hoặc chọn lại nhóm dịch vụ.</p><a class="btn btn-outline-primary" href="{{ route('public.dichvu.index') }}">Xóa bộ lọc</a></div></div>
                @endforelse
            </div>
            @include('public.partials.services-pagination', ['paginator' => $dichVus])
        </div>
    </section>
    <section class="services-cta-section">
        <div class="container"><div class="services-cta" data-reveal>
            <div><h2>Chưa biết xe cần dịch vụ gì?</h2><p>Bạn có thể đặt lịch và mô tả tình trạng xe để garage kiểm tra.</p></div>
            <div class="services-cta-actions"><a class="btn btn-primary" href="{{ route('public.datlich.create') }}"><i class="bi bi-calendar-plus me-2" aria-hidden="true"></i>Đặt lịch kiểm tra</a><a class="btn services-secondary" href="{{ route('public.contact') }}">Liên hệ tư vấn</a></div>
        </div></div>
    </section>
</div>
@endsection

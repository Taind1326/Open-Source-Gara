@extends('public.layout')

@section('title', 'Dịch vụ Garage')

@section('content')
    <section class="garage-hero p-4 p-md-5 mb-4">
        <div class="text-uppercase small mb-2">
            Chăm sóc và sửa chữa ô tô
        </div>

        <h1 class="fw-bold">Dịch vụ tại Garage</h1>

        <p class="mb-0">
            Khám phá dịch vụ sửa chữa, bảo dưỡng và chăm sóc xe.
            Chi phí cụ thể được xác nhận sau khi kiểm tra thực tế.
        </p>
    </section>

    <section class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form
                method="GET"
                action="{{ route('public.dichvu.index') }}"
                class="row g-3 align-items-end"
            >
                <div class="col-md-6">
                    <label for="tu_khoa" class="form-label">
                        Tìm dịch vụ
                    </label>

                    <input
                        id="tu_khoa"
                        name="tu_khoa"
                        type="search"
                        maxlength="100"
                        value="{{ $tuKhoa }}"
                        class="form-control"
                        placeholder="Nhập tên dịch vụ..."
                    >
                </div>

                <div class="col-md-3">
                    <label for="ma_loai" class="form-label">
                        Nhóm dịch vụ
                    </label>

                    <select
                        id="ma_loai"
                        name="ma_loai"
                        class="form-select"
                    >
                        <option value="">Tất cả nhóm</option>

                        @foreach ($loaiDichVus as $loai)
                            <option
                                value="{{ $loai->MaLoaiDV }}"
                                @selected($maLoai == $loai->MaLoaiDV)
                            >
                                {{ $loai->TenLoaiDV }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">
                        Tìm kiếm
                    </button>

                    <a
                        href="{{ route('public.dichvu.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Đặt lại
                    </a>
                </div>
            </form>
        </div>
    </section>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a
            href="{{ route('public.dichvu.index', [
                'tu_khoa' => $tuKhoa,
            ]) }}"
            class="btn {{ !$maLoai ? 'btn-primary' : 'btn-outline-primary' }}"
        >
            Tất cả
        </a>

        @foreach ($loaiDichVus as $loai)
            <a
                href="{{ route('public.dichvu.index', [
                    'ma_loai' => $loai->MaLoaiDV,
                    'tu_khoa' => $tuKhoa,
                ]) }}"
                class="btn {{ $maLoai == $loai->MaLoaiDV
                    ? 'btn-primary'
                    : 'btn-outline-primary' }}"
            >
                {{ $loai->TenLoaiDV }}
            </a>
        @endforeach
    </div>

    <p class="text-muted">
        Tìm thấy {{ $dichVus->total() }} dịch vụ.
    </p>

    <div class="row g-4">
        @forelse ($dichVus as $dichVu)
            <div class="col-md-6 col-lg-4">
                <article class="card service-card shadow-sm h-100">
                    @if ($dichVu->HinhAnh)
                        <img
                            src="{{ asset('storage/' . $dichVu->HinhAnh) }}"
                            alt="{{ $dichVu->TenDV }}"
                            class="service-image"
                            loading="lazy"
                        >
                    @else
                        <div class="service-placeholder">
                            {{ $dichVu->loaiDichVu->TenLoaiDV }}
                        </div>
                    @endif

                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge text-bg-light">
                                {{ $dichVu->loaiDichVu->TenLoaiDV }}
                            </span>
                        </div>

                        <h5 class="fw-bold">
                            {{ $dichVu->TenDV }}
                        </h5>

                        <p class="text-muted">
                            {{ \Illuminate\Support\Str::limit(
                                $dichVu->MoTa ?: 'Liên hệ Garage để được tư vấn.',
                                120
                            ) }}
                        </p>

                        <div class="mt-auto">
                            <div class="small text-muted">
                                Giá tham khảo
                            </div>

                            <div class="service-price mb-3">
                                {{ number_format($dichVu->Gia, 0, ',', '.') }}đ
                            </div>

                            <a
                                href="{{ route(
                                    'public.dichvu.show',
                                    $dichVu->MaDV
                                ) }}"
                                class="btn btn-outline-primary w-100"
                            >
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 p-5 text-center">
                    <h5>Không tìm thấy dịch vụ</h5>

                    <p class="text-muted mb-0">
                        Thử thay đổi từ khóa hoặc nhóm dịch vụ.
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($dichVus->hasPages())
        <nav class="mt-4" aria-label="Phân trang dịch vụ">
            <ul class="pagination justify-content-center flex-wrap">
                <li class="page-item {{ $dichVus->onFirstPage() ? 'disabled' : '' }}">
                    @if ($dichVus->onFirstPage())
                        <span class="page-link">Trước</span>
                    @else
                        <a
                            class="page-link"
                            href="{{ $dichVus->previousPageUrl() }}"
                        >
                            Trước
                        </a>
                    @endif
                </li>

                @for ($page = max(1, $dichVus->currentPage() - 2);
                      $page <= min($dichVus->lastPage(), $dichVus->currentPage() + 2);
                      $page++)
                    <li class="page-item {{ $page === $dichVus->currentPage() ? 'active' : '' }}">
                        <a
                            class="page-link"
                            href="{{ $dichVus->url($page) }}"
                        >
                            {{ $page }}
                        </a>
                    </li>
                @endfor

                <li class="page-item {{ !$dichVus->hasMorePages() ? 'disabled' : '' }}">
                    @if ($dichVus->hasMorePages())
                        <a
                            class="page-link"
                            href="{{ $dichVus->nextPageUrl() }}"
                        >
                            Sau
                        </a>
                    @else
                        <span class="page-link">Sau</span>
                    @endif
                </li>
            </ul>
        </nav>
    @endif
@endsection
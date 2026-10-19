@extends('public.layout')

@section('title', 'Hóa đơn của tôi')

@section('content')
    <section class="hero">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">
                Hóa đơn của tôi
            </h1>

            <p class="hero-description mb-0">
                Xem chi phí sửa chữa, thanh toán và bảo hành của xe.
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <div class="feature-card h-auto">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <p class="section-eyebrow mb-2">
                            Chi phí sửa chữa
                        </p>

                        <h2 class="h4 fw-bold mb-1">
                            Danh sách hóa đơn
                        </h2>

                        <p class="text-muted small mb-0">
                            {{ $hoaDons->total() }} hóa đơn
                        </p>
                    </div>

                    <a
                        href="{{ route('public.datlich.index') }}"
                        class="btn btn-outline-primary"
                    >
                        Lịch hẹn của tôi
                    </a>
                </div>

                @if($hoaDons->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Hóa đơn</th>
                                    <th scope="col">Xe</th>
                                    <th scope="col">Ngày lập</th>
                                    <th scope="col" class="text-end">
                                        Tổng tiền
                                    </th>
                                    <th scope="col">Thanh toán</th>
                                    <th scope="col" class="text-end">
                                        Thao tác
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($hoaDons as $hoaDon)
                                    <tr>
                                        <td class="fw-semibold text-nowrap">
                                            #{{ $hoaDon->MaHD }}
                                        </td>

                                        <td>
                                            <div class="fw-semibold text-nowrap">
                                                {{ $hoaDon->BienSo }}
                                            </div>

                                            <small class="text-muted">
                                                {{ $hoaDon->HangXe }}
                                                {{ $hoaDon->DongXe }}
                                            </small>
                                        </td>

                                        <td class="text-nowrap">
                                            {{ \Carbon\Carbon::parse($hoaDon->NgayLap)->format('d/m/Y H:i') }}
                                        </td>

                                        <td class="text-end text-nowrap fw-semibold">
                                            {{ number_format($hoaDon->TongTien, 0, ',', '.') }} ₫
                                        </td>

                                        <td class="text-nowrap">
                                            @if($hoaDon->TrangThai === 'DA_THANH_TOAN')
                                                <span class="badge text-success bg-success-subtle">
                                                    Đã thanh toán
                                                </span>
                                            @else
                                                <span class="badge text-warning-emphasis bg-warning-subtle">
                                                    Chưa thanh toán
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-end">
                                            <a
                                                href="{{ route('hoadon.show', $hoaDon->MaHD) }}"
                                                class="btn btn-outline-primary btn-sm text-nowrap"
                                            >
                                                Xem hóa đơn
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($hoaDons->hasPages())
                        <div class="mt-4">
                            {{ $hoaDons->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <span class="feature-icon">
                            <i class="bi bi-receipt" aria-hidden="true"></i>
                        </span>

                        <h3 class="h5 fw-bold">
                            Bạn chưa có hóa đơn
                        </h3>

                        <p class="text-muted mb-0">
                            Hóa đơn được tạo sau khi kỹ thuật viên
                            hoàn thành sửa chữa và kiểm tra xe lần cuối.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
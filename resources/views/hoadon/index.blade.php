@extends('public.layout')

@section('title', 'Hóa đơn của tôi')

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
                        Hóa đơn của tôi
                    </li>
                </ol>
            </nav>

            <h1 class="display-5 fw-bold mb-3">
                Hóa đơn của tôi
            </h1>

            <p class="hero-description mb-0">
                Xem chi tiết chi phí và trạng thái thanh toán
                của các lần sửa chữa.
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            @if(!empty($thieuTaiKhoan))
                <div class="alert alert-danger" role="alert">
                    Không xác định được tài khoản khách hàng.
                    Vui lòng đăng xuất và đăng nhập lại.
                </div>
            @else
                <div class="feature-card h-auto">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <p class="section-eyebrow mb-2">
                                Thông tin thanh toán
                            </p>

                            <h2 class="h4 fw-bold mb-0">
                                Danh sách hóa đơn
                            </h2>
                        </div>

                        <a href="{{ route('public.taikhoan.edit') }}"
                           class="btn btn-outline-primary">
                            <i class="bi bi-person me-1" aria-hidden="true"></i>
                            Tài khoản của tôi
                        </a>
                    </div>

                    @if($danhSach->count() > 0)
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Mã hóa đơn</th>
                                        <th scope="col">Phiếu sửa chữa</th>
                                        <th scope="col">Biển số</th>
                                        <th scope="col" class="text-end">
                                            Tổng thanh toán
                                        </th>
                                        <th scope="col">Trạng thái</th>
                                        <th scope="col" class="text-end">
                                            Chi tiết
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($danhSach as $hoaDon)
                                        <tr>
                                            <td class="fw-semibold text-nowrap">
                                                #{{ $hoaDon->MaHD }}
                                            </td>

                                            <td class="text-nowrap">
                                                #{{ $hoaDon->MaPSC }}
                                            </td>

                                            <td class="text-nowrap">
                                                {{ $hoaDon->BienSo }}
                                            </td>

                                            <td class="text-end text-nowrap fw-bold">
                                                {{ number_format(
                                                    $hoaDon->TongThanhToan,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }} ₫
                                            </td>

                                            <td class="text-nowrap">
                                                @if($hoaDon->TrangThaiThanhToan === 'DA_THANH_TOAN')
                                                    <span class="badge rounded-pill text-success bg-success-subtle px-3 py-2">
                                                        Đã thanh toán
                                                    </span>
                                                @else
                                                    <span class="badge rounded-pill text-warning-emphasis bg-warning-subtle px-3 py-2">
                                                        Chưa thanh toán
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="text-end text-nowrap">
                                                <a href="{{ route('hoadon.show', [
                                                    'maHD' => $hoaDon->MaHD,
                                                ]) }}"
                                                   class="btn btn-outline-primary btn-sm">
                                                    Xem chi tiết
                                                    <i class="bi bi-arrow-right ms-1"
                                                       aria-hidden="true"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($danhSach instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            @if($danhSach->hasPages())
                                <div class="mt-4">
                                    {{ $danhSach->links('pagination::bootstrap-5') }}
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="text-center py-5">
                            <span class="feature-icon">
                                <i class="bi bi-receipt" aria-hidden="true"></i>
                            </span>

                            <h3 class="h5 fw-bold">
                                Bạn chưa có hóa đơn
                            </h3>

                            <p class="text-muted mb-4">
                                Hóa đơn sẽ xuất hiện sau khi gara lập hóa đơn
                                cho hồ sơ sửa chữa của bạn.
                            </p>

                            <a href="{{ route('public.dichvu.index') }}"
                               class="btn btn-primary">
                                Khám phá dịch vụ
                            </a>
                        </div>
                    @endif
                </div>

                <div class="alert alert-info d-flex gap-3 mt-4 mb-0">
                    <i class="bi bi-info-circle" aria-hidden="true"></i>

                    <div>
                        Hóa đơn được cập nhật đã thanh toán sau khi
                        gara kiểm tra và xác nhận đã nhận tiền.
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
@extends('public.layout')

@section('title', 'Hóa đơn #' . $hoaDon->MaHD)

@section('content')
    @php
        $daThanhToan = $hoaDon->TrangThai === 'DA_THANH_TOAN';

        $tenUuDai = match ($hoaDon->LoaiUuDai) {
            'DOI_DIEM' => 'Đổi 50 điểm — giảm 5% dịch vụ',
            'BAO_HANH' => 'Bảo hành — giảm 30% dịch vụ đủ điều kiện',
            default => 'Không áp dụng ưu đãi',
        };
    @endphp

    <section class="hero">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">
                Hóa đơn #{{ $hoaDon->MaHD }}
            </h1>

            <p class="hero-description mb-0">
                Xe {{ $hoaDon->BienSo }}
                · Lịch hẹn #{{ $hoaDon->MaLH }}
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <a
                href="{{ route('hoadon.index') }}"
                class="btn btn-outline-primary mb-4"
            >
                Quay lại hóa đơn của tôi
            </a>

            @if($daThanhToan && $hoaDon->DaXuLyDiem)
    <a
        href="{{ route('hoadon.print', $hoaDon->MaHD) }}"
        class="btn btn-primary mb-4 ms-2"
        target="_blank"
        rel="noopener"
    >
        <i class="bi bi-printer me-2" aria-hidden="true"></i>
        In hóa đơn
    </a>
@endif

            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <div class="feature-card h-auto mb-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                            <h2 class="h4 fw-bold mb-0">
                                Thông tin hóa đơn
                            </h2>

                            <span class="badge rounded-pill px-3 py-2 {{ $daThanhToan ? 'text-success bg-success-subtle' : 'text-warning-emphasis bg-warning-subtle' }}">
                                {{ $daThanhToan ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                            </span>
                        </div>

                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted fw-normal">
                                Khách hàng
                            </dt>
                            <dd class="col-sm-8">
                                {{ $hoaDon->HoTen }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Số điện thoại
                            </dt>
                            <dd class="col-sm-8">
                                {{ $hoaDon->SoDienThoai ?: 'Chưa cập nhật' }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Xe
                            </dt>
                            <dd class="col-sm-8">
                                <strong>{{ $hoaDon->BienSo }}</strong>
                                — {{ $hoaDon->HangXe }}
                                {{ $hoaDon->DongXe }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Ngày lập
                            </dt>
                            <dd class="col-sm-8">
                                {{ \Carbon\Carbon::parse($hoaDon->NgayLap)->format('d/m/Y H:i') }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Hoàn thành sửa
                            </dt>
                            <dd class="col-sm-8">
                                {{ $hoaDon->NgayHoanThanh
                                    ? \Carbon\Carbon::parse($hoaDon->NgayHoanThanh)->format('d/m/Y H:i')
                                    : 'Chưa có thông tin' }}
                            </dd>
                        </dl>
                    </div>

                    <div class="feature-card h-auto mb-4">
                        <h2 class="h4 fw-bold mb-3">
                            Chi tiết chi phí
                        </h2>

                        <p class="text-muted small">
                            Giá dịch vụ đã bao gồm chi phí trong gói.
                            Phụ tùng phát sinh được liệt kê riêng
                            theo phiếu bạn đã đồng ý.
                        </p>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Hạng mục</th>
                                        <th scope="col">Số lượng</th>
                                        <th scope="col" class="text-end">
                                            Đơn giá
                                        </th>
                                        <th scope="col" class="text-end">
                                            Giảm
                                        </th>
                                        <th scope="col" class="text-end">
                                            Thành tiền
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($chiTiets as $chiTiet)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">
                                                    {{ $chiTiet->TenHangMuc }}
                                                </div>

                                                <small class="text-muted">
                                                    {{ $chiTiet->LoaiHangMuc === 'DICH_VU' ? 'Dịch vụ' : 'Phụ tùng' }}
                                                </small>

                                                @if((float) $chiTiet->TyLeGiam > 0)
                                                    <small class="d-block text-success">
                                                        Giảm {{ number_format($chiTiet->TyLeGiam, 0) }}%
                                                    </small>
                                                @endif
                                            </td>

                                            <td class="text-nowrap">
                                                {{ $chiTiet->SoLuong }}
                                                {{ $chiTiet->DonViTinh }}
                                            </td>

                                            <td class="text-end text-nowrap">
                                                {{ number_format($chiTiet->DonGia, 0, ',', '.') }} ₫
                                            </td>

                                            <td class="text-end text-nowrap">
                                                {{ number_format($chiTiet->TienGiam, 0, ',', '.') }} ₫
                                            </td>

                                            <td class="text-end text-nowrap fw-semibold">
                                                {{ number_format($chiTiet->ThanhTien, 0, ',', '.') }} ₫
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($hoaDon->GhiChuSuaChua)
                        <div class="feature-card h-auto mb-4">
                            <h2 class="h5 fw-bold mb-3">
                                Lưu ý sau sửa chữa
                            </h2>

                            <p
                                class="text-muted text-break mb-0"
                                style="white-space: pre-line;"
                            >{{ $hoaDon->GhiChuSuaChua }}</p>
                        </div>
                    @endif

                    <div class="feature-card h-auto">
                        <h2 class="h5 fw-bold mb-3">
                            Bảo hành sửa chữa
                        </h2>

                        @if($hoaDon->BaoHanhDen)
                            <p class="mb-2">
                                Bảo hành 14 ngày, đến
                                <strong>
                                    {{ \Carbon\Carbon::parse($hoaDon->BaoHanhDen)->format('d/m/Y H:i') }}
                                </strong>.
                            </p>

                            <p class="text-muted small mb-0">
                                Nếu xe quay lại trong hạn, kỹ thuật viên
                                sẽ kiểm tra và xác nhận hạng mục đủ điều kiện
                                để áp dụng giảm 30% dịch vụ bảo hành.
                                Không cộng dồn ưu đãi đổi điểm.
                            </p>
                        @else
                            <p class="text-muted mb-0">
                                Chưa có thông tin bảo hành.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="feature-card h-auto">
                        <h2 class="h5 fw-bold mb-4">
                            Tổng chi phí
                        </h2>

                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <span class="text-muted">Trước giảm</span>

                            <strong class="text-nowrap">
                                {{ number_format($hoaDon->TongTruocGiam, 0, ',', '.') }} ₫
                            </strong>
                        </div>

                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <span class="text-muted">Giảm giá</span>

                            <strong class="text-success text-nowrap">
                                {{ number_format($hoaDon->TienGiam, 0, ',', '.') }} ₫
                            </strong>
                        </div>

                        <p class="small text-muted">
                            {{ $tenUuDai }}
                        </p>

                        <div class="d-flex flex-wrap justify-content-between gap-2 border-top pt-3 mb-4">
                            <span class="fw-semibold">Tổng thanh toán</span>

                            <strong class="fs-4 text-primary">
                                {{ number_format($hoaDon->TongTien, 0, ',', '.') }} ₫
                            </strong>
                        </div>

                        @if($daThanhToan)
                            <div class="alert alert-success mb-3">
                                Hóa đơn đã thanh toán.
                            </div>

                            @if($thanhToan)
                                <p class="small mb-2">
                                    Phương thức:
                                    <strong>
                                        {{ $thanhToan->PhuongThuc === 'TIEN_MAT' ? 'Tiền mặt' : 'Chuyển khoản' }}
                                    </strong>
                                </p>

                                <p class="small mb-3">
                                    Thanh toán lúc:
                                    <strong>
                                        {{ \Carbon\Carbon::parse($thanhToan->NgayThanhToan)->format('d/m/Y H:i') }}
                                    </strong>
                                </p>
                            @endif

                            @if($hoaDon->DaXuLyDiem)
                                <p class="small text-muted mb-0">
                                    Đã cộng {{ $hoaDon->DiemDuocCong }} điểm tích lũy.

                                    @if($hoaDon->DiemSuDung > 0)
                                        Đã sử dụng {{ $hoaDon->DiemSuDung }} điểm
                                        cho ưu đãi của hóa đơn này.
                                    @endif
                                </p>
                            @endif
                        @else
                            <div class="alert alert-warning mb-3">
                                Xe đã sửa xong, đang chờ thanh toán.
                            </div>

                            <p class="text-muted small mb-0">
                                Điểm tích lũy được cập nhật sau khi
                                gara xác nhận thanh toán thành công.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(!$daThanhToan)
    <div class="container pb-4">
        @error('thanh_toan')
            <div class="text-danger mb-3" role="alert">
                {{ $message }}
            </div>
        @enderror

        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#paymentModal"
        >
            <i class="bi bi-credit-card me-2" aria-hidden="true"></i>
            Thanh toán
        </button>
    </div>

    <div
        class="modal fade"
        id="paymentModal"
        tabindex="-1"
        aria-labelledby="paymentModalTitle"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="paymentModalTitle">
                        Chọn phương thức thanh toán
                    </h2>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Đóng"
                    ></button>
                </div>

                <form
                    method="POST"
                    action="{{ route('hoadon.payment.choose', $hoaDon->MaHD) }}"
                    novalidate
                >
                    @csrf

                    <div class="modal-body">
                        <p>
                            Tổng thanh toán:
                            <strong class="text-primary">
                                {{ number_format($hoaDon->TongTien, 0, ',', '.') }} ₫
                            </strong>
                        </p>

                        <label for="customer-payment-method" class="form-label">
                            Phương thức thanh toán
                        </label>

                        <select
                            id="customer-payment-method"
                            name="PhuongThuc"
                            class="form-select @error('PhuongThuc') is-invalid @enderror"
                            required
                        >
                            <option value="">Vui lòng chọn</option>

                            <option
                                value="TIEN_MAT"
                                @selected(old('PhuongThuc') === 'TIEN_MAT')
                            >
                                Tiền mặt tại gara
                            </option>

                            @if(app()->environment('local') && config('garage.payment_demo'))
                                <option
                                    value="CHUYEN_KHOAN"
                                    @selected(old('PhuongThuc') === 'CHUYEN_KHOAN')
                                >
                                    Chuyển khoản — demo
                                </option>
                            @endif
                        </select>

                        @error('PhuongThuc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <p class="small text-muted mt-3 mb-0">
                            Tiền mặt được gara xác nhận sau khi thu.
                            Chuyển khoản mở màn hình QR mô phỏng.
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Đóng
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Tiếp tục thanh toán
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
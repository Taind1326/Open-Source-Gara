@extends('admin.layout')

@section('title', 'Chi tiết hóa đơn')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <a
                href="{{ route('hoadon.admin.index') }}"
                class="text-decoration-none"
            >
                ← Danh sách hóa đơn
            </a>

            <h1 class="h4 fw-bold mt-3 mb-0">
                Hóa đơn #{{ $hoaDon->MaHD }}
            </h1>
        </div>

        @if ($hoaDon->TrangThaiThanhToan === 'DA_THANH_TOAN')
            <span class="badge bg-success-subtle text-success p-3">
                Đã thanh toán
            </span>
        @else
            <span class="badge bg-warning-subtle text-warning-emphasis p-3">
                Chưa thanh toán
            </span>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Thông tin khách hàng</h2>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="small text-secondary">Khách hàng</div>
                            <div class="fw-semibold">{{ $hoaDon->HoTen }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-secondary">Số điện thoại</div>
                            <div>{{ $hoaDon->SoDienThoai }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-secondary">Biển số</div>
                            <div class="fw-semibold">{{ $hoaDon->BienSo }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="small text-secondary">Xe</div>
                            <div>
                                {{ $hoaDon->HangXe }} {{ $hoaDon->DongXe }}
                            </div>
                        </div>
                    </div>

                    <a
    href="{{ route('hoadon.admin.show', $hoaDon->MaHD) }}"
    class="btn btn-outline-primary btn-sm text-nowrap"
>
    Xem hóa đơn
</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-4 border-bottom">
                    <h2 class="h5 fw-bold mb-0">Hạng mục thực tế</h2>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Hạng mục</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th class="text-end pe-4">Thành tiền</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($chiTiet as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-semibold">
                                            {{ $item->TenDV ?? $item->TenPT ?? 'Hạng mục không còn trong danh mục' }}
                                        </div>

                                        <div class="small text-secondary">
                                            {{ $item->MaDV ? 'Dịch vụ' : 'Phụ tùng' }}
                                        </div>
                                    </td>

                                    <td>{{ $item->SoLuong }}</td>

                                    <td class="text-nowrap">
                                        {{ number_format($item->DonGia, 0, ',', '.') }} đ
                                    </td>

                                    <td class="text-end pe-4 text-nowrap">
                                        {{ number_format($item->SoLuong * $item->DonGia, 0, ',', '.') }} đ
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary py-4">
                                        Chưa có hạng mục.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-4">Thanh toán</h2>

                    <div class="d-flex justify-content-between gap-3 mb-3">
                        <span class="text-secondary">Tổng tiền gốc</span>
                        <span>
                            {{ number_format($hoaDon->TongTienGoc, 0, ',', '.') }} đ
                        </span>
                    </div>

                    <div class="d-flex justify-content-between gap-3 mb-3">
                        <span class="text-secondary">Điểm sử dụng</span>
                        <span>{{ $hoaDon->DiemDaDung }} điểm</span>
                    </div>

                    <div class="d-flex justify-content-between gap-3 mb-3">
                        <span class="text-secondary">
                            Giảm {{ $hoaDon->PhanTramGiam }}%
                        </span>

                        <span class="text-success">
                            {{ number_format($hoaDon->SoTienGiam, 0, ',', '.') }} đ
                        </span>
                    </div>

                    <div class="border-top pt-3 mb-4">
                        <div class="text-secondary mb-1">Cần thanh toán</div>

                        <div class="h3 fw-bold text-primary mb-0">
                            {{ number_format($hoaDon->TongThanhToan, 0, ',', '.') }} đ
                        </div>
                    </div>

                    @if ($hoaDon->TrangThaiThanhToan !== 'DA_THANH_TOAN')
                        <form
                            method="POST"
                            action="{{ route('hoadon.admin.confirm', $hoaDon->MaHD) }}"
                            onsubmit="return confirm('Xác nhận đã nhận đủ tiền? Hóa đơn sẽ được đánh dấu đã thanh toán.');"
                        >
                            @csrf

                            <div class="mb-3">
                                <label for="PhuongThucThanhToan" class="form-label">
                                    Phương thức thực tế
                                </label>

                                <select
                                    id="PhuongThucThanhToan"
                                    name="PhuongThucThanhToan"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Chọn phương thức</option>

                                    <option
                                        value="TIEN_MAT"
                                        @selected(old('PhuongThucThanhToan', $hoaDon->PhuongThucThanhToan) === 'TIEN_MAT')
                                    >
                                        Tiền mặt
                                    </option>

                                    <option
                                        value="CHUYEN_KHOAN_QR"
                                        @selected(old('PhuongThucThanhToan', $hoaDon->PhuongThucThanhToan) === 'CHUYEN_KHOAN_QR')
                                    >
                                        Chuyển khoản QR
                                    </option>
                                </select>
                            </div>

                            <div class="form-check mb-4">
                                <input
                                    type="checkbox"
                                    id="DaNhanTien"
                                    name="DaNhanTien"
                                    value="1"
                                    class="form-check-input"
                                    required
                                >

                                <label for="DaNhanTien" class="form-check-label">
                                    Tôi đã kiểm tra và nhận đủ tiền.
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Xác nhận thanh toán
                            </button>

                            <p class="small text-secondary mt-3 mb-0">
                                Xác nhận sau khi nhận tiền mặt hoặc kiểm tra tiền
                                đã vào tài khoản gara.
                            </p>
                        </form>
                    @else
                        <div class="alert alert-success mb-3">
                            Hóa đơn đã thanh toán. Hồ sơ đã hoàn thành.
                        </div>

                        <div class="small text-secondary">
                            Phương thức:
                            {{ $hoaDon->PhuongThucThanhToan === 'TIEN_MAT' ? 'Tiền mặt' : 'Chuyển khoản QR' }}
                        </div>

                        @if ($hoaDon->NgayThanhToan)
                            <div class="small text-secondary mt-2">
                                Thời gian:
                                {{ \Illuminate\Support\Carbon::parse(
                                    $hoaDon->NgayThanhToan,
                                    config('app.timezone')
                                )->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
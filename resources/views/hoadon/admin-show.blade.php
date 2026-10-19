@extends('admin.layout')

@section('title', 'Hóa đơn #' . $hoaDon->MaHD)

@section('content')
    <a
        href="{{ route('hoadon.admin.index') }}"
        class="btn btn-outline-primary mb-4"
    >
        Quay lại danh sách hóa đơn
    </a>

    <div class="row g-4 align-items-start">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h1 class="h3 fw-bold mb-4">
                        Hóa đơn #{{ $hoaDon->MaHD }}
                    </h1>

                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-secondary fw-normal">
                            Khách hàng
                        </dt>
                        <dd class="col-sm-8">
                            {{ $hoaDon->HoTen }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Điện thoại
                        </dt>
                        <dd class="col-sm-8">
                            {{ $hoaDon->SoDienThoai ?: 'Chưa cập nhật' }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Xe
                        </dt>
                        <dd class="col-sm-8">
                            <strong>{{ $hoaDon->BienSo }}</strong>
                            — {{ $hoaDon->HangXe }} {{ $hoaDon->DongXe }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Lịch hẹn
                        </dt>
                        <dd class="col-sm-8">
                            #{{ $hoaDon->MaLH }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Ngày lập
                        </dt>
                        <dd class="col-sm-8">
                            {{ \Carbon\Carbon::parse($hoaDon->NgayLap)->format('d/m/Y H:i') }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Bảo hành đến
                        </dt>
                        <dd class="col-sm-8">
                            {{ $hoaDon->BaoHanhDen
                                ? \Carbon\Carbon::parse($hoaDon->BaoHanhDen)->format('d/m/Y H:i')
                                : 'Chưa có thông tin' }}
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">Chi tiết chi phí</h2>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Hạng mục</th>
                                    <th>Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Giảm giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($chiTiets as $chiTiet)
                                    <tr>
                                        <td>
                                            <div>{{ $chiTiet->TenHangMuc }}</div>
                                            <small class="text-muted">
                                                {{ $chiTiet->LoaiHangMuc === 'DICH_VU' ? 'Dịch vụ' : 'Phụ tùng' }}
                                            </small>
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

                                        <td class="text-end text-nowrap">
                                            {{ number_format($chiTiet->ThanhTien, 0, ',', '.') }} ₫
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-4">Thanh toán</h2>

                    <div class="d-flex justify-content-between gap-3 mb-3">
                        <span>Trước giảm</span>
                        <strong class="text-nowrap">
                            {{ number_format($hoaDon->TongTruocGiam, 0, ',', '.') }} ₫
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between gap-3 mb-3">
                        <span>Giảm giá</span>
                        <strong class="text-nowrap">
                            {{ number_format($hoaDon->TienGiam, 0, ',', '.') }} ₫
                        </strong>
                    </div>

                    <div class="border-top pt-3 mb-4">
                        <div>Tổng thanh toán</div>
                        <strong class="fs-4 text-primary">
                            {{ number_format($hoaDon->TongTien, 0, ',', '.') }} ₫
                        </strong>
                    </div>

                    @if($hoaDon->TrangThai === 'DA_THANH_TOAN')
                        @error('thanh_toan')
                            <p class="text-danger small" role="alert">
                                {{ $message }}
                            </p>
                        @enderror

                        <div class="alert alert-success">
                            Đã thanh toán và cập nhật điểm tích lũy.
                        </div>

                        @if($thanhToan)
                            <p class="small mb-2">
                                Phương thức:
                                <strong>
                                    {{ $thanhToan->PhuongThuc === 'TIEN_MAT' ? 'Tiền mặt' : 'Chuyển khoản' }}
                                </strong>
                            </p>

                            <p class="small mb-2">
                                Thời điểm:
                                {{ \Carbon\Carbon::parse($thanhToan->NgayThanhToan)->format('d/m/Y H:i') }}
                            </p>

                            @if($thanhToan->GhiChu)
                                <p class="small text-muted text-break mb-0">
                                    {{ $thanhToan->GhiChu }}
                                </p>
                            @endif
                        @endif
                    @elseif($yeuCauTienMat || (int) $hoaDon->TongTien === 0)
                        <div class="alert alert-warning">
                            {{ (int) $hoaDon->TongTien === 0
                                ? 'Hóa đơn không phát sinh tiền cần thu.'
                                : 'Khách chọn tiền mặt, đang chờ gara xác nhận đã thu.' }}
                        </div>

                        <form
                            method="POST"
                            action="{{ route('hoadon.admin.confirm', $hoaDon->MaHD) }}"
                            novalidate
                            data-confirm-title="Xác nhận thanh toán tiền mặt"
                            data-confirm="Xác nhận đã thu đủ tiền hoặc hóa đơn không phát sinh tiền cần thu? Hệ thống sẽ cập nhật điểm và hoàn tất đơn."
                            data-confirm-accept="Xác nhận"
                            data-confirm-cancel="Chưa xác nhận"
                        >
                            @csrf

                            <input
                                type="hidden"
                                name="MaTT"
                                value="{{ $yeuCauTienMat?->MaTT ?? 0 }}"
                            >

                            <div class="form-check mb-3">
                                <input
                                    type="checkbox"
                                    id="cash-confirm"
                                    name="XacNhan"
                                    value="1"
                                    class="form-check-input @error('XacNhan') is-invalid @enderror"
                                    required
                                    @checked(old('XacNhan'))
                                >

                                <label class="form-check-label" for="cash-confirm">
                                    {{ (int) $hoaDon->TongTien === 0
                                        ? 'Tôi xác nhận hóa đơn không phát sinh tiền cần thu.'
                                        : 'Tôi đã nhận đủ tiền mặt.' }}
                                </label>

                                @error('XacNhan')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            @error('MaTT')
                                <p class="text-danger small">{{ $message }}</p>
                            @enderror

                            @error('thanh_toan')
                                <p class="text-danger small" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror

                            <button type="submit" class="btn btn-primary w-100">
                                Xác nhận thanh toán
                            </button>
                        </form>
                    @else
                        @error('thanh_toan')
                            <p class="text-danger small" role="alert">
                                {{ $message }}
                            </p>
                        @enderror

                        <div class="alert alert-info mb-0">
                            Chưa có yêu cầu thanh toán tiền mặt.
                            Khách chọn phương thức tại trang hóa đơn.
                            Chuyển khoản demo được hoàn tất tại màn hình QR.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('public.layout')

@section('title', 'Hóa đơn #' . $hoaDon->MaHD)

@section('content')
    @php
        $daThanhToan = $hoaDon->TrangThaiThanhToan === 'DA_THANH_TOAN';

        $maThanhToan = 'HD' . str_pad(
            $hoaDon->MaHD,
            6,
            '0',
            STR_PAD_LEFT
        );

        // Giữ thông tin tài khoản nhận tiền từ file hiện tại.
        $qrUrl = 'https://img.vietqr.io/image/'
            . '970436-1050497105-compact2.png?'
            . http_build_query([
                'amount' => (int) round($hoaDon->TongThanhToan),
                'addInfo' => $maThanhToan,
                'accountName' => 'NGUYEN HOAI PHU',
            ]);

        $phuongThuc = old(
            'PhuongThucThanhToan',
            $hoaDon->PhuongThucThanhToan ?: 'TIEN_MAT'
        );
    @endphp

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
                        <a href="{{ route('hoadon.index') }}" class="text-white">
                            Hóa đơn của tôi
                        </a>
                    </li>

                    <li class="breadcrumb-item active text-white"
                        aria-current="page">
                        #{{ $hoaDon->MaHD }}
                    </li>
                </ol>
            </nav>

            <h1 class="display-5 fw-bold mb-3">
                Hóa đơn #{{ $hoaDon->MaHD }}
            </h1>

            <span class="badge rounded-pill {{ $daThanhToan ? 'text-bg-success' : 'text-bg-warning' }} px-3 py-2">
                {{ $daThanhToan ? 'Đã thanh toán' : 'Chưa thanh toán' }}
            </span>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <a href="{{ route('hoadon.index') }}"
               class="d-inline-flex align-items-center gap-2 fw-semibold mb-4">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                Quay lại danh sách
            </a>

            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <section class="feature-card h-auto mb-4">
                        <p class="section-eyebrow mb-2">Hồ sơ sửa chữa</p>
                        <h2 class="h4 fw-bold mb-4">Thông tin hóa đơn</h2>

                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted fw-normal">
                                Khách hàng
                            </dt>
                            <dd class="col-sm-8 fw-semibold text-break">
                                {{ $hoaDon->HoTen }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Biển số
                            </dt>
                            <dd class="col-sm-8 fw-semibold">
                                {{ $hoaDon->BienSo }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Hãng / dòng xe
                            </dt>
                            <dd class="col-sm-8 text-break">
                                {{ $hoaDon->HangXe }} · {{ $hoaDon->DongXe }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Phiếu sửa chữa
                            </dt>
                            <dd class="col-sm-8">
                                #{{ $hoaDon->MaPSC }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Yêu cầu sửa chữa
                            </dt>
                            <dd class="col-sm-8 mb-0">
                                #{{ $hoaDon->MaYC }}
                            </dd>
                        </dl>
                    </section>

                    <section class="feature-card h-auto mb-4">
                        <h2 class="h4 fw-bold mb-4">Chi tiết sửa chữa</h2>

                        @if($chiTiet->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Hạng mục</th>
                                            <th scope="col" class="text-end">SL</th>
                                            <th scope="col" class="text-end">Đơn giá</th>
                                            <th scope="col" class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($chiTiet as $item)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">
                                                        {{ $item->MaDV ? $item->TenDV : $item->TenPT }}
                                                    </div>

                                                    <small class="text-muted">
                                                        {{ $item->MaDV ? 'Dịch vụ' : 'Phụ tùng' }}
                                                    </small>
                                                </td>

                                                <td class="text-end">
                                                    {{ $item->SoLuong }}
                                                </td>

                                                <td class="text-end text-nowrap">
                                                    {{ number_format($item->DonGia, 0, ',', '.') }} ₫
                                                </td>

                                                <td class="text-end text-nowrap fw-semibold">
                                                    {{ number_format(
                                                        $item->SoLuong * $item->DonGia,
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) }} ₫
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">
                                Không có chi tiết sửa chữa.
                            </p>
                        @endif
                    </section>

                    @if(!$daThanhToan)
                        <section class="feature-card h-auto">
                            <h2 class="h4 fw-bold mb-3">Sử dụng điểm</h2>

                            <p class="text-muted">
                                Điểm hiện có:
                                <strong class="text-primary">
                                    {{ number_format($hoaDon->DiemTichLuy, 0, ',', '.') }}
                                </strong>
                                điểm. Điểm được trừ khi gara xác nhận thanh toán.
                            </p>

                            @if($hoaDon->DiemDaDung > 0)
                                <div class="alert alert-success">
                                    Đang áp dụng
                                    {{ number_format($hoaDon->DiemDaDung, 0, ',', '.') }}
                                    điểm, giảm
                                    {{ number_format($hoaDon->PhanTramGiam, 0) }}%.
                                </div>

                                <form novalidate method="POST"
                                      action="{{ route('hoadon.bo-diem', $hoaDon->MaHD) }}">
                                    @csrf

                                    <button type="submit" class="btn btn-outline-danger">
                                        Bỏ sử dụng điểm
                                    </button>
                                </form>
                            @elseif($hoaDon->DiemTichLuy >= 500)
                                <form novalidate method="POST"
                                      action="{{ route('hoadon.ap-dung-diem', $hoaDon->MaHD) }}">
                                    @csrf

                                    <fieldset class="mb-4">
                                        <legend class="fs-6 fw-semibold">
                                            Chọn mức giảm
                                        </legend>

                                        <div class="form-check mb-2">
                                            <input type="radio"
                                                   id="points500"
                                                   name="diem"
                                                   value="500"
                                                   class="form-check-input"
                                                   @checked((string) old('diem', '500') !== '1000' || $hoaDon->DiemTichLuy < 1000)
                                                   required>

                                            <label for="points500" class="form-check-label">
                                                Dùng 500 điểm — giảm 10%
                                            </label>
                                        </div>

                                        @if($hoaDon->DiemTichLuy >= 1000)
                                            <div class="form-check">
                                                <input type="radio"
                                                       id="points1000"
                                                       name="diem"
                                                       value="1000"
                                                       class="form-check-input"
                                                       @checked((string) old('diem') === '1000')>

                                                <label for="points1000" class="form-check-label">
                                                    Dùng 1.000 điểm — giảm 15%
                                                </label>
                                            </div>
                                        @endif
                                    </fieldset>

                                    <button type="submit" class="btn btn-primary">
                                        Áp dụng điểm
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-light border mb-0">
                                    Bạn chưa đủ 500 điểm để áp dụng giảm giá.
                                </div>
                            @endif
                        </section>
                    @endif
                </div>

                <div class="col-lg-4">
                    <section class="feature-card h-auto mb-4">
                        <h2 class="h4 fw-bold mb-4">Tổng thanh toán</h2>

                        <dl class="row small">
                            <dt class="col-7 text-muted fw-normal">
                                Tổng tiền gốc
                            </dt>
                            <dd class="col-5 text-end">
                                {{ number_format($hoaDon->TongTienGoc, 0, ',', '.') }} ₫
                            </dd>

                            <dt class="col-7 text-muted fw-normal">
                                Điểm áp dụng
                            </dt>
                            <dd class="col-5 text-end">
                                {{ number_format($hoaDon->DiemDaDung, 0, ',', '.') }}
                            </dd>

                            <dt class="col-7 text-muted fw-normal">
                                Mức giảm
                            </dt>
                            <dd class="col-5 text-end">
                                {{ number_format($hoaDon->PhanTramGiam, 0) }}%
                            </dd>

                            <dt class="col-7 text-muted fw-normal">
                                Số tiền giảm
                            </dt>
                            <dd class="col-5 text-end text-success">
                                {{ number_format($hoaDon->SoTienGiam, 0, ',', '.') }} ₫
                            </dd>
                        </dl>

                        <div class="border-top pt-3">
                            <p class="text-muted small mb-1">
                                Tổng cộng
                            </p>

                            <div class="fs-3 fw-bold text-primary">
                                {{ number_format($hoaDon->TongThanhToan, 0, ',', '.') }} ₫
                            </div>
                        </div>
                    </section>

                    <section class="feature-card h-auto">
                        @if($daThanhToan)
                            <span class="feature-icon">
                                <i class="bi bi-check-circle" aria-hidden="true"></i>
                            </span>

                            <h2 class="h5 fw-bold">Đã thanh toán</h2>

                            <p class="text-muted">
                                Gara đã xác nhận thanh toán hóa đơn này.
                            </p>

                            <p class="small mb-2">
                                <strong>Phương thức:</strong>
                                @switch($hoaDon->PhuongThucThanhToan)
                                    @case('TIEN_MAT')
                                        Tiền mặt
                                        @break
                                    @case('CHUYEN_KHOAN_QR')
                                        Chuyển khoản
                                        @break
                                    @default
                                        Chưa có thông tin
                                @endswitch
                            </p>

                            <p class="small mb-0">
                                <strong>Ngày thanh toán:</strong>

                                @if($hoaDon->NgayThanhToan)
                                    {{ \Carbon\Carbon::parse(
                                        $hoaDon->NgayThanhToan,
                                        config('app.timezone')
                                    )->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                                @else
                                    —
                                @endif
                            </p>
                        @else
                            <h2 class="h5 fw-bold mb-3">
                                Phương thức thanh toán
                            </h2>

                            <form novalidate method="POST"
                                  action="{{ route('hoadon.thanh-toan', $hoaDon->MaHD) }}">
                                @csrf

                                <label for="paymentMethod" class="form-label">
                                    Chọn phương thức
                                </label>

                                <select id="paymentMethod"
                                        name="PhuongThucThanhToan"
                                        class="form-select mb-3"
                                        required>
                                    <option value="TIEN_MAT"
                                            @selected($phuongThuc === 'TIEN_MAT')>
                                        Tiền mặt tại gara
                                    </option>

                                    <option value="CHUYEN_KHOAN_QR"
                                            @selected($phuongThuc === 'CHUYEN_KHOAN_QR')>
                                        Chuyển khoản QR
                                    </option>
                                </select>

                                <p class="text-muted small">
                                    Gửi lựa chọn để gara kiểm tra thanh toán.
                                    Thao tác này chưa đánh dấu hóa đơn đã thanh toán.
                                </p>

                                <button type="submit" class="btn btn-primary w-100">
                                    Gửi yêu cầu xác nhận
                                </button>
                            </form>

                            <details class="border-top mt-4 pt-3">
                                <summary class="fw-semibold text-primary">
                                    Xem thông tin chuyển khoản / QR
                                </summary>

                                <div class="mt-3">
                                    <p class="text-muted small">
                                        Kiểm tra thông tin người nhận trước khi chuyển.
                                        Nếu vừa thay đổi điểm giảm giá, hãy dùng QR
                                        mới trên trang đã tải lại.
                                    </p>

                                    <img src="{{ $qrUrl }}"
                                         alt="QR chuyển khoản hóa đơn {{ $hoaDon->MaHD }}"
                                         class="img-fluid rounded border mb-3"
                                         loading="lazy"
                                         referrerpolicy="no-referrer">

                                    <dl class="small mb-0">
                                        <dt>Ngân hàng</dt>
                                        <dd>Vietcombank</dd>

                                        <dt>Số tài khoản</dt>
                                        <dd class="text-break">1050497105</dd>

                                        <dt>Người nhận</dt>
                                        <dd>NGUYEN HOAI PHU</dd>

                                        <dt>Số tiền</dt>
                                        <dd>
                                            {{ number_format($hoaDon->TongThanhToan, 0, ',', '.') }} ₫
                                        </dd>

                                        <dt>Nội dung chuyển khoản</dt>
                                        <dd class="fw-bold mb-0">
                                            {{ $maThanhToan }}
                                        </dd>
                                    </dl>
                                </div>
                            </details>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </section>
@endsection
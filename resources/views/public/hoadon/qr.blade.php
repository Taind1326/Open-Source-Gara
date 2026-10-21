@extends('public.layout')

@section('title', 'Thanh toán chuyển khoản')

@section('content')
    @php
        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?'
            . http_build_query([
                'size' => '240x240',
                'qzone' => 4,
                'data' => $maNoiDung,
            ]);
    @endphp

    <section class="section-space">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="feature-card h-auto">
                        <a
                            href="{{ route('hoadon.show', $hoaDon->MaHD) }}"
                            class="btn btn-outline-primary mb-4"
                        >
                            Quay lại hóa đơn
                        </a>

                        <h1 class="h3 fw-bold mb-3">
                            Thanh toán chuyển khoản
                        </h1>

                        <div class="alert alert-info">
                            Chế độ demo đồ án, không chuyển tiền thật.
                        </div>

                        <div class="text-center my-4">
                            <img
                                src="{{ $qrUrl }}"
                                alt="Mã QR mô phỏng thanh toán"
                                width="240"
                                height="240"
                                class="img-fluid border rounded p-2"
                                referrerpolicy="no-referrer"
                            >
                        </div>

                        <p>
                            Hóa đơn:
                            <strong>#{{ $hoaDon->MaHD }}</strong>
                        </p>

                        <p>
                            Số tiền:
                            <strong class="text-primary">
                                {{ number_format($hoaDon->TongTien, 0, ',', '.') }} ₫
                            </strong>
                        </p>

                        <p class="text-break">
                            Nội dung demo:
                            <strong>{{ $maNoiDung }}</strong>
                        </p>

                        <p class="small text-muted">
                            Quét QR chỉ đọc nội dung demo.
                            Bấm nút bên dưới để mô phỏng giao dịch thành công.
                        </p>

                        <form
                            method="POST"
                            action="{{ route('hoadon.payment.demo', $hoaDon->MaHD) }}"
                            novalidate
                            data-confirm-title="Mô phỏng thanh toán"
                            data-confirm="Mô phỏng thanh toán thành công cho hóa đơn này? Không phát sinh chuyển tiền thật."
                            data-confirm-accept="Mô phỏng thành công"
                        >
                            @csrf

                            <input
                                type="hidden"
                                name="MaTT"
                                value="{{ $giaoDich?->MaTT ?? 0 }}"
                            >

                            <div class="form-check my-3">
                                <input
                                    type="checkbox"
                                    id="confirm-demo"
                                    name="XacNhan"
                                    value="1"
                                    class="form-check-input @error('XacNhan') is-invalid @enderror"
                                    required
                                    @checked(old('XacNhan'))
                                >

                                <label class="form-check-label" for="confirm-demo">
                                    Tôi xác nhận thực hiện giao dịch mô phỏng.
                                </label>

                                @error('XacNhan')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                Mô phỏng thanh toán thành công
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
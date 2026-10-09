<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Chi tiết hóa đơn
    </title>

    @vite('resources/css/hoadon.css')

</head>


<body>

<div class="container">


    <div class="page-header">

        <h1 class="page-title">
            Chi tiết hóa đơn #{{ $hoaDon->MaHD }}
        </h1>


        <a
            href="{{ route(
                'hoadon.index',
                ['maTK' => $hoaDon->MaTK]
            ) }}"
            class="btn btn-back"
        >
            Quay lại
        </a>

    </div>


    @if(session('success'))

        <div class="message message-success">
            {{ session('success') }}
        </div>

    @endif


    @if($errors->any())

        <div class="message message-error">

            @foreach($errors->all() as $error)

                <div>
                    {{ $error }}
                </div>

            @endforeach

        </div>

    @endif


    {{-- =====================================================
         THÔNG TIN HÓA ĐƠN
    ====================================================== --}}

    <div class="section">

        <h2 class="section-title">
            Thông tin hóa đơn
        </h2>


        <div class="info-grid">

            <div class="info-item">

                <span>
                    Mã hóa đơn
                </span>

                <strong>
                    #{{ $hoaDon->MaHD }}
                </strong>

            </div>


            <div class="info-item">

                <span>
                    Mã phiếu sửa chữa
                </span>

                <strong>
                    #{{ $hoaDon->MaPSC }}
                </strong>

            </div>


            <div class="info-item">

                <span>
                    Mã yêu cầu
                </span>

                <strong>
                    #{{ $hoaDon->MaYC }}
                </strong>

            </div>


            <div class="info-item">

                <span>
                    Khách hàng
                </span>

                <strong>
                    {{ $hoaDon->HoTen }}
                </strong>

            </div>


            <div class="info-item">

                <span>
                    Biển số
                </span>

                <strong>
                    {{ $hoaDon->BienSo }}
                </strong>

            </div>


            <div class="info-item">

                <span>
                    Hãng xe
                </span>

                <strong>
                    {{ $hoaDon->HangXe }}
                </strong>

            </div>


            <div class="info-item">

                <span>
                    Dòng xe
                </span>

                <strong>
                    {{ $hoaDon->DongXe }}
                </strong>

            </div>


            <div class="info-item">

                <span>
                    Điểm tích lũy
                </span>

                <strong>

                    {{ number_format(
                        $hoaDon->DiemTichLuy,
                        0,
                        ',',
                        '.'
                    ) }}

                    điểm

                </strong>

            </div>

        </div>

    </div>


    {{-- =====================================================
         CHI TIẾT SỬA CHỮA
    ====================================================== --}}

    <div class="section">

        <h2 class="section-title">
            Chi tiết sửa chữa
        </h2>


        @if($chiTiet->count() > 0)

            <div class="table-wrapper">

                <table class="hoadon-table">

                    <thead>

                        <tr>

                            <th>
                                Loại
                            </th>

                            <th>
                                Tên
                            </th>

                            <th>
                                Số lượng
                            </th>

                            <th>
                                Đơn giá
                            </th>

                            <th>
                                Thành tiền
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @foreach($chiTiet as $item)

                        <tr>

                            <td>

                                @if($item->MaDV)

                                    Dịch vụ

                                @else

                                    Phụ tùng

                                @endif

                            </td>


                            <td>

                                @if($item->MaDV)

                                    {{ $item->TenDV }}

                                @else

                                    {{ $item->TenPT }}

                                @endif

                            </td>


                            <td>
                                {{ $item->SoLuong }}
                            </td>


                            <td>

                                {{ number_format(
                                    $item->DonGia,
                                    0,
                                    ',',
                                    '.'
                                ) }} đ

                            </td>


                            <td class="money">

                                {{ number_format(
                                    $item->SoLuong *
                                    $item->DonGia,
                                    0,
                                    ',',
                                    '.'
                                ) }} đ

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty">
                Không có chi tiết sửa chữa.
            </div>

        @endif

    </div>


    {{-- =====================================================
         THANH TOÁN
    ====================================================== --}}

    <div class="section">

        <h2 class="section-title">
            Thanh toán
        </h2>


        <div class="payment-summary">


            <div class="summary-row">

                <span>
                    Tổng tiền gốc
                </span>

                <strong>

                    {{ number_format(
                        $hoaDon->TongTienGoc,
                        0,
                        ',',
                        '.'
                    ) }} đ

                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Điểm đã dùng
                </span>

                <strong>

                    {{ number_format(
                        $hoaDon->DiemDaDung,
                        0,
                        ',',
                        '.'
                    ) }}

                    điểm

                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Phần trăm giảm
                </span>

                <strong>

                    {{ number_format(
                        $hoaDon->PhanTramGiam,
                        0
                    ) }}%

                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Số tiền giảm
                </span>

                <strong>

                    {{ number_format(
                        $hoaDon->SoTienGiam,
                        0,
                        ',',
                        '.'
                    ) }} đ

                </strong>

            </div>


            <div class="summary-row summary-total">

                <span>
                    Tổng thanh toán
                </span>

                <strong>

                    {{ number_format(
                        $hoaDon->TongThanhToan,
                        0,
                        ',',
                        '.'
                    ) }} đ

                </strong>

            </div>

        </div>


        @if(
            $hoaDon->TrangThaiThanhToan
            === 'CHUA_THANH_TOAN'
        )


            {{-- =================================================
                 ÁP DỤNG / BỎ ĐIỂM
            ================================================== --}}

            @if($hoaDon->DiemDaDung == 0)

                <div class="payment-box">

                    <h3>
                        Sử dụng điểm
                    </h3>


                    <p class="payment-note">

                        Điểm hiện có:

                        <strong>

                            {{ number_format(
                                $hoaDon->DiemTichLuy,
                                0,
                                ',',
                                '.'
                            ) }}

                        </strong>

                        điểm.

                        <br>

                        Điểm chỉ bị trừ
                        khi thanh toán thành công.

                    </p>


                    @if($hoaDon->DiemTichLuy >= 500)

                        <form
                            method="POST"
                            action="{{ route(
                                'hoadon.ap-dung-diem',
                                $hoaDon->MaHD
                            ) }}"
                        >

                            @csrf


                            @if(app()->environment('local'))

                                <input
                                    type="hidden"
                                    name="maTK"
                                    value="{{ $hoaDon->MaTK }}"
                                >

                            @endif


                            <div class="point-options">


                                {{-- 500 ĐIỂM --}}

                                <label class="point-option">

                                    <input
                                        type="radio"
                                        name="diem"
                                        value="500"
                                        checked
                                    >

                                    <span>
                                        Dùng 500 điểm -
                                        giảm 10%
                                    </span>

                                </label>


                                {{-- 1000 ĐIỂM --}}

                                @if(
                                    $hoaDon->DiemTichLuy
                                    >= 1000
                                )

                                    <label class="point-option">

                                        <input
                                            type="radio"
                                            name="diem"
                                            value="1000"
                                        >

                                        <span>
                                            Dùng 1000 điểm -
                                            giảm 15%
                                        </span>

                                    </label>

                                @endif


                            </div>


                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Áp dụng điểm
                            </button>

                        </form>


                    @else

                        <div class="empty small-empty">

                            Bạn chưa đủ 500 điểm
                            để được giảm giá.

                        </div>

                    @endif

                </div>


            @else


                {{-- ĐÃ ÁP DỤNG ĐIỂM --}}

                <div class="payment-box">

                    <div class="message message-success">

                        Hóa đơn đã áp dụng

                        <strong>
                            {{ $hoaDon->DiemDaDung }}
                        </strong>

                        điểm,

                        giảm

                        <strong>
                            {{ $hoaDon->PhanTramGiam }}%
                        </strong>.

                    </div>


                    <form
                        method="POST"
                        action="{{ route(
                            'hoadon.bo-diem',
                            $hoaDon->MaHD
                        ) }}"
                    >

                        @csrf


                        @if(app()->environment('local'))

                            <input
                                type="hidden"
                                name="maTK"
                                value="{{ $hoaDon->MaTK }}"
                            >

                        @endif


                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            Bỏ sử dụng điểm
                        </button>

                    </form>

                </div>

            @endif


            {{-- =================================================
                 PHƯƠNG THỨC THANH TOÁN
            ================================================== --}}

            <div class="payment-box">

                <h3>
                    Phương thức thanh toán
                </h3>


                <form
                    method="POST"
                    action="{{ route(
                        'hoadon.thanh-toan',
                        $hoaDon->MaHD
                    ) }}"
                >

                    @csrf


                    @if(app()->environment('local'))

                        <input
                            type="hidden"
                            name="maTK"
                            value="{{ $hoaDon->MaTK }}"
                        >

                    @endif


                    <div class="payment-methods">


                        <label class="payment-method">

                            <input
                                type="radio"
                                name="PhuongThucThanhToan"
                                value="TIEN_MAT"
                                id="phuongThucTienMat"
                                checked
                            >

                            <span>
                                Tiền mặt
                            </span>

                        </label>


                        <label class="payment-method">

                            <input
                                type="radio"
                                name="PhuongThucThanhToan"
                                value="CHUYEN_KHOAN_QR"
                                id="phuongThucQR"
                            >

                            <span>
                                Chuyển khoản QR
                            </span>

                        </label>


                    </div>


                    @php

                        $maThanhToan =
                            'HD' .
                            str_pad(
                                $hoaDon->MaHD,
                                6,
                                '0',
                                STR_PAD_LEFT
                            );

                        $soTien =
                            (int) round(
                                $hoaDon->TongThanhToan
                            );

                        $qrUrl =
                            'https://img.vietqr.io/image/' .
                            '970436-1050497105-compact2.png' .
                            '?amount=' .
                            $soTien .
                            '&addInfo=' .
                            urlencode(
                                $maThanhToan
                            ) .
                            '&accountName=' .
                            urlencode(
                                'NGUYEN HOAI PHU'
                            );

                    @endphp


                    {{-- QR --}}

                    <div
                        id="qrPaymentBox"
                        class="qr-payment-box"
                    >


                        <div class="qr-title">
                            Quét mã QR để thanh toán
                        </div>


                        <div class="qr-subtitle">
                            Vietcombank
                        </div>


                        <div class="qr-countdown-box">

                            <div class="qr-countdown-label">
                                Thời gian thanh toán còn lại
                            </div>


                            <div
                                id="qrCountdown"
                                class="qr-countdown"
                            >
                                05:00
                            </div>

                        </div>


                        <img
                            src="{{ $qrUrl }}"
                            alt="QR thanh toán hóa đơn"
                            class="qr-image"
                        >


                        <div class="qr-info">


                            <div class="qr-info-row">

                                <span>
                                    Ngân hàng
                                </span>

                                <strong>
                                    Vietcombank
                                </strong>

                            </div>


                            <div class="qr-info-row">

                                <span>
                                    Số tài khoản
                                </span>

                                <strong>
                                    1050497105
                                </strong>

                            </div>


                            <div class="qr-info-row">

                                <span>
                                    Người nhận
                                </span>

                                <strong>
                                    NGUYEN HOAI PHU
                                </strong>

                            </div>


                            <div class="qr-info-row">

                                <span>
                                    Số tiền
                                </span>

                                <strong class="qr-money">

                                    {{ number_format(
                                        $hoaDon->TongThanhToan,
                                        0,
                                        ',',
                                        '.'
                                    ) }} đ

                                </strong>

                            </div>


                            <div class="qr-info-row">

                                <span>
                                    Mã thanh toán
                                </span>

                                <strong>
                                    {{ $maThanhToan }}
                                </strong>

                            </div>


                        </div>


                        <div
                            id="qrExpiredMessage"
                            class="qr-expired-message"
                        >

                            Mã QR đã hết hạn.

                            <br>

                            Vui lòng chọn lại
                            "Chuyển khoản QR"
                            để tạo phiên mới.

                        </div>


                        <div class="qr-note">

                            Vui lòng hoàn tất thanh toán
                            trong 5 phút.

                            <br>

                            Đây là thanh toán demo
                            của hệ thống.

                        </div>


                    </div>


                    <button
                        type="submit"
                        class="btn btn-success btn-payment"
                        id="btnXacNhanThanhToan"
                    >
                        Xác nhận thanh toán
                    </button>


                </form>

            </div>


        @else


            {{-- =================================================
                 ĐÃ THANH TOÁN
            ================================================== --}}

            <div class="paid-box">

                <div class="paid-title">
                    Thanh toán thành công
                </div>


                <div>

                    Phương thức:

                    @if(
                        $hoaDon->PhuongThucThanhToan
                        === 'TIEN_MAT'
                    )

                        Tiền mặt

                    @else

                        Chuyển khoản QR

                    @endif

                </div>


                <div>

                    Ngày thanh toán:

                    @if($hoaDon->NgayThanhToan)

                        {{ date(
                            'd/m/Y H:i',
                            strtotime(
                                $hoaDon->NgayThanhToan
                            )
                        ) }}

                    @else

                        -

                    @endif

                </div>

            </div>

        @endif

    </div>

</div>


<script>

    const radioQR =
        document.getElementById(
            'phuongThucQR'
        );

    const radioTienMat =
        document.getElementById(
            'phuongThucTienMat'
        );

    const qrPaymentBox =
        document.getElementById(
            'qrPaymentBox'
        );

    const qrCountdown =
        document.getElementById(
            'qrCountdown'
        );

    const qrExpiredMessage =
        document.getElementById(
            'qrExpiredMessage'
        );

    const btnXacNhanThanhToan =
        document.getElementById(
            'btnXacNhanThanhToan'
        );

    const maHoaDon =
        '{{ $hoaDon->MaHD }}';

    const storageKey =
        'qr_payment_expiry_' +
        maHoaDon;

    let countdownInterval =
        null;


    function formatThoiGian(
        soGiay
    ) {

        const phut =
            Math.floor(
                soGiay / 60
            );

        const giay =
            soGiay % 60;

        return String(phut)
            .padStart(2, '0')
            + ':'
            + String(giay)
                .padStart(2, '0');
    }


    function taoPhienMoi() {

        const thoiGianHetHan =
            Date.now()
            + (5 * 60 * 1000);

        localStorage.setItem(
            storageKey,
            thoiGianHetHan
        );

        return thoiGianHetHan;
    }


    function hetHanQR() {

        clearInterval(
            countdownInterval
        );

        qrCountdown.textContent =
            '00:00';

        qrCountdown.classList.add(
            'expired'
        );

        qrExpiredMessage.style.display =
            'block';

        btnXacNhanThanhToan.disabled =
            true;

        btnXacNhanThanhToan.classList.add(
            'btn-disabled'
        );
    }


    function batDauDemNguoc() {

        clearInterval(
            countdownInterval
        );


        let thoiGianHetHan =
            parseInt(
                localStorage.getItem(
                    storageKey
                )
            );


        if (
            !thoiGianHetHan
            ||
            thoiGianHetHan <= Date.now()
        ) {

            thoiGianHetHan =
                taoPhienMoi();

        }


        function capNhat() {

            const conLai =
                Math.floor(
                    (
                        thoiGianHetHan
                        -
                        Date.now()
                    )
                    / 1000
                );


            if (conLai <= 0) {

                hetHanQR();

                return;
            }


            qrCountdown.textContent =
                formatThoiGian(
                    conLai
                );

            qrCountdown.classList.remove(
                'expired'
            );

            qrExpiredMessage.style.display =
                'none';

            btnXacNhanThanhToan.disabled =
                false;

            btnXacNhanThanhToan.classList.remove(
                'btn-disabled'
            );

        }


        capNhat();


        countdownInterval =
            setInterval(
                capNhat,
                1000
            );
    }


    function hienThiQR() {

        if (radioQR.checked) {

            qrPaymentBox.style.display =
                'block';

            batDauDemNguoc();

        } else {

            qrPaymentBox.style.display =
                'none';

            clearInterval(
                countdownInterval
            );

            btnXacNhanThanhToan.disabled =
                false;

            btnXacNhanThanhToan.classList.remove(
                'btn-disabled'
            );

        }
    }


    radioQR.addEventListener(
        'change',
        hienThiQR
    );


    radioTienMat.addEventListener(
        'change',
        hienThiQR
    );


    hienThiQR();

</script>

</body>

</html>
<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Thống kê
    </title>

    @vite('resources/css/thongke.css')

</head>


<body>

<div class="container">


    <div class="page-header">

        <div>

            <h1 class="page-title">
                Thống kê
            </h1>

            <p class="page-description">
                Thống kê hoạt động Garage
            </p>

        </div>


        <a
            href="{{ url('/') }}"
            class="btn btn-back"
        >
            Trang chủ
        </a>

    </div>


    {{-- =================================================
         BỘ LỌC
    ================================================== --}}

    <div class="section">

        <h2 class="section-title">
            Khoảng thời gian thống kê
        </h2>


        <div
            id="dateMessage"
            class="date-message"
            style="display: none;"
        ></div>


        <form
            method="GET"
            action="{{ route('thongke.admin.index') }}"
            class="filter-form"
            id="filterForm"
        >


            {{-- =========================
                 TỪ NGÀY
            ========================== --}}

            <div class="filter-item">

                <label>
                    Từ ngày
                </label>


                <div class="date-picker-group">

                    <input
                        type="text"
                        id="tu_ngay_hien_thi"
                        class="date-text"
                        value="{{ \Carbon\Carbon::parse($tuNgay)->format('d/m/Y') }}"
                        placeholder="dd/mm/yyyy"
                        maxlength="10"
                        autocomplete="off"
                    >


                    <button
                        type="button"
                        class="calendar-button"
                        id="btnTuNgay"
                        title="Chọn ngày"
                    >
                        📅
                    </button>


                    <input
                        type="date"
                        id="tu_ngay"
                        name="tu_ngay"
                        value="{{ $tuNgay }}"
                        class="hidden-date-input"
                    >

                </div>

            </div>


            {{-- =========================
                 ĐẾN NGÀY
            ========================== --}}

            <div class="filter-item">

                <label>
                    Đến ngày
                </label>


                <div class="date-picker-group">

                    <input
                        type="text"
                        id="den_ngay_hien_thi"
                        class="date-text"
                        value="{{ \Carbon\Carbon::parse($denNgay)->format('d/m/Y') }}"
                        placeholder="dd/mm/yyyy"
                        maxlength="10"
                        autocomplete="off"
                    >


                    <button
                        type="button"
                        class="calendar-button"
                        id="btnDenNgay"
                        title="Chọn ngày"
                    >
                        📅
                    </button>


                    <input
                        type="date"
                        id="den_ngay"
                        name="den_ngay"
                        value="{{ $denNgay }}"
                        class="hidden-date-input"
                    >

                </div>

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Xem thống kê
            </button>

        </form>

    </div>


    {{-- =================================================
         TỔNG QUAN
    ================================================== --}}

    <div class="stats-grid">


        <div class="stat-card">

            <div class="stat-title">
                Yêu cầu sửa chữa
            </div>

            <div class="stat-value">

                {{ number_format(
                    $tongYeuCau,
                    0,
                    ',',
                    '.'
                ) }}

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Doanh thu
            </div>

            <div class="stat-value">

                {{ number_format(
                    $doanhThu,
                    0,
                    ',',
                    '.'
                ) }} đ

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Hóa đơn đã thanh toán
            </div>

            <div class="stat-value">

                {{ number_format(
                    $soHoaDonThanhToan,
                    0,
                    ',',
                    '.'
                ) }}

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Hóa đơn chưa thanh toán
            </div>

            <div class="stat-value">

                {{ number_format(
                    $soHoaDonChuaThanhToan,
                    0,
                    ',',
                    '.'
                ) }}

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Lượt sử dụng dịch vụ
            </div>

            <div class="stat-value">

                {{ number_format(
                    $tongLuotDichVu,
                    0,
                    ',',
                    '.'
                ) }}

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Lượt sử dụng phụ tùng
            </div>

            <div class="stat-value">

                {{ number_format(
                    $tongLuotPhuTung,
                    0,
                    ',',
                    '.'
                ) }}

            </div>

        </div>

    </div>


    {{-- =================================================
         DOANH THU THEO NGÀY
    ================================================== --}}

    <div class="section">

        <h2 class="section-title">
            Doanh thu theo ngày
        </h2>


        @if($doanhThuTheoNgay->count() > 0)

            @php

                $doanhThuLonNhat =
                    $doanhThuTheoNgay->max(
                        'DoanhThu'
                    );

            @endphp


            <div class="chart">

                @foreach(
                    $doanhThuTheoNgay
                    as $item
                )

                    @php

                        $phanTram =
                            $doanhThuLonNhat > 0
                                ? (
                                    $item->DoanhThu
                                    /
                                    $doanhThuLonNhat
                                ) * 100
                                : 0;

                    @endphp


                    <div class="chart-row">

                        <div class="chart-date">

                            {{
                                \Carbon\Carbon::parse(
                                    $item->Ngay
                                )->format('d/m/Y')
                            }}

                        </div>


                        <div class="chart-bar-wrapper">

                            <div
                                class="chart-bar"
                                style="width: {{ $phanTram }}%;"
                            ></div>

                        </div>


                        <div class="chart-money">

                            {{ number_format(
                                $item->DoanhThu,
                                0,
                                ',',
                                '.'
                            ) }} đ

                        </div>

                    </div>

                @endforeach

            </div>


        @else

            <div class="empty">
                Chưa có doanh thu trong khoảng thời gian này.
            </div>

        @endif

    </div>


    {{-- =================================================
         THỐNG KÊ DỊCH VỤ
    ================================================== --}}

    <div class="section">

        <h2 class="section-title">
            Thống kê dịch vụ
        </h2>


        @if($dichVu->count() > 0)

            <div class="table-wrapper">

                <table class="statistics-table">

                    <thead>

                        <tr>

                            <th>
                                STT
                            </th>

                            <th>
                                Tên dịch vụ
                            </th>

                            <th>
                                Số lượng
                            </th>

                            <th>
                                Thành tiền
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @foreach(
                        $dichVu as $index => $item
                    )

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $item->TenDV }}
                            </td>

                            <td>

                                {{ number_format(
                                    $item->SoLuong,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td class="money">

                                {{ number_format(
                                    $item->ThanhTien,
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
                Chưa có dữ liệu dịch vụ.
            </div>

        @endif

    </div>


    {{-- =================================================
         THỐNG KÊ PHỤ TÙNG
    ================================================== --}}

    <div class="section">

        <h2 class="section-title">
            Thống kê phụ tùng
        </h2>


        @if($phuTung->count() > 0)

            <div class="table-wrapper">

                <table class="statistics-table">

                    <thead>

                        <tr>

                            <th>
                                STT
                            </th>

                            <th>
                                Tên phụ tùng
                            </th>

                            <th>
                                Số lượng
                            </th>

                            <th>
                                Thành tiền
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @foreach(
                        $phuTung as $index => $item
                    )

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $item->TenPT }}
                            </td>

                            <td>

                                {{ number_format(
                                    $item->SoLuong,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td class="money">

                                {{ number_format(
                                    $item->ThanhTien,
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
                Chưa có dữ liệu phụ tùng.
            </div>

        @endif

    </div>

</div>


<script>

    const filterForm =
        document.getElementById(
            'filterForm'
        );


    const dateMessage =
        document.getElementById(
            'dateMessage'
        );


    const inputTuNgay =
        document.getElementById(
            'tu_ngay'
        );


    const inputDenNgay =
        document.getElementById(
            'den_ngay'
        );


    const textTuNgay =
        document.getElementById(
            'tu_ngay_hien_thi'
        );


    const textDenNgay =
        document.getElementById(
            'den_ngay_hien_thi'
        );


    const btnTuNgay =
        document.getElementById(
            'btnTuNgay'
        );


    const btnDenNgay =
        document.getElementById(
            'btnDenNgay'
        );


    function hienThiMessage(
        noiDung
    ) {

        dateMessage.textContent =
            noiDung;

        dateMessage.style.display =
            'block';

    }


    function anMessage() {

        dateMessage.textContent =
            '';

        dateMessage.style.display =
            'none';

    }


    function formatNgay(
        value
    ) {

        if (!value) {
            return '';
        }


        const parts =
            value.split('-');


        if (parts.length !== 3) {
            return '';
        }


        return (
            parts[2]
            + '/'
            + parts[1]
            + '/'
            + parts[0]
        );

    }


    function parseNgay(
        value
    ) {

        const parts =
            value
                .trim()
                .split('/');


        if (parts.length !== 3) {
            return null;
        }


        const ngayText =
            parts[0];

        const thangText =
            parts[1];

        const namText =
            parts[2];


        if (
            ngayText.length !== 2 ||
            thangText.length !== 2 ||
            namText.length !== 4
        ) {

            return null;

        }


        const ngay =
            parseInt(
                ngayText,
                10
            );


        const thang =
            parseInt(
                thangText,
                10
            );


        const nam =
            parseInt(
                namText,
                10
            );


        if (
            Number.isNaN(ngay) ||
            Number.isNaN(thang) ||
            Number.isNaN(nam)
        ) {

            return null;

        }


        if (
            ngay < 1 ||
            ngay > 31
        ) {

            return null;

        }


        if (
            thang < 1 ||
            thang > 12
        ) {

            return null;

        }


        if (
            nam < 1900 ||
            nam > 2100
        ) {

            return null;

        }


        const testDate =
            new Date(
                nam,
                thang - 1,
                ngay
            );


        if (
            testDate.getFullYear()
                !== nam
            ||
            testDate.getMonth()
                !== thang - 1
            ||
            testDate.getDate()
                !== ngay
        ) {

            return null;

        }


        return (
            nam
            + '-'
            + String(thang)
                .padStart(2, '0')
            + '-'
            + String(ngay)
                .padStart(2, '0')
        );

    }


    function moLich(
        input
    ) {

        if (
            typeof input.showPicker
            === 'function'
        ) {

            input.showPicker();

        } else {

            input.focus();

            input.click();

        }

    }


    btnTuNgay.addEventListener(
        'click',
        function () {

            moLich(
                inputTuNgay
            );

        }
    );


    btnDenNgay.addEventListener(
        'click',
        function () {

            moLich(
                inputDenNgay
            );

        }
    );


    inputTuNgay.addEventListener(
        'change',
        function () {

            textTuNgay.value =
                formatNgay(
                    this.value
                );

            textTuNgay.dispatchEvent(
                new Event('change')
            );

            anMessage();

        }
    );


    inputDenNgay.addEventListener(
        'change',
        function () {

            textDenNgay.value =
                formatNgay(
                    this.value
                );

            textDenNgay.dispatchEvent(
                new Event('change')
            );

            anMessage();

        }
    );


    textTuNgay.addEventListener(
        'input',
        function () {

            this.value =
                this.value.replace(
                    /\D/g,
                    ''
                );


            if (
                this.value.length > 8
            ) {

                this.value =
                    this.value.substring(
                        0,
                        8
                    );

            }


            if (
                this.value.length >= 5
            ) {

                this.value =
                    this.value.substring(
                        0,
                        2
                    )
                    + '/'
                    + this.value.substring(
                        2,
                        4
                    )
                    + '/'
                    + this.value.substring(
                        4
                    );

            } else if (
                this.value.length >= 3
            ) {

                this.value =
                    this.value.substring(
                        0,
                        2
                    )
                    + '/'
                    + this.value.substring(
                        2
                    );

            }

        }
    );


    textDenNgay.addEventListener(
        'input',
        function () {

            this.value =
                this.value.replace(
                    /\D/g,
                    ''
                );


            if (
                this.value.length > 8
            ) {

                this.value =
                    this.value.substring(
                        0,
                        8
                    );

            }


            if (
                this.value.length >= 5
            ) {

                this.value =
                    this.value.substring(
                        0,
                        2
                    )
                    + '/'
                    + this.value.substring(
                        2,
                        4
                    )
                    + '/'
                    + this.value.substring(
                        4
                    );

            } else if (
                this.value.length >= 3
            ) {

                this.value =
                    this.value.substring(
                        0,
                        2
                    )
                    + '/'
                    + this.value.substring(
                        2
                    );

            }

        }
    );


    filterForm.addEventListener(
        'submit',
        function (event) {

            anMessage();


            const tuNgay =
                parseNgay(
                    textTuNgay.value
                );


            const denNgay =
                parseNgay(
                    textDenNgay.value
                );


            if (
                !tuNgay ||
                !denNgay
            ) {

                event.preventDefault();

                hienThiMessage(
                    'Vui lòng nhập ngày theo đúng dạng dd/mm/yyyy.'
                );

                return;

            }


            if (
                tuNgay > denNgay
            ) {

                event.preventDefault();

                hienThiMessage(
                    'Từ ngày không được lớn hơn đến ngày.'
                );

                return;

            }


            inputTuNgay.value =
                tuNgay;


            inputDenNgay.value =
                denNgay;

        }
    );


    textTuNgay.addEventListener(
        'blur',
        function () {

            const value =
                parseNgay(
                    this.value
                );


            if (
                this.value !== ''
                &&
                !value
            ) {

                hienThiMessage(
                    'Ngày bắt đầu không hợp lệ. Vui lòng nhập theo dạng dd/mm/yyyy.'
                );

            }

        }
    );


    textDenNgay.addEventListener(
        'blur',
        function () {

            const value =
                parseNgay(
                    this.value
                );


            if (
                this.value !== ''
                &&
                !value
            ) {

                hienThiMessage(
                    'Ngày kết thúc không hợp lệ. Vui lòng nhập theo dạng dd/mm/yyyy.'
                );

            }

        }
    );

</script>


</body>

</html>
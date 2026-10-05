<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Quản lý hóa đơn</title>

    @vite('resources/css/hoadon.css')

</head>


<body>

<div class="container">


    <h1 class="page-title">
        Quản lý hóa đơn
    </h1>


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
         PHIẾU CHƯA LẬP HÓA ĐƠN
    ====================================================== --}}

    <div class="section">

        <h2 class="section-title">
            Phiếu sửa chữa chưa lập hóa đơn
        </h2>


        @if($phieuChuaLap->count() > 0)

            <div class="table-wrapper">

                <table class="hoadon-table">

                    <thead>

                        <tr>

                            <th>
                                Mã phiếu
                            </th>

                            <th>
                                Mã yêu cầu
                            </th>

                            <th>
                                Khách hàng
                            </th>

                            <th>
                                Biển số
                            </th>

                            <th>
                                Ngày hoàn thành
                            </th>

                            <th>
                                Thao tác
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @foreach($phieuChuaLap as $phieu)

                        <tr>

                            <td>
                                {{ $phieu->MaPSC }}
                            </td>

                            <td>
                                {{ $phieu->MaYC }}
                            </td>

                            <td>
                                {{ $phieu->HoTen }}
                            </td>

                            <td>
                                {{ $phieu->BienSo }}
                            </td>

                            <td>

                                @if($phieu->NgayHoanThanh)

                                    {{ date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $phieu->NgayHoanThanh
                                        )
                                    ) }}

                                @else

                                    -

                                @endif

                            </td>

                            <td>

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'hoadon.admin.tao',
                                        $phieu->MaPSC
                                    ) }}"
                                    class="form-inline"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-create"
                                    >
                                        Lập hóa đơn
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty">
                Không có phiếu sửa chữa nào đang chờ lập hóa đơn.
            </div>

        @endif

    </div>


    {{-- =====================================================
         DANH SÁCH HÓA ĐƠN
    ====================================================== --}}

    <div class="section">

        <h2 class="section-title">
            Danh sách hóa đơn
        </h2>


        @if($danhSach->count() > 0)

            <div class="table-wrapper">

                <table class="hoadon-table">

                    <thead>

                        <tr>

                            <th>
                                Mã HĐ
                            </th>

                            <th>
                                Mã PSC
                            </th>

                            <th>
                                Khách hàng
                            </th>

                            <th>
                                Biển số
                            </th>

                            <th>
                                Tổng tiền
                            </th>

                            <th>
                                Trạng thái
                            </th>

                            <th>
                                Thao tác
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @foreach($danhSach as $hoaDon)

                        <tr>

                            <td>
                                {{ $hoaDon->MaHD }}
                            </td>

                            <td>
                                {{ $hoaDon->MaPSC }}
                            </td>

                            <td>
                                {{ $hoaDon->HoTen }}
                            </td>

                            <td>
                                {{ $hoaDon->BienSo }}
                            </td>

                            <td class="money">

                                {{ number_format(
                                    $hoaDon->TongThanhToan,
                                    0,
                                    ',',
                                    '.'
                                ) }} đ

                            </td>

                            <td>

                                @if(
                                    $hoaDon->TrangThaiThanhToan
                                    === 'DA_THANH_TOAN'
                                )

                                    <span class="status-paid">
                                        Đã thanh toán
                                    </span>

                                @else

                                    <span class="status-unpaid">
                                        Chưa thanh toán
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a
                                    href="{{ route(
                                        'hoadon.show',
                                        [
                                            'maHD' =>
                                                $hoaDon->MaHD,
                                            'maTK' =>
                                                $hoaDon->MaTK
                                        ]
                                    ) }}"
                                    class="btn btn-view"
                                >
                                    Xem
                                </a>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="empty">
                Chưa có hóa đơn nào.
            </div>

        @endif

    </div>

</div>

</body>

</html>
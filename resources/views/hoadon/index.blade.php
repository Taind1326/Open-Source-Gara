<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hóa đơn của tôi</title>

    @vite('resources/css/hoadon.css')
</head>

<body>

<div class="container">

    <h1 class="page-title">
        Hóa đơn của tôi
    </h1>


    @if(session('success'))
        <div class="message message-success">
            {{ session('success') }}
        </div>
    @endif


    @if($errors->any())
        <div class="message message-error">

            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach

        </div>
    @endif


    @if(isset($thieuTaiKhoan) && $thieuTaiKhoan)

        <div class="message message-error">
            Không xác định được tài khoản khách hàng.
        </div>

        @if(app()->environment('local'))

            <div class="test-account">
                <strong>Đang chạy môi trường test.</strong>
                <br>
                Tài khoản test hiện có mã:
                <strong>1</strong>
                <br>
                Mở:
                <strong>/hoadon?maTK=1</strong>
                để kiểm tra hóa đơn của tài khoản test.
            </div>

        @endif

    @else

        <div class="section">

            <h2 class="section-title">
                Danh sách hóa đơn
            </h2>

            @if($danhSach->count() > 0)

                <div class="table-wrapper">

                    <table class="hoadon-table">

                        <thead>

                            <tr>
                                <th>Mã HĐ</th>
                                <th>Mã PSC</th>
                                <th>Biển số</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
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
                                    {{ $hoaDon->BienSo }}
                                </td>

                                <td class="money">
                                    {{ number_format($hoaDon->TongThanhToan, 0, ',', '.') }} đ
                                </td>

                                <td>

                                    @if($hoaDon->TrangThaiThanhToan === 'DA_THANH_TOAN')

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
                                        href="{{ route('hoadon.show', [
                                            'maHD' => $hoaDon->MaHD,
                                            'maTK' => request('maTK')
                                        ]) }}"
                                        class="btn btn-view"
                                    >
                                        Xem chi tiết
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="empty">
                    Bạn chưa có hóa đơn nào.
                </div>

            @endif

        </div>

    @endif

</div>

</body>
</html>
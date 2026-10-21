<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Hóa đơn #{{ $hoaDon->MaHD }}</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #f1f3f5;
            color: #10243a;
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }

        .toolbar, .invoice {
            max-width: 900px;
            margin: 24px auto;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding: 0 20px;
        }

        .toolbar button, .toolbar a {
            padding: 10px 18px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
            color: #10243a;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
        }

        .toolbar button {
            background: #f56616;
            border-color: #f56616;
            color: white;
        }

        .invoice {
            padding: 36px;
            background: white;
        }

        header {
            border-bottom: 2px solid #f56616;
            padding-bottom: 20px;
            margin-bottom: 24px;
        }

        h1 { margin: 8px 0; font-size: 25px; }
        h2 { margin: 24px 0 12px; font-size: 17px; }
        p { margin: 5px 0; }
        .brand { color: #d94f00; font-size: 22px; font-weight: bold; }
        .paid { color: #167146; font-weight: bold; }
        .muted { color: #5f6c7b; font-size: 13px; }
        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            margin-top: 24px;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }

        th { background: #fff4eb; }
        .number { text-align: right; white-space: nowrap; }
        .totals { margin: 24px 0; text-align: right; }
        .grand-total { font-size: 20px; font-weight: bold; }

        .signatures {
            display: flex;
            justify-content: space-around;
            gap: 24px;
            min-height: 110px;
            margin-top: 40px;
            text-align: center;
        }

        @page { size: A4; margin: 15mm; }

        @media print {
            body { background: white; color: black; }
            .toolbar { display: none; }
            .invoice { margin: 0; padding: 0; max-width: none; }
            .table-wrap { overflow: visible; }
            thead { display: table-header-group; }
            tr, .totals, .signatures { break-inside: avoid; }
        }

        @media screen and (max-width: 600px) {
            .invoice { padding: 20px; }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">
            In / Lưu PDF
        </button>

        <a href="{{ route('hoadon.show', $hoaDon->MaHD) }}">
            Quay lại hóa đơn
        </a>
    </div>

    <main class="invoice">
        <header>
            <div class="brand">AUTOCARE GARAGE</div>
            <h1>HÓA ĐƠN DỊCH VỤ #{{ $hoaDon->MaHD }}</h1>
            <p>
                Ngày lập:
                {{ \Carbon\Carbon::parse($hoaDon->NgayLap)->format('d/m/Y H:i') }}
            </p>
            <p class="paid">ĐÃ THANH TOÁN</p>
        </header>

        <p><strong>Khách hàng:</strong> {{ $hoaDon->HoTen }}</p>
        <p><strong>Điện thoại:</strong> {{ $hoaDon->SoDienThoai }}</p>
        <p>
            <strong>Xe:</strong>
            {{ $hoaDon->BienSo }}
            — {{ $hoaDon->HangXe }} {{ $hoaDon->DongXe }}
        </p>
        <p><strong>Lịch hẹn:</strong> #{{ $hoaDon->MaLH }}</p>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Hạng mục</th>
                        <th>SL / ĐVT</th>
                        <th class="number">Đơn giá</th>
                        <th class="number">Giảm</th>
                        <th class="number">Thành tiền</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($chiTiets as $chiTiet)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $chiTiet->TenHangMuc }}</td>
                            <td>
                                {{ $chiTiet->SoLuong }}
                                {{ $chiTiet->DonViTinh }}
                            </td>
                            <td class="number">
                                {{ number_format($chiTiet->DonGia, 0, ',', '.') }}
                            </td>
                            <td class="number">
                                {{ number_format($chiTiet->TienGiam, 0, ',', '.') }}
                            </td>
                            <td class="number">
                                {{ number_format($chiTiet->ThanhTien, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals">
            <p>
                Trước giảm:
                {{ number_format($hoaDon->TongTruocGiam, 0, ',', '.') }} ₫
            </p>
            <p>
                Giảm giá:
                {{ number_format($hoaDon->TienGiam, 0, ',', '.') }} ₫
            </p>
            <p class="grand-total">
                Đã thanh toán:
                {{ number_format($hoaDon->TongTien, 0, ',', '.') }} ₫
            </p>
        </div>

        @if($thanhToan)
            <p>
                <strong>Phương thức:</strong>
                {{ $thanhToan->PhuongThuc === 'TIEN_MAT' ? 'Tiền mặt' : 'Chuyển khoản' }}
            </p>

            <p>
                <strong>Thanh toán lúc:</strong>
                {{ \Carbon\Carbon::parse($thanhToan->NgayThanhToan)->format('d/m/Y H:i') }}
            </p>

            @if(\Illuminate\Support\Str::startsWith($thanhToan->GhiChu ?? '', 'DEMO:'))
                <p class="muted">
                    Giao dịch mô phỏng phục vụ đồ án, không thu tiền thật.
                </p>
            @endif
        @elseif((int) $hoaDon->TongTien === 0)
            <p>Hóa đơn không phát sinh tiền cần thu.</p>
        @endif

        @if($hoaDon->LoaiUuDai === 'DOI_DIEM')
            <p>Ưu đãi: dùng 50 điểm giảm 5% dịch vụ.</p>
        @elseif($hoaDon->LoaiUuDai === 'BAO_HANH')
            <p>Ưu đãi: giảm 30% dịch vụ đủ điều kiện bảo hành.</p>
        @endif

        @if($hoaDon->BaoHanhDen)
            <p>
                <strong>Bảo hành đến:</strong>
                {{ \Carbon\Carbon::parse($hoaDon->BaoHanhDen)->format('d/m/Y H:i') }}
            </p>
        @endif

        @if($hoaDon->GhiChuSuaChua)
            <h2>Lưu ý sau sửa chữa</h2>
            <p style="white-space: pre-line; overflow-wrap: anywhere;">{{ $hoaDon->GhiChuSuaChua }}</p>
        @endif

        <div class="signatures">
            <div>
                <strong>Khách hàng</strong>
                <p class="muted">(Ký, ghi rõ họ tên)</p>
            </div>
            <div>
                <strong>Đại diện gara</strong>
                <p class="muted">(Ký, ghi rõ họ tên)</p>
            </div>
        </div>
    </main>
</body>
</html>
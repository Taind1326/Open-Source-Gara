<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chi tiết báo giá</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    <div class="d-flex justify-content-between align-items-start mb-4">

        <div>
            <h3 class="mb-1">
                Chi tiết báo giá
            </h3>

            <div class="text-muted">
                Yêu cầu sửa chữa #{{ $yeuCau->MaYC }}
            </div>
        </div>

        <div>
            @if ($yeuCau->baoGia->TrangThai === 'CHO_DUYET')

                <span class="badge bg-warning text-dark fs-6">
                    Chờ khách hàng duyệt
                </span>

            @elseif ($yeuCau->baoGia->TrangThai === 'DA_DUYET')

                <span class="badge bg-success fs-6">
                    Đã duyệt
                </span>

            @elseif ($yeuCau->baoGia->TrangThai === 'TU_CHOI')

                <span class="badge bg-danger fs-6">
                    Đã từ chối
                </span>

            @else

                <span class="badge bg-secondary fs-6">
                    {{ $yeuCau->baoGia->TrangThai }}
                </span>

            @endif
        </div>

    </div>


    {{-- ================================================== --}}
    {{-- THÔNG TIN XE --}}
    {{-- ================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header fw-bold">
            Thông tin xe
        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">
                    <div class="text-muted small">
                        Biển số
                    </div>

                    <div class="fw-semibold">
                        {{ $yeuCau->xe->BienSo ?? 'Chưa có thông tin' }}
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="text-muted small">
                        Hãng xe
                    </div>

                    <div>
                        {{ $yeuCau->xe->HangXe ?? 'Chưa có thông tin' }}
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="text-muted small">
                        Dòng xe
                    </div>

                    <div>
                        {{ $yeuCau->xe->DongXe ?? 'Chưa có thông tin' }}
                    </div>
                </div>

            </div>

        </div>

    </div>


    {{-- ================================================== --}}
    {{-- THÔNG TIN BÁO GIÁ --}}
    {{-- ================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header fw-bold">
            Thông tin báo giá
        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-4">

                    <div class="text-muted small">
                        Mã báo giá
                    </div>

                    <div class="fw-semibold">
                        #{{ $yeuCau->baoGia->MaBG }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Ngày lập
                    </div>

                    <div>
                        {{ optional($yeuCau->baoGia->NgayTao)
                            ->format('d/m/Y H:i') }}
                    </div>

                </div>


                <div class="col-md-4">

                    <div class="text-muted small">
                        Tổng tiền
                    </div>

                    <div class="fw-bold fs-5">
                        {{ number_format(
                            $yeuCau->baoGia->TongTien,
                            0,
                            ',',
                            '.'
                        ) }}đ
                    </div>

                </div>

            </div>


            @if ($yeuCau->baoGia->GhiChu)

                <hr>

                <div class="text-muted small mb-1">
                    Ghi chú
                </div>

                <div>
                    {{ $yeuCau->baoGia->GhiChu }}
                </div>

            @endif

        </div>

    </div>


    {{-- ================================================== --}}
    {{-- CHI TIẾT BÁO GIÁ --}}
    {{-- ================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header fw-bold">
            Chi tiết báo giá
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th>Nội dung</th>
                            <th>Loại</th>

                            <th class="text-center">
                                Số lượng
                            </th>

                            <th class="text-end">
                                Đơn giá
                            </th>

                            <th class="text-end">
                                Thành tiền
                            </th>
                        </tr>

                    </thead>


                    <tbody>

                    @forelse ($yeuCau->baoGia->chiTietBaoGias as $chiTiet)

                        @php

                            if ($chiTiet->MaDV) {
                                $ten = $chiTiet->dichVu?->TenDV;
                                $loai = 'Dịch vụ';
                            } else {
                                $ten = $chiTiet->phuTung?->TenPT;
                                $loai = 'Phụ tùng';
                            }

                            $thanhTien =
                                $chiTiet->DonGia
                                * $chiTiet->SoLuong;

                        @endphp


                        <tr>

                            <td class="fw-semibold">

                                {{ $ten ?? 'Không xác định' }}

                            </td>


                            <td>

                                @if ($loai === 'Dịch vụ')

                                    <span class="badge bg-primary">
                                        Dịch vụ
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Phụ tùng
                                    </span>

                                @endif

                            </td>


                            <td class="text-center">

                                {{ $chiTiet->SoLuong }}

                            </td>


                            <td class="text-end">

                                {{ number_format(
                                    $chiTiet->DonGia,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ

                            </td>


                            <td class="text-end fw-semibold">

                                {{ number_format(
                                    $thanhTien,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center text-muted py-4"
                            >
                                Báo giá chưa có chi tiết.
                            </td>

                        </tr>

                    @endforelse

                    </tbody>


                    <tfoot class="table-light">

                        <tr>

                            <td
                                colspan="4"
                                class="text-end fw-bold"
                            >
                                Tổng cộng
                            </td>

                            <td class="text-end fw-bold fs-5">

                                {{ number_format(
                                    $yeuCau->baoGia->TongTien,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ

                            </td>

                        </tr>

                    </tfoot>

                </table>

            </div>

        </div>

    </div>


    {{-- ================================================== --}}
    {{-- GHI CHÚ --}}
    {{-- ================================================== --}}

    <div class="alert alert-info">

        <strong>Lưu ý:</strong>
        Báo giá được lập dựa trên kết quả kiểm tra và đề xuất kỹ thuật.
        Phụ tùng trong báo giá chưa được trừ khỏi tồn kho.

    </div>


    {{-- ================================================== --}}
    {{-- NÚT --}}
    {{-- ================================================== --}}

    <div class="d-flex justify-content-end">

        <button
            type="button"
            class="btn btn-outline-secondary"
            onclick="history.back()"
        >
            Quay lại
        </button>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
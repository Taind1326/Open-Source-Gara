<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kết quả kiểm tra xe</title>

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


    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">
                Kết quả kiểm tra xe
            </h3>

            <div class="text-muted">
                Yêu cầu sửa chữa #{{ $yeuCau->MaYC }}
            </div>
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
    {{-- KẾT QUẢ KIỂM TRA --}}
    {{-- ================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header fw-bold">
            Chẩn đoán của kỹ thuật viên
        </div>

        <div class="card-body">

            <div class="mb-4">

                <div class="text-muted small mb-1">
                    Tình trạng xe
                </div>

                <div>
                    {{ $yeuCau->kiemTraXe->TinhTrang }}
                </div>

            </div>


            <div class="mb-4">

                <div class="text-muted small mb-1">
                    Chẩn đoán
                </div>

                <div>
                    {{ $yeuCau->kiemTraXe->ChanDoan }}
                </div>

            </div>


            <div class="mb-4">

                <div class="text-muted small mb-1">
                    Ghi chú
                </div>

                <div>
                    {{ $yeuCau->kiemTraXe->GhiChu ?: 'Không có ghi chú.' }}
                </div>

            </div>


            <div class="mb-3">

                <div class="text-muted small mb-1">
                    Thời gian kiểm tra
                </div>

                <div>
                    {{ optional($yeuCau->kiemTraXe->NgayKiemTra)
                        ->format('d/m/Y H:i') }}
                </div>

            </div>


            @if ($yeuCau->kiemTraXe->HinhAnh)

                <hr>

                <div class="text-muted small mb-2">
                    Hình ảnh kiểm tra
                </div>

                <img
                    src="{{ asset(
                        'storage/' . $yeuCau->kiemTraXe->HinhAnh
                    ) }}"
                    alt="Hình ảnh kiểm tra xe"
                    class="img-fluid rounded border"
                    style="max-height: 400px;"
                >

            @endif

        </div>

    </div>


    {{-- ================================================== --}}
    {{-- DỊCH VỤ ĐỀ XUẤT --}}
    {{-- ================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header fw-bold">
            Dịch vụ kỹ thuật viên đề xuất
        </div>

        <div class="card-body p-0">

            @if ($yeuCau->kiemTraXe->deXuatDichVus->count() > 0)

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th>Dịch vụ</th>
                                <th class="text-end">
                                    Giá tham khảo
                                </th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach (
                                $yeuCau->kiemTraXe->deXuatDichVus
                                as $deXuat
                            )

                                <tr>

                                    <td>
                                        {{ $deXuat->dichVu->TenDV }}
                                    </td>

                                    <td class="text-end">
                                        {{ number_format(
                                            $deXuat->dichVu->Gia,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="p-3 text-muted">
                    Kỹ thuật viên không đề xuất thêm dịch vụ.
                </div>

            @endif

        </div>

    </div>


    {{-- ================================================== --}}
    {{-- PHỤ TÙNG ĐỀ XUẤT --}}
    {{-- ================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header fw-bold">
            Phụ tùng kỹ thuật viên đề xuất
        </div>

        <div class="card-body p-0">

            @if ($yeuCau->kiemTraXe->deXuatPhuTungs->count() > 0)

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th>Phụ tùng</th>
                                <th>Đơn vị</th>
                                <th class="text-center">
                                    Số lượng
                                </th>
                                <th class="text-end">
                                    Giá tham khảo
                                </th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach (
                                $yeuCau->kiemTraXe->deXuatPhuTungs
                                as $deXuat
                            )

                                <tr>

                                    <td>
                                        {{ $deXuat->phuTung->TenPT }}
                                    </td>

                                    <td>
                                        {{ $deXuat->phuTung->DonViTinh }}
                                    </td>

                                    <td class="text-center">
                                        {{ $deXuat->SoLuong }}
                                    </td>

                                    <td class="text-end">
                                        {{ number_format(
                                            $deXuat->phuTung->Gia,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="p-3 text-muted">
                    Kỹ thuật viên không đề xuất phụ tùng.
                </div>

            @endif

        </div>

    </div>


    {{-- ================================================== --}}
    {{-- GHI CHÚ NGHIỆP VỤ --}}
    {{-- ================================================== --}}

    <div class="alert alert-info">

        <strong>Lưu ý:</strong>
        Dịch vụ và phụ tùng trên là đề xuất kỹ thuật sau khi
        kiểm tra xe. Phụ tùng đề xuất chưa được trừ khỏi tồn kho.

    </div>


    <button
        type="button"
        onclick="history.back()"
        class="btn btn-outline-secondary"
    >
        Quay lại
    </button>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


@if($yeuCau->TrangThai === 'DA_KIEM_TRA')
<a class="btn btn-primary mb-3" href="{{ route('baogia.create', $yeuCau->MaYC) }}">Lập báo giá</a>
@elseif($yeuCau->baoGia)
<a class="btn btn-primary mb-3" href="{{ route('baogia.show', $yeuCau->MaYC) }}">Xem báo giá</a>
@endif

</body>
</html>
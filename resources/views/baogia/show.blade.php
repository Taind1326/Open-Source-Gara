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

    {{-- ================================================== --}}
    {{-- THÔNG BÁO --}}
    {{-- ================================================== --}}

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">

            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif


    {{-- ================================================== --}}
    {{-- TIÊU ĐỀ --}}
    {{-- ================================================== --}}

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
    {{-- THÔNG TIN TỔNG QUAN --}}
    {{-- ================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header fw-bold">
            Thông tin tổng quan
        </div>

        <div class="card-body">

            <div class="row g-4">

                {{-- XE --}}
                <div class="col-lg-5">

                    <div class="text-muted small mb-2">
                        Thông tin xe
                    </div>

                    <div class="border rounded p-3 h-100">

                        <div class="mb-3">

                            <div class="text-muted small">
                                Biển số
                            </div>

                            <div class="fw-semibold fs-5">
                                {{ $yeuCau->xe->BienSo ?? 'Chưa có thông tin' }}
                            </div>

                        </div>


                        <div class="row g-3">

                            <div class="col-6">

                                <div class="text-muted small">
                                    Hãng xe
                                </div>

                                <div>
                                    {{ $yeuCau->xe->HangXe ?? 'Chưa có thông tin' }}
                                </div>

                            </div>


                            <div class="col-6">

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


                {{-- BÁO GIÁ --}}
                <div class="col-lg-7">

                    <div class="text-muted small mb-2">
                        Thông tin báo giá
                    </div>

                    <div class="border rounded p-3 h-100">

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

            </div>

        </div>

    </div>


    {{-- ================================================== --}}
    {{-- KẾT QUẢ KIỂM TRA --}}
    {{-- ================================================== --}}

    @if ($yeuCau->kiemTraXe)

        <div class="card shadow-sm mb-4">

            <div class="card-header fw-bold">
                Kết quả kiểm tra kỹ thuật
            </div>

            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-6">

                        <div class="text-muted small mb-1">
                            Tình trạng xe
                        </div>

                        <div>
                            {{ $yeuCau->kiemTraXe->TinhTrang }}
                        </div>

                    </div>


                    <div class="col-md-6">

                        <div class="text-muted small mb-1">
                            Chẩn đoán
                        </div>

                        <div>
                            {{ $yeuCau->kiemTraXe->ChanDoan }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endif


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

                            <th>
                                Nội dung
                            </th>

                            <th>
                                Loại
                            </th>

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
    {{-- TRẠNG THÁI --}}
    {{-- ================================================== --}}

    @if ($yeuCau->baoGia->TrangThai === 'CHO_DUYET')

        <div class="alert alert-warning">

            <strong>
                Báo giá đang chờ xác nhận.
            </strong>

            <div class="mt-1">
                Vui lòng kiểm tra các dịch vụ, phụ tùng và tổng chi phí
                trước khi đồng ý hoặc từ chối.
            </div>

        </div>

    @elseif ($yeuCau->baoGia->TrangThai === 'DA_DUYET')

        <div class="alert alert-success">

            <strong>
                Báo giá đã được đồng ý.
            </strong>

            <div class="mt-1">
                Xe đã được chuyển sang giai đoạn sửa chữa.
            </div>

        </div>

    @elseif ($yeuCau->baoGia->TrangThai === 'TU_CHOI')

        <div class="alert alert-danger">

            <strong>
                Báo giá đã bị từ chối.
            </strong>

            <div class="mt-1">
                Yêu cầu sửa chữa sẽ không tiếp tục theo báo giá này.
            </div>

        </div>

    @endif


    {{-- ================================================== --}}
    {{-- NÚT THAO TÁC --}}
    {{-- ================================================== --}}

    <div class="d-flex justify-content-between align-items-center">

        <button
            type="button"
            class="btn btn-outline-secondary"
            onclick="history.back()"
        >
            Quay lại
        </button>


        @if ($yeuCau->baoGia->TrangThai === 'CHO_DUYET')

            <div class="d-flex gap-2">

                <button
                    type="button"
                    class="btn btn-outline-danger"
                    data-bs-toggle="modal"
                    data-bs-target="#rejectModal"
                >
                    Từ chối
                </button>


                <button
                    type="button"
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#approveModal"
                >
                    Đồng ý báo giá
                </button>

            </div>

        @endif

    </div>

</div>


{{-- ================================================== --}}
{{-- MODAL ĐỒNG Ý --}}
{{-- ================================================== --}}

@if ($yeuCau->baoGia->TrangThai === 'CHO_DUYET')

<div
    class="modal fade"
    id="approveModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Xác nhận đồng ý báo giá
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Đóng"
                ></button>

            </div>


            <div class="modal-body">

                <p class="mb-2">
                    Bạn có chắc muốn đồng ý báo giá này không?
                </p>

                <div class="border rounded p-3 bg-light">

                    <div class="text-muted small">
                        Tổng thanh toán dự kiến
                    </div>

                    <div class="fw-bold fs-4">

                        {{ number_format(
                            $yeuCau->baoGia->TongTien,
                            0,
                            ',',
                            '.'
                        ) }}đ

                    </div>

                </div>


                <div class="text-muted small mt-3">

                    Sau khi xác nhận, xe sẽ được chuyển sang
                    giai đoạn sửa chữa.

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Hủy
                </button>


                <form
                    action="{{ route(
                        'baogia.approve',
                        $yeuCau->MaYC
                    ) }}"
                    method="POST"
                >

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-success"
                    >
                        Xác nhận đồng ý
                    </button>

                </form>

            </div>

        </div>

    </div>
</div>


{{-- ================================================== --}}
{{-- MODAL TỪ CHỐI --}}
{{-- ================================================== --}}

<div
    class="modal fade"
    id="rejectModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Xác nhận từ chối báo giá
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Đóng"
                ></button>

            </div>


            <div class="modal-body">

                <p class="mb-2">
                    Bạn có chắc muốn từ chối báo giá này không?
                </p>

                <div class="alert alert-warning mb-0">

                    Nếu từ chối, yêu cầu sửa chữa sẽ không
                    tiếp tục theo báo giá hiện tại.

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    data-bs-dismiss="modal"
                >
                    Hủy
                </button>


                <form
                    action="{{ route(
                        'baogia.reject',
                        $yeuCau->MaYC
                    ) }}"
                    method="POST"
                >

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-danger"
                    >
                        Xác nhận từ chối
                    </button>

                </form>

            </div>

        </div>

    </div>
</div>

@endif


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


@if($yeuCau->baoGia?->TrangThai === 'DA_DUYET')
<a class="btn btn-primary mb-3" href="{{ route('suachua.show', $yeuCau->MaYC) }}">Phiếu sửa chữa và tiến độ</a>
@endif

</body>
</html>
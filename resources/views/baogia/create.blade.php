<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lập báo giá</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-4">

    {{-- TIÊU ĐỀ --}}
    <div class="d-flex justify-content-between align-items-start mb-4">

        <div>
            <h3 class="mb-1">
                Lập báo giá sửa chữa
            </h3>

            <div class="text-muted">
                Yêu cầu sửa chữa #{{ $yeuCau->MaYC }}
            </div>
        </div>

        <span class="badge bg-primary fs-6">
            Chờ lập báo giá
        </span>

    </div>


    {{-- THÔNG BÁO LỖI --}}
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
    {{-- THÔNG TIN TIẾP NHẬN --}}
    {{-- ================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header fw-bold">
            Thông tin tiếp nhận
        </div>

        <div class="card-body">

            <div class="row g-4">

                {{-- THÔNG TIN XE --}}
                <div class="col-lg-5">

                    <div class="text-muted small mb-2">
                        Thông tin xe
                    </div>

                    <div class="border rounded p-3 h-100">

                        <div class="row g-3">

                            <div class="col-md-4 col-lg-12">

                                <div class="text-muted small">
                                    Biển số
                                </div>

                                <div class="fw-semibold fs-5">
                                    {{ $yeuCau->xe->BienSo ?? 'Chưa có thông tin' }}
                                </div>

                            </div>


                            <div class="col-md-4 col-lg-6">

                                <div class="text-muted small">
                                    Hãng xe
                                </div>

                                <div>
                                    {{ $yeuCau->xe->HangXe ?? 'Chưa có thông tin' }}
                                </div>

                            </div>


                            <div class="col-md-4 col-lg-6">

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


                {{-- KẾT QUẢ KIỂM TRA --}}
                <div class="col-lg-7">

                    <div class="text-muted small mb-2">
                        Kết quả kiểm tra kỹ thuật
                    </div>

                    <div class="border rounded p-3 h-100">

                        <div class="mb-3">

                            <div class="text-muted small mb-1">
                                Tình trạng xe
                            </div>

                            <div>
                                {{ $yeuCau->kiemTraXe->TinhTrang }}
                            </div>

                        </div>


                        <div>

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

        </div>

    </div>


    {{-- ================================================== --}}
    {{-- NỘI DUNG BÁO GIÁ --}}
    {{-- ================================================== --}}

    <div class="row g-4 mb-4">

        {{-- DỊCH VỤ --}}
        <div class="col-lg-6">

            <div class="card shadow-sm h-100">

                <div class="card-header fw-bold">
                    Dịch vụ đề xuất
                </div>

                <div class="card-body p-0">

                    @if ($yeuCau->kiemTraXe->deXuatDichVus->count() > 0)

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>
                                        <th>
                                            Dịch vụ
                                        </th>

                                        <th class="text-end">
                                            Đơn giá
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
                            Không có dịch vụ được đề xuất.
                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- PHỤ TÙNG --}}
        <div class="col-lg-6">

            <div class="card shadow-sm h-100">

                <div class="card-header fw-bold">
                    Phụ tùng đề xuất
                </div>

                <div class="card-body p-0">

                    @if ($yeuCau->kiemTraXe->deXuatPhuTungs->count() > 0)

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>
                                        <th>
                                            Phụ tùng
                                        </th>

                                        <th class="text-center">
                                            SL
                                        </th>

                                        <th class="text-end">
                                            Thành tiền
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

                                            <div class="fw-semibold">
                                                {{ $deXuat->phuTung->TenPT }}
                                            </div>

                                            <div class="text-muted small">
                                                {{ number_format(
                                                    $deXuat->phuTung->Gia,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}đ / {{ $deXuat->phuTung->DonViTinh }}
                                            </div>

                                        </td>


                                        <td class="text-center">
                                            {{ $deXuat->SoLuong }}
                                        </td>


                                        <td class="text-end fw-semibold">
                                            {{ number_format(
                                                $deXuat->phuTung->Gia
                                                * $deXuat->SoLuong,
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
                            Không có phụ tùng được đề xuất.
                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================== --}}
    {{-- TÍNH TỔNG --}}
    {{-- ================================================== --}}

    @php

        $tongDichVu = $yeuCau->kiemTraXe
            ->deXuatDichVus
            ->sum(function ($deXuat) {
                return $deXuat->dichVu->Gia;
            });

        $tongPhuTung = $yeuCau->kiemTraXe
            ->deXuatPhuTungs
            ->sum(function ($deXuat) {
                return $deXuat->phuTung->Gia
                    * $deXuat->SoLuong;
            });

        $tongTien = $tongDichVu + $tongPhuTung;

    @endphp


    {{-- ================================================== --}}
    {{-- TÓM TẮT CHI PHÍ --}}
    {{-- ================================================== --}}

    <div class="card shadow-sm mb-4">

        <div class="card-header fw-bold">
            Tóm tắt chi phí
        </div>

        <div class="card-body">

            <div class="row justify-content-end">

                <div class="col-md-6 col-lg-5">

                    <div class="d-flex justify-content-between mb-2">

                        <span class="text-muted">
                            Dịch vụ
                        </span>

                        <span>
                            {{ number_format(
                                $tongDichVu,
                                0,
                                ',',
                                '.'
                            ) }}đ
                        </span>

                    </div>


                    <div class="d-flex justify-content-between mb-3">

                        <span class="text-muted">
                            Phụ tùng
                        </span>

                        <span>
                            {{ number_format(
                                $tongPhuTung,
                                0,
                                ',',
                                '.'
                            ) }}đ
                        </span>

                    </div>


                    <hr>


                    <div class="d-flex justify-content-between align-items-center">

                        <span class="fw-bold">
                            Tổng tiền dự kiến
                        </span>

                        <span class="fs-4 fw-bold">
                            {{ number_format(
                                $tongTien,
                                0,
                                ',',
                                '.'
                            ) }}đ
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================== --}}
    {{-- FORM LẬP BÁO GIÁ --}}
    {{-- ================================================== --}}

    <form
        action="{{ route('baogia.store', $yeuCau->MaYC) }}"
        method="POST"
    >

        @csrf


        <div class="card shadow-sm mb-4">

            <div class="card-header fw-bold">
                Ghi chú báo giá
            </div>

            <div class="card-body">

                <textarea
                    name="GhiChu"
                    rows="3"
                    class="form-control @error('GhiChu') is-invalid @enderror"
                    placeholder="Nhập ghi chú cho khách hàng nếu có..."
                >{{ old('GhiChu') }}</textarea>

                @error('GhiChu')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror


                <div class="form-text">
                    Đơn giá được lấy từ danh mục hiện tại và không chỉnh sửa trực tiếp tại đây.
                </div>

            </div>

        </div>


        {{-- NÚT --}}
        <div class="d-flex justify-content-between">

            <button
                type="button"
                class="btn btn-outline-secondary"
                onclick="history.back()"
            >
                Quay lại
            </button>


            <button
                type="submit"
                class="btn btn-primary px-4"
            >
                Lập báo giá
            </button>

        </div>

    </form>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
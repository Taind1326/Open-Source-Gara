<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kiểm tra xe</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Kiểm tra và chẩn đoán xe</h3>
            <div class="text-muted">
                Yêu cầu sửa chữa #{{ $yeuCau->MaYC }}
            </div>
        </div>
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
                        Biển số xe
                    </div>

                    <div class="fw-semibold">
                        {{ $yeuCau->xe->BienSo ?? 'Chưa có thông tin' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">
                        Hãng xe
                    </div>

                    <div class="fw-semibold">
                        {{ $yeuCau->xe->HangXe ?? 'Chưa có thông tin' }}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small">
                        Dòng xe
                    </div>

                    <div class="fw-semibold">
                        {{ $yeuCau->xe->DongXe ?? 'Chưa có thông tin' }}
                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- ================================================== --}}
    {{-- YÊU CẦU KHÁCH HÀNG --}}
    {{-- ================================================== --}}

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold">
            Nội dung yêu cầu
        </div>

        <div class="card-body">

            <p class="mb-3">
                {{ $yeuCau->MoTa ?: 'Không có mô tả.' }}
            </p>

            <div class="fw-semibold mb-2">
                Dịch vụ khách hàng đã chọn:
            </div>

            @if ($yeuCau->dichVu->count() > 0)

                <ul class="mb-0">
                    @foreach ($yeuCau->dichVu as $dichVu)
                        <li>
                            {{ $dichVu->TenDV }}

                            @if (isset($dichVu->Gia))
                                -
                                {{ number_format($dichVu->Gia, 0, ',', '.') }}đ
                            @endif
                        </li>
                    @endforeach
                </ul>

            @else

                <div class="text-muted">
                    Khách hàng chưa chọn dịch vụ cụ thể.
                </div>

            @endif

        </div>
    </div>


    {{-- ================================================== --}}
    {{-- FORM KIỂM TRA --}}
    {{-- ================================================== --}}

    <form
        action="{{ route('kiemtraxe.store', $yeuCau->MaYC) }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf


        {{-- ================================================== --}}
        {{-- KẾT QUẢ KIỂM TRA --}}
        {{-- ================================================== --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header fw-bold">
                Kết quả kiểm tra
            </div>

            <div class="card-body">

                {{-- TÌNH TRẠNG --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Tình trạng xe
                        <span class="text-danger">*</span>
                    </label>

                    <textarea
                        name="TinhTrang"
                        rows="4"
                        class="form-control @error('TinhTrang') is-invalid @enderror"
                        placeholder="Ví dụ: Xe rung khi khởi động, động cơ phát tiếng kêu..."
                        required
                    >{{ old('TinhTrang') }}</textarea>

                    @error('TinhTrang')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- CHẨN ĐOÁN --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Chẩn đoán
                        <span class="text-danger">*</span>
                    </label>

                    <textarea
                        name="ChanDoan"
                        rows="4"
                        class="form-control @error('ChanDoan') is-invalid @enderror"
                        placeholder="Nhập kết quả chẩn đoán của kỹ thuật viên..."
                        required
                    >{{ old('ChanDoan') }}</textarea>

                    @error('ChanDoan')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- GHI CHÚ --}}
                <div class="mb-3">

                    <label class="form-label fw-semibold">
                        Ghi chú
                    </label>

                    <textarea
                        name="GhiChu"
                        rows="3"
                        class="form-control @error('GhiChu') is-invalid @enderror"
                        placeholder="Ghi chú thêm nếu có..."
                    >{{ old('GhiChu') }}</textarea>

                    @error('GhiChu')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- HÌNH ẢNH --}}
                <div>

                    <label class="form-label fw-semibold">
                        Hình ảnh kiểm tra
                    </label>

                    <input
                        type="file"
                        name="HinhAnh"
                        class="form-control @error('HinhAnh') is-invalid @enderror"
                        accept=".jpg,.jpeg,.png,.webp"
                    >

                    <div class="form-text">
                        JPG, JPEG, PNG hoặc WEBP. Tối đa 2MB.
                    </div>

                    @error('HinhAnh')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>
        </div>


        {{-- ================================================== --}}
        {{-- DỊCH VỤ ĐỀ XUẤT --}}
        {{-- ================================================== --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header fw-bold">
                Dịch vụ đề xuất
            </div>

            <div class="card-body">

                <p class="text-muted mb-3">
                    Chọn các dịch vụ cần thực hiện sau khi kiểm tra xe.
                </p>

                @if ($dichVus->count() > 0)

                    <div class="row g-3">

                        @foreach ($dichVus as $dichVu)

                            <div class="col-md-6">

                                <div class="border rounded p-3 h-100">

                                    <div class="form-check">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="dich_vu_de_xuat[]"
                                            value="{{ $dichVu->MaDV }}"
                                            id="dichvu_{{ $dichVu->MaDV }}"
                                            {{ in_array(
                                                $dichVu->MaDV,
                                                old('dich_vu_de_xuat', [])
                                            ) ? 'checked' : '' }}
                                        >

                                        <label
                                            class="form-check-label w-100"
                                            for="dichvu_{{ $dichVu->MaDV }}"
                                        >

                                            <div class="fw-semibold">
                                                {{ $dichVu->TenDV }}
                                            </div>

                                            <div class="text-muted small">
                                                {{ number_format(
                                                    $dichVu->Gia,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}đ
                                            </div>

                                        </label>

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="alert alert-light border mb-0">
                        Hiện chưa có dịch vụ đang hoạt động.
                    </div>

                @endif

            </div>
        </div>


        {{-- ================================================== --}}
        {{-- PHỤ TÙNG ĐỀ XUẤT --}}
        {{-- ================================================== --}}

        <div class="card shadow-sm mb-4">

            <div class="card-header fw-bold">
                Phụ tùng đề xuất
            </div>

            <div class="card-body">

                <p class="text-muted mb-3">
                    Chọn phụ tùng cần thiết và nhập số lượng đề xuất.
                    Việc đề xuất chưa làm giảm tồn kho.
                </p>

                @if ($phuTungs->count() > 0)

                    <div class="row g-3">

                        @foreach ($phuTungs as $index => $phuTung)

                            @php
                                $oldPhuTung =
                                    old("phu_tung_de_xuat.$index.MaPT");

                                $daChon =
                                    (string) $oldPhuTung ===
                                    (string) $phuTung->MaPT;
                            @endphp

                            <div class="col-md-6">

                                <div class="border rounded p-3 h-100">

                                    <div class="form-check mb-2">

                                        <input
                                            class="form-check-input phu-tung-checkbox"
                                            type="checkbox"
                                            id="phutung_{{ $phuTung->MaPT }}"
                                            data-index="{{ $index }}"
                                            {{ $daChon ? 'checked' : '' }}
                                        >

                                        <label
                                            class="form-check-label"
                                            for="phutung_{{ $phuTung->MaPT }}"
                                        >
                                            <span class="fw-semibold">
                                                {{ $phuTung->TenPT }}
                                            </span>
                                        </label>

                                    </div>


                                    <div class="small text-muted mb-2">

                                        Đơn giá:
                                        {{ number_format(
                                            $phuTung->Gia,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                                        · Tồn:
                                        {{ $phuTung->SoLuongTon }}

                                        {{ $phuTung->DonViTinh }}

                                    </div>


                                    {{-- MaPT chỉ được gửi khi checkbox được chọn --}}
                                    <input
                                        type="hidden"
                                        id="mapt_{{ $index }}"
                                        name="phu_tung_de_xuat[{{ $index }}][MaPT]"
                                        value="{{ $phuTung->MaPT }}"
                                        {{ $daChon ? '' : 'disabled' }}
                                    >


                                    <div class="input-group">

                                        <span class="input-group-text">
                                            Số lượng
                                        </span>

                                        <input
                                            type="number"
                                            id="soluong_{{ $index }}"
                                            name="phu_tung_de_xuat[{{ $index }}][SoLuong]"
                                            class="form-control"
                                            min="1"
                                            value="{{ old(
                                                "phu_tung_de_xuat.$index.SoLuong",
                                                1
                                            ) }}"
                                            {{ $daChon ? '' : 'disabled' }}
                                        >

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="alert alert-light border mb-0">
                        Hiện chưa có phụ tùng đang sử dụng.
                    </div>

                @endif

            </div>
        </div>


        {{-- ================================================== --}}
        {{-- NÚT THAO TÁC --}}
        {{-- ================================================== --}}

        <div class="d-flex justify-content-end gap-2 mb-4">

            <button
                type="button"
                class="btn btn-outline-secondary"
                onclick="history.back()"
            >
                Quay lại
            </button>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Lưu kết quả kiểm tra
            </button>

        </div>

    </form>

</div>


{{-- ================================================== --}}
{{-- JS PHỤ TÙNG --}}
{{-- Chỉ gửi MaPT + SoLuong của phụ tùng đã được tick --}}
{{-- ================================================== --}}

<script>
    document.querySelectorAll('.phu-tung-checkbox').forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {

            const index = this.dataset.index;

            const maPT =
                document.getElementById('mapt_' + index);

            const soLuong =
                document.getElementById('soluong_' + index);

            if (this.checked) {

                maPT.disabled = false;
                soLuong.disabled = false;

                if (!soLuong.value || soLuong.value < 1) {
                    soLuong.value = 1;
                }

            } else {

                maPT.disabled = true;
                soLuong.disabled = true;
            }
        });

    });
</script>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
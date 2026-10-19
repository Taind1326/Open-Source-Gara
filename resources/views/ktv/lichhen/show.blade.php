@extends('public.layout')

@section('title', 'Công việc #' . $lichHen->MaLH)

@section('content')
    <section class="hero">
        <div class="container">
            <h1 class="display-5 fw-bold mb-3">
                Công việc #{{ $lichHen->MaLH }}
            </h1>

            <p class="hero-description mb-0">
                {{ $lichHen->xe->BienSo }}
                — {{ $lichHen->xe->HangXe }}
                {{ $lichHen->xe->DongXe }}
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <a
                href="{{ route('ktv.lichhen.index') }}"
                class="btn btn-outline-primary mb-4"
            >
                Quay lại công việc của tôi
            </a>

            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <div class="feature-card h-auto mb-4">
                        <h2 class="h4 fw-bold mb-4">
                            Thông tin tiếp nhận
                        </h2>

                        <dl class="row mb-0">
                            <dt class="col-sm-4 text-muted fw-normal">
                                Khách hàng
                            </dt>
                            <dd class="col-sm-8">
                                {{ $lichHen->taiKhoan->HoTen }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Số điện thoại
                            </dt>
                            <dd class="col-sm-8">
                                {{ $lichHen->taiKhoan->SoDienThoai }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Biển số
                            </dt>
                            <dd class="col-sm-8 fw-semibold">
                                {{ $lichHen->xe->BienSo }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Hãng / dòng xe
                            </dt>
                            <dd class="col-sm-8">
                                {{ $lichHen->xe->HangXe }}
                                — {{ $lichHen->xe->DongXe }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Năm sản xuất
                            </dt>
                            <dd class="col-sm-8">
                                {{ $lichHen->xe->NamSX ?? 'Chưa cập nhật' }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Màu sắc
                            </dt>
                            <dd class="col-sm-8">
                                {{ $lichHen->xe->MauSac ?: 'Chưa cập nhật' }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Ngày hẹn
                            </dt>
                            <dd class="col-sm-8">
                                {{ $lichHen->NgayHen->format('d/m/Y') }}
                            </dd>

                            <dt class="col-sm-4 text-muted fw-normal">
                                Khung giờ
                            </dt>
                            <dd class="col-sm-8">
                                {{ $lichHen->KhungGioHen }}
                            </dd>
                        </dl>
                    </div>

                    <div class="feature-card h-auto mb-4">
                        <h2 class="h4 fw-bold mb-3">
                            Dịch vụ khách chọn ban đầu
                        </h2>

                        <ul class="mb-0">
                            @forelse($dichVuBanDau as $chiTiet)
                                <li class="mb-2">
                                    {{ $chiTiet->dichVu?->TenDV ?? 'Dịch vụ không còn trong danh mục' }}
                                    — {{ number_format($chiTiet->DonGia, 0, ',', '.') }} ₫

                                    @if($chiTiet->SoLuong > 1)
                                        × {{ $chiTiet->SoLuong }}
                                    @endif
                                </li>
                            @empty
                                <li class="text-muted">
                                    Chưa có dịch vụ khách chọn.
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="feature-card h-auto">
                        <h2 class="h4 fw-bold mb-3">
                            Mô tả của khách
                        </h2>

                        <p
                            class="text-muted text-break mb-0"
                            style="white-space: pre-line;"
                        >{{ $lichHen->MoTaBanDau ?: 'Không có mô tả thêm.' }}</p>

                        @if($lichHen->phanCong?->GhiChu)
                            <div class="border-top mt-4 pt-3">
                                <h3 class="h6 fw-bold">
                                    Ghi chú từ admin
                                </h3>

                                <p
                                    class="text-muted text-break mb-0"
                                    style="white-space: pre-line;"
                                >{{ $lichHen->phanCong->GhiChu }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="feature-card h-auto">
                        <h2 class="h5 fw-bold mb-3">
                            Trạng thái công việc
                        </h2>

                        <div class="mb-4">
                            @include('admin.lichhen._status')
                        </div>

                        @if($lichHen->TrangThai === 'CHO_NHAN_XE')
                            <p class="text-muted">
                                Chỉ xác nhận nhận xe khi khách đã mang xe
                                tới gara. Sau khi nhận, xe chuyển sang
                                trạng thái đang kiểm tra.
                            </p>

                            <form
                                method="POST"
                                action="{{ route('ktv.lichhen.receive', $lichHen->MaLH) }}"
                                novalidate
                                data-confirm-title="Xác nhận nhận xe"
                                data-confirm="Khách đã mang xe tới gara và bạn đã tiếp nhận xe để kiểm tra?"
                                data-confirm-accept="Đã nhận xe"
                                data-confirm-cancel="Chưa nhận"
                            >
                                @csrf

                                @error('nhan_xe')
                                    <div class="text-danger small mb-3">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <button
                                    type="submit"
                                    class="btn btn-primary w-100"
                                >
                                    Nhận xe
                                </button>
                            </form>
                        @else
                            @error('nhan_xe')
                                <div class="text-danger small mb-3">
                                    {{ $message }}
                                </div>
                            @enderror

                            @if($lichHen->NgayNhanXe)
                                <p class="text-muted small mb-3">
                                    Đã nhận xe:
                                    <strong>
                                        {{ $lichHen->NgayNhanXe->format('d/m/Y H:i') }}
                                    </strong>
                                </p>
                            @endif

                            @if($lichHen->TrangThai === 'DANG_KIEM_TRA')
                                <div class="alert alert-info mb-0">
                                    Xe đang trong quá trình kiểm tra.
                                    Việc sửa chữa chỉ bắt đầu sau khi
                                    khách xác nhận các hạng mục và chi phí.
                                </div>

                                <a
                                    href="{{ route('ktv.kiemtra.edit', $lichHen->MaLH) }}"
                                    class="btn btn-primary w-100 mt-3"
                                >
                                    Lập / cập nhật phiếu kiểm tra
                                </a>
                            @elseif($lichHen->TrangThai === 'CHO_XAC_NHAN')
                                <div class="alert alert-info mb-0">
                                    Đã gửi phiếu kiểm tra, đang chờ khách
                                    xác nhận các hạng mục và chi phí
                                    trước khi sửa chữa.
                                </div>
                            @elseif($lichHen->TrangThai === 'CHO_SUA_CHUA')
                                <div class="alert alert-success mb-3">
                                    Khách đã đồng ý các hạng mục và chi phí.
                                    Bạn có thể bắt đầu sửa chữa.
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('ktv.suachua.start', $lichHen->MaLH) }}"
                                    novalidate
                                    data-confirm-title="Bắt đầu sửa chữa"
                                    data-confirm="Bắt đầu sửa xe theo các hạng mục khách đã đồng ý? Phụ tùng sử dụng sẽ được trừ khỏi tồn kho."
                                    data-confirm-accept="Bắt đầu sửa"
                                    data-confirm-cancel="Để sau"
                                >
                                    @csrf

                                    @error('sua_chua')
                                        <div class="text-danger small mb-3" role="alert">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <button
                                        type="submit"
                                        class="btn btn-primary w-100"
                                    >
                                        <i class="bi bi-tools me-2" aria-hidden="true"></i>
                                        Bắt đầu sửa chữa
                                    </button>
                                </form>

                            @elseif($lichHen->TrangThai === 'DANG_SUA_CHUA')
    <div class="alert alert-info mb-3">
        Xe đang được sửa theo các hạng mục khách đã đồng ý.
        Chỉ xác nhận hoàn thành sau khi đã kiểm tra xe lần cuối.
    </div>

    <form
        method="POST"
        action="{{ route('ktv.suachua.finish', $lichHen->MaLH) }}"
        novalidate
        data-confirm-title="Hoàn thành sửa chữa"
        data-confirm="Xác nhận đã sửa xong và kiểm tra xe lần cuối? Hệ thống sẽ tạo hóa đơn theo chi phí khách đã đồng ý."
        data-confirm-accept="Hoàn thành sửa chữa"
        data-confirm-cancel="Tiếp tục sửa"
    >
        @csrf

        <div class="mb-3">
            <label for="repair-note" class="form-label">
                Ghi chú sau sửa chữa
            </label>

            <textarea
                id="repair-note"
                name="GhiChu"
                rows="3"
                maxlength="2000"
                class="form-control @error('GhiChu') is-invalid @enderror"
                placeholder="Lưu ý vận hành, bảo dưỡng sau sửa chữa..."
            >{{ old('GhiChu') }}</textarea>

            @error('GhiChu')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input
                type="checkbox"
                id="final-check"
                name="DaKiemTraCuoi"
                value="1"
                class="form-check-input @error('DaKiemTraCuoi') is-invalid @enderror"
                required
                @checked(old('DaKiemTraCuoi'))
            >

            <label class="form-check-label" for="final-check">
                Tôi đã sửa xong và kiểm tra xe lần cuối.
            </label>

            @error('DaKiemTraCuoi')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        @error('sua_chua')
            <div class="text-danger small mb-3" role="alert">
                {{ $message }}
            </div>
        @enderror

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-check2-circle me-2" aria-hidden="true"></i>
            Hoàn thành sửa chữa
        </button>
    </form>

@elseif($lichHen->TrangThai === 'CHO_THANH_TOAN')
    @error('sua_chua')
        <div class="text-danger small mb-3" role="alert">
            {{ $message }}
        </div>
    @enderror

    <div class="alert alert-success mb-0">
        Đã hoàn thành sửa chữa và kiểm tra cuối.
        Hóa đơn đã được tạo, đang chờ thanh toán.
    </div>

@elseif($lichHen->TrangThai === 'HOAN_TAT')
    <div class="alert alert-success mb-0">
        Khách đã thanh toán. Đơn sửa chữa đã hoàn tất.
    </div>
                            @elseif($lichHen->TrangThai === 'DA_HUY')
                                <div class="alert alert-secondary mb-0">
                                    Lịch đã hủy.
                                    {{ $lichHen->LyDoHuy }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
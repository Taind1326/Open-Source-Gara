@extends('admin.layout')

@section('title', 'Chi tiết lịch hẹn #' . $lichHen->MaLH)

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">
                Lịch hẹn #{{ $lichHen->MaLH }}
            </h1>

            @include('admin.lichhen._status')
        </div>

        <a
            href="{{ route('admin.lichhen.index') }}"
            class="btn btn-outline-secondary"
        >
            Quay lại danh sách
        </a>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-4">
                        Khách hàng và xe
                    </h2>

                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-secondary fw-normal">
                            Khách hàng
                        </dt>
                        <dd class="col-sm-8 fw-semibold">
                            {{ $lichHen->taiKhoan->HoTen }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Số điện thoại
                        </dt>
                        <dd class="col-sm-8">
                            {{ $lichHen->taiKhoan->SoDienThoai }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Email
                        </dt>
                        <dd class="col-sm-8 text-break">
                            {{ $lichHen->taiKhoan->Email }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Biển số
                        </dt>
                        <dd class="col-sm-8 fw-semibold">
                            {{ $lichHen->xe->BienSo }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Hãng / dòng xe
                        </dt>
                        <dd class="col-sm-8">
                            {{ $lichHen->xe->HangXe }}
                            — {{ $lichHen->xe->DongXe }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Năm sản xuất
                        </dt>
                        <dd class="col-sm-8">
                            {{ $lichHen->xe->NamSX ?? 'Chưa cập nhật' }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Màu sắc
                        </dt>
                        <dd class="col-sm-8">
                            {{ $lichHen->xe->MauSac ?: 'Chưa cập nhật' }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Ngày tiếp nhận
                        </dt>
                        <dd class="col-sm-8">
                            {{ $lichHen->NgayHen->format('d/m/Y') }}
                        </dd>

                        <dt class="col-sm-4 text-secondary fw-normal">
                            Khung giờ
                        </dt>
                        <dd class="col-sm-8">
                            {{ $lichHen->KhungGioHen }}
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">
                        Dịch vụ khách chọn ban đầu
                    </h2>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($dichVuBanDau as $chiTiet)
                                    <tr>
                                        <td>
                                            {{ $chiTiet->dichVu?->TenDV ?? 'Dịch vụ không còn trong danh mục' }}
                                        </td>

                                        <td class="text-center">
                                            {{ $chiTiet->SoLuong }}
                                        </td>

                                        <td class="text-end text-nowrap">
                                            {{ number_format($chiTiet->DonGia, 0, ',', '.') }} ₫
                                        </td>

                                        <td class="text-end text-nowrap">
                                            {{ number_format((int) $chiTiet->DonGia * $chiTiet->SoLuong, 0, ',', '.') }} ₫
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary">
                                            Chưa có dịch vụ khách chọn.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="3">Tổng ban đầu</th>
                                    <th class="text-end text-nowrap">
                                        {{ number_format($tongBanDau, 0, ',', '.') }} ₫
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <p class="text-secondary small mb-0">
                        Đây là các dịch vụ khách chọn khi đặt lịch.
                        Hạng mục và tổng chi phí sửa chữa sẽ được khách
                        xác nhận sau khi kỹ thuật viên kiểm tra xe.
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">
                        Mô tả của khách
                    </h2>

                    <p class="text-secondary text-break mb-0" style="white-space: pre-line;">{{ $lichHen->MoTaBanDau ?: 'Không có mô tả thêm.' }}</p>

                    @if($lichHen->TrangThai === 'DA_HUY')
                        <div class="alert alert-secondary mt-3 mb-0">
                            <strong>Lý do hủy:</strong>
                            {{ $lichHen->LyDoHuy }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">
                        Phân công kỹ thuật viên
                    </h2>

                    @if($lichHen->phanCong)
                        <dl class="mb-0">
                            <dt class="text-secondary fw-normal mb-1">
                                Kỹ thuật viên phụ trách
                            </dt>
                            <dd class="fw-semibold mb-3">
                                {{ $lichHen->phanCong->ktv?->HoTen }}
                            </dd>

                            <dt class="text-secondary fw-normal mb-1">
                                Người phân công
                            </dt>
                            <dd class="mb-3">
                                {{ $lichHen->phanCong->admin?->HoTen }}
                            </dd>

                            <dt class="text-secondary fw-normal mb-1">
                                Thời điểm phân công
                            </dt>
                            <dd class="mb-3">
                                {{ $lichHen->phanCong->NgayPhanCong?->format('d/m/Y H:i') }}
                            </dd>

                            <dt class="text-secondary fw-normal mb-1">
                                Ghi chú
                            </dt>
                            <dd class="text-break mb-0">
                                {{ $lichHen->phanCong->GhiChu ?: 'Không có ghi chú.' }}
                            </dd>
                        </dl>

                        @error('MaKTV')
                            <div class="text-danger small mt-3">
                                {{ $message }}
                            </div>
                        @enderror
                    @elseif($lichHen->TrangThai === 'CHO_PHAN_CONG')
                        @if($kyThuatViens->isEmpty())
                            <div class="alert alert-warning mb-0">
                                Chưa có kỹ thuật viên đang hoạt động
                                để phân công.
                            </div>
                        @else
                            <form
                                method="POST"
                                action="{{ route('admin.lichhen.assign', $lichHen->MaLH) }}"
                                novalidate
                                data-confirm="Kỹ thuật viên được chọn sẽ phụ trách kiểm tra và sửa chữa xe của lịch hẹn này."
                                data-confirm-title="Xác nhận phân công"
                                data-confirm-accept="Phân công"
                                data-confirm-cancel="Chọn lại"
                            >
                                @csrf

                                <div class="mb-3">
                                    <label for="assignmentTechnician" class="form-label fw-semibold">
                                        Kỹ thuật viên
                                    </label>

                                    <select
                                        id="assignmentTechnician"
                                        name="MaKTV"
                                        class="form-select @error('MaKTV') is-invalid @enderror"
                                        required
                                    >
                                        <option value="">
                                            Chọn kỹ thuật viên
                                        </option>

                                        @foreach($kyThuatViens as $ktv)
                                            <option
                                                value="{{ $ktv->MaTK }}"
                                                @selected((string) old('MaKTV') === (string) $ktv->MaTK)
                                            >
                                                {{ $ktv->HoTen }}
                                                — {{ $ktv->SoLichDangPhuTrach }} lịch đang phụ trách
                                                — {{ $ktv->SoLichCungKhung }} lịch cùng khung
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('MaKTV')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                    <div class="form-text">
                                        Ưu tiên hiển thị người ít lịch cùng khung giờ
                                        và ít lịch đang phụ trách.
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="assignmentNote" class="form-label fw-semibold">
                                        Ghi chú
                                        <span class="text-secondary fw-normal">
                                            (không bắt buộc)
                                        </span>
                                    </label>

                                    <textarea
                                        id="assignmentNote"
                                        name="GhiChu"
                                        rows="4"
                                        maxlength="1000"
                                        class="form-control @error('GhiChu') is-invalid @enderror"
                                        placeholder="Lưu ý khi tiếp nhận và kiểm tra xe..."
                                    >{{ old('GhiChu') }}</textarea>

                                    @error('GhiChu')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    Phân công KTV
                                </button>
                            </form>
                        @endif
                    @else
                        <p class="text-secondary mb-0">
                            Lịch hiện không ở trạng thái cho phép phân công.
                        </p>

                        @error('MaKTV')
                            <div class="text-danger small mt-3">
                                {{ $message }}
                            </div>
                        @enderror
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
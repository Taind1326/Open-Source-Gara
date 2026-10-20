@extends('ktv.layout')

@section('title', 'Kiểm tra xe #' . $lichHen->MaLH)

@section('content')
    @php
        $submitted = old('_inspection_form') === '1';

        $oldServices = $submitted
            ? old('dich_vus', [])
            : $dichVuDaChon;

        $selectedServices = collect(
            is_array($oldServices) ? $oldServices : []
        )
            ->filter(fn ($id) => is_scalar($id))
            ->map(fn ($id) => (string) $id)
            ->all();

        $coBanNhapDaLuu = $phieu
            && $phieu->TrangThaiXacNhan === \App\Models\PhieuKiemTra::NHAP;

        $duocXemLai = $coBanNhapDaLuu && !$submitted;
    @endphp

    <div class="ktv-container">
        <a
            href="{{ route('ktv.lichhen.show', $lichHen->MaLH) }}"
            class="btn btn-outline-secondary mb-4"
        >
            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>
            Quay lại công việc
        </a>

        <div class="ktv-page-heading">
            <h1>Kiểm tra xe</h1>

            <p>
                #{{ $lichHen->MaLH }}
                · {{ $lichHen->xe->BienSo }}
                · {{ $lichHen->xe->HangXe }}
                {{ $lichHen->xe->DongXe }}
            </p>
        </div>

        <section class="ktv-panel mb-4">
            <h2 class="h5 fw-bold mb-3">
                Nhu cầu ban đầu của khách
            </h2>

            <p
                class="text-secondary text-break mb-0"
                style="white-space: pre-line;"
            >{{ $lichHen->MoTaBanDau ?: 'Khách không có mô tả thêm.' }}</p>
        </section>

        <form
            id="inspection-draft-form"
            method="POST"
            action="{{ route('ktv.kiemtra.save', $lichHen->MaLH) }}"
            novalidate
        >
            @csrf

            <input type="hidden" name="_inspection_form" value="1">

            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <section class="ktv-panel mb-4">
                        <h2 class="h5 fw-bold mb-4">
                            Kết quả kiểm tra
                        </h2>

                        <div class="mb-4">
                            <label
                                for="inspectionConclusion"
                                class="form-label fw-semibold"
                            >
                                Kết luận kiểm tra
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                id="inspectionConclusion"
                                name="KetLuan"
                                rows="6"
                                maxlength="5000"
                                class="form-control @error('KetLuan') is-invalid @enderror"
                                placeholder="Xe gặp vấn đề gì? Cần thực hiện hạng mục nào và vì sao?"
                                required
                            >{{ old('KetLuan', $phieu?->KetLuan) }}</textarea>

                            @error('KetLuan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Giải thích rõ để khách hiểu lý do cần giữ,
                                bỏ hoặc bổ sung dịch vụ.
                            </div>
                        </div>

                        <div>
                            <label
                                for="inspectionNote"
                                class="form-label fw-semibold"
                            >
                                Ghi chú thêm
                            </label>

                            <textarea
                                id="inspectionNote"
                                name="GhiChu"
                                rows="3"
                                maxlength="2000"
                                class="form-control @error('GhiChu') is-invalid @enderror"
                            >{{ old('GhiChu', $phieu?->GhiChu) }}</textarea>

                            @error('GhiChu')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </section>

                    <section class="ktv-panel mb-4">
                        <h2 class="h5 fw-bold mb-2">
                            Dịch vụ đề xuất
                        </h2>

                        <p class="text-secondary mb-4">
                            Giữ tick để thực hiện, bỏ tick nếu không cần.
                            Tick thêm dịch vụ phù hợp sau khi kiểm tra xe.
                        </p>

                        <div
                            class="row g-3"
                            data-validation-group="dich_vus"
                            data-max-selected="50"
                        >
                            @forelse($dichVus as $dichVu)
                                @php
                                    $chiTiet = $chiTietDichVus->get($dichVu->MaDV);
                                    $khachDaChon = $chiTiet?->KhachDaChon ?? false;

                                    $hoatDong = $dichVu->HoatDong
                                        && ($dichVu->loaiDichVu?->HoatDong ?? false);

                                    $gia = $chiTiet?->DonGia ?? $dichVu->Gia;
                                    $soLuong = $chiTiet?->SoLuong ?? 1;
                                @endphp

                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100">
                                        <div class="form-check">
                                            <input
                                                type="checkbox"
                                                id="inspectionService{{ $dichVu->MaDV }}"
                                                name="dich_vus[]"
                                                value="{{ $dichVu->MaDV }}"
                                                class="form-check-input"
                                                @checked(in_array(
                                                    (string) $dichVu->MaDV,
                                                    $selectedServices,
                                                    true
                                                ))
                                            >

                                            <label
                                                for="inspectionService{{ $dichVu->MaDV }}"
                                                class="form-check-label"
                                            >
                                                <span class="d-block fw-semibold">
                                                    {{ $dichVu->TenDV }}
                                                </span>

                                                @if($khachDaChon)
                                                    <span class="badge text-primary bg-primary-subtle mt-2">
                                                        Khách đã chọn
                                                    </span>
                                                @endif

                                                @if(!$hoatDong)
                                                    <span class="badge text-danger bg-danger-subtle mt-2">
                                                        Đã ngừng hoạt động
                                                    </span>

                                                    <small class="d-block text-danger mt-1">
                                                        Bỏ chọn để tiếp tục.
                                                    </small>
                                                @endif

                                                <span class="d-block fw-semibold mt-2">
                                                    {{ number_format($gia, 0, ',', '.') }} ₫

                                                    @if($soLuong > 1)
                                                        × {{ $soLuong }}
                                                    @endif
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-secondary">
                                    Chưa có dịch vụ trong danh mục.
                                </div>
                            @endforelse
                        </div>

                        <div class="invalid-feedback {{ $errors->has('dich_vus') || $errors->has('dich_vus.*') ? 'd-block' : '' }}">
                            {{ $errors->first('dich_vus') ?: $errors->first('dich_vus.*') }}
                        </div>
                    </section>

                    <section class="ktv-panel">
                        <h2 class="h5 fw-bold mb-2">
                            Phụ tùng ngoài gói dịch vụ
                        </h2>

                        <p class="text-secondary mb-4">
                            Chỉ thêm phụ tùng chưa nằm trong giá dịch vụ.
                            Nhập 0 nếu không sử dụng.
                            Số lượng không được vượt quá tồn kho hiện tại.
                        </p>

                        @error('phu_tungs')
                            <div class="text-danger small mb-3">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="row g-3">
                            @forelse($phuTungs as $phuTung)
                                @php
                                    $chiTiet = $chiTietPhuTungs->get($phuTung->MaPT);

                                    $soLuongMacDinh = $chiTiet?->KTVDeXuat
                                        ? $chiTiet->SoLuong
                                        : 0;

                                    $gia = $chiTiet?->DonGia ?? $phuTung->GiaBan;
                                    $tonKho = (int) $phuTung->SoLuongTon;
                                    $errorKey = 'phu_tungs.' . $phuTung->MaPT;
                                    $inputId = 'inspectionPart' . $phuTung->MaPT;

                                    $stockMessage = 'Số lượng không được vượt quá tồn kho hiện tại ('
                                        . $tonKho . ' ' . $phuTung->DonViTinh . ').';
                                @endphp

                                <div class="col-md-6">
                                    <div class="border rounded-3 p-3 h-100">
                                        <label
                                            for="{{ $inputId }}"
                                            class="form-label fw-semibold"
                                        >
                                            {{ $phuTung->TenPT }}
                                        </label>

                                        <p class="text-secondary small mb-2">
                                            {{ number_format($gia, 0, ',', '.') }} ₫
                                            / {{ $phuTung->DonViTinh }}
                                            — Tồn: {{ $tonKho }}
                                        </p>

                                        @if(!$phuTung->HoatDong)
                                            <p class="text-danger small">
                                                Phụ tùng đã ngừng hoạt động.
                                                Để số lượng bằng 0.
                                            </p>
                                        @elseif($tonKho === 0)
                                            <p class="text-danger small">
                                                Phụ tùng đã hết hàng.
                                                Để số lượng bằng 0.
                                            </p>
                                        @endif

                                        <input
                                            type="number"
                                            id="{{ $inputId }}"
                                            name="phu_tungs[{{ $phuTung->MaPT }}]"
                                            value="{{ old($errorKey, $soLuongMacDinh) }}"
                                            min="0"
                                            max="{{ $tonKho }}"
                                            step="1"
                                            data-stock="{{ $tonKho }}"
                                            data-max-message="{{ $stockMessage }}"
                                            class="form-control @error($errorKey) is-invalid @enderror"
                                            aria-describedby="{{ $inputId }}Error"
                                            required
                                        >

                                        <div
                                            id="{{ $inputId }}Error"
                                            class="invalid-feedback @error($errorKey) d-block @enderror"
                                        >{{ $errors->first($errorKey) }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-secondary">
                                    Chưa có phụ tùng trong danh mục.
                                </div>
                            @endforelse
                        </div>
                    </section>
                </div>

                <div class="col-lg-4">
                    <aside class="ktv-panel">
                        <h2 class="h5 fw-bold mb-3">
                            Phiếu kiểm tra
                        </h2>

                        <p class="text-secondary">
                            Khách hàng:
                            <strong class="text-break">
                                {{ $lichHen->taiKhoan->HoTen }}
                            </strong>
                        </p>

                        @if($phieu)
                            <p class="text-secondary mb-2">
                                Mã phiếu: #{{ $phieu->MaPKT }}
                            </p>

                            <p class="text-secondary">
                                Trạng thái: {{ $phieu->ten_trang_thai }}
                            </p>

                            <div class="border-top pt-3 mb-4">
                                <p class="text-secondary small mb-1">
                                    Tổng bản nháp đã lưu, trước ưu đãi
                                </p>

                                <p class="fs-4 fw-bold mb-0">
                                    {{ number_format($phieu->TongTruocGiam, 0, ',', '.') }} ₫
                                </p>
                            </div>
                        @endif

                        <div class="alert alert-info small">
                            Lưu nháp để tiếp tục chỉnh sửa kết quả kiểm tra.
                            Nội dung chưa được gửi cho khách xác nhận.
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Lưu nháp phiếu kiểm tra
                        </button>

                        <a
                            id="inspection-preview-link"
                            href="{{ route('ktv.kiemtra.preview', $lichHen->MaLH) }}"
                            class="btn btn-outline-primary w-100 mt-2 {{ $duocXemLai ? '' : 'd-none' }}"
                            data-saved="{{ $duocXemLai ? '1' : '0' }}"
                        >
                            Xem lại và gửi khách
                        </a>

                        <p
                            id="inspection-save-reminder"
                            class="small text-secondary mt-2 mb-0"
                            role="status"
                            aria-live="polite"
                        >
                            {{ $duocXemLai
                                ? 'Nội dung đã được lưu. Bạn có thể xem lại trước khi gửi khách.'
                                : 'Vui lòng lưu phiếu nháp thành công trước khi xem lại và gửi khách.' }}
                        </p>

                        <a
                            href="{{ route('ktv.lichhen.show', $lichHen->MaLH) }}"
                            class="btn btn-outline-secondary w-100 mt-3"
                        >
                            Quay lại công việc
                        </a>
                    </aside>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            function initInspectionSaveState() {
                const form = document.getElementById('inspection-draft-form');
                const previewLink = document.getElementById(
                    'inspection-preview-link'
                );
                const reminder = document.getElementById(
                    'inspection-save-reminder'
                );

                if (!form || !previewLink || !reminder) {
                    return;
                }

                const hasSavedVersion = previewLink.dataset.saved === '1';

                function formState() {
                    return JSON.stringify(
                        Array.from(new FormData(form).entries()).map(
                            ([name, value]) => [name, String(value)]
                        )
                    );
                }

                const savedState = formState();

                function canPreview() {
                    return hasSavedVersion && formState() === savedState;
                }

                function updateSaveState() {
                    const allowed = canPreview();

                    previewLink.classList.toggle('d-none', !allowed);

                    if (allowed) {
                        reminder.textContent =
                            'Nội dung đã được lưu. Bạn có thể xem lại trước khi gửi khách.';
                    } else if (hasSavedVersion) {
                        reminder.textContent =
                            'Bạn có thay đổi chưa lưu. Vui lòng lưu thay đổi trước khi gửi khách.';
                    } else {
                        reminder.textContent =
                            'Vui lòng lưu phiếu nháp thành công trước khi xem lại và gửi khách.';
                    }
                }

                form.addEventListener('input', updateSaveState);
                form.addEventListener('change', updateSaveState);

                form.addEventListener('reset', () => {
                    requestAnimationFrame(updateSaveState);
                });

                previewLink.addEventListener('click', (event) => {
                    if (!canPreview()) {
                        event.preventDefault();
                        updateSaveState();
                    }
                });

                window.addEventListener('pageshow', updateSaveState);

                updateSaveState();
            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    initInspectionSaveState,
                    { once: true }
                );
            } else {
                initInspectionSaveState();
            }
        })();
    </script>
@endpush
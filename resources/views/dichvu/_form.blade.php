@php
    $isEdit = isset($dichvu);
    $selectedLoai = old('MaLoaiDV', $isEdit ? $dichvu->MaLoaiDV : '');
@endphp

<div class="row g-4">
    <div class="col-md-6">
        <label for="TenDV" class="form-label fw-semibold">
            Tên dịch vụ <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            id="TenDV"
            name="TenDV"
            value="{{ old('TenDV', $isEdit ? $dichvu->TenDV : '') }}"
            class="form-control @error('TenDV') is-invalid @enderror"
            maxlength="100"
            placeholder="Ví dụ: Thay dầu động cơ"
            required
        >

        @error('TenDV')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="MaLoaiDV" class="form-label fw-semibold">
            Loại dịch vụ <span class="text-danger">*</span>
        </label>

        <select
            id="MaLoaiDV"
            name="MaLoaiDV"
            class="form-select @error('MaLoaiDV') is-invalid @enderror"
            required
        >
            <option value="">Chọn loại dịch vụ</option>

            @if ($isEdit && ! $loaiDichVuList->contains('MaLoaiDV', $dichvu->MaLoaiDV))
                <option
                    value="{{ $dichvu->MaLoaiDV }}"
                    @selected((string) $selectedLoai === (string) $dichvu->MaLoaiDV)
                >
                    {{ $dichvu->loaiDichVu?->TenLoaiDV ?? 'Loại dịch vụ hiện tại' }}
                    — ngừng hoạt động
                </option>
            @endif

            @foreach ($loaiDichVuList as $loai)
                <option
                    value="{{ $loai->MaLoaiDV }}"
                    @selected((string) $selectedLoai === (string) $loai->MaLoaiDV)
                >
                    {{ $loai->TenLoaiDV }}
                </option>
            @endforeach
        </select>

        @error('MaLoaiDV')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="Gia" class="form-label fw-semibold">
            Giá dịch vụ (đồng) <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            id="Gia"
            name="Gia"
            value="{{ old('Gia', $isEdit ? $dichvu->Gia : '') }}"
            class="form-control @error('Gia') is-invalid @enderror"
            min="0"
            max="9999999999.99"
            step="0.01"
            placeholder="Ví dụ: 350000"
            required
        >

        @error('Gia')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if ($isEdit)
        <div class="col-md-6">
            <label for="TrangThai" class="form-label fw-semibold">
                Trạng thái
            </label>

            <select
                id="TrangThai"
                name="TrangThai"
                class="form-select @error('TrangThai') is-invalid @enderror"
                required
            >
                <option
                    value="HOAT_DONG"
                    @selected(old('TrangThai', $dichvu->TrangThai) === 'HOAT_DONG')
                >
                    Hoạt động
                </option>

                <option
                    value="NGUNG_HOAT_DONG"
                    @selected(old('TrangThai', $dichvu->TrangThai) === 'NGUNG_HOAT_DONG')
                >
                    Ngừng hoạt động
                </option>
            </select>

            @error('TrangThai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endif

    <div class="col-12">
        <label for="MoTa" class="form-label fw-semibold">Mô tả</label>

        <textarea
            id="MoTa"
            name="MoTa"
            rows="5"
            maxlength="500"
            class="form-control @error('MoTa') is-invalid @enderror"
            placeholder="Mô tả nội dung và phạm vi dịch vụ..."
        >{{ old('MoTa', $isEdit ? $dichvu->MoTa : '') }}</textarea>

        @error('MoTa')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">Tối đa 500 ký tự.</div>
    </div>

    <div class="col-12">
        <label for="HinhAnh" class="form-label fw-semibold">
            {{ $isEdit ? 'Hình ảnh mới' : 'Hình ảnh' }}
        </label>

        <input
            type="file"
            id="HinhAnh"
            name="HinhAnh"
            class="form-control @error('HinhAnh') is-invalid @enderror"
            accept=".jpg,.jpeg,.png,.webp"
        >

        @error('HinhAnh')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">
            JPG, JPEG, PNG hoặc WEBP. Dung lượng tối đa 2 MB.
            @if ($isEdit)
                Để trống để giữ ảnh hiện tại.
            @endif
        </div>

        @if ($isEdit && $dichvu->HinhAnh)
            <div class="mt-3">
                <div class="small text-secondary mb-2">
                    Hình ảnh hiện tại
                </div>

                <img
                    src="{{ asset('storage/' . $dichvu->HinhAnh) }}"
                    alt="{{ $dichvu->TenDV }}"
                    width="180"
                    height="120"
                    class="rounded"
                    style="object-fit: cover;"
                >
            </div>
        @endif
    </div>
</div>

<div class="d-flex gap-2 mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'Lưu thay đổi' : 'Lưu dịch vụ' }}
    </button>

    <a href="{{ route('dichvu.index') }}" class="btn btn-outline-secondary">
        Hủy
    </a>
</div>
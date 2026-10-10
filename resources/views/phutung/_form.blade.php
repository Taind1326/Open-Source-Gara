@php
    $isEdit = isset($phuTung);
@endphp

<div class="row g-4">
    <div class="col-md-6">
        <label for="TenPT" class="form-label fw-semibold">
            Tên phụ tùng <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            id="TenPT"
            name="TenPT"
            value="{{ old('TenPT', $isEdit ? $phuTung->TenPT : '') }}"
            class="form-control @error('TenPT') is-invalid @enderror"
            maxlength="100"
            placeholder="Ví dụ: Lọc dầu động cơ"
            required
        >

        @error('TenPT')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="DonViTinh" class="form-label fw-semibold">
            Đơn vị tính <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            id="DonViTinh"
            name="DonViTinh"
            value="{{ old('DonViTinh', $isEdit ? $phuTung->DonViTinh : '') }}"
            class="form-control @error('DonViTinh') is-invalid @enderror"
            maxlength="30"
            placeholder="Ví dụ: Cái, Bộ, Bình"
            required
        >

        @error('DonViTinh')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="Gia" class="form-label fw-semibold">
            Đơn giá (đồng) <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            id="Gia"
            name="Gia"
            value="{{ old('Gia', $isEdit ? $phuTung->Gia : '') }}"
            class="form-control @error('Gia') is-invalid @enderror"
            min="0"
            max="9999999999.99"
            step="0.01"
            required
        >

        @error('Gia')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="SoLuongTon" class="form-label fw-semibold">
            Số lượng tồn <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            id="SoLuongTon"
            name="SoLuongTon"
            value="{{ old('SoLuongTon', $isEdit ? $phuTung->SoLuongTon : 0) }}"
            class="form-control @error('SoLuongTon') is-invalid @enderror"
            min="0"
            step="1"
            required
        >

        @error('SoLuongTon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

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
                value="DANG_SU_DUNG"
                @selected(old('TrangThai', $isEdit ? $phuTung->TrangThai : 'DANG_SU_DUNG') === 'DANG_SU_DUNG')
            >
                Đang sử dụng
            </option>

            <option
                value="NGUNG_SU_DUNG"
                @selected(old('TrangThai', $isEdit ? $phuTung->TrangThai : 'DANG_SU_DUNG') === 'NGUNG_SU_DUNG')
            >
                Ngừng sử dụng
            </option>
        </select>

        @error('TrangThai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="d-flex gap-2 mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'Lưu thay đổi' : 'Lưu phụ tùng' }}
    </button>

    <a href="{{ route('phutung.index') }}" class="btn btn-outline-secondary">
        Hủy
    </a>
</div>
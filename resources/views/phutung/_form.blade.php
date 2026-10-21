@php
    $isEdit = isset($phuTung) && $phuTung->exists;
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label for="TenPT" class="form-label fw-semibold">
            Tên phụ tùng <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            id="TenPT"
            name="TenPT"
            value="{{ old('TenPT', $isEdit ? $phuTung->TenPT : '') }}"
            class="form-control @error('TenPT') is-invalid @enderror"
            maxlength="150"
            required
        >

        @error('TenPT')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="DonViTinh" class="form-label fw-semibold">
            Đơn vị tính <span class="text-danger">*</span>
        </label>

        <input
            type="text"
            id="DonViTinh"
            name="DonViTinh"
            value="{{ old('DonViTinh', $isEdit ? $phuTung->DonViTinh : '') }}"
            class="form-control @error('DonViTinh') is-invalid @enderror"
            placeholder="Cái, bộ, lít..."
            maxlength="30"
            required
        >

        @error('DonViTinh')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="GiaBan" class="form-label fw-semibold">
            Giá bán (đ) <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            id="GiaBan"
            name="GiaBan"
            value="{{ old('GiaBan', $isEdit ? $phuTung->GiaBan : '') }}"
            class="form-control @error('GiaBan') is-invalid @enderror"
            min="0"
            max="999999999999"
            step="1"
            required
        >

        @error('GiaBan')
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
            max="2147483647"
            step="1"
            required
        >

        @error('SoLuongTon')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="HoatDong" class="form-label fw-semibold">
            Trạng thái <span class="text-danger">*</span>
        </label>

        <select
            id="HoatDong"
            name="HoatDong"
            class="form-select @error('HoatDong') is-invalid @enderror"
            required
        >
            <option value="">Chọn trạng thái</option>

            <option
                value="1"
                @selected((string) old('HoatDong', $isEdit ? (int) $phuTung->HoatDong : 1) === '1')
            >
                Đang sử dụng
            </option>

            <option
                value="0"
                @selected((string) old('HoatDong', $isEdit ? (int) $phuTung->HoatDong : 1) === '0')
            >
                Ngừng sử dụng
            </option>
        </select>

        @error('HoatDong')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'Lưu thay đổi' : 'Thêm phụ tùng' }}
    </button>

    <a href="{{ route('phutung.index') }}" class="btn btn-outline-secondary">
        Hủy
    </a>
</div>
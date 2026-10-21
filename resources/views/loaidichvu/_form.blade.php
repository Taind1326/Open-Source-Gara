@php
    $isEdit = isset($loaidichvu) && $loaidichvu->exists;
@endphp

<div class="mb-3">
    <label for="TenLoaiDV" class="form-label fw-semibold">
        Tên loại dịch vụ <span class="text-danger">*</span>
    </label>

    <input
        type="text"
        id="TenLoaiDV"
        name="TenLoaiDV"
        value="{{ old('TenLoaiDV', $isEdit ? $loaidichvu->TenLoaiDV : '') }}"
        class="form-control @error('TenLoaiDV') is-invalid @enderror"
        maxlength="100"
        required
    >

    @error('TenLoaiDV')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="MoTa" class="form-label fw-semibold">
        Mô tả
    </label>

    <textarea
        id="MoTa"
        name="MoTa"
        class="form-control @error('MoTa') is-invalid @enderror"
        rows="4"
        maxlength="5000"
    >{{ old('MoTa', $isEdit ? $loaidichvu->MoTa : '') }}</textarea>

    @error('MoTa')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
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
            @selected((string) old('HoatDong', $isEdit ? (int) $loaidichvu->HoatDong : 1) === '1')
        >
            Hoạt động
        </option>

        <option
            value="0"
            @selected((string) old('HoatDong', $isEdit ? (int) $loaidichvu->HoatDong : 1) === '0')
        >
            Ngừng hoạt động
        </option>
    </select>

    @error('HoatDong')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    <p class="form-text mb-0">
        Khi ngừng hoạt động, các dịch vụ thuộc nhóm sẽ không được
        hiển thị trên danh sách dịch vụ công khai.
    </p>
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'Lưu thay đổi' : 'Thêm loại dịch vụ' }}
    </button>

    <a href="{{ route('loaidichvu.index') }}" class="btn btn-outline-secondary">
        Hủy
    </a>
</div>
@php
    $isEdit = isset($dichvu) && $dichvu->exists;
    $hasImage = $isEdit && filled($dichvu->HinhAnh);
@endphp

<div class="row g-3">
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

            @foreach($loaiDichVuList as $loai)
                <option
                    value="{{ $loai->MaLoaiDV }}"
                    @selected(
                        (string) old('MaLoaiDV', $isEdit ? $dichvu->MaLoaiDV : '')
                        === (string) $loai->MaLoaiDV
                    )
                >
                    {{ $loai->TenLoaiDV }}{{ $loai->HoatDong ? '' : ' — Ngừng hoạt động' }}
                </option>
            @endforeach
        </select>

        @error('MaLoaiDV')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">
            Dịch vụ thuộc nhóm ngừng hoạt động sẽ được ẩn khỏi danh sách công khai.
        </div>
    </div>

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
            maxlength="150"
            required
        >

        @error('TenDV')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="Gia" class="form-label fw-semibold">
            Giá dịch vụ (đ) <span class="text-danger">*</span>
        </label>

        <input
            type="number"
            id="Gia"
            name="Gia"
            value="{{ old('Gia', $isEdit ? $dichvu->Gia : '') }}"
            class="form-control @error('Gia') is-invalid @enderror"
            min="0"
            max="999999999999"
            step="1"
            required
        >

        @error('Gia')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text">
            Giá trọn gói của dịch vụ.
        </div>
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
                @selected(
                    (string) old('HoatDong', $isEdit ? (int) $dichvu->HoatDong : 1)
                    === '1'
                )
            >
                Hoạt động
            </option>

            <option
                value="0"
                @selected(
                    (string) old('HoatDong', $isEdit ? (int) $dichvu->HoatDong : 1)
                    === '0'
                )
            >
                Ngừng hoạt động
            </option>
        </select>

        @error('HoatDong')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="MoTa" class="form-label fw-semibold">
            Mô tả dịch vụ
        </label>

        <textarea
            id="MoTa"
            name="MoTa"
            class="form-control @error('MoTa') is-invalid @enderror"
            rows="5"
            maxlength="10000"
        >{{ old('MoTa', $isEdit ? $dichvu->MoTa : '') }}</textarea>

        @error('MoTa')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="HinhAnh" class="form-label fw-semibold">
            Hình ảnh dịch vụ

            @unless($hasImage)
                <span class="text-danger">*</span>
            @endunless
        </label>

        <input
            type="file"
            id="HinhAnh"
            name="HinhAnh"
            class="form-control @error('HinhAnh') is-invalid @enderror"
            accept="image/jpeg,image/png,image/webp"
            aria-describedby="HinhAnhHelp"
            @if(!$hasImage) required @endif
        >

        @error('HinhAnh')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div id="HinhAnhHelp" class="form-text">
            JPG, JPEG, PNG hoặc WebP, tối đa 2 MB.

            @if($hasImage)
                Không chọn ảnh mới nếu muốn giữ ảnh hiện tại.
            @else
                Vui lòng chọn ảnh cho dịch vụ.
            @endif
        </div>

        @if($hasImage)
            <div class="mt-3">
                <p class="small text-secondary mb-2">
                    Ảnh hiện tại
                </p>

                <img
                    src="{{ asset('storage/' . $dichvu->HinhAnh) }}"
                    alt="Ảnh hiện tại của {{ $dichvu->TenDV }}"
                    class="rounded-3 border"
                    width="280"
                    height="168"
                    style="object-fit: cover; max-width: 100%;"
                >
            </div>
        @endif
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'Lưu thay đổi' : 'Thêm dịch vụ' }}
    </button>

    <a href="{{ route('dichvu.index') }}" class="btn btn-outline-secondary">
        Hủy
    </a>
</div>
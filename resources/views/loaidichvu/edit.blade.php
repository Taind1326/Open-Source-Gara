@extends('admin.layout')

@section('title', 'Cập nhật loại dịch vụ')

@section('content')
    <div class="mb-4">
        <a
            href="{{ route('loaidichvu.index') }}"
            class="text-decoration-none"
        >
            ← Danh sách loại dịch vụ
        </a>

        <h1 class="h4 fw-bold mt-3 mb-1">Cập nhật loại dịch vụ</h1>

        <p class="text-secondary mb-0">
            Chỉnh sửa thông tin loại dịch vụ #{{ $loaidichvu->MaLoaiDV }}.
        </p>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body p-4">
                    <form
                        action="{{ route('loaidichvu.update', $loaidichvu->MaLoaiDV) }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="TenLoaiDV" class="form-label fw-semibold">
                                Tên loại dịch vụ
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                id="TenLoaiDV"
                                name="TenLoaiDV"
                                value="{{ old('TenLoaiDV', $loaidichvu->TenLoaiDV) }}"
                                class="form-control @error('TenLoaiDV') is-invalid @enderror"
                                maxlength="100"
                                required
                            >

                            @error('TenLoaiDV')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="MoTa" class="form-label fw-semibold">
                                Mô tả
                            </label>

                            <textarea
                                id="MoTa"
                                name="MoTa"
                                rows="5"
                                maxlength="500"
                                class="form-control @error('MoTa') is-invalid @enderror"
                            >{{ old('MoTa', $loaidichvu->MoTa) }}</textarea>

                            @error('MoTa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">Tối đa 500 ký tự.</div>
                        </div>

                        <div class="mb-4">
                            <label for="HinhAnh" class="form-label fw-semibold">
                                Hình ảnh mới
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
                                JPG, JPEG, PNG hoặc WEBP, tối đa 2 MB.
                                Để trống nếu muốn giữ ảnh hiện tại.
                            </div>

                            @if ($loaidichvu->HinhAnh)
                                <div class="mt-3">
                                    <div class="small text-secondary mb-2">
                                        Hình ảnh hiện tại
                                    </div>

                                    <img
                                        src="{{ asset('storage/' . $loaidichvu->HinhAnh) }}"
                                        alt="{{ $loaidichvu->TenLoaiDV }}"
                                        width="180"
                                        height="120"
                                        class="rounded"
                                        style="object-fit: cover;"
                                    >
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
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
                                    @selected(old('TrangThai', $loaidichvu->TrangThai) === 'HOAT_DONG')
                                >
                                    Hoạt động
                                </option>

                                <option
                                    value="NGUNG_HOAT_DONG"
                                    @selected(old('TrangThai', $loaidichvu->TrangThai) === 'NGUNG_HOAT_DONG')
                                >
                                    Ngừng hoạt động
                                </option>
                            </select>

                            @error('TrangThai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">
                                Khi ngừng hoạt động, nhóm này và các dịch vụ thuộc
                                nhóm sẽ không hiển thị trên trang dịch vụ public.
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary">
                                Lưu thay đổi
                            </button>

                            <a
                                href="{{ route('loaidichvu.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
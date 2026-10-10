@extends('admin.layout')

@section('title', 'Thêm loại dịch vụ')

@section('content')
    <div class="mb-4">
        <a
            href="{{ route('loaidichvu.index') }}"
            class="text-decoration-none"
        >
            ← Danh sách loại dịch vụ
        </a>

        <h1 class="h4 fw-bold mt-3 mb-1">Thêm loại dịch vụ</h1>

        <p class="text-secondary mb-0">
            Loại dịch vụ mới sẽ được đặt ở trạng thái hoạt động.
        </p>
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body p-4">
                    <form
                        action="{{ route('loaidichvu.store') }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        @csrf

                        <div class="mb-4">
                            <label for="TenLoaiDV" class="form-label fw-semibold">
                                Tên loại dịch vụ
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                id="TenLoaiDV"
                                name="TenLoaiDV"
                                value="{{ old('TenLoaiDV') }}"
                                class="form-control @error('TenLoaiDV') is-invalid @enderror"
                                maxlength="100"
                                placeholder="Ví dụ: Bảo dưỡng định kỳ"
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
                                placeholder="Giới thiệu ngắn về nhóm dịch vụ..."
                            >{{ old('MoTa') }}</textarea>

                            @error('MoTa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-text">Tối đa 500 ký tự.</div>
                        </div>

                        <div class="mb-4">
                            <label for="HinhAnh" class="form-label fw-semibold">
                                Hình ảnh
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
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary">
                                Lưu loại dịch vụ
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
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cập nhật dịch vụ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="card shadow-sm">

        <div class="card-header">
            <h4 class="mb-0">Cập nhật dịch vụ</h4>
        </div>

        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dichvu.update', $dichvu->MaDV) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">
                        Loại dịch vụ
                    </label>

                    <select name="MaLoaiDV"
                            class="form-select">

                        @foreach($loaiDichVuList as $loai)

                            <option value="{{ $loai->MaLoaiDV }}"
                                @selected(
                                    old('MaLoaiDV', $dichvu->MaLoaiDV)
                                    == $loai->MaLoaiDV
                                )>
                                {{ $loai->TenLoaiDV }}
                            </option>

                        @endforeach

                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Tên dịch vụ
                    </label>

                    <input type="text"
                           name="TenDV"
                           value="{{ old('TenDV', $dichvu->TenDV) }}"
                           class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Mô tả
                    </label>

                    <textarea name="MoTa"
                              rows="4"
                              class="form-control">{{ old('MoTa', $dichvu->MoTa) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Giá
                    </label>

                    <div class="input-group">

                        <input type="number"
                               name="Gia"
                               value="{{ old('Gia', $dichvu->Gia) }}"
                               min="0"
                               step="1000"
                               class="form-control">

                        <span class="input-group-text">VNĐ</span>

                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Hình ảnh mới
                    </label>

                    <input type="file"
                           name="HinhAnh"
                           accept="image/*"
                           class="form-control">

                    @if($dichvu->HinhAnh)

                        <div class="mt-2">
                            <img
                                src="{{ asset('storage/' . $dichvu->HinhAnh) }}"
                                width="120"
                                class="rounded">
                        </div>

                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Trạng thái
                    </label>

                    <select name="TrangThai"
                            class="form-select">

                        <option value="HOAT_DONG"
                            @selected(
                                old('TrangThai', $dichvu->TrangThai)
                                === 'HOAT_DONG'
                            )>
                            Hoạt động
                        </option>

                        <option value="NGUNG_HOAT_DONG"
                            @selected(
                                old('TrangThai', $dichvu->TrangThai)
                                === 'NGUNG_HOAT_DONG'
                            )>
                            Ngừng hoạt động
                        </option>

                    </select>
                </div>

                <button class="btn btn-primary">
                    Cập nhật
                </button>

                <a href="{{ route('dichvu.index') }}"
                   class="btn btn-secondary">
                    Quay lại
                </a>

            </form>

        </div>
    </div>

</div>

</body>
</html>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm dịch vụ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="card shadow-sm">

        <div class="card-header">
            <h4 class="mb-0">Thêm dịch vụ</h4>
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

            <form action="{{ route('dichvu.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Loại dịch vụ
                    </label>

                    <select name="MaLoaiDV"
                            class="form-select">

                        <option value="">
                            -- Chọn loại dịch vụ --
                        </option>

                        @foreach($loaiDichVuList as $loai)
                            <option value="{{ $loai->MaLoaiDV }}"
                                @selected(old('MaLoaiDV') == $loai->MaLoaiDV)>
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
                           value="{{ old('TenDV') }}"
                           class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Mô tả
                    </label>

                    <textarea name="MoTa"
                              rows="4"
                              class="form-control">{{ old('MoTa') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Giá dịch vụ
                    </label>

                    <div class="input-group">
                        <input type="number"
                               name="Gia"
                               value="{{ old('Gia') }}"
                               min="0"
                               step="1000"
                               class="form-control">

                        <span class="input-group-text">VNĐ</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Hình ảnh
                    </label>

                    <input type="file"
                           name="HinhAnh"
                           accept="image/*"
                           class="form-control">
                </div>

                <button class="btn btn-primary">
                    Lưu dịch vụ
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
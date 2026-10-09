<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm loại dịch vụ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="card shadow-sm">

        <div class="card-header">
            <h4 class="mb-0">Thêm loại dịch vụ</h4>
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

            <form
                action="{{ route('loaidichvu.store') }}"
                method="POST"
                enctype="multipart/form-data">

                @csrf

                <div class="mb-3">
                    <label class="form-label">
                        Tên loại dịch vụ
                    </label>

                    <input
                        type="text"
                        name="TenLoaiDV"
                        value="{{ old('TenLoaiDV') }}"
                        class="form-control">

                    @error('TenLoaiDV')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Mô tả
                    </label>

                    <textarea
                        name="MoTa"
                        rows="4"
                        class="form-control">{{ old('MoTa') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        Hình ảnh
                    </label>

                    <input
                        type="file"
                        name="HinhAnh"
                        class="form-control"
                        accept="image/*">
                </div>

                <button class="btn btn-primary">
                    Lưu loại dịch vụ
                </button>

                <a href="{{ route('loaidichvu.index') }}"
                   class="btn btn-secondary">
                    Quay lại
                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>
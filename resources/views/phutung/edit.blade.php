<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sửa phụ tùng</title>

    @vite('resources/css/phutung.css')
</head>

<body>

<div class="container page">

    <div class="page-header">

        <div>
            <h1 class="page-title">Sửa phụ tùng</h1>

            <p class="page-description">
                Cập nhật thông tin phụ tùng trong kho
            </p>
        </div>

        <a
            href="{{ route('phutung.index') }}"
            class="btn btn-secondary"
        >
            ← Quay lại
        </a>

    </div>

    @if($errors->any())
        <div class="alert alert-danger">

            <strong>Có lỗi xảy ra:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif

    <div class="card form-card">

        <form
            action="{{ route('phutung.update', $phuTung->MaPT) }}"
            method="POST"
        >

            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">
                        Mã phụ tùng
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="{{ $phuTung->MaPT }}"
                        readonly
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Tên phụ tùng
                    </label>

                    <input
                        type="text"
                        name="TenPT"
                        class="form-control"
                        value="{{ old('TenPT', $phuTung->TenPT) }}"
                        placeholder="Nhập tên phụ tùng"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Đơn vị tính
                    </label>

                    <input
                        type="text"
                        name="DonViTinh"
                        class="form-control"
                        value="{{ old('DonViTinh', $phuTung->DonViTinh) }}"
                        placeholder="Ví dụ: Cái, Bộ, Bình"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Giá
                    </label>

                    <div class="input-group">

                        <input
                            type="number"
                            name="Gia"
                            class="form-control no-spin"
                            value="{{ old('Gia', $phuTung->Gia) }}"
                            min="0"
                            step="0.01"
                        >

                        <span class="input-group-text">
                            đ
                        </span>

                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Số lượng tồn
                    </label>

                    <input
                        type="number"
                        name="SoLuongTon"
                        class="form-control"
                        value="{{ old('SoLuongTon', $phuTung->SoLuongTon) }}"
                        min="0"
                    >
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Trạng thái
                    </label>

                    <select
                        name="TrangThai"
                        class="form-select"
                    >

                        <option
                            value="DANG_SU_DUNG"
                            {{ old('TrangThai', $phuTung->TrangThai) === 'DANG_SU_DUNG' ? 'selected' : '' }}
                        >
                            Đang sử dụng
                        </option>

                        <option
                            value="NGUNG_SU_DUNG"
                            {{ old('TrangThai', $phuTung->TrangThai) === 'NGUNG_SU_DUNG' ? 'selected' : '' }}
                        >
                            Ngừng sử dụng
                        </option>

                    </select>
                </div>

            </div>

            <div class="form-actions">

                <a
                    href="{{ route('phutung.index') }}"
                    class="btn btn-secondary"
                >
                    Hủy
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Lưu thay đổi
                </button>

            </div>

        </form>

    </div>

</div>

</body>
</html>
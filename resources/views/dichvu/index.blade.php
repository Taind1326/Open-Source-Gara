<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý dịch vụ</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý dịch vụ</h2>

        <a href="{{ route('dichvu.create') }}"
           class="btn btn-primary">
            + Thêm dịch vụ
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET"
          action="{{ route('dichvu.index') }}"
          class="row g-2 mb-4">

        <div class="col-md-5">
            <input type="text"
                   name="tu_khoa"
                   value="{{ $tuKhoa }}"
                   class="form-control"
                   placeholder="Tìm theo tên dịch vụ...">
        </div>

        <div class="col-md-3">
            <select name="ma_loai" class="form-select">
                <option value="">Tất cả loại dịch vụ</option>

                @foreach($loaiDichVuList as $loai)
                    <option value="{{ $loai->MaLoaiDV }}"
                        @selected($maLoai == $loai->MaLoaiDV)>
                        {{ $loai->TenLoaiDV }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-dark w-100">
                Tìm kiếm
            </button>
        </div>

        <div class="col-md-2">
            <a href="{{ route('dichvu.index') }}"
               class="btn btn-secondary w-100">
                Làm mới
            </a>
        </div>

    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-dark">
                <tr>
                    <th>Mã</th>
                    <th>Hình ảnh</th>
                    <th>Tên dịch vụ</th>
                    <th>Loại dịch vụ</th>
                    <th>Giá</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
                </thead>

                <tbody>

                @forelse($danhSach as $item)

                    <tr>
                        <td>{{ $item->MaDV }}</td>

                        <td>
                            @if($item->HinhAnh)
                                <img
                                    src="{{ asset('storage/' . $item->HinhAnh) }}"
                                    width="80"
                                    height="60"
                                    style="object-fit: cover;"
                                    class="rounded">
                            @else
                                <span class="text-muted">
                                    Chưa có ảnh
                                </span>
                            @endif
                        </td>

                        <td>
                            <strong>{{ $item->TenDV }}</strong>

                            @if($item->MoTa)
                                <div class="small text-muted">
                                    {{ \Illuminate\Support\Str::limit($item->MoTa, 60) }}
                                </div>
                            @endif
                        </td>

                        <td>
                            {{ $item->loaiDichVu?->TenLoaiDV ?? 'Không xác định' }}
                        </td>

                        <td>
                            {{ number_format($item->Gia, 0, ',', '.') }} đ
                        </td>

                        <td>
                            @if($item->TrangThai === 'HOAT_DONG')
                                <span class="badge bg-success">
                                    Hoạt động
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    Ngừng hoạt động
                                </span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('dichvu.edit', $item->MaDV) }}"
                               class="btn btn-sm btn-warning">
                                Sửa
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="7"
                            class="text-center py-4 text-muted">
                            Chưa có dịch vụ.
                        </td>
                    </tr>

                @endforelse

                </tbody>
            </table>

        </div>
    </div>

    <div class="mt-3">
        {{ $danhSach->links() }}
    </div>

</div>

</body>
</html>
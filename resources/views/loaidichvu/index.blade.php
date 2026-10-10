@extends('admin.layout')

@section('title', 'Quản lý loại dịch vụ')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Loại dịch vụ</h1>

            <p class="text-secondary mb-0">
                Quản lý các nhóm dịch vụ của gara.
            </p>
        </div>

        <a href="{{ route('loaidichvu.create') }}" class="btn btn-primary">
            + Thêm loại dịch vụ
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('loaidichvu.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label for="tu_khoa" class="form-label">
                            Tìm loại dịch vụ
                        </label>

                        <input
                            type="search"
                            id="tu_khoa"
                            name="tu_khoa"
                            value="{{ $tuKhoa }}"
                            class="form-control"
                            placeholder="Nhập tên loại dịch vụ..."
                            maxlength="100"
                        >
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            Tìm kiếm
                        </button>
                    </div>

                    <div class="col-md-2">
                        <a
                            href="{{ route('loaidichvu.index') }}"
                            class="btn btn-outline-secondary w-100"
                        >
                            Làm mới
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4 border-bottom">
            <div class="fw-semibold">
                Danh sách loại dịch vụ
                <span class="badge bg-primary-subtle text-primary ms-2">
                    {{ $danhSach->total() }}
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Mã</th>
                        <th>Hình ảnh</th>
                        <th>Tên loại dịch vụ</th>
                        <th>Mô tả</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($danhSach as $item)
                        <tr>
                            <td class="ps-4">
                                #{{ $item->MaLoaiDV }}
                            </td>

                            <td>
                                @if ($item->HinhAnh)
                                    <img
                                        src="{{ asset('storage/' . $item->HinhAnh) }}"
                                        alt="{{ $item->TenLoaiDV }}"
                                        width="80"
                                        height="60"
                                        class="rounded"
                                        style="object-fit: cover;"
                                        loading="lazy"
                                    >
                                @else
                                    <span class="small text-secondary">
                                        Chưa có ảnh
                                    </span>
                                @endif
                            </td>

                            <td class="fw-semibold">
                                {{ $item->TenLoaiDV }}
                            </td>

                            <td>
                                {{ \Illuminate\Support\Str::limit($item->MoTa ?: 'Chưa có mô tả', 100) }}
                            </td>

                            <td>
                                @if ($item->TrangThai === 'HOAT_DONG')
                                    <span class="badge bg-success-subtle text-success">
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        Ngừng hoạt động
                                    </span>
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                <a
                                    href="{{ route('loaidichvu.edit', $item->MaLoaiDV) }}"
                                    class="btn btn-outline-primary btn-sm"
                                >
                                    Chỉnh sửa
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="fw-semibold mb-1">
                                    Không có loại dịch vụ phù hợp
                                </div>

                                <div class="text-secondary small">
                                    Thử từ khóa khác hoặc thêm loại dịch vụ mới.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-body border-top">
            {{ $danhSach->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection
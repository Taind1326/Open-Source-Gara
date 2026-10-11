@extends('admin.layout')

@section('title', 'Quản lý dịch vụ')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Dịch vụ</h1>

            <p class="text-secondary mb-0">
                Quản lý thông tin, hình ảnh và giá dịch vụ.
            </p>
        </div>

        <a href="{{ route('dichvu.create') }}" class="btn btn-primary">
            + Thêm dịch vụ
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('dichvu.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <label for="tu_khoa" class="form-label">
                            Tên dịch vụ
                        </label>

                        <input
                            type="search"
                            id="tu_khoa"
                            name="tu_khoa"
                            value="{{ $tuKhoa }}"
                            class="form-control"
                            maxlength="100"
                            placeholder="Nhập tên dịch vụ..."
                        >
                    </div>

                    <div class="col-lg-3">
                        <label for="ma_loai" class="form-label">
                            Loại dịch vụ
                        </label>

                        <select id="ma_loai" name="ma_loai" class="form-select">
                            <option value="">Tất cả loại dịch vụ</option>

                            @foreach ($loaiDichVuList as $loai)
                                <option
                                    value="{{ $loai->MaLoaiDV }}"
                                    @selected((string) $maLoai === (string) $loai->MaLoaiDV)
                                >
                                    {{ $loai->TenLoaiDV }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-6">
                        <button type="submit" class="btn btn-primary w-100">
                            Tìm kiếm
                        </button>
                    </div>

                    <div class="col-lg-2 col-6">
                        <a
                            href="{{ route('dichvu.index') }}"
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
                Danh sách dịch vụ

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
                        <th>Dịch vụ</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($danhSach as $item)
                        <tr>
                            <td class="ps-4">#{{ $item->MaDV }}</td>

                            <td>
                                @if ($item->HinhAnh)
                                    <img
                                        src="{{ asset('storage/' . $item->HinhAnh) }}"
                                        alt="{{ $item->TenDV }}"
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

                            <td>
                                <div class="fw-semibold">{{ $item->TenDV }}</div>

                                <div class="small text-secondary mt-1">
                                    {{ $item->loaiDichVu?->TenLoaiDV ?? 'Chưa có loại dịch vụ' }}
                                </div>

                                @if ($item->MoTa)
                                    <div class="small text-secondary mt-1">
                                        {{ \Illuminate\Support\Str::limit($item->MoTa, 80) }}
                                    </div>
                                @endif
                            </td>

                            <td class="fw-semibold text-nowrap">
                                {{ number_format($item->Gia, 0, ',', '.') }} đ
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

                                @if ($item->loaiDichVu?->TrangThai === 'NGUNG_HOAT_DONG')
                                    <div class="small text-secondary mt-1">
                                        Loại dịch vụ đã ngừng hoạt động
                                    </div>
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                <a
                                    href="{{ route('dichvu.edit', $item->MaDV) }}"
                                    class="btn btn-outline-primary btn-sm text-nowrap"
                                >
                                    Chỉnh sửa
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="fw-semibold mb-1">
                                    Không có dịch vụ phù hợp
                                </div>

                                <div class="small text-secondary">
                                    Thử thay đổi từ khóa hoặc loại dịch vụ.
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
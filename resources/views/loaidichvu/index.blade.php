@extends('admin.layout')

@section('title', 'Quản lý loại dịch vụ')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">
                Quản lý loại dịch vụ
            </h1>

            <p class="text-secondary mb-0">
                Quản lý nhóm dịch vụ và trạng thái hoạt động.
            </p>
        </div>

        <a href="{{ route('loaidichvu.create') }}" class="btn btn-primary">
            Thêm loại dịch vụ
        </a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form
                method="GET"
                action="{{ route('loaidichvu.index') }}"
                novalidate
            >
                <label for="category-search" class="form-label">
                    Tìm loại dịch vụ
                </label>

                <div class="d-flex flex-wrap align-items-start gap-2">
                    <div class="flex-grow-1">
                        <input
                            type="search"
                            id="category-search"
                            name="tu_khoa"
                            value="{{ old('tu_khoa', $tuKhoa) }}"
                            class="form-control @error('tu_khoa') is-invalid @enderror"
                            placeholder="Nhập tên loại dịch vụ..."
                            maxlength="100"
                        >

                        @error('tu_khoa')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Tìm kiếm
                    </button>

                    <a
                        href="{{ route('loaidichvu.index') }}"
                        class="btn btn-outline-secondary"
                    >
                        Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <p class="text-secondary small mb-3">
                {{ $danhSach->total() }} loại dịch vụ
            </p>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Mã</th>
                            <th scope="col">Tên loại dịch vụ</th>
                            <th scope="col">Mô tả</th>
                            <th scope="col" class="text-nowrap">
                                Trạng thái
                            </th>
                            <th scope="col" class="text-end">
                                Thao tác
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($danhSach as $item)
                            <tr>
                                <td class="text-secondary">
                                    #{{ $item->MaLoaiDV }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $item->TenLoaiDV }}
                                </td>

                                <td>
                                    {{ \Illuminate\Support\Str::limit(
                                        $item->MoTa ?: 'Chưa có mô tả.',
                                        110
                                    ) }}
                                </td>

                                <td class="text-nowrap">
                                    <span class="badge {{ $item->HoatDong ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $item->HoatDong ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    <div class="d-flex flex-nowrap justify-content-end gap-2">
                                        <a
                                            href="{{ route('loaidichvu.edit', $item->MaLoaiDV) }}"
                                            class="btn btn-outline-primary btn-sm text-nowrap"
                                        >
                                            Sửa
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('loaidichvu.destroy', $item->MaLoaiDV) }}"
                                            class="m-0"
                                            novalidate
                                            data-confirm-title="Xóa loại dịch vụ"
                                            data-confirm="Bạn muốn xóa loại dịch vụ {{ $item->TenLoaiDV }}? Chỉ xóa được nhóm chưa chứa dịch vụ."
                                            data-confirm-accept="Xóa loại dịch vụ"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm text-nowrap"
                                            >
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-5">
                                    Chưa có loại dịch vụ phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($danhSach->hasPages())
                <div class="mt-4">
                    {{ $danhSach->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    <p class="small text-secondary mt-3 mb-0">
        Chọn Sửa để bật hoặc ngừng hoạt động.
        Chỉ có thể xóa loại dịch vụ chưa chứa dịch vụ;
        nhóm đã có dịch vụ có thể chuyển sang ngừng hoạt động để giữ lịch sử.
    </p>
@endsection
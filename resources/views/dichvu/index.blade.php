@extends('admin.layout')

@section('title', 'Quản lý dịch vụ')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">
                Quản lý dịch vụ
            </h1>

            <p class="text-secondary mb-0">
                Quản lý thông tin, giá trọn gói và trạng thái dịch vụ.
            </p>
        </div>

        <a href="{{ route('dichvu.create') }}" class="btn btn-primary">
            Thêm dịch vụ
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <form
                method="GET"
                action="{{ route('dichvu.index') }}"
                novalidate
            >
                <div class="row g-3 align-items-start">
                    <div class="col-md-5">
                        <label for="service-search" class="form-label">
                            Tên dịch vụ
                        </label>

                        <input
                            type="search"
                            id="service-search"
                            name="tu_khoa"
                            value="{{ old('tu_khoa', $tuKhoa) }}"
                            class="form-control @error('tu_khoa') is-invalid @enderror"
                            placeholder="Nhập tên dịch vụ..."
                            maxlength="100"
                        >

                        @error('tu_khoa')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="service-category" class="form-label">
                            Loại dịch vụ
                        </label>

                        <select
                            id="service-category"
                            name="ma_loai"
                            class="form-select @error('ma_loai') is-invalid @enderror"
                        >
                            <option value="">Tất cả loại dịch vụ</option>

                            @foreach($loaiDichVuList as $loai)
                                <option
                                    value="{{ $loai->MaLoaiDV }}"
                                    @selected((string) old('ma_loai', $maLoai) === (string) $loai->MaLoaiDV)
                                >
                                    {{ $loai->TenLoaiDV }}
                                </option>
                            @endforeach
                        </select>

                        @error('ma_loai')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex flex-wrap gap-2 mt-md-4 pt-md-2">
                            <button type="submit" class="btn btn-primary">
                                Tìm kiếm
                            </button>

                            <a
                                href="{{ route('dichvu.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                Đặt lại
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="text-secondary small mb-3">
                {{ $danhSach->total() }} dịch vụ
            </p>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Dịch vụ</th>
                            <th scope="col">Loại dịch vụ</th>

                            <th scope="col" class="text-end text-nowrap">
                                Giá dịch vụ
                            </th>

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
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($item->HinhAnh)
                                            <img
                                                src="{{ asset('storage/' . $item->HinhAnh) }}"
                                                alt="{{ $item->TenDV }}"
                                                class="rounded-3 flex-shrink-0"
                                                width="72"
                                                height="54"
                                                style="object-fit: cover;"
                                                loading="lazy"
                                            >
                                        @endif

                                        <div>
                                            <div class="fw-semibold">
                                                {{ $item->TenDV }}
                                            </div>

                                            <small class="text-secondary">
                                                #{{ $item->MaDV }}
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    {{ $item->loaiDichVu?->TenLoaiDV ?? 'Không có nhóm' }}

                                    @if(!$item->loaiDichVu?->HoatDong)
                                        <div class="small text-secondary">
                                            Nhóm ngừng hoạt động
                                        </div>
                                    @endif
                                </td>

                                <td class="text-end text-nowrap">
                                    {{ number_format($item->Gia, 0, ',', '.') }} đ
                                </td>

                                <td class="text-nowrap">
                                    <span class="badge {{ $item->HoatDong ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $item->HoatDong ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                    </span>

                                    @if($item->HoatDong && !$item->loaiDichVu?->HoatDong)
                                        <div class="small text-secondary mt-1">
                                            Đang ẩn do nhóm ngừng hoạt động
                                        </div>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="d-flex flex-nowrap justify-content-end gap-2">
                                        <a
                                            href="{{ route('dichvu.edit', $item->MaDV) }}"
                                            class="btn btn-outline-primary btn-sm text-nowrap"
                                        >
                                            Sửa
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('dichvu.destroy', $item->MaDV) }}"
                                            class="m-0"
                                            novalidate
                                            data-confirm-title="Xóa dịch vụ"
                                            data-confirm="Bạn muốn xóa dịch vụ {{ $item->TenDV }}? Dịch vụ đã có trong lịch hẹn sẽ không thể xóa."
                                            data-confirm-accept="Xóa dịch vụ"
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
                                    Chưa có dịch vụ phù hợp.
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
        Chọn Sửa để cập nhật thông tin, bật hoặc ngừng hoạt động.
        Chỉ có thể xóa dịch vụ chưa có trong lịch hẹn.
    </p>
@endsection
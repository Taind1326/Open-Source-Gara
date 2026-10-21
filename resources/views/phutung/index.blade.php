@extends('admin.layout')

@section('title', 'Quản lý phụ tùng')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-2">
                Quản lý phụ tùng
            </h1>

            <p class="text-secondary mb-0">
                Quản lý giá bán, số lượng tồn và trạng thái sử dụng.
            </p>
        </div>

        <a href="{{ route('phutung.create') }}" class="btn btn-primary">
            Thêm phụ tùng
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <p class="text-secondary small mb-3">
                {{ $danhSach->count() }} phụ tùng
            </p>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Mã</th>
                            <th scope="col">Tên phụ tùng</th>
                            <th scope="col" class="text-nowrap">Đơn vị tính</th>
                            <th scope="col" class="text-end text-nowrap">Giá bán</th>
                            <th scope="col" class="text-end text-nowrap">Tồn kho</th>
                            <th scope="col" class="text-nowrap">Trạng thái</th>
                            <th scope="col" class="text-end">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($danhSach as $phuTung)
                            <tr>
                                <td class="text-secondary">
                                    #{{ $phuTung->MaPT }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $phuTung->TenPT }}
                                </td>

                                <td>
                                    {{ $phuTung->DonViTinh }}
                                </td>

                                <td class="text-end text-nowrap">
                                    {{ number_format($phuTung->GiaBan, 0, ',', '.') }} đ
                                </td>

                                <td class="text-end">
                                    <span class="{{ $phuTung->SoLuongTon === 0 ? 'text-danger fw-semibold' : '' }}">
                                        {{ number_format($phuTung->SoLuongTon, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td class="text-nowrap">
                                    <span class="badge {{ $phuTung->HoatDong ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ $phuTung->HoatDong ? 'Đang sử dụng' : 'Ngừng sử dụng' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a
                                            href="{{ route('phutung.edit', $phuTung->MaPT) }}"
                                            class="btn btn-outline-primary btn-sm text-nowrap"
                                        >
                                            Sửa
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('phutung.destroy', $phuTung->MaPT) }}"
                                            class="m-0"
                                            novalidate
                                            data-confirm-title="Xóa phụ tùng"
                                            data-confirm="Bạn muốn xóa phụ tùng {{ $phuTung->TenPT }}? Phụ tùng đã có trong lịch hẹn sẽ không thể xóa."
                                            data-confirm-accept="Xóa phụ tùng"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger btn-sm"
                                            >
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-5">
                                    Chưa có phụ tùng.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p class="text-secondary small mt-3 mb-0">
        Phụ tùng đã có trong lịch hẹn được giữ lại để bảo toàn lịch sử.
        Chuyển sang ngừng sử dụng nếu không còn cung cấp.
    </p>
@endsection
@extends('admin.layout')

@section('title', 'Quản lý phụ tùng')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">Phụ tùng</h1>
            <p class="text-secondary mb-0">
                Quản lý đơn giá và số lượng phụ tùng trong kho.
            </p>
        </div>

        <a href="{{ route('phutung.create') }}" class="btn btn-primary">
            + Thêm phụ tùng
        </a>
    </div>

    <div class="card">
        <div class="card-body p-4 border-bottom">
            <div class="fw-semibold">
                Danh sách phụ tùng
                <span class="badge bg-primary-subtle text-primary ms-2">
                    {{ $danhSach->count() }}
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Mã</th>
                        <th>Tên phụ tùng</th>
                        <th>Đơn vị tính</th>
                        <th>Đơn giá</th>
                        <th>Tồn kho</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($danhSach as $phuTung)
                        <tr>
                            <td class="ps-4">#{{ $phuTung->MaPT }}</td>

                            <td class="fw-semibold">
                                {{ $phuTung->TenPT }}
                            </td>

                            <td>{{ $phuTung->DonViTinh }}</td>

                            <td class="text-nowrap">
                                {{ number_format($phuTung->Gia, 0, ',', '.') }} đ
                            </td>

                            <td>
                                @if ($phuTung->SoLuongTon == 0)
                                    <span class="badge bg-danger-subtle text-danger">
                                        Hết hàng
                                    </span>
                                @else
                                    <span class="fw-semibold">
                                        {{ $phuTung->SoLuongTon }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                @if ($phuTung->TrangThai === 'DANG_SU_DUNG')
                                    <span class="badge bg-success-subtle text-success">
                                        Đang sử dụng
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        Ngừng sử dụng
                                    </span>
                                @endif
                            </td>

                            <td class="pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a
                                        href="{{ route('phutung.edit', $phuTung->MaPT) }}"
                                        class="btn btn-outline-primary btn-sm"
                                    >
                                        Sửa
                                    </a>

                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-action="{{ route('phutung.destroy', $phuTung->MaPT) }}"
                                        data-name="{{ $phuTung->TenPT }}"
                                    >
                                        Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">
                                Chưa có phụ tùng. Thêm phụ tùng để bắt đầu quản lý kho.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div
        class="modal fade"
        id="deleteModal"
        tabindex="-1"
        aria-labelledby="deleteModalTitle"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="deleteModalTitle">
                        Xác nhận xóa phụ tùng
                    </h2>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Đóng"
                    ></button>
                </div>

                <div class="modal-body">
                    <p>
                        Bạn muốn xóa phụ tùng
                        <strong id="deletePartName"></strong>?
                    </p>

                    <p class="small text-secondary mb-0">
                        Phụ tùng đã dùng trong nghiệp vụ nên được chuyển
                        sang trạng thái ngừng sử dụng để giữ lịch sử.
                    </p>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                    >
                        Hủy
                    </button>

                    <form id="deleteForm" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">
                            Xóa phụ tùng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const deleteModal = document.getElementById('deleteModal');

        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            if (!button) {
                return;
            }

            document.getElementById('deleteForm').action = button.dataset.action;
            document.getElementById('deletePartName').textContent = button.dataset.name;
        });
    </script>
@endpush
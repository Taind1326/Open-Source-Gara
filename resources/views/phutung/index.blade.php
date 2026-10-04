<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý phụ tùng</title>

    @vite('resources/css/phutung.css')
</head>

<body>

<div class="container page">

    <div class="page-header">

        <div>
            <h1 class="page-title">Quản lý phụ tùng</h1>

            <p class="page-description">
                Danh sách phụ tùng trong kho Garage
            </p>
        </div>

        <a
            href="{{ route('phutung.create') }}"
            class="btn btn-primary"
        >
            + Thêm phụ tùng
        </a>

    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card">

        <div class="table-wrapper">

            <table class="table">

                <thead>
                    <tr>
                        <th>Mã PT</th>
                        <th>Tên phụ tùng</th>
                        <th>Đơn vị tính</th>
                        <th>Giá</th>
                        <th>Số lượng tồn</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($danhSach as $phuTung)

                        <tr>

                            <td>
                                {{ $phuTung->MaPT }}
                            </td>

                            <td>
                                <strong>
                                    {{ $phuTung->TenPT }}
                                </strong>
                            </td>

                            <td>
                                {{ $phuTung->DonViTinh }}
                            </td>

                            <td>
                                {{ number_format($phuTung->Gia, 0, ',', '.') }} đ
                            </td>

                            <td>
                                {{ $phuTung->SoLuongTon }}
                            </td>

                            <td>

                                @if($phuTung->TrangThai === 'DANG_SU_DUNG')

                                    <span class="badge badge-active">
                                        Đang sử dụng
                                    </span>

                                @else

                                    <span class="badge badge-inactive">
                                        Ngừng sử dụng
                                    </span>

                                @endif

                            </td>

                            <td class="table-actions">

                                <a
                                    href="{{ route('phutung.edit', $phuTung->MaPT) }}"
                                    class="btn btn-warning"
                                >
                                    Sửa
                                </a>

                                <button
                                    type="button"
                                    class="btn btn-danger btn-delete"
                                    data-action="{{ route('phutung.destroy', $phuTung->MaPT) }}"
                                >
                                    Xóa
                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="empty">
                                Chưa có phụ tùng nào.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- MODAL XÁC NHẬN XÓA -->
<div id="deleteModal" class="delete-modal-overlay">

    <div class="delete-modal">

        <div class="delete-modal-icon">
            !
        </div>

        <h3>Xác nhận xóa</h3>

        <p>
            Bạn có chắc chắn muốn xóa phụ tùng này không?
        </p>

        <div class="delete-modal-actions">

            <button
                type="button"
                id="cancelDelete"
                class="btn btn-secondary"
            >
                Hủy
            </button>

            <form
                id="deleteForm"
                method="POST"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="btn btn-danger"
                >
                    Xóa
                </button>

            </form>

        </div>

    </div>

</div>

<script>
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const cancelDelete = document.getElementById('cancelDelete');
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            deleteForm.action = this.dataset.action;

            deleteModal.classList.add('show');

        });

    });

    cancelDelete.addEventListener('click', function () {

        deleteModal.classList.remove('show');

    });

    deleteModal.addEventListener('click', function (event) {

        if (event.target === deleteModal) {

            deleteModal.classList.remove('show');

        }

    });

    document.addEventListener('keydown', function (event) {

        if (event.key === 'Escape') {

            deleteModal.classList.remove('show');

        }

    });
</script>

</body>
</html>
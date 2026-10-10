<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sửa chữa và tiến độ</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">
<main class="container py-4">

    @php
        $phieu = $yeuCau->phieuSuaChua;
        $dangSua = $phieu->TrangThai === 'DANG_SUA';

        $trangThaiHienThi = [
            'CHO_SUA' => ['Chờ sửa chữa', 'secondary'],
            'DANG_SUA' => ['Đang sửa chữa', 'primary'],
            'KIEM_TRA_SAU_SUA' => ['Đang kiểm tra sau sửa chữa', 'warning'],
            'HOAN_THANH_KY_THUAT' => ['Hoàn thành kỹ thuật', 'success'],
            'HOAN_THANH' => ['Hoàn tất', 'success'],
        ];

        [$tenTrangThai, $mauTrangThai] =
            $trangThaiHienThi[$phieu->TrangThai]
            ?? ['Đang cập nhật', 'secondary'];

        $luaChonTienDo = [
            'DANG_SUA' => 'Đang sửa chữa',
            'KIEM_TRA_SAU_SUA' => 'Đang kiểm tra sau sửa chữa',
            'HOAN_THANH_KY_THUAT' => 'Hoàn thành kỹ thuật',
        ];

        $tongThucTe = $phieu->chiTiet->sum(
            fn ($ct) => $ct->SoLuong * $ct->DonGia
        );
    @endphp

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="mb-1">
                Sửa chữa xe {{ $yeuCau->xe->BienSo }}
            </h3>

            <div class="text-muted">
                Yêu cầu #{{ $yeuCau->MaYC }}
                · Phiếu #{{ $phieu->MaPSC }}
                · KTV #{{ $phieu->MaKTV }}
            </div>
        </div>

        <a
            href="{{ route('baogia.show', $yeuCau->MaYC) }}"
            class="btn btn-outline-secondary"
        >
            Xem báo giá
        </a>
    </div>

    <div class="alert alert-info d-flex align-items-center gap-2">
        <span>Trạng thái phiếu:</span>

        <span class="badge text-bg-{{ $mauTrangThai }}">
            {{ $tenTrangThai }}
        </span>
    </div>

    {{-- HẠNG MỤC KHÁCH ĐÃ DUYỆT --}}
    <section class="card shadow-sm mb-4">
        <div class="card-header fw-semibold">
            Hạng mục đã duyệt
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Hạng mục</th>
                    <th class="text-center">Số lượng duyệt</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-center">Thực tế đã ghi</th>
                </tr>
                </thead>

                <tbody>
                @forelse ($hangMucs as $hangMuc)
                    @php
                        $daGhi = $phieu->chiTiet
                            ->filter(function ($ct) use ($hangMuc) {
                                if ($hangMuc->MaDV !== null) {
                                    return $ct->MaDV == $hangMuc->MaDV;
                                }

                                return $ct->MaPT == $hangMuc->MaPT;
                            })
                            ->sum('SoLuong');
                    @endphp

                    <tr>
                        <td>
                            {{ $hangMuc->dichVu?->TenDV
                                ?? $hangMuc->phuTung?->TenPT }}
                        </td>

                        <td class="text-center">
                            {{ $hangMuc->SoLuong }}
                        </td>

                        <td class="text-end">
                            {{ number_format($hangMuc->DonGia, 0, ',', '.') }}đ
                        </td>

                        <td class="text-center">
                            {{ $daGhi }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">
                            Không có hạng mục báo giá.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- GHI NHẬN HẠNG MỤC THỰC TẾ --}}
    @if ($dangSua)
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <section class="card shadow-sm h-100">
                    <div class="card-header fw-semibold">
                        Ghi dịch vụ thực tế
                    </div>

                    <div class="card-body">
                        <form
                            method="POST"
                            action="{{ route('suachua.service', $yeuCau->MaYC) }}"
                        >
                            @csrf

                            <div class="mb-3">
                                <label for="MaDV" class="form-label">
                                    Dịch vụ đã được duyệt
                                </label>

                                <select
                                    id="MaDV"
                                    name="MaDV"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Chọn dịch vụ</option>

                                    @foreach ($hangMucs->whereNotNull('MaDV') as $hm)
                                        <option
                                            value="{{ $hm->MaDV }}"
                                            @selected(old('MaDV') == $hm->MaDV)
                                        >
                                            {{ $hm->dichVu?->TenDV }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Ghi nhận dịch vụ
                            </button>
                        </form>
                    </div>
                </section>
            </div>

            <div class="col-md-6">
                <section class="card shadow-sm h-100">
                    <div class="card-header fw-semibold">
                        Ghi phụ tùng thực tế
                    </div>

                    <div class="card-body">
                        <form
                            method="POST"
                            action="{{ route('suachua.part', $yeuCau->MaYC) }}"
                        >
                            @csrf

                            <div class="mb-3">
                                <label for="MaPT" class="form-label">
                                    Phụ tùng đã được duyệt
                                </label>

                                <select
                                    id="MaPT"
                                    name="MaPT"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Chọn phụ tùng</option>

                                    @foreach ($hangMucs->whereNotNull('MaPT') as $hm)
                                        <option
                                            value="{{ $hm->MaPT }}"
                                            @selected(old('MaPT') == $hm->MaPT)
                                        >
                                            {{ $hm->phuTung?->TenPT }}
                                            · Tồn {{ $hm->phuTung?->SoLuongTon }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="SoLuong" class="form-label">
                                    Số lượng dùng thêm
                                </label>

                                <input
                                    id="SoLuong"
                                    name="SoLuong"
                                    type="number"
                                    min="1"
                                    value="{{ old('SoLuong', 1) }}"
                                    class="form-control"
                                    required
                                >
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Ghi nhận và trừ kho
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    @endif

    {{-- CHI TIẾT SỬA CHỮA THỰC TẾ --}}
    <section class="card shadow-sm mb-4">
        <div class="card-header fw-semibold">
            Chi tiết sửa chữa thực tế
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Hạng mục</th>
                    <th class="text-center">Số lượng</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-end">Thành tiền</th>
                </tr>
                </thead>

                <tbody>
                @forelse ($phieu->chiTiet as $ct)
                    <tr>
                        <td>
                            {{ $ct->dichVu?->TenDV ?? $ct->phuTung?->TenPT }}
                        </td>

                        <td class="text-center">
                            {{ $ct->SoLuong }}
                        </td>

                        <td class="text-end">
                            {{ number_format($ct->DonGia, 0, ',', '.') }}đ
                        </td>

                        <td class="text-end">
                            {{ number_format(
                                $ct->SoLuong * $ct->DonGia,
                                0,
                                ',',
                                '.'
                            ) }}đ
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">
                            Chưa ghi nhận hạng mục thực tế.
                        </td>
                    </tr>
                @endforelse
                </tbody>

                <tfoot class="table-light">
                <tr>
                    <th colspan="3" class="text-end">
                        Tổng thực tế
                    </th>

                    <th class="text-end">
                        {{ number_format($tongThucTe, 0, ',', '.') }}đ
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>
    </section>

    {{-- TIẾN ĐỘ SỬA CHỮA --}}
    <section class="card shadow-sm mb-4">
        <div class="card-header fw-semibold">
            Tiến độ sửa chữa
        </div>

        <div class="card-body">
            @forelse ($phieu->tienDos->sortByDesc('MaTD') as $td)
                @php
                    [$tenTienDo, $mauTienDo] =
                        $trangThaiHienThi[$td->TrangThai]
                        ?? ['Đang cập nhật', 'secondary'];
                @endphp

                <article
                    class="border-start border-3 border-{{ $mauTienDo }} ps-3 py-2 mb-3"
                >
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="badge text-bg-{{ $mauTienDo }}">
                            {{ $tenTienDo }}
                        </span>

                        <time class="text-muted small">
                            {{ $td->NgayCapNhat
                                ?->copy()
                                ->timezone('Asia/Ho_Chi_Minh')
                                ->format('d/m/Y · H:i') }}
                        </time>
                    </div>

                    <p class="mb-0" style="white-space: pre-line;">{{ $td->NoiDung }}</p>
                </article>
            @empty
                <p class="text-muted mb-0">
                    Chưa có cập nhật tiến độ.
                </p>
            @endforelse

            @if ($dangSua)
                <hr>

                <h5 class="mb-3">Cập nhật tiến độ</h5>

                <form
                    id="progressForm"
                    method="POST"
                    action="{{ route('suachua.progress', $yeuCau->MaYC) }}"
                >
                    @csrf

                    <div class="mb-3">
                        <label for="TrangThai" class="form-label">
                            Trạng thái tiến độ
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            id="TrangThai"
                            name="TrangThai"
                            class="form-select"
                            required
                        >
                            <option value="">Chọn trạng thái</option>

                            @foreach ($luaChonTienDo as $ma => $ten)
                                <option
                                    value="{{ $ma }}"
                                    @selected(old('TrangThai') === $ma)
                                >
                                    {{ $ten }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="GhiChu" class="form-label">
                            Ghi chú
                            <span class="text-muted">(không bắt buộc)</span>
                        </label>

                        <textarea
                            id="GhiChu"
                            name="GhiChu"
                            rows="3"
                            maxlength="180"
                            class="form-control"
                            placeholder="Ví dụ: Đã kiểm tra phanh, đang chạy thử xe."
                        >{{ old('GhiChu') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Cập nhật tiến độ
                    </button>
                </form>
            @endif
        </div>
    </section>

    @if ($phieu->TrangThai === 'HOAN_THANH_KY_THUAT')
        <div class="alert alert-success">
            Đã hoàn thành kỹ thuật. Admin có thể lập hóa đơn
            và xử lý thanh toán.
        </div>
    @elseif ($phieu->TrangThai === 'HOAN_THANH')
        <div class="alert alert-success">
            Phiếu sửa chữa đã hoàn tất.
        </div>
    @endif

</main>

<script>
    const progressForm = document.getElementById('progressForm');

    if (progressForm) {
        progressForm.addEventListener('submit', function (event) {
            const status = document.getElementById('TrangThai').value;

            if (status === 'HOAN_THANH_KY_THUAT') {
                const confirmed = confirm(
                    'Xác nhận hoàn thành kỹ thuật? '
                    + 'Sau bước này không thể ghi thêm vật tư hoặc tiến độ.'
                );

                if (!confirmed) {
                    event.preventDefault();
                }
            }
        });
    }
</script>

</body>
</html>
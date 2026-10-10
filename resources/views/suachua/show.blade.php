<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>
            Sửa chữa xe
        </title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <main class="container py-4">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <h2>
                Sửa chữa xe {{ $yeuCau->xe->BienSo }}
            </h2>
            <p class="text-muted">
                Yêu cầu #{{ $yeuCau->MaYC }}
            </p>
            <a class="btn btn-outline-secondary mb-3" href="{{ route('baogia.show', $yeuCau->MaYC) }}">Xem báo giá
            </a>
            @php
                $phieu = $yeuCau->phieuSuaChua;
                $dangSua = $phieu->TrangThai === 'DANG_SUA';
            @endphp
            <div class="alert alert-info">
                Phiếu #{{ $phieu->MaPSC }} · {{ $phieu->TrangThai }} · KTV #{{ $phieu->MaKTV }}
            </div>
            <section class="card mb-4">
                <div class="card-header">
                    Hạng mục đã duyệt
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>
                                    Hạng mục
                                </th>
                                <th>
                                    Số lượng duyệt
                                </th>
                                <th>
                                    Đơn giá
                                </th>
                                <th>
                                    Thực tế đã ghi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hangMucs as $hangMuc)
                                @php
                                    $daGhi = $phieu->chiTiet->filter(fn($ct) => $hangMuc->MaDV !== null ? $ct->MaDV == $hangMuc->MaDV : $ct->MaPT == $hangMuc->MaPT)->sum('SoLuong');
                                @endphp
                                <tr>
                                    <td>
                                        {{ $hangMuc->dichVu?->TenDV ?? $hangMuc->phuTung?->TenPT }}
                                    </td>
                                    <td>
                                        {{ $hangMuc->SoLuong }}
                                    </td>
                                    <td>
                                        {{ number_format($hangMuc->DonGia, 0, ',', '.') }}đ
                                    </td>
                                    <td>
                                        {{ $daGhi }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            @if($dangSua)
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <section class="card p-3 h-100">
                            <h5>
                                Ghi dịch vụ thực tế
                            </h5>
                            <form method="POST" action="{{ route('suachua.service', $yeuCau->MaYC) }}">
                                @csrf
                                <label for="MaDV" class="form-label">
                                    Dịch vụ đã được duyệt
                                </label>
                                <select id="MaDV" name="MaDV" class="form-select mb-3" required>
                                    <option value="">
                                        Chọn dịch vụ
                                    </option>
                                    @foreach($hangMucs->whereNotNull('MaDV') as $hm)
                                        <option value="{{ $hm->MaDV }}">{{ $hm->dichVu?->TenDV }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary">
                                    Ghi nhận dịch vụ
                                </button>
                            </form>
                        </section>
                    </div>
                    <div class="col-md-6">
                        <section class="card p-3 h-100">
                            <h5>
                                Ghi phụ tùng thực tế
                            </h5>
                            <form method="POST" action="{{ route('suachua.part', $yeuCau->MaYC) }}">
                                @csrf
                                <label for="MaPT" class="form-label">
                                    Phụ tùng đã được duyệt
                                </label>
                                <select id="MaPT" name="MaPT" class="form-select mb-2" required>
                                    <option value="">
                                        Chọn phụ tùng
                                    </option>
                                    @foreach($hangMucs->whereNotNull('MaPT') as $hm)
                                        <option value="{{ $hm->MaPT }}">{{ $hm->phuTung?->TenPT }} · Tồn {{ $hm->phuTung?->SoLuongTon }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="SoLuong" class="form-label">
                                    Số lượng dùng thêm
                                </label>
                                <input id="SoLuong" name="SoLuong" type="number" min="1" value="{{ old('SoLuong', 1) }}" required class="form-control mb-3">
                                <button class="btn btn-primary">
                                    Ghi nhận và trừ kho
                                </button>
                            </form>
                        </section>
                    </div>
                </div>
            @endif
            <section class="card mb-4">
                <div class="card-header">
                    Chi tiết sửa chữa thực tế
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>
                                    Hạng mục
                                </th>
                                <th>
                                    Số lượng
                                </th>
                                <th>
                                    Đơn giá
                                </th>
                                <th>
                                    Thành tiền
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($phieu->chiTiet as $ct)
                                <tr>
                                    <td>
                                        {{ $ct->dichVu?->TenDV ?? $ct->phuTung?->TenPT }}
                                    </td>
                                    <td>
                                        {{ $ct->SoLuong }}
                                    </td>
                                    <td>
                                        {{ number_format($ct->DonGia, 0, ',', '.') }}đ
                                    </td>
                                    <td>
                                        {{ number_format($ct->SoLuong * $ct->DonGia, 0, ',', '.') }}đ
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        Chưa ghi nhận hạng mục thực tế.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">
                                    Tổng thực tế
                                </th>
                                <th>
                                    {{ number_format($phieu->chiTiet->sum(fn($ct) => $ct->SoLuong * $ct->DonGia), 0, ',', '.') }}đ
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
            <section class="card p-3 mb-4">
                <h5>
                    Tiến độ sửa chữa
                </h5>
                @foreach($phieu->tienDos->sortByDesc('MaTD') as $td)
                    <div class="border-bottom py-2">
                        <small class="text-muted">
                            {{ $td->NgayCapNhat?->format('d/m/Y H:i') }} · {{ $td->TrangThai }}
                        </small>
                        <p class="mb-1">
                            {{ $td->NoiDung }}
                        </p>
                    </div>
                @endforeach
                @if($dangSua)
                    <form class="mt-3" method="POST" action="{{ route('suachua.progress', $yeuCau->MaYC) }}">
                        @csrf
                        <label for="NoiDung" class="form-label">
                            Nội dung cập nhật
                        </label>
                        <textarea id="NoiDung" name="NoiDung" required maxlength="255" class="form-control mb-2">
                            {{ old('NoiDung') }}
                        </textarea>
                        <button class="btn btn-primary">
                            Cập nhật tiến độ
                        </button>
                    </form>
                @endif
            </section>
            @if($dangSua)
                <form method="POST" action="{{ route('suachua.complete', $yeuCau->MaYC) }}" onsubmit="return confirm('Xác nhận đã hoàn thành kỹ thuật?');">
                    @csrf
                    <button class="btn btn-success">
                        Hoàn thành kỹ thuật
                    </button>
                </form>
            @else
                <div class="alert alert-success">
                    Đã kết thúc thao tác kỹ thuật. Theo dõi hóa đơn và thanh toán ở bước tiếp theo.
                </div>
            @endif
        </main>
    </body>
</html>

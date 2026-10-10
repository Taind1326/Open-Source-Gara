@extends('admin.layout')

@section('title', 'Quản lý hóa đơn')

@section('content')
    <div class="mb-4">
        <h1 class="h4 fw-bold mb-1">Hóa đơn</h1>

        <p class="text-secondary mb-0">
            Lập hóa đơn từ phiếu đã hoàn thành kỹ thuật và theo dõi thanh toán.
        </p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-secondary mb-2">Chờ lập hóa đơn</div>

                    <div class="h3 fw-bold text-primary mb-0">
                        {{ $phieuChuaLap->count() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-secondary mb-2">Chưa thanh toán</div>

                    <div class="h3 fw-bold text-warning mb-0">
                        {{ $danhSach->where('TrangThaiThanhToan', 'CHUA_THANH_TOAN')->count() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-secondary mb-2">Đã thanh toán</div>

                    <div class="h3 fw-bold text-success mb-0">
                        {{ $danhSach->where('TrangThaiThanhToan', 'DA_THANH_TOAN')->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4 border-bottom">
            <div class="fw-semibold">
                Phiếu sửa chữa chờ lập hóa đơn

                <span class="badge bg-primary-subtle text-primary ms-2">
                    {{ $phieuChuaLap->count() }}
                </span>
            </div>

            <div class="small text-secondary mt-1">
                Các phiếu đã hoàn thành kỹ thuật và chưa có hóa đơn.
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Phiếu sửa chữa</th>
                        <th>Yêu cầu</th>
                        <th>Khách hàng</th>
                        <th>Biển số</th>
                        <th>Hoàn thành kỹ thuật</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($phieuChuaLap as $phieu)
                        <tr>
                            <td class="ps-4">#{{ $phieu->MaPSC }}</td>
                            <td>#{{ $phieu->MaYC }}</td>

                            <td class="fw-semibold">
                                {{ $phieu->HoTen }}
                            </td>

                            <td class="text-nowrap">{{ $phieu->BienSo }}</td>

                            <td class="text-nowrap">
                                @if ($phieu->NgayHoanThanh)
                                    {{ \Illuminate\Support\Carbon::parse(
                                        $phieu->NgayHoanThanh,
                                        config('app.timezone')
                                    )->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                                @else
                                    —
                                @endif
                            </td>

                            <td class="pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a
                                        href="{{ route('suachua.show', $phieu->MaYC) }}"
                                        class="btn btn-outline-secondary btn-sm text-nowrap"
                                    >
                                        Hồ sơ
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('hoadon.admin.tao', $phieu->MaPSC) }}"
                                        class="m-0"
                                        onsubmit="return confirm('Lập hóa đơn từ các dịch vụ và phụ tùng thực tế của phiếu này?');"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="btn btn-primary btn-sm text-nowrap"
                                        >
                                            Lập hóa đơn
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-secondary py-5">
                                Không có phiếu đang chờ lập hóa đơn.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-4 border-bottom">
            <div class="fw-semibold">
                Danh sách hóa đơn

                <span class="badge bg-primary-subtle text-primary ms-2">
                    {{ $danhSach->count() }}
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Hóa đơn</th>
                        <th>Khách hàng / Xe</th>
                        <th>Ngày lập</th>
                        <th>Tổng thanh toán</th>
                        <th>Phương thức</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Hồ sơ</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($danhSach as $hoaDon)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold">
                                    #{{ $hoaDon->MaHD }}
                                </div>

                                <div class="small text-secondary">
                                    Phiếu #{{ $hoaDon->MaPSC }}
                                </div>
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $hoaDon->HoTen }}
                                </div>

                                <div class="small text-secondary">
                                    {{ $hoaDon->BienSo }}
                                </div>
                            </td>

                            <td class="text-nowrap">
                                @if ($hoaDon->NgayLap)
                                    {{ \Illuminate\Support\Carbon::parse(
                                        $hoaDon->NgayLap,
                                        config('app.timezone')
                                    )->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                                @else
                                    —
                                @endif
                            </td>

                            <td class="fw-semibold text-nowrap">
                                {{ number_format($hoaDon->TongThanhToan, 0, ',', '.') }} đ

                                @if ($hoaDon->SoTienGiam > 0)
                                    <div class="small fw-normal text-success">
                                        Giảm {{ number_format($hoaDon->SoTienGiam, 0, ',', '.') }} đ
                                    </div>
                                @endif
                            </td>

                            <td>
                                @switch($hoaDon->PhuongThucThanhToan)
                                    @case('TIEN_MAT')
                                        Tiền mặt
                                        @break

                                    @case('CHUYEN_KHOAN_QR')
                                        Chuyển khoản QR
                                        @break

                                    @default
                                        <span class="text-secondary">Chưa chọn</span>
                                @endswitch
                            </td>

                            <td>
                                @if ($hoaDon->TrangThaiThanhToan === 'DA_THANH_TOAN')
                                    <span class="badge bg-success-subtle text-success">
                                        Đã thanh toán
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis">
                                        Chưa thanh toán
                                    </span>
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                <a
                                    href="{{ route('suachua.show', $hoaDon->MaYC) }}"
                                    class="btn btn-outline-primary btn-sm text-nowrap"
                                >
                                    Hồ sơ sửa chữa
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-secondary py-5">
                                Chưa có hóa đơn nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
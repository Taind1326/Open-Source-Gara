@extends('admin.layout')

@section('title', 'Quản lý hóa đơn')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">Quản lý hóa đơn</h1>
        <p class="text-secondary mb-0">
            Theo dõi hóa đơn và xác nhận thanh toán tiền mặt.
        </p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Hóa đơn</th>
                            <th scope="col">Khách hàng</th>
                            <th scope="col">Xe</th>
                            <th scope="col">Ngày lập</th>
                            <th scope="col" class="text-end">Tổng tiền</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col" class="text-end">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($hoaDons as $hoaDon)
                            <tr>
                                <td class="fw-semibold">
                                    #{{ $hoaDon->MaHD }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $hoaDon->HoTen }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $hoaDon->SoDienThoai }}
                                    </small>
                                </td>

                                <td class="text-nowrap">
                                    {{ $hoaDon->BienSo }}
                                </td>

                                <td class="text-nowrap">
                                    {{ \Carbon\Carbon::parse($hoaDon->NgayLap)->format('d/m/Y H:i') }}
                                </td>

                                <td class="text-end text-nowrap fw-semibold">
                                    {{ number_format($hoaDon->TongTien, 0, ',', '.') }} ₫
                                </td>

                                <td class="text-nowrap">
                                    <span class="badge {{ $hoaDon->TrangThai === 'DA_THANH_TOAN' ? 'text-bg-success' : 'text-bg-warning' }}">
                                        {{ $hoaDon->TrangThai === 'DA_THANH_TOAN' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    <a
                                        href="{{ route('hoadon.admin.show', $hoaDon->MaHD) }}"
                                        class="btn btn-outline-primary btn-sm text-nowrap"
                                    >
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Chưa có hóa đơn.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($hoaDons->hasPages())
                <div class="mt-4">
                    {{ $hoaDons->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
@endsection
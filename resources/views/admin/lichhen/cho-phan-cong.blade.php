@extends('layouts.app')

@section('title', 'Lịch chờ phân công')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            Lịch chờ phân công
            <span class="badge bg-secondary ms-2">{{ $danhSach->total() }}</span>
        </h4>
        <a href="{{ route('admin.lichhen.tiep-nhan') }}" class="btn btn-outline-primary">
            Tiếp nhận xe
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if ($danhSach->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                Hiện không có lịch nào đang chờ phân công.
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã</th>
                            <th>Ngày &amp; giờ hẹn</th>
                            <th>Xe</th>
                            <th>Khách hàng</th>
                            <th>Dịch vụ</th>
                            <th>Mô tả</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($danhSach as $yc)
                            @php
                                $laHomNay = $yc->NgayHen->isToday();
                                $quaHan = $yc->NgayHen->isPast() && !$laHomNay;
                            @endphp
                            <tr class="{{ $quaHan ? 'table-danger' : '' }}">
                                <td>#{{ $yc->MaYC }}</td>

                                <td>
                                    {{ $yc->NgayHen->format('d/m/Y') }}
                                    @if ($laHomNay)
                                        <span class="badge bg-warning text-dark">Hôm nay</span>
                                    @elseif ($quaHan)
                                        <span class="badge bg-danger">Quá hạn</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $yc->KhungGioHen }}</small>
                                </td>

                                <td>
                                    <strong>{{ $yc->xe->BienSo }}</strong><br>
                                    <small class="text-muted">{{ $yc->xe->HangXe }} {{ $yc->xe->DongXe }}</small>
                                </td>

                                <td>
                                    {{ $yc->xe->taiKhoan->HoTen ?? '—' }}<br>
                                    <small class="text-muted">{{ $yc->xe->taiKhoan->SoDienThoai ?? '' }}</small>
                                </td>

                                <td>
                                    @forelse ($yc->dichVu as $dv)
                                        <span class="badge bg-light text-dark border">{{ $dv->TenDV }}</span>
                                    @empty
                                        <small class="text-muted">Chưa chọn</small>
                                    @endforelse
                                </td>

                                <td style="max-width: 220px;">
                                    {{ \Illuminate\Support\Str::limit($yc->MoTa, 60) ?: '—' }}
                                    @if ($yc->HinhAnh)
                                        <a href="{{ asset('storage/' . $yc->HinhAnh) }}" target="_blank"
                                           class="d-block small">Xem ảnh</a>
                                    @endif
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('admin.lichhen.phan-cong', $yc->MaYC) }}"
                                       class="btn btn-sm btn-primary">
                                        Phân công KTV
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            {{ $danhSach->links() }}
        </div>
    @endif

</div>
@endsection
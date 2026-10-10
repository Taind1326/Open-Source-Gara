@extends('layouts.app')

@section('title', 'Lịch hẹn của tôi')

@php
    // Nhãn + màu badge cho từng trạng thái (khớp ENUM trong bảng YEUCAUSUACHUA)
    $trangThaiMap = [
        'CHO_PHAN_CONG'     => ['Chờ phân công',     'secondary'],
        'DA_PHAN_CONG'      => ['Đã phân công KTV',  'info'],
        'DA_TIEP_NHAN'      => ['Đã tiếp nhận xe',   'primary'],
        'DA_KIEM_TRA'       => ['Đã kiểm tra',       'primary'],
        'CHO_DUYET_BAO_GIA' => ['Chờ duyệt báo giá', 'warning'],
        'DANG_SUA'          => ['Đang sửa chữa',     'warning'],
        'HOAN_THANH'        => ['Hoàn thành',        'success'],
        'TU_CHOI'           => ['Đã từ chối',        'danger'],
        'DA_HUY'            => ['Đã hủy',            'dark'],
    ];
@endphp

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Lịch hẹn của tôi</h4>
        <a href="{{ route('lichhen.create') }}" class="btn btn-primary">
            + Đặt lịch mới
        </a>
    </div>

    {{-- Thông báo --}}
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
                Bạn chưa có lịch hẹn nào.
                <a href="{{ route('lichhen.create') }}">Đặt lịch ngay</a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã</th>
                            <th>Xe</th>
                            <th>Ngày &amp; giờ hẹn</th>
                            <th>Dịch vụ</th>
                            <th>KTV phụ trách</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($danhSach as $yc)
                            @php
                                [$nhan, $mau] = $trangThaiMap[$yc->TrangThai] ?? [$yc->TrangThai, 'secondary'];
                            @endphp
                            <tr>
                                <td>#{{ $yc->MaYC }}</td>

                                <td>
                                    <strong>{{ $yc->xe->BienSo }}</strong><br>
                                    <small class="text-muted">
                                        {{ $yc->xe->HangXe }} {{ $yc->xe->DongXe }}
                                    </small>
                                </td>

                                <td>
                                    {{ $yc->NgayHen->format('d/m/Y') }}<br>
                                    <small class="text-muted">{{ $yc->KhungGioHen }}</small>
                                </td>

                                <td>
                                    @forelse ($yc->dichVu as $dv)
                                        <span class="badge bg-light text-dark border">{{ $dv->TenDV }}</span>
                                    @empty
                                        <small class="text-muted">Chưa chọn</small>
                                    @endforelse
                                </td>

                                <td>
                                    @if ($yc->phanCong && $yc->phanCong->ktv)
                                        {{ $yc->phanCong->ktv->HoTen }}
                                    @else
                                        <small class="text-muted">Chưa phân công</small>
                                    @endif
                                </td>

                                <td>
                                    <span class="badge bg-{{ $mau }}">{{ $nhan }}</span>
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('lichhen.show', $yc->MaYC) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Chi tiết
                                    </a>

                                    {{-- Chỉ cho hủy khi chưa được phân công (khớp logic huy() trong controller) --}}
                                    @if ($yc->TrangThai === 'CHO_PHAN_CONG')
                                        <form action="{{ route('lichhen.huy', $yc->MaYC) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Bạn chắc chắn muốn hủy lịch hẹn #{{ $yc->MaYC }}?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Hủy
                                            </button>
                                        </form>
                                    @endif
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
@extends('ktv.layout')

@section('title', 'Công việc của tôi')

@section('content')
    @php
        $trangThais = [
            'CHO_NHAN_XE' => [
                'label' => 'Chờ nhận xe',
                'icon' => 'bi-car-front',
                'tone' => 'blue',
            ],
            'DANG_KIEM_TRA' => [
                'label' => 'Đang kiểm tra',
                'icon' => 'bi-search',
                'tone' => 'purple',
            ],
            'CHO_XAC_NHAN' => [
                'label' => 'Chờ xác nhận',
                'icon' => 'bi-file-earmark-text',
                'tone' => 'yellow',
            ],
            'CHO_SUA_CHUA' => [
                'label' => 'Chờ sửa chữa',
                'icon' => 'bi-tools',
                'tone' => 'orange',
            ],
            'DANG_SUA_CHUA' => [
                'label' => 'Đang sửa chữa',
                'icon' => 'bi-wrench-adjustable',
                'tone' => 'orange',
            ],
            'CHO_THANH_TOAN' => [
                'label' => 'Chờ thanh toán',
                'icon' => 'bi-receipt',
                'tone' => 'yellow',
            ],
            'HOAN_TAT' => [
                'label' => 'Hoàn tất',
                'icon' => 'bi-check-circle',
                'tone' => 'green',
            ],
            'DA_HUY' => [
                'label' => 'Đã hủy',
                'icon' => 'bi-x-circle',
                'tone' => 'gray',
            ],
        ];

        $theThongKe = [
            [
                'status' => 'CHO_NHAN_XE',
                'label' => 'Chờ nhận xe',
                'icon' => 'bi-car-front',
                'tone' => 'blue',
            ],
            [
                'status' => 'DANG_KIEM_TRA',
                'label' => 'Đang kiểm tra',
                'icon' => 'bi-search',
                'tone' => 'purple',
            ],
            [
                'status' => 'CHO_XAC_NHAN',
                'label' => 'Chờ khách xác nhận',
                'icon' => 'bi-file-earmark-text',
                'tone' => 'yellow',
            ],
            [
                'status' => 'DANG_SUA_CHUA',
                'label' => 'Đang sửa chữa',
                'icon' => 'bi-wrench-adjustable',
                'tone' => 'orange',
            ],
        ];

        $boLocChung = [
            'tu_khoa' => $tuKhoa,
            'ngay_hen' => $ngayHen,
        ];
    @endphp

    <div class="ktv-container">
        <div class="ktv-page-heading">
            <h1>Công việc của tôi</h1>

            <p>
                Theo dõi xe được phân công và cập nhật công việc sửa chữa.
            </p>
        </div>

        <div class="row g-3 mb-4">
            @foreach($theThongKe as $the)
                <div class="col-6 col-xl-3">
                    <a
                        href="{{ route('ktv.lichhen.index', [
                            'trang_thai' => $the['status'],
                        ]) }}"
                        class="ktv-stat"
                    >
                        <span class="ktv-stat-icon ktv-tone-{{ $the['tone'] }}">
                            <i
                                class="bi {{ $the['icon'] }}"
                                aria-hidden="true"
                            ></i>
                        </span>

                        <div>
                            <p class="ktv-stat-label">
                                {{ $the['label'] }}
                            </p>

                            <p class="ktv-stat-number">
                                {{ number_format((int) ($soLieu[$the['status']] ?? 0)) }}
                            </p>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <section class="ktv-panel" aria-label="Danh sách công việc">
            <form
                method="GET"
                action="{{ route('ktv.lichhen.index') }}"
                class="ktv-filter-form"
                novalidate
            >
                <input
                    type="hidden"
                    name="trang_thai"
                    value="{{ $trangThai }}"
                >

                <div class="row g-3 align-items-start">
                    <div class="col-lg-6">
                        <label for="jobKeyword" class="visually-hidden">
                            Tìm kiếm công việc
                        </label>

                        <div class="ktv-search-field">
                            <i class="bi bi-search" aria-hidden="true"></i>

                            <input
                                type="search"
                                id="jobKeyword"
                                name="tu_khoa"
                                value="{{ old('tu_khoa', $tuKhoa) }}"
                                maxlength="100"
                                placeholder="Tìm biển số, tên khách hoặc số điện thoại..."
                                class="form-control @error('tu_khoa') is-invalid @enderror"
                            >

                            @error('tu_khoa')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <label for="jobDate" class="visually-hidden">
                            Ngày hẹn
                        </label>

                        <input
                            type="date"
                            id="jobDate"
                            name="ngay_hen"
                            value="{{ old('ngay_hen', $ngayHen) }}"
                            class="form-control @error('ngay_hen') is-invalid @enderror"
                        >

                        @error('ngay_hen')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-lg-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="bi bi-search me-2" aria-hidden="true"></i>
                                Tìm kiếm
                            </button>

                            <a
                                href="{{ route('ktv.lichhen.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                Đặt lại
                            </a>
                        </div>
                    </div>
                </div>

                @error('trang_thai')
                    <div class="text-danger small mt-2" role="alert">
                        {{ $message }}
                    </div>
                @enderror
            </form>

            <nav class="ktv-status-tabs" aria-label="Lọc trạng thái công việc">
                <a
                    href="{{ route('ktv.lichhen.index', $boLocChung) }}"
                    class="ktv-status-tab {{ $trangThai === '' ? 'is-active' : '' }}"
                    @if($trangThai === '') aria-current="page" @endif
                >
                    Tất cả
                </a>

                @foreach($trangThais as $value => $thongTin)
                    <a
                        href="{{ route('ktv.lichhen.index', array_merge(
                            $boLocChung,
                            ['trang_thai' => $value]
                        )) }}"
                        class="ktv-status-tab {{ $trangThai === $value ? 'is-active' : '' }}"
                        @if($trangThai === $value) aria-current="page" @endif
                    >
                        {{ $thongTin['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="table-responsive">
                <table class="table ktv-table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Lịch hẹn</th>
                            <th scope="col">Xe / Khách hàng</th>
                            <th scope="col">Ngày hẹn</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col" class="text-end">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($lichHens as $lichHen)
                            @php
                                $trangThaiHienTai = $trangThais[$lichHen->TrangThai] ?? [
                                    'label' => 'Chờ phân công',
                                    'icon' => 'bi-clock',
                                    'tone' => 'gray',
                                ];

                                $dangKiemTra = $lichHen->TrangThai === 'DANG_KIEM_TRA';
                            @endphp

                            <tr>
                                <td class="fw-semibold text-nowrap">
                                    <a
                                        href="{{ route('ktv.lichhen.show', $lichHen->MaLH) }}"
                                        class="text-reset text-decoration-none"
                                    >
                                        #LH{{ $lichHen->MaLH }}
                                    </a>
                                </td>

                                <td>
                                    <div class="ktv-vehicle-name">
                                        {{ $lichHen->xe?->HangXe }}
                                        {{ $lichHen->xe?->DongXe }}

                                        <span class="fw-normal">
                                            · {{ $lichHen->xe?->BienSo ?? 'Chưa có biển số' }}
                                        </span>
                                    </div>

                                    <div class="ktv-customer-name">
                                        {{ $lichHen->taiKhoan?->HoTen ?? 'Chưa có thông tin khách' }}
                                    </div>
                                </td>

                                <td class="text-nowrap">
                                    {{ $lichHen->NgayHen->format('d/m/Y') }}

                                    <span class="text-secondary ms-1">
                                        {{ $lichHen->KhungGioHen }}
                                    </span>
                                </td>

                                <td>
                                    <span class="ktv-status-badge ktv-tone-{{ $trangThaiHienTai['tone'] }}">
                                        <i
                                            class="bi {{ $trangThaiHienTai['icon'] }}"
                                            aria-hidden="true"
                                        ></i>

                                        {{ $trangThaiHienTai['label'] }}
                                    </span>
                                </td>

                                <td class="text-end">
                                    @if($lichHen->TrangThai === 'CHO_NHAN_XE')
                                        <form
                                            method="POST"
                                            action="{{ route('ktv.lichhen.receive', $lichHen->MaLH) }}"
                                            data-confirm-title="Xác nhận nhận xe"
                                            data-confirm="Khách đã mang xe {{ $lichHen->xe?->BienSo }} tới garage và bạn đã nhận xe để kiểm tra?"
                                            data-confirm-accept="Đã nhận xe"
                                            novalidate
                                        >
                                            @csrf

                                            <button
                                                type="submit"
                                                class="btn btn-primary ktv-job-action"
                                            >
                                                Nhận xe
                                            </button>
                                        </form>
                                    @elseif($dangKiemTra)
                                        <a
                                            href="{{ route('ktv.kiemtra.edit', $lichHen->MaLH) }}"
                                            class="btn btn-outline-primary ktv-job-action"
                                        >
                                            Lập phiếu
                                        </a>
                                    @else
                                        <a
                                            href="{{ route('ktv.lichhen.show', $lichHen->MaLH) }}"
                                            class="btn btn-outline-secondary ktv-job-action"
                                        >
                                            {{ in_array($lichHen->TrangThai, ['CHO_SUA_CHUA', 'DANG_SUA_CHUA'], true)
                                                ? 'Mở công việc'
                                                : 'Xem chi tiết' }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="ktv-empty">
                                        <i class="bi bi-inbox" aria-hidden="true"></i>

                                        <p class="fw-semibold mb-1">
                                            Chưa có công việc phù hợp
                                        </p>

                                        <p class="small mb-3">
                                            Thử thay đổi từ khóa, ngày hẹn hoặc trạng thái.
                                        </p>

                                        <a
                                            href="{{ route('ktv.lichhen.index') }}"
                                            class="btn btn-outline-secondary"
                                        >
                                            Xóa bộ lọc
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($lichHens->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $lichHens->links('shared.pagination') }}
                </div>
            @endif
        </section>
    </div>
@endsection
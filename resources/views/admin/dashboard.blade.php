@extends('admin.layout')

@section('title', 'Tổng quan')

@section('content')
    @php
        $statCards = [
            [
                'label' => 'Chờ phân công',
                'status' => 'CHO_PHAN_CONG',
                'icon' => 'bi-car-front',
                'color' => '',
            ],
            [
                'label' => 'Đang kiểm tra',
                'status' => 'DANG_KIEM_TRA',
                'icon' => 'bi-search',
                'color' => 'dashboard-icon-blue',
            ],
            [
                'label' => 'Đang sửa chữa',
                'status' => 'DANG_SUA_CHUA',
                'icon' => 'bi-tools',
                'color' => '',
            ],
            [
                'label' => 'Chờ thanh toán',
                'status' => 'CHO_THANH_TOAN',
                'icon' => 'bi-receipt',
                'color' => 'dashboard-icon-purple',
            ],
        ];

        $pendingAppointmentsUrl = route('admin.lichhen.index', [
            'trang_thai' => 'CHO_PHAN_CONG',
        ]);
    @endphp

    <div class="admin-dashboard">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h1 class="h3 fw-bold mb-2">Tổng quan garage</h1>

                <p class="text-secondary mb-0">
                    Theo dõi hoạt động và các công việc cần xử lý.
                </p>
            </div>

            <span class="text-secondary small">
                {{ $now->format('d/m/Y') }}
            </span>
        </div>

        <div class="row g-3 mb-4">
            @foreach($statCards as $stat)
                <div class="col-6 col-xl-3">
                    <a
                        href="{{ route('admin.lichhen.index', [
                            'trang_thai' => $stat['status'],
                        ]) }}"
                        class="card dashboard-stat text-decoration-none"
                    >
                        <span class="dashboard-icon {{ $stat['color'] }}">
                            <i class="bi {{ $stat['icon'] }}" aria-hidden="true"></i>
                        </span>

                        <div>
                            <div class="dashboard-stat-label">
                                {{ $stat['label'] }}
                            </div>

                            <div class="dashboard-stat-number">
                                {{ number_format($summary[$stat['status']], 0, ',', '.') }}
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-7">
                <section class="card h-100">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <h2 class="h5 fw-bold mb-0">
                                Doanh thu tháng {{ $now->format('m/Y') }}
                            </h2>

                            <a
                                href="{{ route('thongke.admin.index') }}"
                                class="dashboard-link"
                            >
                                Xem thống kê
                            </a>
                        </div>

                        <div class="dashboard-revenue">
                            {{ number_format($doanhThuThang, 0, ',', '.') }} đ
                        </div>

                        <p class="small text-secondary mb-0">
                            Hóa đơn đã thanh toán, tính theo ngày nhận tiền.
                        </p>

                        <div class="dashboard-chart" id="dashboardChartWrap">
                            <canvas
                                id="dashboardRevenueChart"
                                role="img"
                                aria-label="Doanh thu từng ngày trong tháng hiện tại"
                            ></canvas>
                        </div>

                        @if($doanhThuThang === 0)
                            <p class="small text-secondary mt-2 mb-0">
                                Tháng này chưa ghi nhận doanh thu.
                            </p>
                        @endif

                        <details class="mt-3">
                            <summary class="small text-secondary">
                                Xem số liệu theo ngày
                            </summary>

                            <div class="table-responsive mt-2">
                                <table class="table table-sm mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Ngày</th>
                                            <th scope="col" class="text-end">Doanh thu</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($chartLabels as $index => $day)
                                            <tr>
                                                <td>{{ $day }}/{{ $now->format('m') }}</td>
                                                <td class="text-end">
                                                    {{ number_format($chartValues[$index], 0, ',', '.') }} đ
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </details>
                    </div>
                </section>
            </div>

            <div class="col-xl-5">
                <section class="card h-100">
                    <div class="card-body">
                        <h2 class="h5 fw-bold mb-2">Cần xử lý</h2>

                        <div class="dashboard-task">
                            <span class="dashboard-icon">
                                <i class="bi bi-calendar-check" aria-hidden="true"></i>
                            </span>

                            <strong class="dashboard-task-number">
                                {{ $summary['CHO_PHAN_CONG'] }}
                            </strong>

                            <span class="dashboard-task-label">
                                Lịch chờ phân công
                            </span>

                            <a href="{{ $pendingAppointmentsUrl }}" class="dashboard-link">
                                Xem
                                <span class="visually-hidden">lịch chờ phân công</span>
                            </a>
                        </div>

                        <div class="dashboard-task">
                            <span class="dashboard-icon dashboard-icon-purple">
                                <i class="bi bi-cash-stack" aria-hidden="true"></i>
                            </span>

                            <strong class="dashboard-task-number">
                                {{ $soYeuCauTienMat }}
                            </strong>

                            <span class="dashboard-task-label">
                                Yêu cầu thanh toán tiền mặt
                            </span>

                            <a href="#dashboardCashPayments" class="dashboard-link">
                                Xem
                                <span class="visually-hidden">yêu cầu tiền mặt</span>
                            </a>
                        </div>

                        <div class="dashboard-task">
                            <span class="dashboard-icon dashboard-icon-blue">
                                <i class="bi bi-envelope" aria-hidden="true"></i>
                            </span>

                            <strong class="dashboard-task-number">
                                {{ $soTinNhan }}
                            </strong>

                            <span class="dashboard-task-label">
                                Tin nhắn liên hệ đã nhận
                            </span>

                            <a href="{{ route('admin.contact.index') }}" class="dashboard-link">
                                Xem
                                <span class="visually-hidden">tin nhắn liên hệ</span>
                            </a>
                        </div>

                        <p class="small text-secondary mb-0">
                            Số tin nhắn là tổng số đã nhận, không phải số chưa đọc.
                        </p>
                    </div>
                </section>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-6">
                <section class="card h-100 overflow-hidden">
                    <div class="dashboard-panel-heading">
                        <h2>Lịch chờ phân công</h2>

                        <a href="{{ $pendingAppointmentsUrl }}" class="dashboard-link">
                            Xem tất cả
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Xe / Khách hàng</th>
                                    <th scope="col">Ngày hẹn</th>
                                    <th scope="col" class="text-end">Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($lichChoPhanCong as $lich)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-nowrap">
                                                {{ $lich->BienSo }}
                                            </div>

                                            <div class="small text-secondary">
                                                {{ $lich->HoTen }}
                                            </div>
                                        </td>

                                        <td class="text-nowrap">
                                            {{ \Carbon\Carbon::parse($lich->NgayHen)->format('d/m/Y') }}

                                            <div class="small text-secondary">
                                                {{ $lich->KhungGioHen }}
                                            </div>
                                        </td>

                                        <td class="text-end">
                                            <a
                                                href="{{ route('admin.lichhen.show', $lich->MaLH) }}"
                                                class="btn btn-outline-primary btn-sm text-nowrap"
                                            >
                                                Phân công
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-secondary py-4">
                                            Không có lịch chờ phân công.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            <div class="col-xl-6">
                <section
                    class="card h-100 overflow-hidden"
                    id="dashboardCashPayments"
                    style="scroll-margin-top: 100px;"
                >
                    <div class="dashboard-panel-heading">
                        <h2>Thanh toán tiền mặt</h2>

                        <a href="{{ route('hoadon.admin.index') }}" class="dashboard-link">
                            Danh sách hóa đơn
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Hóa đơn / Khách hàng</th>
                                    <th scope="col" class="text-end">Số tiền</th>
                                    <th scope="col" class="text-end">Thao tác</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($thanhToanTienMat as $hoaDon)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">
                                                #HD{{ $hoaDon->MaHD }}
                                            </div>

                                            <div class="small text-secondary mb-1">
                                                {{ $hoaDon->HoTen }}
                                            </div>

                                            <span class="dashboard-waiting">
                                                Chờ xác nhận
                                            </span>
                                        </td>

                                        <td class="text-end text-nowrap">
                                            {{ number_format($hoaDon->TongTien, 0, ',', '.') }} đ
                                        </td>

                                        <td class="text-end">
                                            <a
                                                href="{{ route('hoadon.admin.show', $hoaDon->MaHD) }}"
                                                class="btn btn-outline-primary btn-sm text-nowrap"
                                            >
                                                Xem hóa đơn
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-secondary py-4">
                                            Chưa có yêu cầu tiền mặt chờ xác nhận.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>

        <p class="small text-secondary mt-3 mb-0">
            Các số đếm trạng thái tính trên toàn bộ lịch hẹn hiện tại.
            Doanh thu bao gồm giao dịch mô phỏng đã xác nhận thành công.
        </p>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>

    <script>
        (() => {
            const chartWrap = document.getElementById('dashboardChartWrap');
            const canvas = document.getElementById('dashboardRevenueChart');

            if (!chartWrap || !canvas) {
                return;
            }

            const showTable = () => {
                chartWrap.hidden = true;

                const details = chartWrap.parentElement.querySelector('details');

                if (details) {
                    details.open = true;
                }
            };

            if (typeof Chart === 'undefined') {
                showTable();
                return;
            }

            const labels = {{ \Illuminate\Support\Js::from($chartLabels) }};
            const values = {{ \Illuminate\Support\Js::from($chartValues) }};
            const money = new Intl.NumberFormat('vi-VN');

            try {
                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Doanh thu',
                            data: values,
                            backgroundColor: '#2563eb',
                            hoverBackgroundColor: '#1d4ed8',
                            borderRadius: 6,
                            maxBarThickness: 45
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        animation: window.matchMedia(
                            '(prefers-reduced-motion: reduce)'
                        ).matches ? false : { duration: 400 },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    title: items => 'Ngày ' + items[0].label,
                                    label: context =>
                                        'Doanh thu: '
                                        + money.format(context.parsed.y)
                                        + ' ₫'
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#64748b',
                                    maxTicksLimit: 10
                                },
                                border: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.15)'
                                },
                                border: {
                                    display: false
                                },
                                ticks: {
                                    color: '#64748b',
                                    maxTicksLimit: 6,
                                    callback: value =>
                                        money.format(value) + ' ₫'
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                showTable();
                console.error('Không thể hiển thị biểu đồ doanh thu.', error);
            }
        })();
    </script>
@endpush
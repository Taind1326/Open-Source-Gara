@extends('admin.layout')

@section('title', 'Thống kê doanh thu')

@push('styles')
    <style>
        .chart-container {
            position: relative;
            height: 300px;
        }

        .stat-value {
            font-size: 1.65rem;
            font-weight: 800;
        }
    </style>
@endpush

@section('content')
    <div class="mb-4">
        <h1 class="h4 fw-bold mb-1">Thống kê doanh thu</h1>

        <p class="text-secondary mb-0">
            Doanh thu thực nhận, xu hướng tăng trưởng và hạng mục đóng góp nhiều nhất.
        </p>
    </div>

    <div class="card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('thongke.admin.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="tu_ngay" class="form-label">Từ ngày</label>

                        <input
                            type="date"
                            id="tu_ngay"
                            name="tu_ngay"
                            value="{{ $tuNgay }}"
                            max="{{ now('Asia/Ho_Chi_Minh')->format('Y-m-d') }}"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="den_ngay" class="form-label">Đến ngày</label>

                        <input
                            type="date"
                            id="den_ngay"
                            name="den_ngay"
                            value="{{ $denNgay }}"
                            max="{{ now('Asia/Ho_Chi_Minh')->format('Y-m-d') }}"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            Xem thống kê
                        </button>
                    </div>

                    <div class="col-md-2">
                        <a
                            href="{{ route('thongke.admin.index') }}"
                            class="btn btn-outline-secondary w-100"
                        >
                            Tháng này
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="text-secondary mb-2">Doanh thu thực nhận</div>

                    <div class="stat-value text-primary">
                        {{ number_format($doanhThu, 0, ',', '.') }} đ
                    </div>

                    <div class="small text-secondary mt-2">
                        Hóa đơn thanh toán trong khoảng đã chọn.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="text-secondary mb-2">Đã thanh toán</div>

                    <div class="stat-value text-success">
                        {{ $soHoaDonThanhToan }}
                    </div>

                    <div class="small text-secondary mt-2">
                        Theo ngày thanh toán.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="text-secondary mb-2">Chưa thanh toán</div>

                    <div class="stat-value text-warning">
                        {{ $soHoaDonChuaThanhToan }}
                    </div>

                    <div class="small text-secondary mt-2">
                        Lập trong khoảng chọn, hiện chưa thanh toán.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="text-secondary mb-2">Yêu cầu mới</div>

                    <div class="stat-value">{{ $tongYeuCau }}</div>

                    <div class="small text-secondary mt-2">
                        Theo ngày tạo yêu cầu.
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($soHoaDonThanhToan === 0)
        <div class="alert alert-info">
            Chưa có hóa đơn đã thanh toán trong khoảng chọn.
            Biểu đồ theo ngày và bảng top sẽ chưa có doanh thu.
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-xl-7">
            <div class="card h-100">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold">Doanh thu theo ngày</h2>

                    <p class="small text-secondary">
                        Trong khoảng ngày đã chọn, theo giờ Việt Nam.
                    </p>

                    <div class="chart-container">
                        <canvas
                            id="dailyChart"
                            role="img"
                            aria-label="Biểu đồ doanh thu theo ngày"
                        ></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card h-100">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold">So sánh với tháng trước</h2>

                    <p class="small text-secondary mb-3">
                        So sánh ngày 01–{{ $comparisonDays }} của
                        tháng {{ $currentMonthLabel }} và {{ $previousMonthLabel }}.
                    </p>

                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="h4 fw-bold mb-0">
                            {{ number_format($currentRevenue, 0, ',', '.') }} đ
                        </span>

                        @if ($growth !== null)
                            <span class="badge {{ $growth >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                {{ $growth > 0 ? '+' : '' }}{{ number_format($growth, 1, ',', '.') }}%
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary">
                                Tháng trước bằng 0
                            </span>
                        @endif
                    </div>

                    <p class="small text-secondary">
                        Chênh lệch:
                        {{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 0, ',', '.') }} đ
                    </p>

                    <div class="chart-container" style="height: 220px;">
                        <canvas
                            id="comparisonChart"
                            role="img"
                            aria-label="Biểu đồ so sánh doanh thu hai tháng"
                        ></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold">Doanh thu 6 tháng</h2>

                    <p class="small text-secondary">
                        Kết thúc ở tháng {{ $currentMonthLabel }}.
                        Tháng cuối chỉ tính đến ngày {{ $denNgay }};
                        các tháng trước tính cả tháng.
                    </p>

                    <div class="chart-container">
                        <canvas
                            id="monthlyChart"
                            role="img"
                            aria-label="Biểu đồ doanh thu sáu tháng"
                        ></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <h2 class="h5 fw-bold mb-1">Top hạng mục theo doanh thu</h2>

        <p class="small text-secondary mb-0">
            Trong khoảng ngày đã chọn, chỉ tính hóa đơn đã thanh toán.
            Giảm giá được phân bổ theo tỷ trọng giá trị từng hạng mục.
        </p>
    </div>

    <div class="row g-4">
        @foreach ([
            ['title' => 'Top 10 dịch vụ', 'items' => $topServices, 'count' => $tongLuotDichVu],
            ['title' => 'Top 10 phụ tùng', 'items' => $topParts, 'count' => $tongLuotPhuTung],
        ] as $group)
            <div class="col-xl-6">
                <div class="card h-100">
                    <div class="card-body p-4 border-bottom">
                        <h3 class="h5 fw-bold mb-1">{{ $group['title'] }}</h3>

                        <div class="small text-secondary">
                            Tổng số lượng đã thanh toán:
                            {{ number_format($group['count'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Hạng</th>
                                    <th>Hạng mục</th>
                                    <th>Số lượng</th>
                                    <th class="text-end pe-4">Doanh thu</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($group['items'] as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge {{ $loop->first ? 'bg-primary' : 'bg-secondary-subtle text-secondary' }}">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>

                                        <td class="fw-semibold">{{ $item->Ten }}</td>
                                        <td>{{ $item->SoLuong }}</td>

                                        <td class="text-end pe-4 text-nowrap">
                                            {{ number_format($item->DoanhThu, 0, ',', '.') }} đ
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-secondary py-4">
                                            Chưa có doanh thu.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="chartError" class="alert alert-warning mt-4 d-none">
        Không tải được thư viện biểu đồ. Kiểm tra kết nối mạng rồi tải lại trang.
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>

    <script>
        const report = {{ \Illuminate\Support\Js::from($chartData) }};
        const money = new Intl.NumberFormat('vi-VN');

        if (typeof Chart === 'undefined') {
            document.getElementById('chartError').classList.remove('d-none');
        } else {
            function drawChart(id, type, labels, values, colors) {
                return new Chart(document.getElementById(id), {
                    type,
                    data: {
                        labels,
                        datasets: [{
                            label: 'Doanh thu',
                            data: values,
                            backgroundColor: colors,
                            borderColor: '#2563eb',
                            borderWidth: type === 'line' ? 2 : 0,
                            borderRadius: type === 'bar' ? 6 : 0,
                            fill: type === 'line',
                            tension: 0.3,
                            pointRadius: values.length > 60 ? 0 : 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: context =>
                                        'Doanh thu: ' + money.format(context.parsed.y) + ' đ'
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxTicksLimit: 12
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => money.format(value) + ' đ'
                                }
                            }
                        }
                    }
                });
            }

            drawChart(
                'dailyChart',
                'line',
                report.dailyLabels,
                report.dailyValues,
                'rgba(37, 99, 235, 0.12)'
            );

            drawChart(
                'monthlyChart',
                'bar',
                report.monthlyLabels,
                report.monthlyValues,
                '#2563eb'
            );

            drawChart(
                'comparisonChart',
                'bar',
                report.comparisonLabels,
                report.comparisonValues,
                ['#94a3b8', '#2563eb']
            );
        }
    </script>
@endpush
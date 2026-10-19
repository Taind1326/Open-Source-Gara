@extends('admin.layout')

@section('title', 'Thống kê doanh thu')

@push('styles')
    <style>
        .garage-stat-value {
            font-size: clamp(1.4rem, 2vw, 1.9rem);
            font-weight: 750;
            overflow-wrap: anywhere;
        }

        .garage-chart-container {
            position: relative;
            height: 340px;
        }
    </style>
@endpush

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">
            Thống kê doanh thu
        </h1>

        <p class="text-secondary mb-0">
            Doanh thu sau giảm giá, tính theo thời điểm thanh toán thành công.
        </p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form
                method="GET"
                action="{{ route('thongke.admin.index') }}"
                novalidate
            >
                <div class="row g-3 align-items-end">
                    <div class="col-sm-6 col-lg-4">
                        <label for="report-year" class="form-label">
                            Năm thống kê
                        </label>

                        <input
                            type="number"
                            id="report-year"
                            name="nam"
                            min="1900"
                            max="{{ now('Asia/Ho_Chi_Minh')->year }}"
                            value="{{ old('nam', $nam) }}"
                            class="form-control @error('nam') is-invalid @enderror"
                            required
                        >

                        @error('nam')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-sm-6 col-lg-auto">
                        <button type="submit" class="btn btn-primary w-100">
                            Xem thống kê
                        </button>
                    </div>

                    <div class="col-lg-auto">
                        <a
                            href="{{ route('thongke.admin.index') }}"
                            class="btn btn-outline-secondary w-100"
                        >
                            Năm hiện tại
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="text-secondary mb-2">
                        Doanh thu năm {{ $nam }}
                    </div>

                    <div class="garage-stat-value text-primary">
                        {{ number_format($doanhThu, 0, ',', '.') }} ₫
                    </div>

                    <p class="small text-secondary mt-2 mb-0">
                        Theo ngày thanh toán, đã trừ ưu đãi.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="text-secondary mb-2">
                        Hóa đơn đã thanh toán
                    </div>

                    <div class="garage-stat-value text-success">
                        {{ number_format($soHoaDonThanhToan, 0, ',', '.') }}
                    </div>

                    <p class="small text-secondary mt-2 mb-0">
                        Thanh toán trong năm {{ $nam }}.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="text-secondary mb-2">
                        Hóa đơn chờ thanh toán
                    </div>

                    <div class="garage-stat-value text-warning">
                        {{ number_format($soHoaDonChuaThanhToan, 0, ',', '.') }}
                    </div>

                    <p class="small text-secondary mt-2 mb-0">
                        Lập trong năm {{ $nam }}, hiện chưa thanh toán.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="text-secondary mb-2">
                        Lịch hẹn được tạo
                    </div>

                    <div class="garage-stat-value">
                        {{ number_format($tongLichHen, 0, ',', '.') }}
                    </div>

                    <p class="small text-secondary mt-2 mb-0">
                        Tạo trong năm {{ $nam }}, gồm cả lịch đã hủy.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold">
                        Doanh thu 12 tháng — {{ $nam }}
                    </h2>

                    <p class="small text-secondary">
                        Tháng không phát sinh doanh thu hiển thị 0 ₫.
                        Năm hiện tại tính đến thời điểm xem.
                    </p>

                    <div class="garage-chart-container">
                        <canvas
                            id="revenueChart"
                            role="img"
                            aria-label="Biểu đồ doanh thu 12 tháng"
                        >
                            Doanh thu theo tháng được liệt kê trong bảng bên dưới.
                        </canvas>
                    </div>

                    <p
                        id="chart-error"
                        class="text-danger small mt-3 mb-0 d-none"
                        role="status"
                    >
                        Không tải được biểu đồ. Bạn vẫn xem được số liệu trong bảng.
                    </p>

                    <details class="mt-3">
                        <summary class="text-primary">
                            Xem số liệu từng tháng
                        </summary>

                        <div class="table-responsive mt-3">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Tháng</th>
                                        <th class="text-end">Doanh thu</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($chartData['monthlyLabels'] as $index => $label)
                                        <tr>
                                            <td>{{ $label }}</td>
                                            <td class="text-end">
                                                {{ number_format($chartData['monthlyValues'][$index], 0, ',', '.') }} ₫
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </details>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3">
                        So sánh doanh thu tháng
                    </h2>

                    <p class="small text-secondary">
                        Ngày 01–{{ $comparisonDays }} của tháng
                        {{ $currentMonthLabel }} và {{ $previousMonthLabel }}.
                        Tháng hiện tại chỉ tính đến thời điểm xem.
                    </p>

                    <div class="mb-4">
                        <div class="text-secondary mb-1">
                            Tháng {{ $currentMonthLabel }}
                        </div>

                        <div class="garage-stat-value text-primary">
                            {{ number_format($currentRevenue, 0, ',', '.') }} ₫
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="text-secondary mb-1">
                            Tháng {{ $previousMonthLabel }}
                        </div>

                        <div class="fs-5 fw-semibold">
                            {{ number_format($previousRevenue, 0, ',', '.') }} ₫
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <p class="mb-2">
                            Chênh lệch:
                            <strong class="{{ $difference >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 0, ',', '.') }} ₫
                            </strong>
                        </p>

                        @if($growth !== null)
                            <span class="badge {{ $growth >= 0 ? 'text-bg-success' : 'text-bg-danger' }}">
                                {{ $growth > 0 ? '+' : '' }}{{ number_format($growth, 1, ',', '.') }}%
                            </span>
                        @else
                            <span class="small text-secondary">
                                Chưa tính tỷ lệ vì tháng trước có doanh thu bằng 0.
                            </span>
                        @endif
                    </div>

                    <p class="small text-secondary mt-4 mb-0">
                        Năm hiện tại so sánh tháng này với tháng trước.
                        Năm cũ so sánh tháng 12 với tháng 11.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <h2 class="h5 fw-bold mb-2">
            Top hạng mục năm {{ $nam }}
        </h2>

        <p class="small text-secondary mb-0">
            Xếp theo số lượng trên hóa đơn đã thanh toán;
            nếu bằng nhau thì ưu tiên doanh thu cao hơn.
            Doanh thu hạng mục đã trừ đúng ưu đãi lưu trên hóa đơn.
        </p>
    </div>

    <div class="row g-4">
        @foreach([
            ['title' => 'Top 10 dịch vụ', 'items' => $topServices],
            ['title' => 'Top 10 phụ tùng', 'items' => $topParts],
        ] as $group)
            <div class="col-xl-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 border-bottom">
                        <h3 class="h5 fw-bold mb-0">
                            {{ $group['title'] }}
                        </h3>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Hạng</th>
                                    <th>Hạng mục</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end pe-4">Doanh thu</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($group['items'] as $item)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge {{ $loop->first ? 'text-bg-primary' : 'text-bg-secondary' }}">
                                                {{ $loop->iteration }}
                                            </span>
                                        </td>

                                        <td class="fw-semibold">
                                            {{ $item->Ten }}
                                        </td>

                                        <td class="text-end">
                                            {{ number_format($item->SoLuong, 0, ',', '.') }}
                                        </td>

                                        <td class="text-end text-nowrap pe-4">
                                            {{ number_format($item->DoanhThu, 0, ',', '.') }} ₫
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td
                                            colspan="4"
                                            class="text-center text-secondary py-4"
                                        >
                                            Chưa có dữ liệu đã thanh toán trong năm này.
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

    <p class="small text-secondary mt-4 mb-0">
        Số liệu bao gồm các giao dịch mô phỏng đã hoàn tất trong dữ liệu đồ án.
        Lịch hủy và hóa đơn chưa thanh toán không được cộng vào doanh thu.
    </p>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>

    <script>
        (() => {
            const report = {{ \Illuminate\Support\Js::from($chartData) }};
            const canvas = document.getElementById('revenueChart');

            if (!canvas) {
                return;
            }

            if (typeof Chart === 'undefined') {
                document.getElementById('chart-error')?.classList.remove('d-none');
                return;
            }

            const money = new Intl.NumberFormat('vi-VN');

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: report.monthlyLabels,
                    datasets: [{
                        label: 'Doanh thu',
                        data: report.monthlyValues,
                        backgroundColor: '#2563eb',
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
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: context =>
                                    'Doanh thu: ' + money.format(context.parsed.y) + ' ₫'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: value => money.format(value) + ' ₫'
                            }
                        }
                    }
                }
            });
        })();
    </script>
@endpush
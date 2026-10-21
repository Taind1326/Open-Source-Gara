@extends('admin.layout')

@section('title', 'Quản lý lịch hẹn')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">Quản lý lịch hẹn</h1>
        <p class="text-secondary mb-0">
            Theo dõi lịch tiếp nhận xe và phân công kỹ thuật viên.
        </p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form
                method="GET"
                action="{{ route('admin.lichhen.index') }}"
                novalidate
            >
                <div class="row g-3 align-items-end">
                    <div class="col-lg-4">
                        <label for="appointmentKeyword" class="form-label">
                            Tìm kiếm
                        </label>

                        <input
                            type="search"
                            id="appointmentKeyword"
                            name="tu_khoa"
                            value="{{ $tuKhoa }}"
                            maxlength="100"
                            placeholder="Tên khách, email, số điện thoại, biển số..."
                            class="form-control @error('tu_khoa') is-invalid @enderror"
                        >

                        @error('tu_khoa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-3">
                        <label for="appointmentDate" class="form-label">
                            Ngày hẹn
                        </label>

                        <input
                            type="date"
                            id="appointmentDate"
                            name="ngay_hen"
                            value="{{ $ngayHen }}"
                            class="form-control @error('ngay_hen') is-invalid @enderror"
                        >

                        @error('ngay_hen')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-3">
                        <label for="appointmentStatus" class="form-label">
                            Trạng thái
                        </label>

                        <select
                            id="appointmentStatus"
                            name="trang_thai"
                            class="form-select @error('trang_thai') is-invalid @enderror"
                        >
                            <option value="">Tất cả trạng thái</option>

                            @foreach(\App\Models\LichHen::NHAN_TRANG_THAI as $value => $label)
                                <option
                                    value="{{ $value }}"
                                    @selected($trangThai === $value)
                                >
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('trang_thai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-lg-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Lọc
                            </button>

                            <a
                                href="{{ route('admin.lichhen.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                Đặt lại
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h2 class="h5 fw-bold mb-4">
                Danh sách lịch hẹn
                <span class="text-secondary fw-normal">
                    ({{ $lichHens->total() }})
                </span>
            </h2>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã lịch</th>
                            <th>Khách hàng</th>
                            <th>Xe</th>
                            <th>Lịch tiếp nhận</th>
                            <th>Kỹ thuật viên</th>
                            <th>Trạng thái</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($lichHens as $lichHen)
                            <tr>
                                <td class="fw-semibold">
                                    #{{ $lichHen->MaLH }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $lichHen->taiKhoan->HoTen }}
                                    </div>

                                    <small class="text-secondary">
                                        {{ $lichHen->taiKhoan->SoDienThoai }}
                                    </small>
                                </td>

                                <td>
                                    <div class="fw-semibold text-nowrap">
                                        {{ $lichHen->xe->BienSo }}
                                    </div>

                                    <small class="text-secondary">
                                        {{ $lichHen->xe->HangXe }}
                                        {{ $lichHen->xe->DongXe }}
                                    </small>
                                </td>

                                <td class="text-nowrap">
                                    <div>
                                        {{ $lichHen->NgayHen->format('d/m/Y') }}
                                    </div>

                                    <small class="text-secondary">
                                        {{ $lichHen->KhungGioHen }}
                                    </small>
                                </td>

                                <td>
                                    {{ $lichHen->phanCong?->ktv?->HoTen ?? 'Chưa phân công' }}
                                </td>

                                <td class="text-nowrap">
                                    @include('admin.lichhen._status')
                                </td>

                                <td class="text-end">
                                    <a
                                        href="{{ route('admin.lichhen.show', $lichHen->MaLH) }}"
                                        class="btn btn-outline-primary btn-sm text-nowrap"
                                    >
                                        {{ $lichHen->TrangThai === 'CHO_PHAN_CONG' ? 'Phân công' : 'Chi tiết' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-secondary py-5">
                                    Không có lịch hẹn phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($lichHens->hasPages())
                <div class="mt-4">
                    {{ $lichHens->links('shared.pagination') }}
                </div>
            @endif
        </div>
    </div>
@endsection
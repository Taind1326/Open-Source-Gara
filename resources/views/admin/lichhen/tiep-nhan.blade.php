@extends('layouts.app')

@section('title', 'Tiếp nhận xe')

@section('content')
<div class="container-fluid py-4" style="max-width: 1000px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Tiếp nhận xe</h4>
        <a href="{{ route('admin.lichhen.cho-phan-cong') }}" class="btn btn-outline-secondary btn-sm">← Lịch chờ phân công</a>
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

    {{-- Ô tìm kiếm --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.lichhen.tiep-nhan') }}" class="row g-2">
                <div class="col-md-9">
                    <input type="text" name="tu_khoa" value="{{ $tuKhoa }}" class="form-control"
                        placeholder="Nhập mã yêu cầu, biển số xe hoặc số điện thoại khách..." autofocus>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    @if ($tuKhoa)

    {{-- A. Khách đã đặt lịch --}}
    <div class="card mb-3">
        <div class="card-header fw-semibold">
            A. Khách đã đặt lịch (đã phân công KTV)
            <span class="badge bg-secondary ms-1">{{ $ketQua->count() }}</span>
        </div>

        @if ($ketQua->isEmpty())
        <div class="card-body text-muted">
            Không tìm thấy lịch đã phân công khớp với "{{ $tuKhoa }}".
            Nếu lịch còn ở trạng thái chờ phân công, hãy
            <a href="{{ route('admin.lichhen.cho-phan-cong') }}">phân công KTV</a> trước.
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã</th>
                        <th>Ngày &amp; giờ hẹn</th>
                        <th>Xe</th>
                        <th>Khách hàng</th>
                        <th>KTV phụ trách</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ketQua as $yc)
                    <tr>
                        <td>#{{ $yc->MaYC }}</td>
                        <td>
                            {{ $yc->NgayHen->format('d/m/Y') }}
                            @if ($yc->NgayHen->isToday())
                            <span class="badge bg-warning text-dark">Hôm nay</span>
                            @endif
                            <br><small class="text-muted">{{ $yc->KhungGioHen }}</small>
                        </td>
                        <td>
                            <strong>{{ $yc->xe->BienSo }}</strong><br>
                            <small class="text-muted">{{ $yc->xe->HangXe }} {{ $yc->xe->DongXe }}</small>
                        </td>
                        <td>
                            {{ $yc->xe->taiKhoan->HoTen ?? '—' }}<br>
                            <small class="text-muted">{{ $yc->xe->taiKhoan->SoDienThoai ?? '' }}</small>
                        </td>
                        <td>{{ $yc->phanCong->ktv->HoTen ?? '—' }}</td>
                        <td class="text-end">
                            <form action="{{ route('admin.lichhen.xu-ly-tiep-nhan', $yc->MaYC) }}" method="POST"
                                onsubmit="return confirm('Xác nhận tiếp nhận xe {{ $yc->xe->BienSo }}?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Tiếp nhận xe</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- B. Khách đến trực tiếp --}}
    <div class="card">
        <div class="card-header fw-semibold">B. Khách đến trực tiếp (không đặt trước)</div>
        <div class="card-body">
            @if ($xeTimThay->isEmpty())
            <div class="text-muted">
                Không tìm thấy xe nào khớp với "{{ $tuKhoa }}".
                <a href="{{ route('admin.xe.create') }}">Thêm xe cho khách</a>
                (nếu khách chưa có tài khoản thì
                <a href="{{ route('admin.taikhoan.create') }}">tạo tài khoản</a> trước).
            </div>
            @else
            <form method="POST" action="{{ route('admin.lichhen.tiep-nhan-truc-tiep') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label fw-semibold">Chọn xe của khách</label>
                    @foreach ($xeTimThay as $xe)
                    <div class="form-check border rounded p-2 ps-5 mb-2">
                        <input class="form-check-input" type="radio" name="MaXe"
                            id="xe{{ $xe->MaXe }}" value="{{ $xe->MaXe }}"
                            @checked(old('MaXe')==$xe->MaXe) required>
                        <label class="form-check-label w-100" for="xe{{ $xe->MaXe }}">
                            <strong>{{ $xe->BienSo }}</strong> - {{ $xe->HangXe }} {{ $xe->DongXe }} ({{ $xe->NamSanXuat }})<br>
                            <small class="text-muted">
                                {{ $xe->taiKhoan->HoTen ?? '—' }} - {{ $xe->taiKhoan->SoDienThoai ?? '' }}
                            </small>
                        </label>
                    </div>
                    @endforeach
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Dịch vụ khách yêu cầu <small class="text-muted">(không bắt buộc)</small></label>
                    <div class="row">
                        @foreach ($dichVuList as $dv)
                        <div class="col-md-4">
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="dich_vu[]"
                                    value="{{ $dv->MaDV }}" id="dvtt{{ $dv->MaDV }}"
                                    @checked(in_array($dv->MaDV, old('dich_vu', [])))>
                                <label class="form-check-label" for="dvtt{{ $dv->MaDV }}">{{ $dv->TenDV }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả tình trạng</label>
                    <textarea name="MoTa" rows="2" maxlength="1000" class="form-control"
                        placeholder="Khách báo xe gặp vấn đề gì...">{{ old('MoTa') }}</textarea>
                </div>

                <div class="small text-muted mb-2">
                    Hệ thống tự lấy khung giờ hiện tại (Garage mở cửa 07:30–20:00), sau đó chuyển sang
                    chọn KTV rảnh và tự tiếp nhận xe.
                </div>

                <button type="submit" class="btn btn-primary">Tạo yêu cầu &amp; phân công KTV</button>
            </form>
            @endif
        </div>
    </div>

    @else
    <div class="text-center text-muted py-4">
        Nhập mã yêu cầu, biển số hoặc số điện thoại để tìm lịch cần tiếp nhận
        hoặc tạo yêu cầu cho khách đến trực tiếp.
    </div>
    @endif
</div>
@endsection
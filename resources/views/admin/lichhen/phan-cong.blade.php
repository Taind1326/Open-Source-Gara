@extends('layouts.app')

@section('title', 'Phân công KTV - #' . $yeuCau->MaYC)

@section('content')
<div class="container-fluid py-4" style="max-width: 960px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            Phân công KTV cho yêu cầu #{{ $yeuCau->MaYC }}
            @if ($trucTiep)
                <span class="badge bg-warning text-dark ms-2">Khách tại quầy</span>
            @endif
        </h4>
        <a href="{{ route('admin.lichhen.cho-phan-cong') }}" class="btn btn-outline-secondary btn-sm">← Danh sách chờ</a>
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

    <div class="row g-3">
        {{-- Thông tin yêu cầu --}}
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header fw-semibold">Thông tin yêu cầu</div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>Ngày &amp; giờ hẹn:</strong>
                        {{ $yeuCau->NgayHen->format('d/m/Y') }} ({{ $yeuCau->KhungGioHen }})
                    </p>
                    <p class="mb-1">
                        <strong>Xe:</strong>
                        {{ $yeuCau->xe->BienSo }} - {{ $yeuCau->xe->HangXe }} {{ $yeuCau->xe->DongXe }}
                        ({{ $yeuCau->xe->NamSanXuat }})
                    </p>
                    <p class="mb-1">
                        <strong>Khách hàng:</strong>
                        {{ $yeuCau->xe->taiKhoan->HoTen ?? '—' }}
                        @if (!empty($yeuCau->xe->taiKhoan->SoDienThoai))
                            - {{ $yeuCau->xe->taiKhoan->SoDienThoai }}
                        @endif
                    </p>
                    <p class="mb-1">
                        <strong>Dịch vụ quan tâm:</strong>
                        @forelse ($yeuCau->dichVu as $dv)
                            <span class="badge bg-light text-dark border">{{ $dv->TenDV }}</span>
                        @empty
                            <span class="text-muted">Chưa chọn</span>
                        @endforelse
                    </p>
                    <p class="mb-1"><strong>Mô tả:</strong></p>
                    <p style="white-space: pre-line;">{{ $yeuCau->MoTa ?: 'Không có mô tả.' }}</p>

                    @if ($yeuCau->HinhAnh)
                        <img src="{{ asset('storage/' . $yeuCau->HinhAnh) }}" alt="Ảnh minh họa"
                             class="img-fluid rounded border" style="max-height: 220px;">
                    @endif
                </div>
            </div>
        </div>

        {{-- Form chọn KTV --}}
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header fw-semibold">Chọn KTV rảnh</div>
                <div class="card-body">

                    @if ($yeuCau->phanCong && $yeuCau->phanCong->ktv)
                        <div class="alert alert-info py-2">
                            Hiện đã phân công: <strong>{{ $yeuCau->phanCong->ktv->HoTen }}</strong>.
                            Chọn KTV khác để đổi.
                        </div>
                    @endif

                    @if ($ktvRanh->isEmpty())
                        <div class="alert alert-warning mb-0">
                            Không có KTV nào rảnh trong khung giờ
                            <strong>{{ $yeuCau->KhungGioHen }}</strong> ngày
                            {{ $yeuCau->NgayHen->format('d/m/Y') }}.
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.lichhen.luu-phan-cong', $yeuCau->MaYC) }}"
                              onsubmit="return confirm('Xác nhận phân công KTV này?');">
                            @csrf

                            @if ($trucTiep)
                                {{-- Khách tại quầy: phân công xong tự chuyển sang Đã tiếp nhận --}}
                                <input type="hidden" name="tiep_nhan_ngay" value="1">
                                <div class="small text-muted mb-2">
                                    Sau khi phân công, hệ thống tự tiếp nhận xe để KTV bắt đầu kiểm tra.
                                </div>
                            @endif

                            @foreach ($ktvRanh as $ktv)
                                <div class="form-check border rounded p-2 ps-5 mb-2">
                                    <input class="form-check-input" type="radio" name="MaKTV"
                                           id="ktv{{ $ktv->MaTK }}" value="{{ $ktv->MaTK }}"
                                           @checked(old('MaKTV', optional($yeuCau->phanCong)->MaKTV) == $ktv->MaTK) required>
                                    <label class="form-check-label w-100" for="ktv{{ $ktv->MaTK }}">
                                        <strong>{{ $ktv->HoTen }}</strong><br>
                                        <small class="text-muted">{{ $ktv->SoDienThoai }}</small>
                                    </label>
                                </div>
                            @endforeach

                            @error('MaKTV') <div class="text-danger mb-2">{{ $message }}</div> @enderror

                            <button type="submit" class="btn btn-primary w-100 mt-2">Xác nhận phân công</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
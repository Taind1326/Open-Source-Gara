@extends('layouts.app')

@section('title', 'Chi tiết lịch hẹn #' . $yeuCau->MaYC)

@php
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
    [$nhan, $mau] = $trangThaiMap[$yeuCau->TrangThai] ?? [$yeuCau->TrangThai, 'secondary'];

    // Các mốc tiến trình hiển thị cho khách (bỏ qua nhánh hủy/từ chối)
    $cacMoc = [
        'CHO_PHAN_CONG'     => 'Chờ phân công',
        'DA_PHAN_CONG'      => 'Đã phân công',
        'DA_TIEP_NHAN'      => 'Tiếp nhận xe',
        'DA_KIEM_TRA'       => 'Kiểm tra',
        'CHO_DUYET_BAO_GIA' => 'Báo giá',
        'DANG_SUA'          => 'Sửa chữa',
        'HOAN_THANH'        => 'Hoàn thành',
    ];
    $keys = array_keys($cacMoc);
    $viTri = array_search($yeuCau->TrangThai, $keys, true); // false nếu DA_HUY/TU_CHOI
    $daKetThucSom = in_array($yeuCau->TrangThai, ['DA_HUY', 'TU_CHOI'], true);
@endphp

@section('content')
<div class="container py-4" style="max-width: 860px;">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            Lịch hẹn #{{ $yeuCau->MaYC }}
            <span class="badge bg-{{ $mau }} ms-2">{{ $nhan }}</span>
        </h4>
        <a href="{{ route('lichhen.index') }}" class="btn btn-outline-secondary btn-sm">← Danh sách</a>
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

    {{-- Tiến trình --}}
    @unless ($daKetThucSom)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between text-center">
                    @foreach ($cacMoc as $key => $ten)
                        @php
                            $i = $loop->index;
                            $xong = $viTri !== false && $i <= $viTri;
                        @endphp
                        <div class="flex-fill">
                            <div class="rounded-circle mx-auto mb-1 d-flex align-items-center justify-content-center
                                        {{ $xong ? 'bg-primary text-white' : 'bg-light text-muted border' }}"
                                 style="width:28px;height:28px;font-size:13px;">
                                {{ $xong ? '✓' : $i + 1 }}
                            </div>
                            <small class="{{ $xong ? 'fw-semibold' : 'text-muted' }}">{{ $ten }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-secondary">
            Yêu cầu này đã {{ $yeuCau->TrangThai === 'DA_HUY' ? 'bị hủy' : 'bị từ chối' }}.
        </div>
    @endunless

    <div class="row g-3">
        {{-- Thông tin xe --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Thông tin xe</div>
                <div class="card-body">
                    <p class="mb-1"><strong>Biển số:</strong> {{ $yeuCau->xe->BienSo }}</p>
                    <p class="mb-1"><strong>Hãng / Dòng:</strong> {{ $yeuCau->xe->HangXe }} {{ $yeuCau->xe->DongXe }}</p>
                    <p class="mb-0"><strong>Năm sản xuất:</strong> {{ $yeuCau->xe->NamSanXuat }}</p>
                </div>
            </div>
        </div>

        {{-- Lịch hẹn --}}
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header fw-semibold">Lịch hẹn</div>
                <div class="card-body">
                    <p class="mb-1"><strong>Ngày:</strong> {{ $yeuCau->NgayHen->format('d/m/Y') }}</p>
                    <p class="mb-1"><strong>Khung giờ:</strong> {{ $yeuCau->KhungGioHen }}</p>
                    <p class="mb-1">
                        <strong>KTV phụ trách:</strong>
                        @if ($yeuCau->phanCong && $yeuCau->phanCong->ktv)
                            {{ $yeuCau->phanCong->ktv->HoTen }}
                        @else
                            <span class="text-muted">Chưa phân công</span>
                        @endif
                    </p>
                    <p class="mb-0"><strong>Ngày tạo:</strong> {{ $yeuCau->NgayTao->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- Dịch vụ --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-semibold">Dịch vụ quan tâm</div>
                <div class="card-body">
                    @forelse ($yeuCau->dichVu as $dv)
                        <span class="badge bg-light text-dark border me-1 mb-1">
                            {{ $dv->TenDV }} ({{ number_format($dv->Gia, 0, ',', '.') }}đ)
                        </span>
                    @empty
                        <span class="text-muted">Chưa chọn dịch vụ. KTV sẽ đề xuất sau khi kiểm tra xe.</span>
                    @endforelse
                    <div class="small text-muted mt-2">
                        Giá trên chỉ mang tính tham khảo; báo giá chính thức sau khi KTV kiểm tra xe.
                    </div>
                </div>
            </div>
        </div>

        {{-- Mô tả + ảnh --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-semibold">Mô tả tình trạng</div>
                <div class="card-body">
                    @if ($yeuCau->MoTa)
                        <p style="white-space: pre-line;">{{ $yeuCau->MoTa }}</p>
                    @else
                        <p class="text-muted">Không có mô tả.</p>
                    @endif

                    @if ($yeuCau->HinhAnh)
                        <img src="{{ asset('storage/' . $yeuCau->HinhAnh) }}"
                             alt="Ảnh minh họa" class="img-fluid rounded border"
                             style="max-height: 300px;">
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Thao tác --}}
    <div class="mt-3 d-flex gap-2">
        @if ($yeuCau->TrangThai === 'CHO_PHAN_CONG' && $yeuCau->xe->MaTK === Auth::id())
            <form action="{{ route('lichhen.huy', $yeuCau->MaYC) }}" method="POST"
                  onsubmit="return confirm('Bạn chắc chắn muốn hủy lịch hẹn này?');">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Hủy lịch hẹn</button>
            </form>
        @endif
    </div>

</div>
@endsection
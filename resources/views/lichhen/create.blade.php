@extends('layouts.app')

@section('title', 'Đặt lịch hẹn')

@section('content')
<div class="container py-4" style="max-width: 860px;">

    <h4 class="mb-3">Đặt lịch hẹn</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if ($xeList->isEmpty())
        <div class="alert alert-warning">
            Bạn chưa có xe nào. Vui lòng thêm xe trước khi đặt lịch.
        </div>
    @else
    <form method="POST" action="{{ route('lichhen.store') }}" enctype="multipart/form-data">
        @csrf

        {{-- 1. Chọn xe --}}
        <div class="card mb-3">
            <div class="card-header fw-semibold">1. Chọn xe</div>
            <div class="card-body">
                <select name="MaXe" class="form-select @error('MaXe') is-invalid @enderror" required>
                    <option value="">-- Chọn xe --</option>
                    @foreach ($xeList as $xe)
                        <option value="{{ $xe->MaXe }}" @selected(old('MaXe') == $xe->MaXe)>
                            {{ $xe->BienSo }} - {{ $xe->HangXe }} {{ $xe->DongXe }} ({{ $xe->NamSanXuat }})
                        </option>
                    @endforeach
                </select>
                @error('MaXe') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- 2. Chọn dịch vụ --}}
        <div class="card mb-3">
            <div class="card-header fw-semibold">2. Dịch vụ quan tâm <small class="text-muted">(không bắt buộc)</small></div>
            <div class="card-body">
                <div class="row">
                    @foreach ($dichVuList as $dv)
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox"
                                       name="dich_vu[]" value="{{ $dv->MaDV }}" id="dv{{ $dv->MaDV }}"
                                       @checked(in_array($dv->MaDV, old('dich_vu', [])))>
                                <label class="form-check-label" for="dv{{ $dv->MaDV }}">
                                    {{ $dv->TenDV }}
                                    <small class="text-muted">({{ number_format($dv->Gia, 0, ',', '.') }}đ)</small>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 3. Chọn ngày + khung giờ --}}
        <div class="card mb-3">
            <div class="card-header fw-semibold">3. Ngày &amp; khung giờ <small class="text-muted">(Garage mở cửa 07:30–20:00)</small></div>
            <div class="card-body">
                <div class="mb-3" style="max-width: 260px;">
                    <label class="form-label">Ngày hẹn</label>
                    {{-- Đổi ngày → tải lại trang qua GET để tính lại khung giờ FULL/đã qua --}}
                    <input type="date" name="NgayHen" id="NgayHen"
                           class="form-control @error('NgayHen') is-invalid @enderror"
                           value="{{ old('NgayHen', $ngay) }}"
                           min="{{ now()->toDateString() }}" required>
                    @error('NgayHen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <label class="form-label">Khung giờ</label>
                <div class="row g-2">
                    @foreach ($khungGioList as $kg)
                        @php
                            $id = 'kg' . $loop->index;
                            $nhan = $kg['da_qua_gio'] ? 'Đã qua giờ' : ($kg['con_cho'] ? 'Còn chỗ' : 'HẾT LỊCH');
                        @endphp
                        <div class="col-6 col-md-3">
                            <input type="radio" class="btn-check" name="KhungGioHen"
                                   id="{{ $id }}" value="{{ $kg['khung_gio'] }}"
                                   @disabled(!$kg['con_cho'])
                                   @checked(old('KhungGioHen') === $kg['khung_gio'] && $kg['con_cho'])>
                            <label class="btn w-100 {{ $kg['con_cho'] ? 'btn-outline-primary' : 'btn-outline-secondary' }}"
                                   for="{{ $id }}">
                                {{ $kg['khung_gio'] }}<br>
                                <small>{{ $nhan }}</small>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('KhungGioHen') <div class="text-danger mt-2">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- 4. Mô tả + ảnh --}}
        <div class="card mb-3">
            <div class="card-header fw-semibold">4. Mô tả tình trạng xe</div>
            <div class="card-body">
                <textarea name="MoTa" rows="3" maxlength="1000"
                          class="form-control mb-3 @error('MoTa') is-invalid @enderror"
                          placeholder="Ví dụ: phanh kêu khi đạp, điều hòa không mát...">{{ old('MoTa') }}</textarea>
                @error('MoTa') <div class="invalid-feedback">{{ $message }}</div> @enderror

                <label class="form-label">Ảnh minh họa <small class="text-muted">(không bắt buộc, tối đa 2MB)</small></label>
                <input type="file" name="HinhAnh" accept="image/*"
                       class="form-control @error('HinhAnh') is-invalid @enderror">
                @error('HinhAnh') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('lichhen.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            <button type="submit" class="btn btn-primary">Gửi lịch hẹn</button>
        </div>
    </form>
    @endif
</div>

<script>
    // Đổi ngày → tải lại trang để server tính lại khung giờ FULL / đã qua giờ
    document.getElementById('NgayHen')?.addEventListener('change', function () {
        if (this.value) {
            window.location.href = "{{ route('lichhen.create') }}?ngay=" + this.value;
        }
    });
</script>
@endsection
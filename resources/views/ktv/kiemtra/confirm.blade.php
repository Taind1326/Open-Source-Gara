@extends('public.layout')
@section('title', 'Gửi phiếu kiểm tra')
@section('content')
<section class="py-5"><div class="container">
    <a href="{{ route('ktv.kiemtra.edit', $lichHen->MaLH) }}" class="btn btn-outline-primary mb-4">Quay lại phiếu nháp</a>
    <h1 class="h2">Xem lại trước khi gửi khách</h1>
    <p>Lịch #{{ $lichHen->MaLH }} · Xe {{ $lichHen->xe->BienSo }} · {{ $lichHen->taiKhoan->HoTen }}</p>
    <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
        <h2 class="h5">Kết quả kiểm tra</h2>
        <p class="text-break" style="white-space: pre-line">{{ $phieu->KetLuan }}</p>
        @if($phieu->GhiChu)<p class="text-break" style="white-space: pre-line">{{ $phieu->GhiChu }}</p>@endif
        @include('shared.inspection-items', ['snapshot' => $snapshot])
    </div></div>
    <form method="POST" action="{{ route('ktv.kiemtra.send', $lichHen->MaLH) }}" novalidate data-confirm="Gửi các hạng mục và chi phí cho khách xác nhận? Sau khi gửi, phiếu không còn được sửa." data-confirm-title="Gửi phiếu kiểm tra" data-confirm-accept="Gửi khách">
        @csrf
        <input type="hidden" name="phien_ban" value="{{ $phienBan }}">
        <div class="card mb-4"><div class="card-body p-4">
            <h2 class="h5">Bảo hành (nếu có)</h2>
            @if($baoHanhs->isNotEmpty())
                <label for="warranty-invoice" class="form-label">Hóa đơn cũ còn bảo hành tại thời điểm nhận xe</label>
                <select id="warranty-invoice" name="MaHDBaoHanh" class="form-select @error('MaHDBaoHanh') is-invalid @enderror">
                    <option value="">Không áp dụng bảo hành</option>
                    @foreach($baoHanhs as $maHD => $sources)
                        <option value="{{ $maHD }}" @selected((string) old('MaHDBaoHanh') === (string) $maHD)>Hóa đơn #{{ $maHD }}</option>
                    @endforeach
                </select>
                @error('MaHDBaoHanh')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="small text-muted my-3">
                    @foreach($baoHanhs as $maHD => $sources)
                        <p class="mb-1">#{{ $maHD }}: {{ $sources->pluck('TenHangMuc')->implode(', ') }} — hạn {{ \Carbon\Carbon::parse($sources->first()->BaoHanhDen)->format('d/m/Y H:i') }}</p>
                    @endforeach
                </div>
                <fieldset data-validation-group="bao_hanh_dich_vus">
                    <legend class="fs-6">Chọn đúng dịch vụ thuộc hạng mục được bảo hành</legend>
                    @foreach($snapshot['HangMuc'] as $item)
                        @if($item['KTVDeXuat'] && $item['MaDV'])
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="warranty-{{ $item['MaDV'] }}" name="bao_hanh_dich_vus[]" value="{{ $item['MaDV'] }}" @checked(in_array((string) $item['MaDV'], array_map('strval', old('bao_hanh_dich_vus', [])), true))>
                                <label class="form-check-label" for="warranty-{{ $item['MaDV'] }}">{{ $item['TenHangMuc'] }}</label>
                            </div>
                        @endif
                    @endforeach
                    @error('bao_hanh_dich_vus')<div class="text-danger small">{{ $message }}</div>@enderror
                    @foreach($errors->get('bao_hanh_dich_vus.*') as $messages) @foreach($messages as $message)<div class="text-danger small">{{ $message }}</div>@endforeach @endforeach
                </fieldset>
                <p class="small text-muted mt-3 mb-0">Chỉ chọn sau khi kiểm tra đúng hạng mục, cùng xe và cùng hóa đơn. Giảm 30% trên các dịch vụ đã chọn, không giảm phụ tùng và không cộng dồn đổi điểm. Chi phí sau giảm sẽ được lưu khi gửi.</p>
            @else
                <p class="mb-0">Xe chưa có hạng mục đủ điều kiện bảo hành.</p>
            @endif
        </div></div>
        <p class="text-muted">Trang này dùng bản nháp đã lưu. Nếu cần đổi hạng mục, hãy quay lại chỉnh và lưu nháp trước.</p>
        @error('phien_ban')<p class="text-danger" role="alert">{{ $message }}</p>@enderror
        <button type="submit" class="btn btn-primary">Gửi khách xác nhận</button>
    </form>
</div></section>
@endsection

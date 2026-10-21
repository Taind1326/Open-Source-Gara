@extends('public.layout')
@section('title', 'Xác nhận sửa chữa')
@section('content')
<section class="py-5"><div class="container">
    <a class="btn btn-outline-primary mb-4" href="{{ route('public.datlich.index') }}">Lịch hẹn của tôi</a>
    <h1 class="h2">Phiếu kiểm tra xe</h1>
    <p>Lịch #{{ $lichHen->MaLH }} · Xe {{ $lichHen->xe->BienSo }}</p>
    @error('phien_ban')<div class="text-danger mb-3" role="alert">{{ $message }}</div>@enderror
    <div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
        <h2 class="h5">Kết quả kiểm tra</h2>
        <p class="text-break" style="white-space: pre-line">{{ $snapshot['KetLuan'] ?? '' }}</p>
        @if($snapshot['GhiChu'] ?? null)<p class="text-break" style="white-space: pre-line">{{ $snapshot['GhiChu'] }}</p>@endif
        @include('shared.inspection-items', ['snapshot' => $coThePhanHoi ? $banKhongDiem : $snapshot])
    </div></div>
    @if($coThePhanHoi)
        <p>Vui lòng xem các hạng mục giữ lại, thêm hoặc bỏ. Kỹ thuật viên chỉ sửa sau khi bạn đồng ý. Thanh toán sau khi sửa xong.</p>
        <form class="card mb-4" method="POST" action="{{ route('public.xacnhan.approve', $lichHen->MaLH) }}" novalidate data-confirm="Đồng ý các hạng mục và tổng chi phí của phương án ưu đãi đã chọn?" data-confirm-title="Xác nhận sửa chữa" data-confirm-accept="Đồng ý sửa chữa">
            @csrf
            <input type="hidden" name="phien_ban" value="{{ $phienBan }}">
            <div class="card-body p-4">
                <label for="use-points" class="form-label">Phương án ưu đãi và tổng chi phí</label>
                <select name="su_dung_diem" id="use-points" class="form-select @error('su_dung_diem') is-invalid @enderror" required>
                    <option value="0" @selected((string) old('su_dung_diem', '0') === '0')>{{ $snapshot['LoaiUuDai'] === 'BAO_HANH' ? 'Áp dụng bảo hành' : 'Không dùng điểm' }} — {{ number_format($banKhongDiem['TongSauGiam'], 0, ',', '.') }}đ</option>
                    @if($banDoiDiem && $diemKhaDung >= 50 && $banDoiDiem['GiaTriDuocGiam'] > 0)
                        <option value="1" @selected((string) old('su_dung_diem') === '1')>Dùng 50 điểm giảm 5% dịch vụ — {{ number_format($banDoiDiem['TongSauGiam'], 0, ',', '.') }}đ</option>
                    @endif
                </select>
                @error('su_dung_diem')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <p class="small text-muted mt-2">Bạn có {{ $diemKhaDung }} điểm khả dụng. Bảng trên thể hiện chi phí chưa đổi điểm; tổng khi dùng điểm được ghi trong lựa chọn. Ưu đãi không áp dụng lên phụ tùng, không cộng dồn bảo hành.</p>
                <div class="form-check my-3">
                    <input class="form-check-input @error('dong_y') is-invalid @enderror" id="accept-repair" type="checkbox" name="dong_y" value="1" required @checked(old('dong_y'))>
                    <label class="form-check-label" for="accept-repair">Tôi đã đọc và đồng ý các hạng mục cùng tổng chi phí của phương án đã chọn.</label>
                    @error('dong_y')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <button class="btn btn-primary" type="submit">Đồng ý sửa chữa</button>
            </div>
        </form>
        <form class="card" method="POST" action="{{ route('public.xacnhan.reject', $lichHen->MaLH) }}" novalidate data-confirm="Từ chối sửa chữa và hủy lịch này?" data-confirm-title="Từ chối sửa chữa" data-confirm-accept="Từ chối và hủy lịch">
            @csrf
            <input type="hidden" name="phien_ban" value="{{ $phienBan }}">
            <div class="card-body p-4">
                <label class="form-label" for="reject-reason">Lý do từ chối sửa chữa</label>
                <textarea class="form-control @error('LyDo') is-invalid @enderror" id="reject-reason" name="LyDo" rows="3" maxlength="1000" required>{{ old('LyDo') }}</textarea>
                @error('LyDo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <p class="small text-muted mt-2">Lịch bị hủy sẽ được giữ trong lịch sử và không tạo hóa đơn.</p>
                <button class="btn btn-outline-danger" type="submit">Từ chối sửa chữa</button>
            </div>
        </form>
    @else
        <div class="alert alert-info">
            <strong>{{ $phieu->ten_trang_thai }}</strong>
            @if($phieu->NoiDungPhanHoi)<p class="mb-0 text-break">{{ $phieu->NoiDungPhanHoi }}</p>@endif
        </div>
    @endif
</div></section>
@endsection

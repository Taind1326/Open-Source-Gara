<div class="table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Hạng mục</th><th>Thay đổi</th><th>Số lượng</th><th class="text-end">Đơn giá</th><th class="text-end">Giảm giá</th><th class="text-end">Thành tiền</th></tr></thead>
        <tbody>
            @foreach($snapshot['HangMuc'] as $item)
                <tr class="{{ !$item['KTVDeXuat'] ? 'text-muted' : '' }}">
                    <td>{{ $item['TenHangMuc'] }}
                        @if($item['GhiChu'] ?? null)<small class="d-block">{{ $item['GhiChu'] }}</small>@endif
                        @if(($item['TyLeGiam'] ?? 0) == 30)<span class="badge text-bg-info">Bảo hành giảm 30%</span>@endif
                    </td>
                    <td><span class="badge {{ !$item['KTVDeXuat'] ? 'text-bg-secondary' : ($item['KhachDaChon'] ? 'text-bg-primary' : 'text-bg-success') }}">{{ !$item['KTVDeXuat'] ? 'Bỏ' : ($item['KhachDaChon'] ? 'Giữ lại' : 'Thêm') }}</span></td>
                    <td>{{ $item['SoLuong'] }} {{ $item['DonViTinh'] }}</td>
                    <td class="text-end text-nowrap">{{ number_format($item['DonGia'], 0, ',', '.') }}đ</td>
                    <td class="text-end text-nowrap">{{ number_format($item['TienGiam'] ?? 0, 0, ',', '.') }}đ</td>
                    <td class="text-end text-nowrap">{{ $item['KTVDeXuat'] ? number_format($item['ThanhTien'], 0, ',', '.') . 'đ' : 'Không tính' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="text-end border-top pt-3">
    <p class="mb-1">Tổng trước giảm: <strong>{{ number_format($snapshot['TongTruocGiam'], 0, ',', '.') }}đ</strong></p>
    <p class="mb-1">Giảm giá: <strong>{{ number_format($snapshot['TienGiam'], 0, ',', '.') }}đ</strong></p>
    <p class="fs-5 mb-0">Tổng chi phí: <strong>{{ number_format($snapshot['TongSauGiam'], 0, ',', '.') }}đ</strong></p>
</div>

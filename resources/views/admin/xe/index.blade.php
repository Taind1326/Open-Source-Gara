@extends('layouts.app')
@section('title', 'Quản lý xe')

@section('content')
<div class="head">
    <h1>Quản lý xe &amp; khách hàng</h1>
    <a class="btn" href="{{ route('admin.xe.create') }}">Thêm xe cho khách</a>
</div>

<form method="GET" action="{{ route('admin.xe.index') }}" class="filter" role="search">
    <input type="search" name="tu_khoa" value="{{ $tuKhoa }}" placeholder="Tìm theo biển số, hãng/dòng xe, tên hoặc SĐT chủ xe" aria-label="Từ khóa">
    <select name="trang_thai" aria-label="Trạng thái">
        <option value="">Tất cả trạng thái</option>
        <option value="HOAT_DONG" @selected($trangThai === 'HOAT_DONG')>Đang dùng</option>
        <option value="NGUNG_SU_DUNG" @selected($trangThai === 'NGUNG_SU_DUNG')>Ngừng sử dụng</option>
    </select>
    <button type="submit" class="btn">Lọc</button>
</form>

@if($dsXe->isEmpty())
    <div class="empty">Không có xe nào phù hợp.</div>
@else
    <div class="tablewrap">
        <table>
            <thead>
                <tr><th>Biển số</th><th>Hãng / dòng xe</th><th>Năm</th><th>Chủ xe</th><th>SĐT</th><th>Trạng thái</th><th></th></tr>
            </thead>
            <tbody>
            @foreach($dsXe as $xe)
                <tr>
                    <td><span class="plate">{{ $xe->BienSo }}</span></td>
                    <td>{{ $xe->HangXe }} {{ $xe->DongXe }}</td>
                    <td>{{ $xe->NamSanXuat ?: '—' }}</td>
                    <td><a href="{{ route('admin.taikhoan.show', $xe->MaTK) }}">{{ $xe->taiKhoan->HoTen }}</a></td>
                    <td>{{ $xe->taiKhoan->SoDienThoai }}</td>
                    <td>
                        @if($xe->TrangThai === \App\Models\Xe::HOAT_DONG)
                            <span class="tag tag-ok">Đang dùng</span>
                        @else
                            <span class="tag tag-off">Ngừng sử dụng</span>
                        @endif
                    </td>
                    <td class="right">
                        <div class="actions">
                            <a class="btn btn-line btn-sm" href="{{ route('admin.xe.edit', $xe->MaXe) }}">Sửa</a>
                            <form class="inline" method="POST" action="{{ route('admin.xe.trang-thai', $xe->MaXe) }}"
                                  onsubmit="return confirm('Đổi trạng thái xe {{ $xe->BienSo }}?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $xe->TrangThai === \App\Models\Xe::HOAT_DONG ? 'btn-danger' : 'btn-line' }}">
                                    {{ $xe->TrangThai === \App\Models\Xe::HOAT_DONG ? 'Ngừng' : 'Kích hoạt' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $dsXe->links('pagination.gara') }}
@endif
@endsection

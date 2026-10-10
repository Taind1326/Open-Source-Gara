@extends('layouts.app')
@section('title', 'Quản lý tài khoản')

@section('content')
@php
    $nhanVaiTro = ['USER' => 'Khách hàng', 'TECHNICIAN' => 'Kỹ thuật viên', 'ADMIN' => 'Quản trị viên'];
@endphp

<div class="head">
    <h1>Quản lý tài khoản</h1>
    <a class="btn" href="{{ route('admin.taikhoan.create') }}">Tạo tài khoản</a>
</div>

<form method="GET" action="{{ route('admin.taikhoan.index') }}" class="filter" role="search">
    <input type="search" name="tu_khoa" value="{{ $tuKhoa }}" placeholder="Tìm theo tên, số điện thoại, email" aria-label="Từ khóa">
    <select name="vai_tro" aria-label="Vai trò">
        <option value="">Tất cả vai trò</option>
        @foreach($nhanVaiTro as $ma => $ten)
            <option value="{{ $ma }}" @selected($vaiTro === $ma)>{{ $ten }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn">Lọc</button>
</form>

@if($dsTaiKhoan->isEmpty())
    <div class="empty">Không có tài khoản nào phù hợp.</div>
@else
    <div class="tablewrap">
        <table>
            <thead>
                <tr>
                    <th>Họ tên</th><th>Số điện thoại</th><th>Email</th><th>Vai trò</th><th>Trạng thái</th><th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($dsTaiKhoan as $tk)
                <tr>
                    <td><a href="{{ route('admin.taikhoan.show', $tk->MaTK) }}">{{ $tk->HoTen }}</a></td>
                    <td>{{ $tk->SoDienThoai }}</td>
                    <td>{{ $tk->Email ?: '—' }}</td>
                    <td><span class="tag tag-role">{{ $nhanVaiTro[$tk->VaiTro] ?? $tk->VaiTro }}</span></td>
                    <td>
                        @if($tk->dangHoatDong())
                            <span class="tag tag-ok">Hoạt động</span>
                        @else
                            <span class="tag tag-off">Bị khóa</span>
                        @endif
                    </td>
                    <td class="right">
                        <a class="btn btn-line btn-sm" href="{{ route('admin.taikhoan.edit', $tk->MaTK) }}">Sửa</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{ $dsTaiKhoan->links('pagination.gara') }}
@endif
@endsection

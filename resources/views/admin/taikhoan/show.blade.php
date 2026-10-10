@extends('layouts.app')
@section('title', $taiKhoan->HoTen)

@section('content')
@php
    $nhanVaiTro = ['USER' => 'Khách hàng', 'TECHNICIAN' => 'Kỹ thuật viên', 'ADMIN' => 'Quản trị viên'];
    $laToi = $taiKhoan->MaTK === auth()->id();
@endphp

<div class="head">
    <h1>{{ $taiKhoan->HoTen }}</h1>
    <div class="actions">
        <a class="btn btn-line" href="{{ route('admin.taikhoan.index') }}">Danh sách</a>
        <a class="btn" href="{{ route('admin.taikhoan.edit', $taiKhoan->MaTK) }}">Sửa</a>
        @unless($laToi)
            <form class="inline" method="POST" action="{{ route('admin.taikhoan.trang-thai', $taiKhoan->MaTK) }}"
                  onsubmit="return confirm('{{ $taiKhoan->dangHoatDong() ? 'Khóa' : 'Mở khóa' }} tài khoản này?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn {{ $taiKhoan->dangHoatDong() ? 'btn-danger' : 'btn-line' }}">
                    {{ $taiKhoan->dangHoatDong() ? 'Khóa tài khoản' : 'Mở khóa' }}
                </button>
            </form>
        @endunless
    </div>
</div>

<section class="card">
    <h2>Thông tin tài khoản</h2>
    <dl class="kv">
        <dt>Số điện thoại</dt><dd>{{ $taiKhoan->SoDienThoai }}</dd>
        <dt>Email</dt><dd>{{ $taiKhoan->Email ?: '—' }}</dd>
        <dt>Địa chỉ</dt><dd>{{ $taiKhoan->DiaChi ?: '—' }}</dd>
        <dt>Vai trò</dt><dd><span class="tag tag-role">{{ $nhanVaiTro[$taiKhoan->VaiTro] ?? $taiKhoan->VaiTro }}</span></dd>
        <dt>Trạng thái</dt>
        <dd>
            @if($taiKhoan->dangHoatDong())
                <span class="tag tag-ok">Hoạt động</span>
            @else
                <span class="tag tag-off">Bị khóa</span>
            @endif
        </dd>
        @if($taiKhoan->VaiTro === \App\Models\TaiKhoan::VAI_TRO_USER)
            <dt>Điểm tích lũy</dt><dd>{{ number_format($taiKhoan->DiemTichLuy, 0, ',', '.') }} điểm</dd>
        @endif
        <dt>Ngày tạo</dt><dd>{{ optional($taiKhoan->NgayTao)->format('d/m/Y H:i') ?: '—' }}</dd>
    </dl>
</section>

@if($taiKhoan->VaiTro === \App\Models\TaiKhoan::VAI_TRO_USER)
    <section class="card">
        <h2>Xe của khách hàng ({{ $taiKhoan->xe->count() }})</h2>
        <p><a class="btn btn-sm" href="{{ route('admin.xe.create', ['khach' => $taiKhoan->MaTK]) }}">Thêm xe cho khách</a></p>
        @if($taiKhoan->xe->isEmpty())
            <p class="muted">Khách hàng chưa đăng ký xe nào.</p>
        @else
            <div class="tablewrap">
                <table>
                    <thead><tr><th>Biển số</th><th>Hãng / dòng xe</th><th>Năm</th><th>Màu</th><th>Trạng thái</th></tr></thead>
                    <tbody>
                    @foreach($taiKhoan->xe as $xe)
                        <tr>
                            <td><span class="plate">{{ $xe->BienSo }}</span></td>
                            <td>{{ $xe->HangXe }} {{ $xe->DongXe }}</td>
                            <td>{{ $xe->NamSanXuat ?: '—' }}</td>
                            <td>{{ $xe->MauSac ?: '—' }}</td>
                            <td>
                                @if($xe->TrangThai === \App\Models\Xe::HOAT_DONG)
                                    <span class="tag tag-ok">Đang dùng</span>
                                @else
                                    <span class="tag tag-off">Ngừng hoạt động</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endif
@endsection

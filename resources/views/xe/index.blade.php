@extends('layouts.app')
@section('title', 'Xe của tôi')

@section('content')
<div class="head">
    <h1>Xe của tôi</h1>
    <a class="btn" href="{{ route('xe.create') }}">Thêm xe</a>
</div>

@if($dsXe->isEmpty())
    <div class="empty">
        <p>Bạn chưa đăng ký chiếc xe nào.</p>
        <a class="btn" href="{{ route('xe.create') }}">Thêm xe đầu tiên</a>
    </div>
@else
    <div class="cars">
        @foreach($dsXe as $xe)
            <article class="car">
                <span class="plate">{{ $xe->BienSo }}</span>
                <h3>{{ $xe->HangXe }} {{ $xe->DongXe }}</h3>
                <dl>
                    <dt>Năm sản xuất</dt><dd>{{ $xe->NamSanXuat ?: '—' }}</dd>
                    <dt>Màu sắc</dt><dd>{{ $xe->MauSac ?: '—' }}</dd>
                </dl>
                <div class="actions">
                    <a class="btn btn-line btn-sm" href="{{ route('xe.edit', $xe->MaXe) }}">Sửa</a>
                    <form class="inline" method="POST" action="{{ route('xe.destroy', $xe->MaXe) }}"
                          onsubmit="return confirm('Xóa xe {{ $xe->BienSo }} khỏi danh sách?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </div>
            </article>
        @endforeach
    </div>
@endif
@endsection

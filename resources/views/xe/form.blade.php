@extends('layouts.app')
@section('title', $xe->exists ? 'Sửa xe' : 'Thêm xe')
@section('wrap', 'mid')

@section('content')
<div class="head">
    <h1>{{ $xe->exists ? 'Sửa thông tin xe' : 'Thêm xe mới' }}</h1>
</div>

<section class="card">
    <form method="POST" action="{{ $xe->exists ? route('xe.update', $xe->MaXe) : route('xe.store') }}" novalidate>
        @csrf
        @if($xe->exists) @method('PUT') @endif

        <div class="field">
            <label for="BienSo">Biển số</label>
            <input type="text" id="BienSo" name="BienSo" value="{{ old('BienSo', $xe->BienSo) }}" maxlength="20" required
                   autofocus autocapitalize="characters" class="@error('BienSo') is-invalid @enderror">
            <div class="hint">Ví dụ: 59A-12345. Khoảng trắng sẽ được bỏ và chữ được viết hoa tự động.</div>
            @error('BienSo')<div class="err">{{ $message }}</div>@enderror
        </div>

        @include('xe._thong-tin-xe')

        <div class="actions">
            <button type="submit" class="btn">{{ $xe->exists ? 'Lưu thay đổi' : 'Thêm xe' }}</button>
            <a class="btn btn-line" href="{{ route('xe.index') }}">Hủy</a>
        </div>
    </form>
</section>
@endsection

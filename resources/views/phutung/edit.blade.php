@extends('admin.layout')

@section('title', 'Cập nhật phụ tùng')

@section('content')
    <div class="mb-4">
        <a href="{{ route('phutung.index') }}" class="text-decoration-none">
            ← Danh sách phụ tùng
        </a>

        <h1 class="h4 fw-bold mt-3 mb-1">Cập nhật phụ tùng</h1>

        <p class="text-secondary mb-0">
            Chỉnh sửa phụ tùng #{{ $phuTung->MaPT }}.
        </p>
    </div>

    <div class="row">
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body p-4">
                    <form
                        action="{{ route('phutung.update', $phuTung->MaPT) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PUT')

                        @include('phutung._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
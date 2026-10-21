@extends('admin.layout')

@section('title', 'Cập nhật dịch vụ')

@section('content')
    <div class="mb-4">
        <a href="{{ route('dichvu.index') }}" class="text-decoration-none">
            ← Danh sách dịch vụ
        </a>

        <h1 class="h4 fw-bold mt-3 mb-2">Cập nhật dịch vụ</h1>

        <p class="text-secondary mb-0">
            Chỉnh sửa dịch vụ #{{ $dichvu->MaDV }}.
        </p>
    </div>

    @if(!$dichvu->loaiDichVu?->HoatDong)
        <p class="text-secondary mb-4">
            Nhóm hiện tại đã ngừng hoạt động nên dịch vụ đang được ẩn
            khỏi danh sách công khai. Bạn có thể chọn nhóm khác.
        </p>
    @endif

    <div class="row">
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body p-4">
                    <form
                        action="{{ route('dichvu.update', $dichvu->MaDV) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        novalidate
                    >
                        @csrf
                        @method('PUT')

                        @include('dichvu._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
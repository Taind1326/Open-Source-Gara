@extends('admin.layout')

@section('title', 'Cập nhật dịch vụ')

@section('content')
    <div class="mb-4">
        <a href="{{ route('dichvu.index') }}" class="text-decoration-none">
            ← Danh sách dịch vụ
        </a>

        <h1 class="h4 fw-bold mt-3 mb-1">Cập nhật dịch vụ</h1>

        <p class="text-secondary mb-0">
            Chỉnh sửa thông tin dịch vụ #{{ $dichvu->MaDV }}.
        </p>
    </div>

    @if ($dichvu->loaiDichVu?->TrangThai === 'NGUNG_HOAT_DONG')
        <div class="alert alert-warning">
            Loại dịch vụ hiện tại đã ngừng hoạt động.
            Dịch vụ này đang được ẩn trên trang public.
            Bạn có thể chuyển sang loại đang hoạt động.
        </div>
    @endif

    <div class="row">
        <div class="col-xl-9">
            <div class="card">
                <div class="card-body p-4">
                    <form
                        action="{{ route('dichvu.update', $dichvu->MaDV) }}"
                        method="POST"
                        enctype="multipart/form-data"
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
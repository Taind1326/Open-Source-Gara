@extends('admin.layout')

@section('title', 'Thêm dịch vụ')

@section('content')
    <div class="mb-4">
        <a href="{{ route('dichvu.index') }}" class="text-decoration-none">
            ← Danh sách dịch vụ
        </a>

        <h1 class="h4 fw-bold mt-3 mb-1">Thêm dịch vụ</h1>

        <p class="text-secondary mb-0">
            Dịch vụ mới sẽ được đặt ở trạng thái hoạt động.
        </p>
    </div>

    @if ($loaiDichVuList->isEmpty())
        <div class="alert alert-warning">
            Chưa có loại dịch vụ đang hoạt động.

            <a href="{{ route('loaidichvu.create') }}" class="alert-link">
                Thêm loại dịch vụ
            </a>

            hoặc bật lại một loại dịch vụ trước khi tạo dịch vụ mới.
        </div>
    @else
        <div class="row">
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-body p-4">
                        <form
                            action="{{ route('dichvu.store') }}"
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            @csrf

                            @include('dichvu._form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@extends('admin.layout')

@section('title', 'Thêm dịch vụ')

@section('content')
    <div class="mb-4">
        <a href="{{ route('dichvu.index') }}" class="text-decoration-none">
            ← Danh sách dịch vụ
        </a>

        <h1 class="h4 fw-bold mt-3 mb-2">Thêm dịch vụ</h1>

        <p class="text-secondary mb-0">
            Nhập thông tin, giá trọn gói và hình ảnh dịch vụ.
        </p>
    </div>

    @if($loaiDichVuList->isEmpty())
        <div class="card">
            <div class="card-body p-4">
                <p>Vui lòng tạo loại dịch vụ trước khi thêm dịch vụ.</p>

                <a href="{{ route('loaidichvu.create') }}" class="btn btn-primary">
                    Thêm loại dịch vụ
                </a>
            </div>
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
                            novalidate
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
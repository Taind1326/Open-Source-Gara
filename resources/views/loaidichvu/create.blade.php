@extends('admin.layout')

@section('title', 'Thêm loại dịch vụ')

@section('content')
    <div class="mb-4">
        <a href="{{ route('loaidichvu.index') }}" class="text-decoration-none">
            ← Danh sách loại dịch vụ
        </a>

        <h1 class="h4 fw-bold mt-3 mb-2">
            Thêm loại dịch vụ
        </h1>

        <p class="text-secondary mb-0">
            Tạo nhóm để phân loại các dịch vụ của garage.
        </p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form
                        method="POST"
                        action="{{ route('loaidichvu.store') }}"
                        novalidate
                    >
                        @csrf

                        @include('loaidichvu._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
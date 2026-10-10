@extends('public.layout')

@section('title', 'Không có quyền truy cập')

@section('content')
    <div class="row justify-content-center py-5">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5 text-center">
                    <div class="display-1 fw-bold text-primary mb-3">
                        403
                    </div>

                    <h1 class="h4 fw-bold mb-3">
                        Không có quyền truy cập
                    </h1>

                    <p class="text-secondary mb-4">
                        Tài khoản của bạn không được phép sử dụng chức năng này.
                    </p>

                    <a
                        href="{{ route('public.dichvu.index') }}"
                        class="btn btn-primary"
                    >
                        Về trang dịch vụ
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
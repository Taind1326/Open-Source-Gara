@extends('public.layout')

@section('title', 'Thêm xe')

@section('content')
    <section class="hero">
        <div class="container">
            <nav aria-label="Đường dẫn">
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}" class="text-white">
                            Trang chủ
                        </a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="{{ route('public.xe.index') }}"
                           class="text-white">
                            Xe của tôi
                        </a>
                    </li>

                    <li class="breadcrumb-item active text-white"
                        aria-current="page">
                        Thêm xe
                    </li>
                </ol>
            </nav>

            <h1 class="display-5 fw-bold mb-3">
                Thêm chiếc xe của bạn
            </h1>

            <p class="hero-description mb-0">
                Nhập biển số và chọn thông tin xe từ danh mục.
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <form method="POST"
                  action="{{ route('public.xe.store') }}">
                @csrf

                @include('public.xe._form')
            </form>
        </div>
    </section>
@endsection
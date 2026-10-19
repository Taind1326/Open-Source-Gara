@extends('public.layout')

@section('title', 'Sửa thông tin xe')

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
                        Sửa thông tin xe
                    </li>
                </ol>
            </nav>

            <h1 class="display-5 fw-bold mb-3">
                Cập nhật xe {{ $xe->BienSo }}
            </h1>

            <p class="hero-description mb-0">
                Kiểm tra và cập nhật thông tin chiếc xe của bạn.
            </p>
        </div>
    </section>

    <section class="section-space">
        <div class="container">
            <form novalidate method="POST"
                  action="{{ route('public.xe.update', $xe->MaXe) }}">
                @csrf
                @method('PATCH')

                @include('public.xe._form', ['xe' => $xe])
            </form>
        </div>
    </section>
@endsection
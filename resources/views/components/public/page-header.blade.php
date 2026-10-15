@props(['title', 'description' => '', 'current'])
<section class="public-page-header">
    <div class="container" data-reveal>
        <nav aria-label="Đường dẫn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('public.home') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $current }}</li>
            </ol>
        </nav>
        <h1>{{ $title }}</h1>
        @if($description)<p>{{ $description }}</p>@endif
    </div>
</section>

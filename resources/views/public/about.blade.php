@extends('public.layout')
@section('title', 'Giới thiệu')
@section('description', 'Câu chuyện AutoCare Garage, hành trình phát triển và cách chăm sóc xe minh bạch.')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/home-orange.css') }}">
<link rel="stylesheet" href="{{ asset('css/about-orange.css') }}">
@endpush
@section('content')
<div class="orange-home about-page">
    <section class="about-hero">
        <div class="container">
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-6" data-reveal>
                    <p class="home-eyebrow">Câu chuyện AutoCare Garage</p>
                    <h1>Từ tâm huyết với xe,<br>đến sự an tâm của bạn.</h1>
                    <p class="home-lead mb-0">Một hành trình được xây dựng từ sự tận tâm và cách làm việc rõ ràng.</p>
                </div>
                <div class="col-lg-6" data-reveal>
                    @include('public.partials.about-photo', ['filename' => 'hero.jpg', 'alt' => 'Không gian AutoCare Garage', 'icon' => 'shop', 'eager' => true])
                </div>
            </div>
        </div>
    </section>

    <section class="about-section">
        <div class="container">
            <div class="mb-4" data-reveal>
                <p class="home-eyebrow">Khởi đầu</p>
                <h2>Một garage bắt đầu từ sự tận tâm</h2>
            </div>
            <div class="row align-items-center g-4 g-lg-5">
                <div class="col-lg-6" data-reveal>
                    @include('public.partials.about-photo', ['filename' => 'story.jpg', 'alt' => 'Chăm sóc và kiểm tra xe', 'icon' => 'tools'])
                </div>
                <div class="col-lg-6" data-reveal>
                    <p>AutoCare Garage hướng đến một nơi khách hàng có thể hiểu rõ tình trạng xe và chủ động quyết định việc sửa chữa.</p>
                    <p>Từ khâu tiếp nhận đến bàn giao, sự cẩn thận trong công việc và minh bạch về thông tin là điều chúng tôi theo đuổi.</p>
                    <p class="about-statement">Chăm sóc chiếc xe. Trân trọng sự tin tưởng.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section about-history">
        <div class="container">
            <div data-reveal>
                <h2>Hành trình hình thành và phát triển</h2>
                <p>Các mốc dưới đây là bố cục mẫu — cần bổ sung thông tin thực tế.</p>
            </div>
            {{-- Thay các năm và nội dung bên dưới bằng lịch sử thật của garage. --}}
            <ol class="about-milestones">
                @foreach([
                    ['year' => '[Năm thành lập]', 'title' => 'Khởi đầu', 'text' => 'Bổ sung câu chuyện ra đời của garage.', 'image' => 'milestone-1.jpg', 'icon' => 'shop'],
                    ['year' => '[Năm phát triển]', 'title' => 'Hoàn thiện dịch vụ', 'text' => 'Bổ sung cột mốc phát triển thực tế.', 'image' => 'milestone-2.jpg', 'icon' => 'wrench-adjustable'],
                    ['year' => '[Năm hiện tại]', 'title' => 'Kết nối với khách hàng', 'text' => 'Bổ sung định hướng và hoạt động hiện tại.', 'image' => 'milestone-3.jpg', 'icon' => 'people']
                ] as $milestone)
                    <li data-reveal>
                        <span class="about-milestone-dot" aria-hidden="true"></span>
                        <p class="about-year">{{ $milestone['year'] }}</p>
                        <h3>{{ $milestone['title'] }}</h3>
                        <p class="about-milestone-description">{{ $milestone['text'] }}</p>
                        @include('public.partials.about-photo', ['filename' => $milestone['image'], 'alt' => $milestone['title'], 'icon' => $milestone['icon']])
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="about-section">
        <div class="container">
            <h2 class="text-center mb-5" data-reveal>Uy tín đến từ cách chúng tôi làm việc</h2>
            <div class="about-values">
                @foreach([
                    ['icon' => 'search', 'title' => 'Kiểm tra thực tế', 'text' => 'Ghi nhận tình trạng xe trước khi đề xuất sửa chữa.'],
                    ['icon' => 'file-earmark-text', 'title' => 'Báo giá minh bạch', 'text' => 'Khách hàng xác nhận hạng mục và chi phí trước khi thực hiện.'],
                    ['icon' => 'gear', 'title' => 'Theo dõi rõ ràng', 'text' => 'Cập nhật tiến độ, hóa đơn và thông tin sửa chữa.']
                ] as $value)
                    <article data-reveal>
                        <i class="bi bi-{{ $value['icon'] }}" aria-hidden="true"></i>
                        <h3>{{ $value['title'] }}</h3>
                        <p>{{ $value['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="about-team-section">
        <div class="container">
            <div class="about-team" data-reveal>
                @include('public.partials.about-photo', ['filename' => 'team.jpg', 'alt' => 'Đội ngũ AutoCare Garage', 'icon' => 'people'])
                <div class="about-team-copy">
                    <h2>Con người đứng sau mỗi hành trình</h2>
                    <p>Sự tận tâm trong từng công việc là nền tảng để xây dựng niềm tin.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-cta-section">
        <div class="container">
            <div class="home-booking" data-reveal>
                <h2 class="mb-0">Tìm hiểu dịch vụ của AutoCare Garage</h2>
                <div class="d-flex flex-wrap gap-3">
                    <a class="btn btn-primary" href="{{ route('public.dichvu.index') }}">Khám phá dịch vụ <i class="bi bi-chevron-right ms-2" aria-hidden="true"></i></a>
                    <a class="btn btn-home-secondary" href="{{ route('public.contact') }}">Liên hệ gara</a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection


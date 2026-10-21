@extends('public.layout')
@section('title', 'Trang chủ')
@section('description', 'Đặt lịch, xem báo giá và theo dõi tiến độ sửa chữa tại AutoCare Garage.')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/home-orange.css') }}">
@endpush
@section('content')
<div class="orange-home">
<section class="orange-hero">
 <div class="container">
  <div class="row align-items-center g-4 g-xl-5">
   <div class="col-lg-6" data-reveal>
    <p class="home-eyebrow-pill">Đồng hành cùng chiếc xe của bạn</p>
    <h1>Chăm sóc chiếc xe.<br><span>An tâm hành trình.</span></h1>
    <p class="home-lead">Đặt lịch thuận tiện, xem báo giá và theo dõi tiến độ sửa chữa ngay trên website.</p>
    <div class="d-flex flex-wrap gap-3 mt-4">
     <a class="btn btn-primary btn-lg" href="{{ route('public.datlich.create') }}"><i class="bi bi-calendar-plus me-2" aria-hidden="true"></i>Đặt lịch sửa chữa</a>
     <a class="btn btn-home-secondary btn-lg" href="{{ route('public.dichvu.index') }}"><i class="bi bi-list-ul me-2" aria-hidden="true"></i>Khám phá dịch vụ</a>
    </div>
    <p class="home-hours"><i class="bi bi-clock me-2" aria-hidden="true"></i>07:30 – 20:00 hằng ngày</p>
   </div>
   <div class="col-lg-6" data-reveal>
    <div class="home-photo hero-photo">
     @if(file_exists(public_path('images/home/hero.jpg')))
      <img src="{{ asset('images/home/hero.jpg') }}" alt="Kỹ thuật viên chăm sóc xe tại AutoCare Garage" fetchpriority="high" width="900" height="650">
     @else
      <div class="home-photo-placeholder"><i class="bi bi-car-front" aria-hidden="true"></i><span>AutoCare Garage</span><small>Chăm sóc chiếc xe, an tâm hành trình.</small></div>
     @endif
    </div>
   </div>
  </div>
  <div class="home-benefits" data-reveal>
   <div><i class="bi bi-file-earmark-check" aria-hidden="true"></i><span>Báo giá trước khi sửa</span></div>
   <div><i class="bi bi-clock-history" aria-hidden="true"></i><span>Theo dõi tiến độ</span></div>
   <div><i class="bi bi-receipt" aria-hidden="true"></i><span>Lưu thông tin hóa đơn</span></div>
  </div>
 </div>
</section>
<section class="home-services">
 <div class="container">
  <div class="home-section-heading" data-reveal>
   <div><h2>Chăm sóc xe theo nhu cầu</h2><p>Khám phá danh mục và xem chi tiết từng dịch vụ.</p></div>
   <a class="btn btn-outline-primary" href="{{ route('public.dichvu.index') }}">Xem danh mục <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></a>
  </div>
  <div class="row g-4">
   @foreach([
    ['image' => 'maintenance.jpg', 'icon' => 'gear', 'title' => 'Bảo dưỡng ô tô', 'description' => 'Kiểm tra và chăm sóc để xe vận hành ổn định.'],
    ['image' => 'repair.jpg', 'icon' => 'tools', 'title' => 'Sửa chữa ô tô', 'description' => 'Kiểm tra tình trạng và đề xuất phương án sửa chữa.'],
    ['image' => 'cleaning.jpg', 'icon' => 'stars', 'title' => 'Chăm sóc và vệ sinh', 'description' => 'Chăm sóc nội thất và ngoại thất ô tô.']
   ] as $service)
   <div class="col-md-4" data-reveal>
    <article class="home-service-card">
     <div class="home-photo service-photo">
      @if(file_exists(public_path('images/home/' . $service['image'])))
       <img src="{{ asset('images/home/' . $service['image']) }}" alt="{{ $service['title'] }}" loading="lazy" width="600" height="360">
      @else
       <div class="home-photo-placeholder"><i class="bi bi-{{ $service['icon'] }}" aria-hidden="true"></i><span>{{ $service['title'] }}</span></div>
      @endif
     </div>
     <div class="home-service-body"><h3>{{ $service['title'] }}</h3><p>{{ $service['description'] }}</p><a class="btn btn-outline-primary w-100 mt-auto" href="{{ route('public.dichvu.index') }}">Khám phá dịch vụ <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i><span class="visually-hidden">: {{ $service['title'] }}</span></a></div>
    </article>
   </div>
   @endforeach
  </div>
 </div>
</section>
<section class="home-process">
 <div class="container">
  <div data-reveal><p class="home-eyebrow">Quy trình sửa chữa</p><h2>An tâm qua từng bước</h2><p class="home-process-description">Bạn luôn biết chiếc xe đang được chăm sóc như thế nào.</p></div>
  <ol class="home-timeline">
   @foreach([
    ['icon' => 'car-front', 'title' => 'Tiếp nhận xe', 'description' => 'Ghi nhận nhu cầu và tình trạng xe.'],
    ['icon' => 'file-earmark-check', 'title' => 'Kiểm tra & báo giá', 'description' => 'Bạn duyệt báo giá trước khi sửa.'],
    ['icon' => 'wrench-adjustable', 'title' => 'Sửa chữa & cập nhật', 'description' => 'Theo dõi tiến độ trong tài khoản.'],
    ['icon' => 'key', 'title' => 'Thanh toán & bàn giao', 'description' => 'Xem hóa đơn và nhận lại xe.']
   ] as $step)
    <li data-reveal><div class="home-step-icon"><i class="bi bi-{{ $step['icon'] }}" aria-hidden="true"></i><span>{{ sprintf('%02d', $loop->iteration) }}</span></div><div><h3>{{ $step['title'] }}</h3><p>{{ $step['description'] }}</p></div></li>
   @endforeach
  </ol>
  <div class="home-booking" data-reveal>
   <div class="home-booking-copy"><span class="home-booking-icon"><i class="bi bi-calendar-plus" aria-hidden="true"></i></span><div><h2>Bạn đang cần chăm sóc chiếc xe?</h2><p>Chọn dịch vụ phù hợp hoặc liên hệ gara để được hỗ trợ.</p></div></div>
   <div class="d-flex flex-wrap gap-3"><a class="btn btn-primary" href="{{ route('public.datlich.create') }}"><i class="bi bi-calendar-plus me-2" aria-hidden="true"></i>Đặt lịch ngay</a><a class="btn btn-home-secondary" href="{{ route('public.contact') }}"><i class="bi bi-telephone me-2" aria-hidden="true"></i>Liên hệ gara</a></div>
  </div>
 </div>
</section>
</div>
@endsection


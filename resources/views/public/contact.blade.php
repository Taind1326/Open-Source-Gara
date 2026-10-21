@extends('public.layout')
@section('title', 'Liên hệ')
@section('description', 'Liên hệ AutoCare Garage, gửi tin nhắn và tìm đường đến garage.')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/contact-orange.css') }}">
@endpush
@section('content')
@php
    $address = config('garage.address');
    $phone = config('garage.phone');
    $email = config('garage.email');
    $mapUrl = $address ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address) : null;
@endphp
<div class="public-page contact-page">
    <section class="contact-page-header">
        <div class="container" data-reveal>
            <nav aria-label="Đường dẫn"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('public.home') }}">Trang chủ</a></li><li class="breadcrumb-item active" aria-current="page">Liên hệ</li></ol></nav>
            <h1>Kết nối với AutoCare Garage</h1>
            <p>Gửi lời nhắn hoặc ghé garage để trao đổi nhu cầu chăm sóc xe.</p>
        </div>
    </section>
    <section class="contact-main">
        <div class="container">
            <div class="row g-4 g-xl-5">
                <div class="col-lg-5" data-reveal>
                    <h2>Chúng tôi sẵn sàng lắng nghe</h2>
                    <p>Chia sẻ tình trạng xe hoặc điều bạn cần hỗ trợ.</p>
                    <div class="contact-info-row">
                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                        <div><h3>Địa chỉ garage</h3><p>{{ $address ?: 'Địa chỉ đang cập nhật.' }}</p></div>
                    </div>
                    <div class="contact-info-row">
                        <i class="bi bi-clock" aria-hidden="true"></i>
                        <div><h3>Giờ mở cửa</h3><p>{{ config('garage.opening_hours') ?: 'Giờ mở cửa đang cập nhật.' }}</p></div>
                    </div>
                    <div class="contact-info-row">
                        <i class="bi bi-telephone" aria-hidden="true"></i>
                        <div>
                            <h3>Thông tin liên hệ</h3>
                            @if($phone)<p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}">{{ $phone }}</a></p>@endif
                            @if($email)<p><a href="mailto:{{ $email }}">{{ $email }}</a></p>@endif
                            @if(!$phone && !$email)<p>Số điện thoại và email đang cập nhật.</p>@endif
                        </div>
                    </div>
                    <div class="contact-socials">
                        <h3>Kết nối cùng chúng tôi</h3>
                        <p>Theo dõi thông tin và hoạt động của garage.</p>
                        <div class="contact-social-links">
                            @foreach(['Facebook' => 'facebook', 'Zalo' => 'chat-dots', 'YouTube' => 'youtube'] as $platform => $icon)
                                @php
                                    $socialUrl = config('garage.socials.' . $platform);
                                    $validUrl = is_string($socialUrl) && filter_var($socialUrl, FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($socialUrl, PHP_URL_SCHEME) ?? ''), ['http', 'https'], true);
                                @endphp
                                @if($validUrl)
                                    <a href="{{ $socialUrl }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-{{ $icon }}" aria-hidden="true"></i>{{ $platform }}<span class="visually-hidden"> (mở tab mới)</span></a>
                                @else
                                    <span class="contact-social-pending"><i class="bi bi-{{ $icon }}" aria-hidden="true"></i>{{ $platform }}<small>Đang cập nhật</small></span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-7" data-reveal>
                    <form class="contact-form" method="POST" action="{{ route('public.contact.store') }}">
                        @csrf
                        <h2>Gửi tin nhắn cho garage</h2>
                        <p>Điền thông tin bên dưới để chúng tôi có thể liên hệ lại.</p>
                        <div class="row g-3">
                            @foreach(['name' => ['Họ và tên', 'text', true, 100], 'phone' => ['Số điện thoại', 'tel', true, 20], 'email' => ['Email', 'email', false, 255]] as $field => [$label, $type, $required, $maxlength])
                                <div class="col-md-6">
                                    <label for="contact-{{ $field }}" class="form-label">{{ $label }} @if($required)<span class="text-danger" aria-hidden="true">*</span>@endif</label>
                                    <input id="contact-{{ $field }}" type="{{ $type }}" name="{{ $field }}" value="{{ old($field) }}" maxlength="{{ $maxlength }}"
                                        autocomplete="{{ ['name' => 'name', 'phone' => 'tel', 'email' => 'email'][$field] }}"
                                        class="form-control @error($field) is-invalid @enderror" @required($required)
                                        @error($field) aria-invalid="true" aria-describedby="contact-{{ $field }}-error" @enderror>
                                    @error($field)<div id="contact-{{ $field }}-error" class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                            <div class="col-md-6">
                                <label for="contact-subject" class="form-label">Chủ đề <span class="text-danger" aria-hidden="true">*</span></label>
                                <select id="contact-subject" name="subject" required class="form-select @error('subject') is-invalid @enderror">
                                    <option value="">Chọn nội dung cần hỗ trợ</option>
                                    @foreach(['Tư vấn dịch vụ', 'Hỗ trợ đặt lịch', 'Góp ý', 'Nội dung khác'] as $subject)<option value="{{ $subject }}" @selected(old('subject') === $subject)>{{ $subject }}</option>@endforeach
                                </select>
                                @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label for="contact-message" class="form-label">Tin nhắn <span class="text-danger" aria-hidden="true">*</span></label>
                                <textarea id="contact-message" name="message" rows="6" minlength="10" maxlength="5000" required class="form-control @error('message') is-invalid @enderror" placeholder="Bạn cần tư vấn dịch vụ hoặc hỗ trợ điều gì?">{{ old('message') }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <p class="contact-form-note mt-3">* Thông tin bắt buộc. Email không bắt buộc.</p>
                        <p class="contact-form-note">Thông tin bạn cung cấp được dùng để phản hồi yêu cầu.</p>
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-envelope me-2" aria-hidden="true"></i>Gửi tin nhắn</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <section class="contact-map-section">
        <div class="container" data-reveal>
            <div class="contact-map-heading">
                <div><h2>Đường đến AutoCare Garage</h2><p>{{ $address ?: 'Địa chỉ đang cập nhật.' }}</p></div>
                @if($mapUrl)<a class="btn btn-outline-primary" href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer">Mở Google Maps <i class="bi bi-arrow-up-right ms-2" aria-hidden="true"></i><span class="visually-hidden"> (mở tab mới)</span></a>@endif
            </div>
            @if($address)
                <iframe class="contact-map" title="Bản đồ đường đến AutoCare Garage" src="https://www.google.com/maps?q={{ rawurlencode($address) }}&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
            @else
                <p>Garage sẽ bổ sung bản đồ khi có địa chỉ.</p>
            @endif
        </div>
    </section>
    <section class="contact-cta-section">
        <div class="container">
            <div class="contact-booking" data-reveal>
                <div><h2>Bạn muốn đặt lịch chăm sóc xe?</h2><p>Chọn ngày và khung giờ phù hợp với bạn.</p></div>
                <a class="btn btn-primary" href="{{ route('public.datlich.create') }}"><i class="bi bi-calendar-plus me-2" aria-hidden="true"></i>Đặt lịch sửa chữa</a>
            </div>
        </div>
    </section>
</div>
@endsection

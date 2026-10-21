@extends('admin.layout')

@section('title', 'Tin nhắn liên hệ')

@section('content')
    <div class="mb-4">
        <h1 class="h3 fw-bold mb-2">
            Tin nhắn liên hệ
        </h1>

        <p class="text-secondary mb-0">
            Xem yêu cầu tư vấn, hỗ trợ và góp ý từ khách hàng.
        </p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <p class="text-secondary small mb-3">
                {{ number_format($messages->total(), 0, ',', '.') }} tin nhắn
            </p>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Khách hàng</th>
                            <th scope="col">Thông tin liên hệ</th>
                            <th scope="col">Chủ đề</th>
                            <th scope="col" class="text-nowrap">Ngày gửi</th>
                            <th scope="col" class="text-end">Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($messages as $message)
                            <tr>
                                <td>
                                    <div class="fw-semibold">
                                        {{ $message->name }}
                                    </div>

                                    <small class="text-secondary">
                                        #{{ $message->id }}
                                    </small>
                                </td>

                                <td>
                                    <div>{{ $message->phone }}</div>

                                    <small class="text-secondary text-break">
                                        {{ $message->email ?: 'Không cung cấp email' }}
                                    </small>
                                </td>

                                <td>
                                    {{ $message->subject }}
                                </td>

                                <td class="text-nowrap">
                                    {{ $message->created_at?->format('d/m/Y') }}

                                    <div class="small text-secondary">
                                        {{ $message->created_at?->format('H:i') }}
                                    </div>
                                </td>

                                <td class="text-end">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary btn-sm text-nowrap"
                                        data-bs-toggle="modal"
                                        data-bs-target="#contact-message-{{ $message->id }}"
                                    >
                                        Xem nội dung
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <h2 class="h6 mb-2">
                                        Chưa có tin nhắn liên hệ
                                    </h2>

                                    <p class="text-secondary mb-0">
                                        Tin nhắn khách gửi từ trang liên hệ sẽ hiển thị tại đây.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($messages->hasPages())
                <div class="mt-4">
                    {{ $messages->links('shared.pagination') }}
                </div>
            @endif
        </div>
    </div>

    {{-- Đặt popup ngoài bảng để không ảnh hưởng bố cục. --}}
    @foreach($messages as $message)
        <div
            class="modal fade"
            id="contact-message-{{ $message->id }}"
            tabindex="-1"
            aria-labelledby="contact-message-title-{{ $message->id }}"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2
                            class="modal-title fs-5"
                            id="contact-message-title-{{ $message->id }}"
                        >
                            Tin nhắn #{{ $message->id }}
                        </h2>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Đóng"
                        ></button>
                    </div>

                    <div class="modal-body p-4">
                        <dl class="row g-3 mb-4">
                            <dt class="col-sm-3">Khách hàng</dt>
                            <dd class="col-sm-9 mb-0 text-break">
                                {{ $message->name }}
                            </dd>

                            <dt class="col-sm-3">Số điện thoại</dt>
                            <dd class="col-sm-9 mb-0 text-break">
                                {{ $message->phone }}
                            </dd>

                            <dt class="col-sm-3">Email</dt>
                            <dd class="col-sm-9 mb-0 text-break">
                                {{ $message->email ?: 'Không cung cấp' }}
                            </dd>

                            <dt class="col-sm-3">Ngày gửi</dt>
                            <dd class="col-sm-9 mb-0">
                                {{ $message->created_at?->format('d/m/Y H:i') }}
                            </dd>

                            <dt class="col-sm-3">Chủ đề</dt>
                            <dd class="col-sm-9 mb-0 text-break">
                                {{ $message->subject }}
                            </dd>
                        </dl>

                        <h3 class="h6 fw-bold mb-3">
                            Nội dung tin nhắn
                        </h3>

                        <div
                            class="border rounded-3 p-3"
                            style="white-space: pre-wrap; overflow-wrap: anywhere; line-height: 1.7;"
                        >{{ $message->message }}</div>
                    </div>

                    <div class="modal-footer">
                        <button
                            type="button"
                            class="btn btn-outline-secondary"
                            data-bs-dismiss="modal"
                        >
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@extends('ktv.layout')

@section('title', 'Gửi phiếu kiểm tra')

@section('content')
    @php
        $thongBaoPhieu = $loiHangMuc ?? $errors->first('phien_ban');
        $biChanGui = filled($loiHangMuc);

        $dichVuBaoHanhDaChon = old('bao_hanh_dich_vus', []);
        $dichVuBaoHanhDaChon = is_array($dichVuBaoHanhDaChon)
            ? array_map('strval', $dichVuBaoHanhDaChon)
            : [];
    @endphp

    <div class="ktv-container">
        <a
            href="{{ route('ktv.kiemtra.edit', $lichHen->MaLH) }}"
            class="btn btn-outline-secondary mb-4"
        >
            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>
            Quay lại phiếu nháp
        </a>

        <div class="ktv-page-heading">
            <h1>Xem lại trước khi gửi khách</h1>

            <p>
                Lịch #{{ $lichHen->MaLH }}
                · {{ $lichHen->xe->BienSo }}
                · {{ $lichHen->taiKhoan->HoTen }}
            </p>
        </div>

        <section class="ktv-panel mb-4">
            <h2 class="h5 fw-bold mb-3">
                Kết quả kiểm tra
            </h2>

            <p
                class="text-break"
                style="white-space: pre-line;"
            >{{ $phieu->KetLuan }}</p>

            @if($phieu->GhiChu)
                <p
                    class="text-secondary text-break"
                    style="white-space: pre-line;"
                >{{ $phieu->GhiChu }}</p>
            @endif

            @include('shared.inspection-items', [
                'snapshot' => $snapshot,
            ])
        </section>

        <form
            method="POST"
            action="{{ route('ktv.kiemtra.send', $lichHen->MaLH) }}"
            novalidate
            data-confirm="Gửi các hạng mục và chi phí cho khách xác nhận? Sau khi gửi, phiếu không còn được sửa."
            data-confirm-title="Gửi phiếu kiểm tra"
            data-confirm-accept="Gửi khách"
        >
            @csrf

            <input
                type="hidden"
                name="phien_ban"
                value="{{ $phienBan }}"
            >

            <section class="ktv-panel mb-4">
                <h2 class="h5 fw-bold mb-3">
                    Bảo hành
                </h2>

                @if($baoHanhs->isNotEmpty())
                    <div class="mb-3">
                        <label for="warranty-invoice" class="form-label">
                            Hóa đơn cũ còn bảo hành tại thời điểm nhận xe
                        </label>

                        <select
                            id="warranty-invoice"
                            name="MaHDBaoHanh"
                            class="form-select @error('MaHDBaoHanh') is-invalid @enderror"
                        >
                            <option value="">
                                Không áp dụng bảo hành
                            </option>

                            @foreach($baoHanhs as $maHD => $sources)
                                <option
                                    value="{{ $maHD }}"
                                    @selected(
                                        (string) old('MaHDBaoHanh')
                                        === (string) $maHD
                                    )
                                >
                                    Hóa đơn #{{ $maHD }}
                                </option>
                            @endforeach
                        </select>

                        @error('MaHDBaoHanh')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="small text-secondary mb-4">
                        @foreach($baoHanhs as $maHD => $sources)
                            <p class="mb-2">
                                <strong>Hóa đơn #{{ $maHD }}:</strong>
                                {{ $sources->pluck('TenHangMuc')->implode(', ') }}
                                — hạn
                                {{ \Carbon\Carbon::parse(
                                    $sources->first()->BaoHanhDen
                                )->format('d/m/Y H:i') }}
                            </p>
                        @endforeach
                    </div>

                    <fieldset data-validation-group="bao_hanh_dich_vus">
                        <legend class="fs-6 fw-semibold mb-3">
                            Chọn đúng dịch vụ thuộc hạng mục được bảo hành
                        </legend>

                        @foreach($snapshot['HangMuc'] as $item)
                            @if($item['KTVDeXuat'] && $item['MaDV'])
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="warranty-{{ $item['MaDV'] }}"
                                        name="bao_hanh_dich_vus[]"
                                        value="{{ $item['MaDV'] }}"
                                        @checked(in_array(
                                            (string) $item['MaDV'],
                                            $dichVuBaoHanhDaChon,
                                            true
                                        ))
                                    >

                                    <label
                                        class="form-check-label"
                                        for="warranty-{{ $item['MaDV'] }}"
                                    >
                                        {{ $item['TenHangMuc'] }}
                                    </label>
                                </div>
                            @endif
                        @endforeach

                        @error('bao_hanh_dich_vus')
                            <div class="text-danger small">
                                {{ $message }}
                            </div>
                        @enderror

                        @foreach($errors->get('bao_hanh_dich_vus.*') as $messages)
                            @foreach($messages as $message)
                                <div class="text-danger small">
                                    {{ $message }}
                                </div>
                            @endforeach
                        @endforeach
                    </fieldset>

                    <p class="small text-secondary mt-3 mb-0">
                        Chỉ chọn sau khi kiểm tra đúng hạng mục,
                        cùng xe và cùng hóa đơn.
                        Giảm 30% trên các dịch vụ đã chọn,
                        không giảm phụ tùng và không cộng dồn đổi điểm.
                        Chi phí sau giảm sẽ được lưu khi gửi.
                    </p>
                @else
                    <p class="text-secondary mb-0">
                        Xe chưa có hạng mục đủ điều kiện bảo hành.
                    </p>
                @endif
            </section>

            <section class="ktv-panel">
                <h2 class="h5 fw-bold mb-3">
                    Gửi khách xác nhận
                </h2>

                <p class="text-secondary">
                    Nội dung bên trên được lấy từ bản nháp đã lưu.
                    Nếu cần đổi hạng mục, hãy quay lại chỉnh và lưu nháp trước.
                </p>

                <div
                    data-form-error
                    class="text-danger small mb-3"
                    role="alert"
                    tabindex="-1"
                >{{ $thongBaoPhieu }}</div>

                <div class="d-flex flex-wrap gap-2">
                    @if($thongBaoPhieu)
                        <a
                            href="{{ route('ktv.kiemtra.edit', $lichHen->MaLH) }}"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>
                            Quay lại chỉnh phiếu
                        </a>
                    @endif

                    @unless($biChanGui)
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2" aria-hidden="true"></i>
                            Gửi khách xác nhận
                        </button>
                    @endunless
                </div>
            </section>
        </form>
    </div>
@endsection
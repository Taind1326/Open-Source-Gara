@php
    $editingVehicle = isset($xe);
    $currentYear = now('Asia/Ho_Chi_Minh')->year;
    $vehicleCatalog = config('vehicle_catalog', []);
@endphp

<div data-vehicle-form>
    <script type="application/json" data-vehicle-catalog>
        @json($vehicleCatalog)
    </script>

    <div class="row g-4">
        <div class="col-lg-8">
            <section class="feature-card h-auto">
                <p class="section-eyebrow mb-2">
                    Thông tin chiếc xe
                </p>

                <h2 class="h4 fw-bold mb-4">
                    {{ $editingVehicle ? 'Cập nhật thông tin xe' : 'Thêm xe vào tài khoản' }}
                </h2>

                <div class="mb-4">
                    <label for="vehiclePlate" class="form-label fw-semibold">
                        Biển số xe
                    </label>

                    <input
                        type="text"
                        id="vehiclePlate"
                        name="BienSo"
                        class="form-control @error('BienSo') is-invalid @enderror"
                        value="{{ old('BienSo', $editingVehicle ? $xe->BienSo : '') }}"
                        placeholder="Ví dụ: 51A-12345"
                        maxlength="10"
                        pattern="[0-9]{2}[A-Za-z][A-Za-z0-9]?-[0-9]{4,5}"
                        data-pattern-message="Biển số không đúng định dạng. Ví dụ: 51A-12345 hoặc 51A1-1234."
                        spellcheck="false"
                        autocapitalize="characters"
                        aria-describedby="vehiclePlateHelp"
                        required
                    >

                    @error('BienSo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div id="vehiclePlateHelp" class="form-text">
                        Nhập biển số trên giấy đăng ký xe, không có khoảng trắng.
                    </div>
                </div>

                <div class="mb-4">
                    <label for="vehicleMake" class="form-label fw-semibold">
                        Hãng xe
                    </label>

                    <div class="garage-combobox" data-vehicle-combo="make">
                    <input
                        type="text"
                        id="vehicleMake"
                        name="HangXe"
                        data-vehicle-make
                        class="form-control @error('HangXe') is-invalid @enderror"
                        value="{{ old('HangXe', $editingVehicle ? $xe->HangXe : '') }}"
                        placeholder="Gõ tên hãng xe để tìm..."
                        maxlength="50"
                        autocomplete="off"
                        role="combobox"
                        aria-autocomplete="list"
                        aria-expanded="false"
                        aria-controls="vehicleMakeMenu"
                        aria-describedby="vehicleMakeHelp"
                        required
                    >
                        <button type="button" class="garage-combobox-toggle" data-combo-toggle
                                aria-label="Mở danh sách hãng xe" aria-expanded="false"
                                aria-controls="vehicleMakeMenu">
                            <i class="bi bi-chevron-down" aria-hidden="true"></i>
                        </button>
                        <div id="vehicleMakeMenu" data-combo-menu class="garage-combobox-menu"
                             role="listbox" aria-label="Hãng xe" hidden></div>
                    </div>

                    

                    @error('HangXe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div id="vehicleMakeHelp" class="form-text">
                        Chọn hãng trong gợi ý hoặc nhập hãng chưa có.
                    </div>
                </div>

                <div class="mb-4">
                    <label for="vehicleModel" class="form-label fw-semibold">
                        Dòng xe
                    </label>

                    <div class="garage-combobox" data-vehicle-combo="model">
                    <input
                        type="text"
                        id="vehicleModel"
                        name="DongXe"
                        data-vehicle-model
                        class="form-control @error('DongXe') is-invalid @enderror"
                        value="{{ old('DongXe', $editingVehicle ? $xe->DongXe : '') }}"
                        placeholder="Chọn hãng rồi gõ tên dòng xe..."
                        maxlength="70"
                        autocomplete="off"
                        role="combobox"
                        aria-autocomplete="list"
                        aria-expanded="false"
                        aria-controls="vehicleModelMenu"
                        aria-describedby="vehicleModelHelp"
                        required
                    >
                        <button type="button" class="garage-combobox-toggle" data-combo-toggle
                                aria-label="Mở danh sách dòng xe" aria-expanded="false"
                                aria-controls="vehicleModelMenu">
                            <i class="bi bi-chevron-down" aria-hidden="true"></i>
                        </button>
                        <div id="vehicleModelMenu" data-combo-menu class="garage-combobox-menu"
                             role="listbox" aria-label="Dòng xe" hidden></div>
                    </div>

                    

                    @error('DongXe')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div
                        id="vehicleModelHelp"
                        class="form-text"
                        data-vehicle-notice
                        aria-live="polite"
                    >
                        Chọn dòng xe trong gợi ý hoặc nhập dòng xe chưa có.
                    </div>
                </div>

                <div class="mb-4">
                    <label for="vehicleYear" class="form-label fw-semibold">
                        Năm sản xuất
                        <span class="text-muted fw-normal">
                            (không bắt buộc)
                        </span>
                    </label>

                    <input
                        type="number"
                        id="vehicleYear"
                        name="NamSX"
                        class="form-control @error('NamSX') is-invalid @enderror"
                        value="{{ old('NamSX', $editingVehicle ? $xe->NamSX : '') }}"
                        placeholder="Ví dụ: 2018"
                        min="1900"
                        max="{{ $currentYear }}"
                        step="1"
                        aria-describedby="vehicleYearHelp"
                    >

                    

                    @error('NamSX')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div id="vehicleYearHelp" class="form-text">
                        Nhập hoặc chọn năm sản xuất. Chưa rõ có thể để trống.
                    </div>
                </div>

                <div class="mb-4">
                    <label for="vehicleColor" class="form-label fw-semibold">
                        Màu sắc
                        <span class="text-muted fw-normal">
                            (không bắt buộc)
                        </span>
                    </label>

                    <input
                        type="text"
                        id="vehicleColor"
                        name="MauSac"
                        class="form-control @error('MauSac') is-invalid @enderror"
                        value="{{ old('MauSac', $editingVehicle ? $xe->MauSac : '') }}"
                        placeholder="Ví dụ: Trắng, đen, bạc..."
                        maxlength="40"
                    >

                    @error('MauSac')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex flex-wrap gap-2 border-top pt-4">
                    <button type="submit" class="btn btn-primary">
                        <i
                            class="bi bi-check2-circle me-2"
                            aria-hidden="true"
                        ></i>
                        {{ $editingVehicle ? 'Lưu thay đổi' : 'Thêm xe' }}
                    </button>

                    <a
                        href="{{ route('public.xe.index') }}"
                        class="btn btn-light"
                    >
                        Quay lại
                    </a>
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <aside class="feature-card h-auto">
                <span class="feature-icon">
                    <i class="bi bi-car-front" aria-hidden="true"></i>
                </span>

                <h2 class="h5 fw-bold mb-3">
                    Thêm xe nhanh chóng
                </h2>

                <ol class="text-muted ps-3 mb-4">
                    <li class="mb-2">Nhập biển số xe.</li>
                    <li class="mb-2">Gõ tên hãng để tìm và chọn.</li>
                    <li class="mb-2">Gõ tên dòng xe tương ứng.</li>
                    <li>Bổ sung năm sản xuất và màu sắc nếu biết.</li>
                </ol>

                <div class="border-top pt-3">
                    <p class="text-muted small mb-0">
                        Không tìm thấy hãng hoặc dòng xe?
                        Bạn có thể nhập trực tiếp thông tin trên giấy đăng ký xe.
                    </p>
                </div>

                @if($editingVehicle)
                    <div class="border-top mt-3 pt-3">
                        <h3 class="h6 fw-bold">
                            Thông tin đang lưu
                        </h3>

                        <p class="text-muted text-break small mb-1">
                            {{ $xe->HangXe }} · {{ $xe->DongXe }}
                        </p>

                        <p class="text-muted small mb-0">
                            Năm sản xuất:
                            {{ $xe->NamSX ?? 'Chưa cập nhật' }}
                        </p>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/vehicle-form.js') }}" defer></script>
@endpush
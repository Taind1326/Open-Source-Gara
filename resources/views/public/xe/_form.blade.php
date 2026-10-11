@php
    $editingVehicle = isset($xe);

    $selectedYear = old(
        'NamSanXuat',
        $editingVehicle ? $xe->NamSanXuat : ''
    );

    $selectedMakeId = old('MakeId', '');
    $selectedModelId = old('ModelId', '');

    $currentYear = now('Asia/Ho_Chi_Minh')->year;
@endphp

<div data-vehicle-form
     data-makes-url="{{ route('public.xe.catalog.makes') }}"
     data-models-url="{{ route('public.xe.catalog.models') }}"
     data-selected-make-id="{{ $selectedMakeId }}"
     data-selected-model-id="{{ $selectedModelId }}"
     data-existing-make="{{ $editingVehicle ? $xe->HangXe : '' }}"
     data-existing-model="{{ $editingVehicle ? $xe->DongXe : '' }}">
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
                    <label for="vehiclePlate"
                           class="form-label fw-semibold">
                        Biển số xe
                    </label>

                    <input type="text"
                           id="vehiclePlate"
                           name="BienSo"
                           class="form-control @error('BienSo') is-invalid @enderror"
                           value="{{ old('BienSo', $editingVehicle ? $xe->BienSo : '') }}"
                           placeholder="Ví dụ: 51A-123.45"
                           maxlength="15"
                           pattern="[A-Za-z0-9.\-]+"
                           spellcheck="false"
                           autocapitalize="characters"
                           aria-describedby="vehiclePlateHelp"
                           required>

                    @error('BienSo')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div id="vehiclePlateHelp" class="form-text">
                        Nhập đúng biển số trên giấy đăng ký xe,
                        không có khoảng trắng.
                    </div>
                </div>

                <div class="mb-4">
                    <label for="vehicleYear"
                           class="form-label fw-semibold">
                        Năm sản xuất
                    </label>

                    <select id="vehicleYear"
                            name="NamSanXuat"
                            data-vehicle-year
                            class="form-select @error('NamSanXuat') is-invalid @enderror"
                            aria-describedby="vehicleYearHelp"
                            required>
                        <option value="">Chọn năm sản xuất</option>

                        @for($year = $currentYear; $year >= 1990; $year--)
                            <option value="{{ $year }}"
                                    @selected((string) $selectedYear === (string) $year)>
                                {{ $year }}
                            </option>
                        @endfor
                    </select>

                    @error('NamSanXuat')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div id="vehicleYearHelp" class="form-text">
                        Chọn từ năm 1990 đến {{ $currentYear }}.
                        API lọc dòng xe theo năm mẫu xe, có thể khác
                        năm sản xuất thực tế.
                    </div>
                </div>

                <div class="mb-4">
                    <label for="vehicleMake"
                           class="form-label fw-semibold">
                        Hãng xe
                    </label>

                    <select id="vehicleMake"
                            name="MakeId"
                            data-vehicle-make
                            class="form-select @error('MakeId') is-invalid @enderror"
                            required
                            disabled>
                        <option value="">Đang tải hãng xe…</option>
                    </select>

                    @error('MakeId')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="vehicleModel"
                           class="form-label fw-semibold">
                        Dòng xe
                    </label>

                    <select id="vehicleModel"
                            name="ModelId"
                            data-vehicle-model
                            class="form-select @error('ModelId') is-invalid @enderror"
                            aria-describedby="vehicleCatalogNotice"
                            required
                            disabled>
                        <option value="">
                            Chọn năm và hãng xe trước
                        </option>
                    </select>

                    @error('ModelId')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div id="vehicleCatalogNotice"
                         data-vehicle-notice
                         class="form-text"
                         aria-live="polite">
                        Danh sách dòng xe được tải theo hãng và năm đã chọn.
                    </div>
                </div>

                <div data-vehicle-error
                     class="alert alert-warning d-none"
                     role="alert"></div>

                <noscript>
                    <div class="alert alert-warning">
                        Vui lòng bật JavaScript để tải danh sách hãng
                        và dòng xe.
                    </div>
                </noscript>

                <div class="d-flex flex-wrap gap-2 border-top pt-4">
                    <button type="submit"
                            data-vehicle-submit
                            class="btn btn-primary"
                            disabled>
                        <i class="bi bi-check2-circle me-2"
                           aria-hidden="true"></i>
                        {{ $editingVehicle ? 'Lưu thay đổi' : 'Thêm xe' }}
                    </button>

                    <a href="{{ route('public.xe.index') }}"
                       class="btn btn-light">
                        Quay lại
                    </a>

                    <button type="button"
                            data-vehicle-retry
                            class="btn btn-outline-primary">
                        <i class="bi bi-arrow-repeat me-1"
                           aria-hidden="true"></i>
                        Tải lại danh mục
                    </button>
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <aside class="feature-card h-auto">
                <span class="feature-icon">
                    <i class="bi bi-car-front" aria-hidden="true"></i>
                </span>

                <h2 class="h5 fw-bold mb-3">
                    Chọn đúng thông tin xe
                </h2>

                <ol class="text-muted ps-3 mb-4">
                    <li class="mb-2">Nhập biển số xe.</li>
                    <li class="mb-2">Chọn năm sản xuất.</li>
                    <li class="mb-2">Chọn hãng xe từ danh mục.</li>
                    <li>Chọn dòng xe tương ứng.</li>
                </ol>

                <div class="border-top pt-3">
                    <p class="text-muted small mb-0">
                        Danh mục lấy từ NHTSA và có thể chưa có một số
                        dòng xe tại Việt Nam. Với năm 1990–1995,
                        danh sách dòng xe chỉ lọc theo hãng.
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
                            Năm sản xuất: {{ $xe->NamSanXuat }}
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
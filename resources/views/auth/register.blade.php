@extends('public.layout')

@section('title', 'Đăng ký')
@section('description', 'Tạo tài khoản AutoCare Garage.')

@push('styles')
    <style>
        .garage-register {
            padding: 48px 0;
            background: #fff8f2;
            color: #10243a;
        }

        .garage-register .register-card {
            max-width: 720px;
            margin-inline: auto;
            padding: 40px;
            border: 1px solid #e5ebf2;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 12px 40px rgb(16 36 58 / 5%);
        }

        .garage-register .register-eyebrow {
            margin-bottom: 12px;
            color: #bd4806;
            font-size: .85rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .garage-register h1 {
            font-size: clamp(1.8rem, 4vw, 2.3rem);
            font-weight: 750;
            letter-spacing: -.035em;
        }

        .garage-register .register-description {
            color: #62748b;
            line-height: 1.65;
        }

        .garage-register .register-steps {
            display: flex;
            margin: 32px 0;
            padding: 0;
            list-style: none;
        }

        .garage-register .register-step {
            position: relative;
            flex: 1;
            color: #62748b;
            font-size: .9rem;
            font-weight: 600;
            text-align: center;
        }

        .garage-register .register-step:not(:last-child)::after {
            position: absolute;
            top: 19px;
            left: calc(50% + 26px);
            right: calc(-50% + 26px);
            height: 2px;
            background: #e5ebf2;
            content: "";
        }

        .garage-register .register-step.is-complete::after {
            background: #f56616;
        }

        .garage-register .register-step-number {
            display: grid;
            place-items: center;
            width: 40px;
            height: 40px;
            margin: 0 auto 10px;
            border: 1px solid #dbe3ed;
            border-radius: 50%;
            background: #fff;
        }

        .garage-register .register-step.is-active {
            color: #bd4806;
        }

        .garage-register .register-step.is-active .register-step-number {
            border-color: #f56616;
            background: #f56616;
            color: #fff;
            box-shadow: 0 0 0 5px #fff0e6;
        }

        .garage-register .register-step.is-complete .register-step-number {
            border-color: #f56616;
            background: #fff0e6;
            color: #bd4806;
        }

        .garage-register .form-control {
            min-height: 48px;
            border-radius: 10px;
        }

        .garage-register .form-control:focus {
            border-color: #f56616;
            box-shadow: 0 0 0 .2rem rgb(245 102 22 / 12%);
        }

        .garage-register .register-password {
            position: relative;
        }

        .garage-register .register-password input {
            padding-right: 50px;
        }

        .garage-register .register-password-toggle {
            position: absolute;
            top: 0;
            right: 0;
            display: grid;
            place-items: center;
            width: 46px;
            height: 48px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: #62748b;
        }

        .garage-register .register-password input.is-invalid {
            background-image: none;
        }

        .garage-register .register-rules,
        .garage-register .register-summary {
            padding: 20px;
            border: 1px solid #e5ebf2;
            border-radius: 12px;
            background: #f8fafc;
        }

        .garage-register .register-rules ul {
            display: grid;
            gap: 10px;
            margin: 12px 0 0;
            padding: 0;
            list-style: none;
        }

        .garage-register .register-rules li {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #62748b;
            font-size: .9rem;
        }

        .garage-register .register-rules li.is-valid {
            color: #198754;
        }

        .garage-register .register-summary dl {
            display: grid;
            grid-template-columns: 110px minmax(0, 1fr);
            gap: 16px;
            margin: 0;
        }

        .garage-register .register-summary dt {
            color: #62748b;
            font-weight: 400;
        }

        .garage-register .register-summary dd {
            margin: 0;
            overflow-wrap: anywhere;
            font-weight: 600;
            text-align: right;
        }

        .garage-register .register-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .garage-register .register-actions .btn {
            min-height: 48px;
            border-radius: 10px;
        }

        .garage-register .register-actions .register-primary {
            flex: 1;
        }

        .garage-register [hidden] {
            display: none !important;
        }

        .garage-register input {
            scroll-margin-top: 120px;
        }

        @media (max-width: 575.98px) {
            .garage-register {
                padding: 28px 0;
            }

            .garage-register .register-card {
                padding: 24px 18px;
            }

            .garage-register .register-step {
                font-size: .8rem;
            }

            .garage-register .register-summary dl {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .garage-register .register-summary dd {
                margin-bottom: 8px;
                text-align: left;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $initialStep = 1;

        if (
            !$errors->has('HoTen')
            && !$errors->has('Email')
            && !$errors->has('SoDienThoai')
            && ($errors->has('MatKhau') || $errors->has('MatKhau_confirmation'))
        ) {
            $initialStep = 2;
        }
    @endphp

    <section class="garage-register">
        <div class="container">
            <div class="register-card">
                <p class="register-eyebrow">AutoCare Garage</p>

                <h1 class="mb-2">Tạo tài khoản</h1>

                <p class="register-description mb-0">
                    Đặt lịch thuận tiện, theo dõi sửa chữa và tích lũy điểm.
                </p>

                <ol class="register-steps" aria-label="Các bước đăng ký">
                    @foreach(['Thông tin', 'Mật khẩu', 'Xác nhận'] as $label)
                        <li
                            class="register-step"
                            data-step-indicator="{{ $loop->iteration }}"
                        >
                            <span class="register-step-number">
                                {{ $loop->iteration }}
                            </span>

                            <span>{{ $label }}</span>
                        </li>
                    @endforeach
                </ol>

                <noscript>
                    <p class="text-danger">
                        Vui lòng bật JavaScript để sử dụng đăng ký theo từng bước.
                    </p>
                </noscript>

                <form
                    id="garageRegisterForm"
                    method="POST"
                    action="{{ route('register.store') }}"
                    data-initial-step="{{ $initialStep }}"
                    novalidate
                >
                    @csrf

                    <div data-register-panel="1">
                        <h2 class="h5 fw-bold mb-4">Thông tin của bạn</h2>

                        <div class="mb-3">
                            <label for="registerName" class="form-label fw-semibold">
                                Họ và tên
                            </label>

                            <input
                                type="text"
                                id="registerName"
                                name="HoTen"
                                value="{{ old('HoTen') }}"
                                class="form-control @error('HoTen') is-invalid @enderror"
                                placeholder="Nhập họ và tên"
                                maxlength="100"
                                autocomplete="name"
                                required
                            >

                            <div class="invalid-feedback">
                                @error('HoTen') {{ $message }} @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="registerEmail" class="form-label fw-semibold">
                                Email
                            </label>

                            <input
                                type="email"
                                id="registerEmail"
                                name="Email"
                                value="{{ old('Email') }}"
                                class="form-control @error('Email') is-invalid @enderror"
                                placeholder="Nhập email của bạn"
                                maxlength="100"
                                autocomplete="email"
                                required
                            >

                            <div class="invalid-feedback">
                                @error('Email') {{ $message }} @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="registerPhone" class="form-label fw-semibold">
                                Số điện thoại
                            </label>

                            <input
                                type="tel"
                                id="registerPhone"
                                name="SoDienThoai"
                                value="{{ old('SoDienThoai') }}"
                                class="form-control @error('SoDienThoai') is-invalid @enderror"
                                placeholder="Nhập số điện thoại"
                                inputmode="numeric"
                                maxlength="10"
                                pattern="0[0-9]{9}"
                                data-pattern-message="Số điện thoại gồm 10 chữ số và bắt đầu bằng 0."
                                autocomplete="tel"
                                required
                            >

                            <div class="invalid-feedback">
                                @error('SoDienThoai') {{ $message }} @enderror
                            </div>

                            <div class="form-text">
                                Gồm 10 chữ số, bắt đầu bằng số 0.
                            </div>
                        </div>
                    </div>

                    <div data-register-panel="2">
                        <h2 class="h5 fw-bold mb-4">Thiết lập mật khẩu</h2>

                        <div class="mb-3">
                            <label for="registerPassword" class="form-label fw-semibold">
                                Mật khẩu
                            </label>

                            <div class="register-password input-group">
                                <input
                                    type="password"
                                    id="registerPassword"
                                    name="MatKhau"
                                    class="form-control @error('MatKhau') is-invalid @enderror"
                                    placeholder="Tạo mật khẩu"
                                    minlength="8"
                                    maxlength="255"
                                    autocomplete="new-password"
                                    aria-describedby="registerPasswordRules"
                                    required
                                >

                                <button
                                    type="button"
                                    class="register-password-toggle"
                                    data-toggle-password="registerPassword"
                                    aria-label="Hiện mật khẩu"
                                    aria-pressed="false"
                                >
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="invalid-feedback">
                                @error('MatKhau') {{ $message }} @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label
                                for="registerPasswordConfirmation"
                                class="form-label fw-semibold"
                            >
                                Xác nhận mật khẩu
                            </label>

                            <div class="register-password input-group">
                                <input
                                    type="password"
                                    id="registerPasswordConfirmation"
                                    name="MatKhau_confirmation"
                                    class="form-control @error('MatKhau_confirmation') is-invalid @enderror"
                                    placeholder="Nhập lại mật khẩu"
                                    maxlength="255"
                                    autocomplete="new-password"
                                    required
                                >

                                <button
                                    type="button"
                                    class="register-password-toggle"
                                    data-toggle-password="registerPasswordConfirmation"
                                    aria-label="Hiện mật khẩu xác nhận"
                                    aria-pressed="false"
                                >
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="invalid-feedback">
                                @error('MatKhau_confirmation') {{ $message }} @enderror
                            </div>
                        </div>

                        <div class="register-rules" id="registerPasswordRules">
                            <p class="fw-semibold small mb-0">
                                Mật khẩu cần đáp ứng:
                            </p>

                            <ul>
                                <li data-password-rule="length">
                                    <i class="bi bi-circle" aria-hidden="true"></i>
                                    Từ 8 đến 255 ký tự
                                </li>

                                <li data-password-rule="case">
                                    <i class="bi bi-circle" aria-hidden="true"></i>
                                    Có chữ hoa và chữ thường
                                </li>

                                <li data-password-rule="number">
                                    <i class="bi bi-circle" aria-hidden="true"></i>
                                    Có ít nhất một chữ số
                                </li>

                                <li data-password-rule="symbol">
                                    <i class="bi bi-circle" aria-hidden="true"></i>
                                    Có ký tự đặc biệt, ví dụ @, #, !
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div data-register-panel="3">
                        <h2 class="h5 fw-bold mb-3">Kiểm tra thông tin</h2>

                        <p class="register-description">
                            Kiểm tra lại thông tin trước khi tạo tài khoản.
                        </p>

                        <div class="register-summary">
                            <dl>
                                <dt>Họ và tên</dt>
                                <dd data-summary="HoTen"></dd>

                                <dt>Email</dt>
                                <dd data-summary="Email"></dd>

                                <dt>Điện thoại</dt>
                                <dd data-summary="SoDienThoai"></dd>
                            </dl>
                        </div>

                        <p class="small text-muted mt-3 mb-0">
                            Bạn có thể quay lại để chỉnh sửa thông tin.
                        </p>
                    </div>

                    <div class="register-actions">
                        <button
                            type="button"
                            id="registerBack"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>
                            Quay lại
                        </button>

                        <button
                            type="button"
                            id="registerNext"
                            class="btn btn-primary register-primary"
                        >
                            Tiếp theo
                            <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                        </button>

                        <button
                            type="submit"
                            id="registerSubmit"
                            class="btn btn-primary register-primary"
                            hidden
                        >
                            <i class="bi bi-person-plus me-2" aria-hidden="true"></i>
                            Tạo tài khoản
                        </button>
                    </div>
                </form>

                <div class="border-top mt-4 pt-4 text-center">
                    <span class="text-muted">Đã có tài khoản?</span>

                    <a href="{{ route('login') }}" class="fw-semibold ms-1">
                        Đăng nhập
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (() => {
            'use strict';

            const form = document.getElementById('garageRegisterForm');
            const panels = [...form.querySelectorAll('[data-register-panel]')];
            const indicators = [...document.querySelectorAll('[data-step-indicator]')];
            const back = document.getElementById('registerBack');
            const next = document.getElementById('registerNext');
            const submit = document.getElementById('registerSubmit');
            const password = document.getElementById('registerPassword');

            let step = Number(form.dataset.initialStep) || 1;

            function passwordRules() {
                const value = password.value;

                return {
                    length: [...value].length >= 8 && [...value].length <= 255,
                    case: /\p{Ll}/u.test(value) && /\p{Lu}/u.test(value),
                    number: /\p{N}/u.test(value),
                    symbol: /[\p{Z}\p{S}\p{P}]/u.test(value),
                };
            }

            function updateRules() {
                const rules = passwordRules();

                form.querySelectorAll('[data-password-rule]').forEach(item => {
                    const valid = rules[item.dataset.passwordRule];

                    item.classList.toggle('is-valid', valid);
                    item.querySelector('i').className = valid
                        ? 'bi bi-check-circle-fill'
                        : 'bi bi-circle';
                });
            }

            function showStep(value, focus = true) {
                step = value;

                panels.forEach(panel => {
                    panel.hidden = Number(panel.dataset.registerPanel) !== step;
                });

                indicators.forEach(item => {
                    const number = Number(item.dataset.stepIndicator);
                    const complete = number < step;

                    item.classList.toggle('is-active', number === step);
                    item.classList.toggle('is-complete', complete);

                    if (number === step) {
                        item.setAttribute('aria-current', 'step');
                    } else {
                        item.removeAttribute('aria-current');
                    }

                    const circle = item.querySelector('.register-step-number');
                    circle.textContent = complete ? '✓' : String(number);
                });

                back.hidden = step === 1;
                next.hidden = step === 3;
                submit.hidden = step !== 3;

                if (step === 3) {
                    form.querySelectorAll('[data-summary]').forEach(item => {
                        item.textContent = form.elements[item.dataset.summary].value.trim();
                    });
                }

                if (focus) {
                    const panel = panels.find(
                        item => Number(item.dataset.registerPanel) === step
                    );
                    const target = panel.querySelector('input') || submit;

                    target.focus({ preventScroll: true });
                    target.scrollIntoView({
                        behavior: matchMedia('(prefers-reduced-motion: reduce)').matches
                            ? 'auto'
                            : 'smooth',
                        block: 'center',
                    });
                }
            }

            function validateStep() {
                if (!window.GarageForms) {
                    return false;
                }

                // Chỉ kiểm tra các ô trong bước hiện tại.
                // Khôi phục ngay để gửi đầy đủ dữ liệu khi tạo tài khoản.
                const temporarilyDisabled = [];

                panels.forEach(panel => {
                    if (Number(panel.dataset.registerPanel) === step) {
                        return;
                    }

                    panel.querySelectorAll('input').forEach(input => {
                        if (!input.disabled) {
                            input.disabled = true;
                            temporarilyDisabled.push(input);
                        }
                    });
                });

                let first;

                try {
                    first = GarageForms.validate(form);

                    if (
                        step === 2
                        && password.value
                        && !Object.values(passwordRules()).every(Boolean)
                    ) {
                        const target = GarageForms.setError(
                            form,
                            'MatKhau',
                            'Mật khẩu phải từ 8 đến 255 ký tự, có chữ hoa, chữ thường, số và ký tự đặc biệt.'
                        );

                        first ||= target;
                    }
                } finally {
                    temporarilyDisabled.forEach(input => {
                        input.disabled = false;
                    });
                }

                if (first) {
                    GarageForms.focusError(first);
                    return false;
                }

                return true;
            }

            next.addEventListener('click', () => {
                if (validateStep()) {
                    showStep(step + 1);
                }
            });

            back.addEventListener('click', () => {
                showStep(Math.max(1, step - 1));
            });

            password.addEventListener('input', updateRules);

            form.querySelectorAll('[data-toggle-password]').forEach(button => {
                button.addEventListener('click', () => {
                    const input = document.getElementById(button.dataset.togglePassword);
                    const visible = input.type === 'password';

                    input.type = visible ? 'text' : 'password';
                    button.setAttribute('aria-pressed', String(visible));
                    button.setAttribute('aria-label', visible
                        ? 'Ẩn mật khẩu'
                        : 'Hiện mật khẩu');
                    button.querySelector('i').className = visible
                        ? 'bi bi-eye-slash'
                        : 'bi bi-eye';
                });
            });

            form.addEventListener('keydown', event => {
                if (
                    event.key === 'Enter'
                    && event.target.matches('input')
                    && step < 3
                ) {
                    event.preventDefault();
                    next.click();
                }
            });

            // Chạy trước kiểm tra submit chung để mở đúng bước nếu
            // trình duyệt tự điền lại hoặc thay đổi dữ liệu ở bước ẩn.
            window.addEventListener('submit', event => {
                if (event.target !== form) {
                    return;
                }

                if (step !== 3) {
                    event.preventDefault();
                    event.stopImmediatePropagation();

                    if (validateStep()) {
                        showStep(step + 1);
                    }

                    return;
                }

                if (!window.GarageForms) {
                    event.preventDefault();
                    return;
                }

                let first = GarageForms.validate(form);

                if (!Object.values(passwordRules()).every(Boolean)) {
                    const target = GarageForms.setError(
                        form,
                        'MatKhau',
                        'Mật khẩu phải từ 8 đến 255 ký tự, có chữ hoa, chữ thường, số và ký tự đặc biệt.'
                    );

                    first ||= target;
                }

                if (first) {
                    event.preventDefault();
                    event.stopImmediatePropagation();

                    const panel = first.closest('[data-register-panel]');

                    if (panel) {
                        showStep(Number(panel.dataset.registerPanel), false);
                    }

                    GarageForms.focusError(first);
                }
            }, true);

            showStep(step, false);
            updateRules();
        })();
    </script>
@endpush

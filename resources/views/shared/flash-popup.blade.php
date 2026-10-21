@once
    @if(session('success') || session('error'))
        @php
            $isError = (bool) session('error');
        @endphp

        <div
            class="modal fade"
            id="garageFlashModal"
            tabindex="-1"
            aria-labelledby="garageFlashTitle"
            aria-describedby="garageFlashMessage"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 pb-0">
                        <button
                            type="button"
                            class="btn-close ms-auto"
                            data-bs-dismiss="modal"
                            aria-label="Đóng thông báo"
                        ></button>
                    </div>

                    <div class="modal-body text-center px-4 pt-2 pb-4">
                        <div
                            class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 {{ $isError ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}"
                            style="width: 76px; height: 76px;"
                        >
                            <i
                                class="bi {{ $isError ? 'bi-exclamation-circle' : 'bi-check-circle' }}"
                                style="font-size: 2.5rem;"
                                aria-hidden="true"
                            ></i>
                        </div>

                        <h2
                            class="h4 fw-bold mb-3"
                            id="garageFlashTitle"
                        >
                            {{ $isError ? 'Chưa thể thực hiện' : 'Thao tác thành công' }}
                        </h2>

                        <p
                            class="text-body-secondary text-break mb-4"
                            id="garageFlashMessage"
                        >
                            {{ session('error') ?: session('success') }}
                        </p>

                        <button
                            type="button"
                            class="btn btn-primary px-5"
                            data-bs-dismiss="modal"
                            data-flash-close
                        >
                            Đã hiểu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const element = document.getElementById('garageFlashModal');

                if (!element || !window.bootstrap) {
                    return;
                }

                const popup = bootstrap.Modal.getOrCreateInstance(element);
                const openPopup = () => popup.show();

                element.addEventListener('shown.bs.modal', function () {
                    element.querySelector('[data-flash-close]')?.focus();
                });

                // Đợi popup khác đóng, tránh hai popup đè nhau.
                const otherModal = document.querySelector(
                    '.modal.show:not(#garageFlashModal)'
                );

                if (otherModal) {
                    otherModal.addEventListener(
                        'hidden.bs.modal',
                        openPopup,
                        { once: true }
                    );
                } else {
                    openPopup();
                }
            });
        </script>
    @endif
@endonce
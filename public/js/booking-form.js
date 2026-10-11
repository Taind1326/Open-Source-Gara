(() => {
    "use strict";

    function initializeBookingForm(form) {
        const dateInput = form.querySelector("[data-booking-date]");
        const slotSelect = form.querySelector("[data-booking-slot]");
        const serviceInputs = form.querySelectorAll("[data-booking-service]");
        const vehicleSelect = form.querySelector('[name="MaXe"]');
        const errorBox = form.querySelector("[data-booking-error]");
        const notice = form.querySelector("[data-booking-notice]");
        const retryButton = form.querySelector("[data-booking-retry]");
        const submitButton = form.querySelector("[data-booking-submit]");

        let requestController = null;
        let requestVersion = 0;
        let loading = false;
        let submitting = false;
        let initialSlot = form.dataset.selectedSlot || "";

        function selectedServiceCount() {
            return Array.from(serviceInputs).filter(
                (input) => input.checked
            ).length;
        }

        function validDate() {
            return Boolean(dateInput.value) && dateInput.validity.valid;
        }

        function updateSubmitState() {
            const count = selectedServiceCount();

            submitButton.disabled =
                submitting ||
                loading ||
                !validDate() ||
                !vehicleSelect.value ||
                slotSelect.disabled ||
                !slotSelect.value ||
                count < 1 ||
                count > 20;
        }

        function showError(message) {
            errorBox.textContent = message;
            errorBox.classList.remove("d-none");
        }

        function clearError() {
            errorBox.textContent = "";
            errorBox.classList.add("d-none");
        }

        function resetSlots(message) {
            slotSelect.replaceChildren(new Option(message, ""));
            slotSelect.disabled = true;
        }

        async function loadSlots(preferredSlot = "") {
            const version = ++requestVersion;

            requestController?.abort();
            requestController = null;

            loading = false;
            clearError();
            notice.textContent = "";

            resetSlots("Chọn ngày hẹn trước");
            updateSubmitState();

            if (!validDate()) {
                notice.textContent =
                    "Chọn ngày trong khoảng được phép để xem khung giờ.";
                return;
            }

            loading = true;
            retryButton.disabled = true;

            resetSlots("Đang tải khung giờ…");
            updateSubmitState();

            const controller = new AbortController();
            requestController = controller;

            const url = new URL(
                form.dataset.slotsUrl,
                window.location.origin
            );

            url.searchParams.set("NgayHen", dateInput.value);

            try {
                const response = await fetch(url, {
                    method: "GET",
                    credentials: "same-origin",
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    signal: controller.signal,
                });

                const body = await response.json().catch(() => null);

                if (version !== requestVersion) {
                    return;
                }

                if (!response.ok) {
                    if (response.status === 401 || response.status === 403) {
                        throw new Error(
                            "Phiên đăng nhập không còn hợp lệ. " +
                            "Vui lòng tải lại trang và đăng nhập."
                        );
                    }

                    if (response.status === 429) {
                        throw new Error(
                            "Bạn tải khung giờ quá nhanh. " +
                            "Vui lòng chờ một lát rồi thử lại."
                        );
                    }

                    if (response.status === 422) {
                        const messages = Object.values(
                            body?.errors || {}
                        ).flat();

                        throw new Error(
                            messages[0] ||
                            "Ngày hẹn không hợp lệ. Vui lòng chọn lại."
                        );
                    }

                    throw new Error(
                        body?.message ||
                        "Không tải được khung giờ. Vui lòng thử lại."
                    );
                }

                if (!Array.isArray(body?.data)) {
                    throw new Error("Dữ liệu khung giờ không hợp lệ.");
                }

                slotSelect.replaceChildren(
                    new Option("Chọn khung giờ tiếp nhận", "")
                );

                let availableCount = 0;

                body.data.forEach((slot) => {
                    const option = new Option(slot.label, slot.value);

                    option.disabled = !slot.available;

                    if (slot.available) {
                        availableCount += 1;
                    }

                    slotSelect.add(option);
                });

                if (availableCount === 0) {
                    slotSelect.disabled = true;

                    notice.textContent =
                        "Ngày này không còn khung giờ có thể đặt. " +
                        "Vui lòng chọn ngày khác.";

                    return;
                }

                slotSelect.disabled = false;

                if (preferredSlot) {
                    const match = Array.from(slotSelect.options).find(
                        (option) =>
                            option.value === preferredSlot &&
                            !option.disabled
                    );

                    if (match) {
                        slotSelect.value = preferredSlot;
                    } else {
                        showError(
                            "Khung giờ đã chọn không còn khả dụng. " +
                            "Vui lòng chọn khung giờ khác."
                        );
                    }
                }

                notice.textContent =
                    "Số chỗ được cập nhật tại thời điểm tải. " +
                    "Hệ thống sẽ kiểm tra lại khi bạn gửi lịch.";
            } catch (error) {
                if (
                    version !== requestVersion ||
                    error.name === "AbortError"
                ) {
                    return;
                }

                resetSlots("Chưa tải được khung giờ");

                showError(
                    error.message ||
                    "Không tải được khung giờ. Vui lòng thử lại."
                );
            } finally {
                if (version === requestVersion) {
                    loading = false;
                    retryButton.disabled = false;
                    updateSubmitState();
                }
            }
        }

        dateInput.addEventListener("change", () => {
            initialSlot = "";
            void loadSlots();
        });

        slotSelect.addEventListener("change", updateSubmitState);
        vehicleSelect.addEventListener("change", updateSubmitState);

        serviceInputs.forEach((input) => {
            input.addEventListener("change", () => {
                const count = selectedServiceCount();

                if (count > 20) {
                    input.checked = false;

                    showError(
                        "Mỗi lịch hẹn được chọn tối đa 20 dịch vụ."
                    );
                }

                updateSubmitState();
            });
        });

        retryButton.addEventListener("click", () => {
            if (loading || submitting) {
                return;
            }

            const preferredSlot = slotSelect.value || initialSlot;
            void loadSlots(preferredSlot);
        });

        form.addEventListener("submit", (event) => {
            if (submitting) {
                event.preventDefault();
                return;
            }

            const count = selectedServiceCount();

            if (
                loading ||
                !validDate() ||
                !vehicleSelect.value ||
                slotSelect.disabled ||
                !slotSelect.value ||
                count < 1 ||
                count > 20
            ) {
                event.preventDefault();

                showError(
                    "Vui lòng chọn xe, ngày, khung giờ còn chỗ " +
                    "và từ 1 đến 20 dịch vụ."
                );

                updateSubmitState();
                return;
            }

            submitting = true;
            submitButton.disabled = true;
            submitButton.textContent = "Đang gửi lịch…";
            retryButton.disabled = true;
        });

        // Khôi phục nút khi quay lại trang bằng nút Back của trình duyệt.
        window.addEventListener("pageshow", (event) => {
            if (!event.persisted) {
                return;
            }

            submitting = false;
            submitButton.textContent = "Gửi lịch hẹn";
            retryButton.disabled = false;

            void loadSlots(slotSelect.value);
        });

        void loadSlots(initialSlot);
    }

    function initialize() {
        document.querySelectorAll("[data-booking-form]").forEach(
            initializeBookingForm
        );
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initialize, {
            once: true,
        });
    } else {
        initialize();
    }
})();
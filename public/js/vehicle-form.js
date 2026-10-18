(() => {
    "use strict";

    function normalize(value) {
        return String(value)
            .trim()
            .normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/đ/g, "d")
            .replace(/Đ/g, "D")
            .toLocaleLowerCase("vi");
    }

    function initializeVehicleForm(root) {
        const catalogElement = root.querySelector("[data-vehicle-catalog]");
        const makeInput = root.querySelector("[data-vehicle-make]");
        const modelInput = root.querySelector("[data-vehicle-model]");
        const modelOptions = root.querySelector("[data-vehicle-model-options]");
        const notice = root.querySelector("[data-vehicle-notice]");

        if (
            !catalogElement ||
            !makeInput ||
            !modelInput ||
            !modelOptions ||
            !notice
        ) {
            return;
        }

        let catalog;

        try {
            catalog = JSON.parse(catalogElement.textContent);
        } catch {
            notice.textContent =
                "Chưa tải được gợi ý. Bạn vẫn có thể nhập thông tin xe.";
            return;
        }

        const entries = Object.entries(catalog);
        let previousMake = normalize(makeInput.value);

        function updateModels() {
            const currentMake = normalize(makeInput.value);

            const selected = entries.find(
                ([make]) => normalize(make) === currentMake
            );

            modelOptions.replaceChildren();

            if (!currentMake) {
                notice.textContent =
                    "Chọn hãng trước để xem gợi ý dòng xe.";
                return;
            }

            if (!selected) {
                notice.textContent =
                    "Hãng này chưa có trong danh mục. Bạn có thể nhập dòng xe trực tiếp.";
                return;
            }

            const [make, models] = selected;

            models.forEach((model) => {
                const option = document.createElement("option");
                option.value = model;
                modelOptions.append(option);
            });

            notice.textContent =
                `Gợi ý dòng xe ${make}. Không có trong danh sách thì nhập trực tiếp.`;
        }

        makeInput.addEventListener("input", () => {
            const currentMake = normalize(makeInput.value);

            if (currentMake !== previousMake) {
                modelInput.value = "";
                previousMake = currentMake;
            }

            updateModels();
        });

        // Giữ nguyên dòng xe đã lưu khi mở trang sửa.
        updateModels();
    }

    function initialize() {
        document
            .querySelectorAll("[data-vehicle-form]")
            .forEach(initializeVehicleForm);
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initialize, {
            once: true,
        });
    } else {
        initialize();
    }
})();
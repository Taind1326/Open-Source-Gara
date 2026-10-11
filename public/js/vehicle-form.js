(() => {
    "use strict";

    function initializeVehicleForm(root) {
        const yearSelect = root.querySelector("[data-vehicle-year]");
        const makeSelect = root.querySelector("[data-vehicle-make]");
        const modelSelect = root.querySelector("[data-vehicle-model]");
        const notice = root.querySelector("[data-vehicle-notice]");
        const errorBox = root.querySelector("[data-vehicle-error]");
        const submitButton = root.querySelector("[data-vehicle-submit]");
        const retryButton = root.querySelector("[data-vehicle-retry]");

        const form = root.closest("form");

        let makesLoaded = false;
        let loadingMakes = false;
        let loadingModels = false;

        let makeRequest = null;
        let modelRequest = null;

        let makeVersion = 0;
        let modelVersion = 0;

        let initialModelPending = true;

        const initialMakeId = root.dataset.selectedMakeId || "";
        const initialModelId = root.dataset.selectedModelId || "";
        const existingMake = root.dataset.existingMake || "";
        const existingModel = root.dataset.existingModel || "";

        function normalize(value) {
            return String(value).trim().toLocaleUpperCase("en-US");
        }

        function showError(message) {
            errorBox.textContent = message;
            errorBox.classList.remove("d-none");
        }

        function clearError() {
            errorBox.textContent = "";
            errorBox.classList.add("d-none");
        }

        function resetSelect(select, message) {
            select.replaceChildren(new Option(message, ""));
            select.disabled = true;
        }

        function updateSubmitState() {
            submitButton.disabled =
                loadingMakes ||
                loadingModels ||
                !makesLoaded ||
                !yearSelect.value ||
                !makeSelect.value ||
                !modelSelect.value;
        }

        async function fetchJson(url, signal) {
            const response = await fetch(url, {
                method: "GET",
                credentials: "same-origin",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                signal,
            });

            const body = await response.json().catch(() => null);

            if (!response.ok) {
                if (response.status === 401 || response.status === 403) {
                    throw new Error(
                        "Phiên đăng nhập không còn hợp lệ. Vui lòng tải lại trang và đăng nhập."
                    );
                }

                if (response.status === 429) {
                    throw new Error(
                        "Bạn tải danh mục quá nhanh. Vui lòng chờ một lát rồi thử lại."
                    );
                }

                throw new Error(
                    body?.message || "Không tải được danh mục xe. Vui lòng thử lại."
                );
            }

            if (!body || typeof body !== "object") {
                throw new Error("Dữ liệu danh mục không hợp lệ.");
            }

            return body;
        }

        function selectMatchingOption(select, items, preferredId, preferredName) {
            let selected = null;

            if (preferredId) {
                selected = items.find(
                    (item) => String(item.id) === String(preferredId)
                );
            } else if (preferredName) {
                selected = items.find(
                    (item) => normalize(item.name) === normalize(preferredName)
                );
            }

            if (selected) {
                select.value = String(selected.id);
            }

            return Boolean(selected);
        }

        async function loadModels(preferredId = "", preferredName = "") {
            const version = ++modelVersion;

            modelRequest?.abort();
            modelRequest = null;

            loadingModels = false;

            resetSelect(modelSelect, "Chọn năm và hãng xe trước");
            notice.textContent =
                "Danh sách dòng xe được tải theo hãng và năm đã chọn.";

            clearError();
            updateSubmitState();

            if (!makesLoaded || !yearSelect.value || !makeSelect.value) {
                return;
            }

            loadingModels = true;
            resetSelect(modelSelect, "Đang tải dòng xe…");
            updateSubmitState();

            const controller = new AbortController();
            modelRequest = controller;

            const url = new URL(root.dataset.modelsUrl, window.location.origin);
            url.searchParams.set("make_id", makeSelect.value);
            url.searchParams.set("year", yearSelect.value);

            try {
                const data = await fetchJson(url, controller.signal);

                if (version !== modelVersion) {
                    return;
                }

                if (!Array.isArray(data.models)) {
                    throw new Error("Danh sách dòng xe không hợp lệ.");
                }

                notice.textContent = data.notice || "";

                if (data.models.length === 0) {
                    resetSelect(modelSelect, "Không có dòng xe trong danh mục");

                    showError(
                        "API chưa có dòng xe cho hãng và năm này. " +
                        "Kiểm tra lại thông tin hoặc liên hệ gara để được hỗ trợ."
                    );

                    return;
                }

                modelSelect.replaceChildren(new Option("Chọn dòng xe", ""));

                data.models.forEach((item) => {
                    modelSelect.add(
                        new Option(String(item.name), String(item.id))
                    );
                });

                modelSelect.disabled = false;

                const restored = selectMatchingOption(
                    modelSelect,
                    data.models,
                    preferredId,
                    preferredName
                );

                if ((preferredId || preferredName) && !restored) {
                    showError(
                        "Dòng xe đã lưu không có trong danh sách hiện tại. " +
                        "Vui lòng kiểm tra và chọn đúng dòng xe trước khi lưu."
                    );
                }
            } catch (error) {
                if (version !== modelVersion || error.name === "AbortError") {
                    return;
                }

                resetSelect(modelSelect, "Chưa tải được dòng xe");
                showError(error.message || "Không tải được dòng xe.");
            } finally {
                if (version === modelVersion) {
                    loadingModels = false;
                    updateSubmitState();
                }
            }
        }

        async function loadMakes() {
            const preferredMakeId = makeSelect.value || initialMakeId;
            const preferredModelId =
                modelSelect.value ||
                (initialModelPending ? initialModelId : "");

            const preferredModelName =
                initialModelPending && !preferredModelId ? existingModel : "";

            const version = ++makeVersion;

            makeRequest?.abort();
            modelRequest?.abort();

            ++modelVersion;

            makesLoaded = false;
            loadingMakes = true;
            loadingModels = false;

            clearError();

            resetSelect(makeSelect, "Đang tải hãng xe…");
            resetSelect(modelSelect, "Chọn năm và hãng xe trước");

            updateSubmitState();

            const controller = new AbortController();
            makeRequest = controller;

            try {
                const data = await fetchJson(
                    root.dataset.makesUrl,
                    controller.signal
                );

                if (version !== makeVersion) {
                    return;
                }

                if (!Array.isArray(data.data) || data.data.length === 0) {
                    throw new Error("Chưa có danh sách hãng xe.");
                }

                makeSelect.replaceChildren(new Option("Chọn hãng xe", ""));

                data.data.forEach((item) => {
                    makeSelect.add(
                        new Option(String(item.name), String(item.id))
                    );
                });

                makeSelect.disabled = false;
                makesLoaded = true;

                const restored = selectMatchingOption(
                    makeSelect,
                    data.data,
                    preferredMakeId,
                    existingMake
                );

                if ((preferredMakeId || existingMake) && !restored) {
                    showError(
                        "Hãng xe đã lưu không có trong danh mục hiện tại. " +
                        "Vui lòng kiểm tra lại thông tin."
                    );
                }

                loadingMakes = false;

                await loadModels(preferredModelId, preferredModelName);
            } catch (error) {
                if (version !== makeVersion || error.name === "AbortError") {
                    return;
                }

                resetSelect(makeSelect, "Chưa tải được hãng xe");
                resetSelect(modelSelect, "Chọn năm và hãng xe trước");

                showError(error.message || "Không tải được hãng xe.");
            } finally {
                if (version === makeVersion) {
                    loadingMakes = false;
                    updateSubmitState();
                }
            }
        }

        yearSelect.addEventListener("change", () => {
            initialModelPending = false;
            void loadModels();
        });

        makeSelect.addEventListener("change", () => {
            initialModelPending = false;
            void loadModels();
        });

        modelSelect.addEventListener("change", () => {
            initialModelPending = false;
            updateSubmitState();
        });

        retryButton.addEventListener("click", () => {
            if (loadingMakes || loadingModels) {
                return;
            }

            if (!makesLoaded) {
                void loadMakes();
                return;
            }

            const selectedModelId =
                modelSelect.value ||
                (initialModelPending ? initialModelId : "");

            const selectedModelName =
                initialModelPending && !selectedModelId ? existingModel : "";

            void loadModels(selectedModelId, selectedModelName);
        });

        form?.addEventListener("submit", (event) => {
            updateSubmitState();

            if (submitButton.disabled) {
                event.preventDefault();

                showError(
                    "Vui lòng chọn đủ năm, hãng và dòng xe trước khi lưu."
                );
            }
        });

        void loadMakes();
    }

    function initialize() {
        document.querySelectorAll("[data-vehicle-form]").forEach(
            initializeVehicleForm
        );
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initialize, { once: true });
    } else {
        initialize();
    }
})();
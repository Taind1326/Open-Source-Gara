(() => {
    'use strict';
    function initialize(form) {
        const date = form.querySelector('[data-booking-date]');
        const slots = form.querySelector('[data-booking-slot]');
        const notice = form.querySelector('[data-booking-notice]');
        const oldError = form.querySelector('[data-booking-error]');
        const retry = form.querySelector('[data-booking-retry]');
        const submit = form.querySelector('[data-booking-submit]');
        let controller, version = 0, loading = false, submitting = false;
        const selectedSlot = form.dataset.selectedSlot || '';
        function error(key,message) {
            if (window.GarageForms) return GarageForms.setError(form,key,message);
            notice.textContent = message;
        }
        function update() { submit.disabled = loading || submitting; retry.disabled = loading || submitting; }
        async function load(preferred = '') {
            const current = ++version;
            controller?.abort(); controller = new AbortController();
            loading = false;
            if (oldError) oldError.hidden = true;
            slots.replaceChildren(new Option('Chọn ngày hẹn trước','')); slots.disabled = true;
            notice.textContent = '';
            if (!date.value || !date.validity.valid) { update(); return; }
            loading = true; update();
            slots.replaceChildren(new Option('Đang tải khung giờ…',''));
            const url = new URL(form.dataset.slotsUrl,location.origin);
            url.searchParams.set('NgayHen',date.value);
            try {
                const response = await fetch(url,{credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'},signal:controller.signal});
                const body = await response.json().catch(() => null);
                if (current !== version) return;
                if (!response.ok) {
                    if (response.status === 422 && body?.errors) {
                        Object.entries(body.errors).forEach(([key,messages]) => error(key,messages[0]));
                        return;
                    }
                    throw new Error(response.status === 429 ? 'Bạn tải khung giờ quá nhanh. Vui lòng thử lại sau.' : 'Không tải được khung giờ. Kiểm tra kết nối hoặc đăng nhập lại.');
                }
                if (!Array.isArray(body?.data)) throw new Error('Dữ liệu khung giờ không hợp lệ.');
                slots.replaceChildren(new Option('Chọn khung giờ tiếp nhận',''));
                body.data.forEach(slot => { const option = new Option(slot.label,slot.value); option.disabled = !slot.available; slots.add(option); });
                slots.disabled = false;
                if (Array.from(slots.options).some(option => option.value === preferred && !option.disabled)) slots.value = preferred;
                notice.textContent = body.data.some(slot => slot.available) ? 'Số chỗ sẽ được kiểm tra lại khi bạn gửi lịch.' : 'Ngày này không còn khung giờ có thể đặt. Vui lòng chọn ngày khác.';
            } catch (exception) {
                if (exception.name !== 'AbortError' && current === version) error('KhungGioHen',exception.message);
            } finally {
                if (current === version) { loading = false; update(); }
            }
        }
        date.addEventListener('change',() => load());
        retry.addEventListener('click',() => load(slots.value));
        form.addEventListener('submit',event => {
            if (loading || submitting) { event.preventDefault(); return; }
            // Shared capture handler already validates every enabled control.
            if (slots.disabled || !slots.value) {
                event.preventDefault(); const target = error('KhungGioHen','Vui lòng chọn khung giờ tiếp nhận còn chỗ.');
                if (target) window.GarageForms?.focusError(target); return;
            }
            submitting = true; update(); submit.textContent = 'Đang gửi lịch…';
        });
        window.addEventListener('pageshow',event => { if (event.persisted) { submitting = false; submit.textContent = 'Gửi lịch hẹn'; load(slots.value); } });
        load(selectedSlot);
    }
    const start = () => document.querySelectorAll('[data-booking-form]').forEach(initialize);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded',start); else start();
})();

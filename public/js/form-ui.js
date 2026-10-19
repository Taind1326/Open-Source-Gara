(() => {
    'use strict';
    let sequence = 0;
    const fields = form => Array.from(form.elements).filter(el => el.matches('input,select,textarea') && !['hidden','submit','button','reset'].includes(el.type));
    const keyOf = name => name.replace(/\[([^\]]*)\]/g, (_, part) => part ? `.${part}` : '');
    function controls(form, key) {
        return fields(form).filter(el => {
            const name = keyOf(el.name);
            return name === key || name.startsWith(`${key}.`) || (el.name.endsWith('[]') && key.startsWith(`${name}.`));
        });
    }
    function anchor(el) {
        return el.closest('[data-validation-group],.garage-combobox,.input-group,.garage-auth-password,.password-demo-input-wrap') || el;
    }
    function feedback(el) {
        const group = anchor(el);
        if (group._garageFeedback) return group._garageFeedback;
        let next = group.nextElementSibling;
        if (next?.matches('.invalid-feedback,.garage-auth-error')) {
            next.classList.add('garage-field-error');
        } else {
            next = document.createElement('div');
            next.className = 'garage-field-error';
            group.after(next);
        }
        next.id ||= `garageFieldError${++sequence}`;
        group._garageFeedback = next;
        return next;
    }
    function setError(form, key, message) {
        const found = controls(form, key);
        if (!found.length) {
            let general = form.querySelector('[data-form-error]');
            if (!general) {
                general = document.createElement('div');
                general.dataset.formError = '';
                general.className = 'garage-field-error mb-3';
                general.tabIndex = -1;
                form.prepend(general);
            }
            general.textContent = message;
            return general;
        }
        const error = feedback(found[0]);
        error.hidden = false;
        error.textContent = message;
        error.classList.add('d-block');
        found.forEach(el => {
            el.classList.add('is-invalid');
            el.setAttribute('aria-invalid','true');
            const ids = new Set((el.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean));
            ids.add(error.id);
            el.setAttribute('aria-describedby', Array.from(ids).join(' '));
        });
        return found[0];
    }
    function clear(form) {
        fields(form).forEach(el => {
            el.classList.remove('is-invalid');
            el.setAttribute('aria-invalid','false');
        });
        form.querySelectorAll('.garage-field-error,.invalid-feedback,.garage-auth-error').forEach(el => {
            el.textContent = '';
            el.classList.remove('d-block');
            el.hidden = true;
        });
    }
    function focusError(el) {
        if (!el) return;
        const reveal = () => {
            for (let node = el; node; node = node.parentElement) {
                if (node.hasAttribute?.('data-reveal')) node.classList.add('is-visible');
            }
            const headerBottom = Array.from(document.querySelectorAll('.sticky-top,.fixed-top,.admin-topbar'))
                .filter(node => ['fixed','sticky'].includes(getComputedStyle(node).position))
                .reduce((bottom,node) => Math.max(bottom,node.getBoundingClientRect().bottom),0);
            el.focus({preventScroll:true});
            window.scrollTo({top:Math.max(0,window.scrollY + el.getBoundingClientRect().top - headerBottom - 24),behavior:matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth'});
        };
        const modal = el.closest('.modal');
        if (modal && !modal.classList.contains('show') && window.bootstrap) {
            modal.addEventListener('shown.bs.modal',reveal,{once:true});
            bootstrap.Modal.getOrCreateInstance(modal).show();
        } else requestAnimationFrame(reveal);
    }
    function label(el) {
        return (el.labels?.[0]?.textContent || el.name).replace(/\([^)]*\)|\*/g,'').replace(/\s+/g,' ').trim().toLocaleLowerCase('vi');
    }
    function validate(form) {
        clear(form);
        let first = null;
        fields(form).forEach(el => {
            if (el.disabled || el.readOnly || !el.willValidate) return;
            let message = '';
            const title = label(el);
            if (el.required && ((['checkbox','radio'].includes(el.type) && !el.checked && !(el.type === 'radio' && fields(form).some(other => other.name === el.name && other.checked))) || (!['checkbox','radio'].includes(el.type) && !el.value.trim()))) {
                message = `Vui lòng ${el.tagName === 'SELECT' || ['radio','checkbox'].includes(el.type) ? 'chọn' : 'nhập'} ${title}.`;
            } else if (el.value && el.validity.typeMismatch) message = `${title === 'email' ? 'Email' : 'Thông tin'} không đúng định dạng.`;
            else if (el.value && el.validity.patternMismatch) message = el.dataset.patternMessage || `Vui lòng nhập ${title} đúng định dạng.`;
            else if (el.validity.badInput || el.validity.stepMismatch) message = `Vui lòng nhập ${title} hợp lệ.`;
            else if (el.value && el.min && el.validity.rangeUnderflow) message = `${title} phải từ ${el.min} trở lên.`;
            else if (el.value && el.max && el.validity.rangeOverflow) message = `${title} không được vượt quá ${el.max}.`;
            else if (el.value && el.minLength > 0 && el.value.length < el.minLength) message = `${title} phải có ít nhất ${el.minLength} ký tự.`;
            else if (el.value && el.maxLength > 0 && el.value.length > el.maxLength) message = `${title} không được vượt quá ${el.maxLength} ký tự.`;
            if (el.name.endsWith('_confirmation')) {
                const original = fields(form).find(other => other.name === el.name.replace(/_confirmation$/,''));
                if (!message && original && el.value !== original.value) message = 'Mật khẩu xác nhận không khớp.';
            }
            if (message) { const target = setError(form,keyOf(el.name),message); first ||= target; }
        });
        form.querySelectorAll('[data-validation-group]').forEach(group => {
            const choices = Array.from(group.querySelectorAll('input[type="checkbox"]:not(:disabled)'));
            if (!choices.length) return;
            const count = choices.filter(el => el.checked).length;
            const min = Number(group.dataset.minSelected || 0);
            const max = Number(group.dataset.maxSelected || Infinity);
            if (count < min || count > max) {
                const target = setError(form,group.dataset.validationGroup,`Vui lòng chọn từ ${min} đến ${max} dịch vụ.`);
                first ||= target;
            }
        });
        return first;
    }
    const api = window.GarageForms = {setError,focusError,validate};
    function initialize() {
        const forms = Array.from(document.forms);
        forms.forEach(form => { form.noValidate = true; });
        let submitted = null;
        try { submitted = JSON.parse(sessionStorage.getItem('garage.lastForm') || 'null'); sessionStorage.removeItem('garage.lastForm'); } catch {}
        let firstServerError = null;
        try {
            const bags = JSON.parse(document.getElementById('garage-form-errors')?.textContent || '{}');
            Object.entries(bags).forEach(([bag, errors]) => {
                const candidates = forms.filter(form => (form.dataset.errorBag || 'default') === bag);
                Object.entries(errors).forEach(([key,messages]) => {
                    const remembered = submitted?.page === location.pathname ? forms[submitted.index] : null;
                    const form = (candidates.includes(remembered) && controls(remembered,key).length ? remembered : candidates.find(item => controls(item,key).length)) || (candidates.includes(remembered) ? remembered : candidates.find(item => item.method.toLowerCase() === 'post' && fields(item).length > 0));
                    if (form) { const target = setError(form,key,messages[0]); firstServerError ||= target; }
                });
            });
        } catch (error) { console.error('Không thể hiển thị lỗi biểu mẫu.',error); }
        firstServerError ||= document.querySelector('.is-invalid');
        if (firstServerError) focusError(firstServerError);
        const modalElement = document.getElementById('garageConfirmModal');
        let pending = null;
        let approved = null;
        modalElement?.addEventListener('hidden.bs.modal',() => { pending = null; });
        modalElement?.querySelector('[data-confirm-accept]').addEventListener('click',() => {
            if (!pending) return;
            const {form,submitter} = pending;
            pending = null;
            approved = form;
            bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            form.requestSubmit(submitter?.isConnected && !submitter.disabled ? submitter : undefined);
            approved = null;
        });
        document.addEventListener('submit',event => {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) return;
            form.noValidate = true;
            const first = validate(form);
            if (first) {
                event.preventDefault(); event.stopImmediatePropagation(); focusError(first); return;
            }
            const conditional = form.dataset.confirmWhen;
            const needsConfirm = form.dataset.confirm && (!conditional || fields(form).some(el => el.name === conditional && el.value === form.dataset.confirmValue));
            if (needsConfirm && approved !== form) {
                event.preventDefault(); event.stopImmediatePropagation();
                if (!window.bootstrap?.Modal) { focusError(setError(form,'_form','Chưa tải được hộp xác nhận. Vui lòng tải lại trang rồi thử lại.')); return; }
                pending = {form,submitter:event.submitter};
                modalElement.querySelector('#garageConfirmTitle').textContent = form.dataset.confirmTitle || 'Xác nhận thao tác';
                modalElement.querySelector('#garageConfirmMessage').textContent = form.dataset.confirm;
                modalElement.querySelector('[data-confirm-accept]').textContent = form.dataset.confirmAccept || 'Xác nhận';
                modalElement.querySelector('[data-confirm-cancel]').textContent = form.dataset.confirmCancel || 'Quay lại';
                bootstrap.Modal.getOrCreateInstance(modalElement).show(); return;
            }
            try { sessionStorage.setItem('garage.lastForm',JSON.stringify({page:location.pathname,index:forms.indexOf(form)})); } catch {}
        },true);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded',initialize);
    else initialize();
})();

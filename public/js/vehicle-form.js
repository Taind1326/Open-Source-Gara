(() => {
    'use strict';
    const normalize = value => String(value).trim().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/g,'d').replace(/Đ/g,'D').toLocaleLowerCase('vi');
    function initialize(root) {
        const makeInput = root.querySelector('[name="HangXe"]');
        const modelInput = root.querySelector('[name="DongXe"]');
        if (!makeInput || !modelInput) return;
        let catalog = {};
        try { catalog = JSON.parse(root.querySelector('[data-vehicle-catalog]').textContent); } catch {}
        const entries = Object.entries(catalog);
        const closers = [];
        let previousMake = normalize(makeInput.value);
        makeInput.addEventListener('input',() => {
            const current = normalize(makeInput.value);
            if (current !== previousMake) { modelInput.value = ''; previousMake = current; }
        });
        function combo(box,getItems) {
            const input = box.querySelector('input');
            const toggle = box.querySelector('[data-combo-toggle]');
            const menu = box.querySelector('[data-combo-menu]');
            let options = [], active = -1;
            function close() {
                menu.hidden = true; active = -1;
                input.setAttribute('aria-expanded','false');
                toggle.setAttribute('aria-expanded','false');
                input.removeAttribute('aria-activedescendant');
            }
            closers.push(close);
            function select(value) {
                input.value = value;
                input.dispatchEvent(new Event('input',{bubbles:true}));
                input.dispatchEvent(new Event('change',{bubbles:true}));
                input.focus(); close();
            }
            function open(all = false) {
                closers.forEach(closeMenu => closeMenu());
                const keyword = all ? '' : normalize(input.value);
                const matches = getItems().filter(value => normalize(value).includes(keyword)).sort((a,b) => Number(normalize(b).startsWith(keyword)) - Number(normalize(a).startsWith(keyword)) || a.localeCompare(b,'vi'));
                menu.replaceChildren(); options = []; active = -1;
                matches.forEach((value,index) => {
                    const button = document.createElement('button');
                    button.type = 'button'; button.tabIndex = -1;
                    button.id = `${input.id}Option${index}`;
                    button.className = 'garage-combobox-option';
                    button.textContent = value; button.setAttribute('role','option');
                    button.setAttribute('aria-selected','false');
                    button.addEventListener('mousedown',event => event.preventDefault());
                    button.addEventListener('click',() => select(value));
                    menu.append(button); options.push(button);
                });
                if (!matches.length) {
                    const empty = document.createElement('div');
                    empty.className = 'garage-combobox-empty';
                    empty.textContent = 'Không có gợi ý phù hợp. Bạn có thể nhập trực tiếp.';
                    menu.append(empty);
                }
                menu.hidden = false;
                input.setAttribute('aria-expanded','true'); toggle.setAttribute('aria-expanded','true');
            }
            input.addEventListener('focus',() => open());
            input.addEventListener('click',() => open());
            input.addEventListener('input',() => open());
            toggle.addEventListener('mousedown',event => event.preventDefault());
            toggle.addEventListener('click',() => { const wasOpen = !menu.hidden; input.focus(); if (wasOpen) close(); else open(true); });
            input.addEventListener('keydown',event => {
                if (['Escape','Tab'].includes(event.key)) { close(); return; }
                if (['ArrowDown','ArrowUp'].includes(event.key)) {
                    event.preventDefault(); if (menu.hidden) open(); if (!options.length) return;
                    active = event.key === 'ArrowDown' ? (active + 1) % options.length : active <= 0 ? options.length - 1 : active - 1;
                    options.forEach((option,index) => { option.classList.toggle('is-active',index === active); option.setAttribute('aria-selected',String(index === active)); });
                    input.setAttribute('aria-activedescendant',options[active].id);
                    options[active].scrollIntoView({block:'nearest'});
                } else if (event.key === 'Enter' && !menu.hidden && active >= 0) { event.preventDefault(); select(options[active].textContent); }
            });
            box.addEventListener('focusout',() => setTimeout(() => { if (!box.contains(document.activeElement)) close(); },0));
        }
        combo(root.querySelector('[data-vehicle-combo="make"]'),() => entries.map(([make]) => make));
        combo(root.querySelector('[data-vehicle-combo="model"]'),() => entries.find(([make]) => normalize(make) === normalize(makeInput.value))?.[1] || []);
        document.addEventListener('click',event => { if (!root.contains(event.target) || !event.target.closest('[data-vehicle-combo]')) closers.forEach(close => close()); });
    }
    const start = () => document.querySelectorAll('[data-vehicle-form]').forEach(initialize);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded',start); else start();
})();

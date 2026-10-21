(() => {
    const timer = document.querySelector('[data-countdown]');
    if (timer) {
        const expiry = Number(timer.dataset.expires);
        const serverNow = Number(timer.dataset.serverNow);
        const started = performance.now();
        const update = () => {
            const seconds = Math.max(0, Math.ceil((expiry - serverNow - (performance.now() - started)) / 1000));
            const strong = timer.querySelector('strong');
            if (strong) strong.textContent = `${String(Math.floor(seconds / 60)).padStart(2, '0')}:${String(seconds % 60).padStart(2, '0')}`;
            const verify = document.querySelector('[data-verify]');
            const resend = document.querySelector('[data-resend]');
            if (verify) verify.disabled = seconds === 0;
            if (resend) resend.disabled = seconds > 0;
            if (seconds === 0) {
                timer.textContent = 'Mã đã hết hạn. Vui lòng gửi lại mã.';
                return false;
            }
            return true;
        };
        if (update()) {
            const interval = setInterval(() => { if (!update()) clearInterval(interval); }, 250);
        }
    }
    document.querySelectorAll('[data-reset-toggle]').forEach((button) => {
        const input = document.getElementById(button.getAttribute('aria-controls'));
        if (!input) return;
        button.hidden = false;
        button.addEventListener('click', () => {
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(visible));
            button.setAttribute('aria-label', visible ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
            button.querySelector('i').className = visible ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
})();

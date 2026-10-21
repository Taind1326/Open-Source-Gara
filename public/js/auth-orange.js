(() => {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
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

(() => {
    'use strict';

    const storageKey = 'garage-theme';
    const root = document.documentElement;
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)');

    function readPreference() {
        try {
            const value = localStorage.getItem(storageKey);

            return value === 'light' || value === 'dark'
                ? value
                : null;
        } catch {
            return null;
        }
    }

    let preference = readPreference();

    function resolveTheme() {
        return preference ?? (systemTheme.matches ? 'dark' : 'light');
    }

    function applyTheme() {
        const theme = resolveTheme();
        const dark = theme === 'dark';

        root.setAttribute('data-bs-theme', theme);

        const button = document.getElementById('garageThemeToggle');

        if (!button) {
            return;
        }

        const label = dark
            ? 'Chuyển sang giao diện sáng'
            : 'Chuyển sang giao diện tối';

        button.title = label;
        button.setAttribute('aria-label', label);
        button.setAttribute('aria-pressed', String(dark));

        button.querySelector('i').className = dark
            ? 'bi bi-sun'
            : 'bi bi-moon-stars';
    }

    function initializeToggle() {
        if (document.getElementById('garageThemeToggle')) {
            return;
        }

        const button = document.createElement('button');
        const icon = document.createElement('i');

        button.id = 'garageThemeToggle';
        button.type = 'button';
        button.className = 'garage-theme-toggle';

        icon.setAttribute('aria-hidden', 'true');

        button.appendChild(icon);
        document.body.appendChild(button);

        button.addEventListener('click', () => {
            preference = root.getAttribute('data-bs-theme') === 'dark'
                ? 'light'
                : 'dark';

            try {
                localStorage.setItem(storageKey, preference);
            } catch {
                // Allow switching when browser storage is unavailable.
            }

            applyTheme();
        });

        applyTheme();
    }

    applyTheme();

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            initializeToggle,
            { once: true }
        );
    } else {
        initializeToggle();
    }

    systemTheme.addEventListener('change', () => {
        if (preference === null) {
            applyTheme();
        }
    });

    window.addEventListener('storage', (event) => {
        if (event.key === storageKey || event.key === null) {
            preference = readPreference();
            applyTheme();
        }
    });
})();
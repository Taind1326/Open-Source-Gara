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

    // Keep canvas chart labels readable and restore each chart's light settings.
    const chartColors = new WeakMap();

    function updateCharts() {
        if (!window.Chart?.instances) {
            return;
        }

        const dark = root.getAttribute('data-bs-theme') === 'dark';

        Object.values(window.Chart.instances).forEach((chart) => {
            const options = chart.config.options;

            if (!chartColors.has(chart)) {
                chartColors.set(chart, {
                    color: options.color,
                    scales: Object.fromEntries(
                        Object.entries(options.scales ?? {}).map(([key, scale]) => [
                            key,
                            { ticks: scale.ticks?.color, grid: scale.grid?.color }
                        ])
                    )
                });
            }

            const original = chartColors.get(chart);
            options.color = dark ? '#b5b5b5' : original.color;

            Object.entries(options.scales ?? {}).forEach(([key, scale]) => {
                scale.ticks ??= {};
                scale.grid ??= {};
                scale.ticks.color = dark ? '#b5b5b5' : original.scales[key]?.ticks;
                scale.grid.color = dark ? '#3d3d3d' : original.scales[key]?.grid;
            });

            chart.update('none');
        });
    }

    new MutationObserver(updateCharts).observe(root, {
        attributes: true,
        attributeFilter: ['data-bs-theme']
    });

    window.addEventListener('load', updateCharts, { once: true });

    // Bootstrap also reads data-bs-theme while printing.
    // Temporarily use light mode without changing the saved preference.
    let themeBeforePrint = null;

    window.addEventListener('beforeprint', () => {
        themeBeforePrint = root.getAttribute('data-bs-theme');
        root.setAttribute('data-bs-theme', 'light');
        updateCharts();
    });

    window.addEventListener('afterprint', () => {
        if (themeBeforePrint !== null) {
            root.setAttribute('data-bs-theme', themeBeforePrint);
            themeBeforePrint = null;
            applyTheme();
        }
    });
})();
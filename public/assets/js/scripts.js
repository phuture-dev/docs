/**
 * Theme and layout behaviour of the documentation
 */
(function() {
    var STORAGE_KEY = 'docs-theme';
    var DEFAULT_THEME = 'dark';

    var ICONS = {
        dark: 'bi-sun-fill',
        light: 'bi-moon-stars-fill'
    };

    var LABELS = {
        dark: 'Switch to the light theme',
        light: 'Switch to the dark theme'
    };

    /**
     * Theme chosen on an earlier visit, or the default one
     */
    function storedTheme() {
        try {
            var theme = window.localStorage.getItem(STORAGE_KEY);

            return theme === 'light' || theme === 'dark' ? theme : DEFAULT_THEME;
        } catch (error) {
            return DEFAULT_THEME;
        }
    }

    /**
     * Put a theme on the page, where every stylesheet reads it from
     */
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-bs-theme', theme);
    }

    /**
     * Theme the page is on right now, which is what the button switches away from
     */
    function currentTheme() {
        return document.documentElement.getAttribute('data-bs-theme') === 'light' ? 'light' : 'dark';
    }

    /**
     * Keep a theme for the next visit, when the browser lets us store it
     */
    function rememberTheme(theme) {
        try {
            window.localStorage.setItem(STORAGE_KEY, theme);
        } catch (error) {
            // A browser refusing to store it still keeps the theme for this visit
        }
    }

    /**
     * Point the button at the theme it switches to
     */
    function markToggle(button, theme) {
        var icon = button.querySelector('i');

        if (icon) {
            icon.classList.remove(ICONS.dark, ICONS.light);
            icon.classList.add(ICONS[theme]);
        }

        button.setAttribute('aria-label', LABELS[theme]);
        button.setAttribute('title', LABELS[theme]);
    }

    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);

            return;
        }

        callback();
    }

    // Ahead of the first paint, so the theme never flashes
    applyTheme(storedTheme());

    ready(function() {
        var toggle = document.getElementById('theme-toggle');
        var navbar = document.getElementById('navbar');

        if (toggle) {
            markToggle(toggle, currentTheme());

            toggle.addEventListener('click', function() {
                var theme = currentTheme() === 'dark' ? 'light' : 'dark';

                applyTheme(theme);
                rememberTheme(theme);
                markToggle(toggle, theme);
            });
        }

        if (!navbar) {
            return;
        }

        /**
         * Keep the layout in sync with the real height of the top navbar
         */
        function setNavbarHeight() {
            document.documentElement.style.setProperty('--docs-navbar-height', navbar.offsetHeight + 'px');
        }

        setNavbarHeight();
        window.addEventListener('resize', setNavbarHeight);

        if (window.ResizeObserver) {
            new ResizeObserver(setNavbarHeight).observe(navbar);
        }
    });
})();

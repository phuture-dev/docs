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
     * Headings a table of contents is built out of, for a document carrying none
     */
    var OUTLINE_LEVELS = 'h2, h3';

    /**
     * Entries such a table of contents needs before it is worth a column
     */
    var OUTLINE_MINIMUM = 3;

    /**
     * Title given to it, as it has no heading of its own to borrow one from
     */
    var OUTLINE_TITLE = 'On this page';

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

    /**
     * Table of contents a page carries, or null when it carries none
     *
     * It reads as a heading with an ordered list right behind it, every item of
     * which points somewhere into the page. That last part is what tells a
     * table of contents from any other numbered list a heading happens to
     * introduce, such as a checklist.
     *
     * @param {Element} article Article the page was rendered into
     * @returns {{heading: Element, list: Element}|null}
     */
    function tableOfContents(article) {
        var headings = article.querySelectorAll('h1, h2, h3, h4, h5, h6');

        for (var i = 0; i < headings.length; i++) {
            var list = headings[i].nextElementSibling;

            if (list && list.tagName === 'OL' && linksIntoPage(list)) {
                return { heading: headings[i], list: list };
            }
        }

        return null;
    }

    /**
     * Whether every item of a list points somewhere into this page
     *
     * @param {Element} list List to look through
     * @returns {boolean}
     */
    function linksIntoPage(list) {
        var items = list.querySelectorAll('li');

        return items.length > 0 && Array.prototype.every.call(items, function(item) {
            return item.querySelector('a[href^="#"]') !== null;
        });
    }

    /**
     * Text of a heading, without the permalink sitting in front of it
     *
     * @param {Element} heading Heading to read
     * @returns {string}
     */
    function headingText(heading) {
        var copy = heading.cloneNode(true);
        var permalink = copy.querySelector('.heading-permalink');

        if (permalink) {
            permalink.remove();
        }

        return copy.textContent.trim();
    }

    /**
     * Lift the table of contents of the page into the sidebar beside it
     */
    function buildTableOfContents() {
        var sidebar = document.getElementById('toc');
        var article = document.querySelector('.docs-content article');

        if (!sidebar || !article) {
            return;
        }

        var contents = tableOfContents(article);
        var heading = OUTLINE_TITLE;
        var list;

        if (contents !== null) {
            heading = headingText(contents.heading);
            list = contents.list.cloneNode(true);

            // The one written in the document steps aside wherever the sidebar shows
            contents.heading.classList.add('docs-toc-source');
            contents.list.classList.add('docs-toc-source');
        } else {
            list = outline(article);
        }

        if (list === null) {
            return;
        }

        var navigation = document.createElement('nav');
        navigation.setAttribute('aria-label', heading);

        var title = document.createElement('p');
        title.className = 'docs-toc-title';
        title.textContent = heading;

        navigation.appendChild(title);
        navigation.appendChild(list);
        sidebar.appendChild(navigation);

        sidebar.classList.add('is-visible');

        followReading(sidebar);
    }

    /**
     * Table of contents read off the headings of a document that carries none
     *
     * Sections make up the list and their subsections nest underneath, which is
     * the shape a written one has, so the sidebar and the highlighting cannot
     * tell the two apart. Null when the document has too little to navigate.
     *
     * @param {Element} article Article the page was rendered into
     * @returns {Element|null}
     */
    function outline(article) {
        var headings = article.querySelectorAll(OUTLINE_LEVELS);
        var list = document.createElement('ol');
        var branch = null;
        var entries = 0;

        Array.prototype.forEach.call(headings, function(heading) {
            if (!heading.id) {
                return;
            }

            var item = document.createElement('li');
            var link = document.createElement('a');

            link.setAttribute('href', '#' + heading.id);
            link.textContent = headingText(heading);
            item.appendChild(link);
            entries++;

            // A section opens a new branch, a subsection goes on the last one
            if (heading.tagName === 'H2' || list.lastElementChild === null) {
                list.appendChild(item);
                branch = null;

                return;
            }

            if (branch === null) {
                branch = document.createElement('ol');
                list.lastElementChild.appendChild(branch);
            }

            branch.appendChild(item);
        });

        return entries >= OUTLINE_MINIMUM ? list : null;
    }

    /**
     * Keep the entry of the section being read lit up as the page scrolls
     *
     * The band watched for headings starts right below the navbar, where a
     * heading lands when its entry is clicked, and ends part of the way down
     * the page. The section being read is the last heading to have entered it,
     * and it stays lit while a long section scrolls past with no heading in
     * sight.
     *
     * @param {Element} sidebar Sidebar holding the copied table of contents
     */
    function followReading(sidebar) {
        if (!window.IntersectionObserver) {
            return;
        }

        var entries = sidebar.querySelectorAll('a[href^="#"]');
        var links = {};
        var headings = [];

        Array.prototype.forEach.call(entries, function(link) {
            var heading = document.getElementById(decodeURIComponent(link.getAttribute('href').slice(1)));

            if (heading && !links[heading.id]) {
                links[heading.id] = link;
                headings.push(heading);
            }
        });

        if (headings.length === 0) {
            return;
        }

        var reading = {};

        var light = function() {
            var current = null;

            headings.forEach(function(heading) {
                if (reading[heading.id]) {
                    current = heading;
                }
            });

            if (current === null) {
                return;
            }

            headings.forEach(function(heading) {
                links[heading.id].classList.toggle('is-current', heading === current);
            });
        };

        var observer = new IntersectionObserver(function(changes) {
            changes.forEach(function(change) {
                reading[change.target.id] = change.isIntersecting;
            });

            light();
        }, { rootMargin: '-' + Math.round(navbarHeight() + 16) + 'px 0px -70% 0px' });

        headings.forEach(function(heading) {
            observer.observe(heading);
        });
    }

    /**
     * Height the navbar takes at the top of the page
     *
     * @returns {number}
     */
    function navbarHeight() {
        var navbar = document.getElementById('navbar');

        return navbar ? navbar.offsetHeight : 0;
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

        buildTableOfContents();

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

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
     * Labels the copy button of a code block carries, before and after a copy
     */
    var COPY_LABEL = 'Copy code';
    var COPIED_LABEL = 'Copied';

    /**
     * Icons it wears alongside those labels
     */
    var COPY_ICONS = {
        idle: 'bi-clipboard',
        copied: 'bi-check2'
    };

    /**
     * How long it says so for, in milliseconds
     */
    var COPIED_FOR = 1600;

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
                return {
                    heading: headings[i],
                    list: list
                };
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
        }, {
            rootMargin: '-' + Math.round(navbarHeight() + 16) + 'px 0px -70% 0px'
        });

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

    /**
     * Code a block holds, as it was written rather than as it is shown
     *
     * The numbers down the side are the page counting the lines for a reader, not
     * part of the code itself, so they are left out of what is handed over.
     *
     * @param {Element} block Code block to read
     * @returns {string}
     */
    function codeOf(block) {
        var copy = (block.querySelector('code') || block).cloneNode(true);
        var gutters = copy.querySelectorAll('.hl-gutter');

        Array.prototype.forEach.call(gutters, function(gutter) {
            gutter.remove();
        });

        return copy.textContent.replace(/\n+$/, '');
    }

    /**
     * Put a text on the clipboard, however the browser lets us
     *
     * @param {string} text Text to copy
     * @returns {Promise}
     */
    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }

        // A page served over plain http has the older way and nothing else
        return new Promise(function(resolve, reject) {
            var field = document.createElement('textarea');

            field.value = text;
            field.setAttribute('readonly', '');
            field.style.position = 'fixed';
            field.style.opacity = '0';

            document.body.appendChild(field);
            field.select();

            try {
                document.execCommand('copy') ? resolve() : reject();
            } catch (error) {
                reject(error);
            }

            field.remove();
        });
    }

    /**
     * Say on the button itself that the code is on the clipboard
     *
     * @param {Element} button Button that was pressed
     */
    function markCopied(button) {
        var icon = button.querySelector('i');

        button.classList.add('is-copied');
        button.setAttribute('aria-label', COPIED_LABEL);
        button.setAttribute('title', COPIED_LABEL);

        if (icon) {
            icon.classList.remove(COPY_ICONS.idle);
            icon.classList.add(COPY_ICONS.copied);
        }

        window.clearTimeout(button.copiedTimer);

        button.copiedTimer = window.setTimeout(function() {
            button.classList.remove('is-copied');
            button.setAttribute('aria-label', COPY_LABEL);
            button.setAttribute('title', COPY_LABEL);

            if (icon) {
                icon.classList.remove(COPY_ICONS.copied);
                icon.classList.add(COPY_ICONS.idle);
            }
        }, COPIED_FOR);
    }

    /**
     * Button handing the code of a block over to the clipboard
     *
     * @param {Element} block Code block it belongs to
     * @returns {Element}
     */
    function copyButton(block) {
        var button = document.createElement('button');

        button.type = 'button';
        button.className = 'code-copy btn border-0 p-1 lh-1';
        button.setAttribute('aria-label', COPY_LABEL);
        button.setAttribute('title', COPY_LABEL);
        button.innerHTML = '<i class="bi ' + COPY_ICONS.idle + '" aria-hidden="true"></i>';

        button.addEventListener('click', function() {
            copyText(codeOf(block)).then(function() {
                markCopied(button);
            }, function() {
                // Nothing to say on a browser that will not let the page copy for the reader
            });
        });

        return button;
    }

    /**
     * Give every code block of the page a button to copy what it holds
     *
     * The button is built here rather than rendered with the page, so that one
     * never sits on a page whose browser has no way of pressing it.
     *
     * @param {Element} article Article the page was rendered into
     */
    function buildCopyButtons(article) {
        var blocks = article.querySelectorAll('pre');

        Array.prototype.forEach.call(blocks, function(block) {
            var wrapper = document.createElement('div');

            wrapper.className = 'code-block';

            block.parentNode.insertBefore(wrapper, block);
            wrapper.appendChild(block);
            wrapper.appendChild(copyButton(block));
        });
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

        var article = document.querySelector('.docs-content article');

        if (article) {
            buildCopyButtons(article);
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
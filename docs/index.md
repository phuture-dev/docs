# Documentation

Welcome to the documentation. This page is rendered from `docs/index.md` —
edit that file and reload to see your changes.

## Getting started

The home page looks for `index.md` first, then `readme.md`, inside the `docs`
folder. Navigation comes from `docs/sidebar.md`.

- Write your pages in markdown
- Link them from the sidebar
- Reload the browser

## Markdown support

GitHub Flavored Markdown is enabled, so tables, task lists and fenced code
blocks all work:

```php
$app->get('/', ['controller' => 'Phuture\App\Controller\Index', 'action' => 'index']);
```

| Path | Purpose |
| --- | --- |
| `docs/` | Documentation content |
| `views/` | Latte templates |
| `app/` | Controllers and helpers |

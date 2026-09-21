<div align="center">

# Phuture Documentation

**The official documentation of the Phuture Framework.**

![PHP Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2Fphuture-dev%2Fdocs%2Frefs%2Fheads%2Fmain%2Fcomposer.json&query=require.php&style=for-the-badge&label=PHP%20Version&color=purple)
![License](https://img.shields.io/badge/license-MIT-blue?style=for-the-badge)

</div>

## Introduction

This repository holds the documentation of the **Phuture Framework** together with the small PHP application that renders it at **[docs.phuture.dev](https://docs.phuture.dev/)**.

Every page lives in the `docs/` folder as plain markdown. Some of it is written here by hand, and the rest is brought in from the repositories of the packages themselves, so that a readme kept beside the code it describes is the same readme a reader finds on the site.

**Prefer to read the documentation right out of GitHub?** Start at **[docs/index.md](docs/index.md)** and follow the links from there — the whole of `docs/` is readable as markdown, exactly as it is served.

> **Phuture is in Alpha and under active development.** The documentation tracks the `main` branch of each package, so it describes what exists today rather than what a tagged release holds.

## Features

- 📚 **Markdown documentation** - Every page is a plain markdown file, written and reviewed like code
- 🔄 **Synced sources** - Documents and repositories named in `source.json` are brought in on a schedule
- 🔍 **Generated reference** - Reference pages are written from the php sources themselves, docblocks and all
- 🔎 **Full-text search** - Every page is indexed, with headings, scoring and snippets
- 🎨 **Syntax highlighting** - Fenced code is marked up as the language it is written in

## Requirements

- PHP 8.4 or later, with the `dom`, `zip` and `tokenizer` extensions
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) and npm, to build the assets

## Installation

```bash
git clone https://github.com/phuture-dev/docs.git
cd docs

composer install
npm install && npx gulp
```

Serve the site locally:

```bash
composer server
```

Bring the documentation up to date, which is what the cron entry runs:

```bash
composer cron
```

## Project Layout

| Path            | Purpose                                                        |
|-----------------|----------------------------------------------------------------|
| `docs/`         | The documentation itself, as markdown                           |
| `app/`          | Controllers, commands and helpers of the site                   |
| `views/`        | Latte templates the pages are rendered with                     |
| `public/`       | Document root, assets and the front controller                  |
| `bin/cron`      | Entry point of the scheduled run that syncs the documentation   |
| `source.json`   | The documents and repositories the documentation is brought in from |

## Testing

```bash
composer test
```

This runs [PHPStan](https://phpstan.org/) at level 6, the [Nette Tester](https://tester.nette.org/) suite, and [PHP CS Fixer](https://cs.symfony.com/) over `app/`.

## Contributing

Thank you for considering contributing to this project! You can read the **[Contribution Guide](docs/contributing.md)** and our **[Developer Workflow Guide](docs/workflow.md)**.

Corrections to a page brought in from another repository belong in that repository, since anything written here would be replaced on the next sync.

## Code of Conduct

This project follows a Code of Conduct that all community members and contributors are expected to adhere to our **[Contributor Code of Conduct](docs/code_of_conduct.md)**.

## License

This project is open-source and available under the **MIT License**.

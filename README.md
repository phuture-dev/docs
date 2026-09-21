<div align="center">

# Phuture Documentation

**The official documentation of the Phuture Framework.**

![PHP Version](https://img.shields.io/badge/dynamic/json?url=https%3A%2F%2Fraw.githubusercontent.com%2Fphuture-dev%2Fdocs%2Frefs%2Fheads%2Fmain%2Fcomposer.json&query=require.php&style=for-the-badge&label=PHP%20Version&color=purple)
![License](https://img.shields.io/badge/license-MIT-blue?style=for-the-badge)

</div>

## Introduction

This repository holds the documentation of the **Phuture Framework** together with the small PHP application that renders it at **[docs.phuture.dev](https://docs.phuture.dev/)**.

Every page lives in the `docs/` folder as plain markdown. Some of it is written here by hand, and the rest is brought in from the repositories of the packages themselves, so that a readme kept beside the code it describes is the same readme a reader finds on the site.

**The `docs/` folder keeps itself up to date.** A workflow runs every night, reading `source.json`, bringing in every document and repository it names, and writing the reference pages again from the php sources that came with them. A page changed in a package repository reaches the site within a day, and anything committed under `docs/` by that run was written by it rather than by hand.

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

## Deployment

The site is served out of the `public/` folder. Point the document root of the domain or subdomain at it and nothing else is needed.

Where the document root cannot be moved, which is what shared hosting such as cPanel gives, put the project inside the folder that is already published, `public_html` or a folder of it. The `.htaccess` in the project root serves the site out of `public/` wherever it sits, and every url the site writes is carried under that folder. Nothing outside `public/` is reachable either way.

Whichever way it is installed, `cache/` has to be writable by the web server. An install that pulls from this repository is served the pages the nightly workflow already committed, and one that reads its own sources keeps them up to date with `bin/cron`:

```
* * * * * /usr/local/bin/php /home/user/public_html/bin/cron
```

## Contributing

Thank you for considering contributing to this project! You can read the **[Contribution Guide](CONTRIBUTING.md)**.

Corrections to a page brought in from another repository belong in that repository, since anything written here would be replaced on the next sync.

## Code of Conduct

This project follows a Code of Conduct that all community members and contributors are expected to adhere to our **[Contributor Code of Conduct](CODE_OF_CONDUCT.md)**.

## License

This project is open-source and available under the **MIT License**.

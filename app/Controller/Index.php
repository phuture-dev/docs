<?php

namespace Phuture\App\Controller;

use Phuture\App\{Controller, Helper\Document};

/**
 * Controller answering the front page of the site.
 *
 * Looks in the documentation folder for the document named `index`, in any of the
 * formats a page may be written in, and sends it out as the home page.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Index extends Controller
{
    /**
     * Renders the home page of the documentation.
     *
     * Looks in the documentation folder for the document named `index`, in any of
     * the formats a page may be written in, and sends it out as the front page of
     * the site. When there is no such document the reader is shown the page that
     * says nothing was found.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller\Index;
     *
     * (new Index())->index();
     *
     * // Prints the rendered docs/index.md
     * ```
     *
     * @return void
     */
    public function index(): void
    {
        self::renderDocument(Document::find(DOCS_DIR, Document::fileNames('index')));
    }
}

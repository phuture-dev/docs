<?php

namespace Phuture\App\Controller;

use Phuture\App\{Controller, Helper\Document};

class Index extends Controller
{
    /**
     * Render the documentation home page out of the /docs/index file
     *
     * @return void
     */
    public function index(): void
    {
        self::renderDocument(Document::find(DOCS_DIR, Document::fileNames('index')));
    }
}

<?php

namespace Phuture\App\Controller;

use Phuture\App\{Controller, Helper\Document};

class Docs extends Controller
{
    /**
     * Documents that drive the site rather than being pages of it
     */
    protected const PROTECTED_DOCUMENTS = ['sidebar'];

    /**
     * Render any page of the docs folder
     *
     * @param string|array $page Url segments below the docs folder
     * @return void
     */
    public function index(string|array $page = []): void
    {
        self::renderDocument(self::documentPath((array) $page));
    }

    /**
     * Path of the document a url points at, or null when it leads nowhere
     *
     * @param array $segments Url segments below the docs folder
     * @return string|null
     */
    protected static function documentPath(array $segments): ?string
    {
        $segments = array_values(array_filter($segments, fn ($segment) => $segment !== ''));

        if ($segments === []) {
            return null;
        }

        // Plain names only, so a url can never walk out of the docs folder
        foreach ($segments as $segment) {
            if (!preg_match('/^[a-z0-9][a-z0-9._-]*$/i', $segment)) {
                return null;
            }
        }

        $path = DOCS_DIR . implode(DS, $segments);
        $name = array_pop($segments);

        // A folder is served by the document inside it, a page by any format it is written in
        $document = is_dir($path)
            ? Document::find($path, Document::fileNames(Document::DEFAULT_NAMES))
            : Document::find(dirname($path), Document::fileNames($name));

        if (in_array(strtolower(pathinfo((string) $document, PATHINFO_FILENAME)), self::PROTECTED_DOCUMENTS, true)) {
            return null;
        }

        return self::isInside($document, DOCS_DIR) ? $document : null;
    }

    /**
     * Whether a file exists inside a directory, following any symlink on the way
     *
     * @param string|null $path Path of the file
     * @param string $directory Directory the file has to live in
     * @return bool
     */
    protected static function isInside(?string $path, string $directory): bool
    {
        if ($path === null || $path === '') {
            return false;
        }

        $resolvedFile = realpath($path);
        $resolvedDirectory = realpath($directory);

        if ($resolvedFile === false || $resolvedDirectory === false) {
            return false;
        }

        return str_starts_with($resolvedFile, rtrim($resolvedDirectory, DS) . DS);
    }
}

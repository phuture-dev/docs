<?php

namespace Phuture\App\Controller;

use Phuture\App\{Controller, Helper\Document};

/**
 * Controller answering a url below the site root with a page of the documentation.
 *
 * The segments of the url are walked down the documentation folder and the
 * document they land on is rendered. A url pointing at a folder is answered with
 * the document inside it, and a url pointing nowhere is answered with the page
 * that says nothing was found. Nothing outside the documentation folder can be
 * reached this way.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Docs extends Controller
{
    /**
     * Shape a url segment has to have before it is looked for on disk.
     *
     * A segment opens with a letter or a digit and goes on with letters, digits,
     * dots, dashes and underscores. Nothing else is a name a document can be saved
     * under, and a url made of anything else is answered with the page that says
     * nothing was found.
     *
     * @var string
     */
    protected const SEGMENT_PATTERN = '/^[a-z0-9][a-z0-9._-]*$/i';

    /**
     * Renders any page of the documentation folder.
     *
     * Takes the segments of the url below the site root, finds the document they
     * point at and sends it out as a page. A url pointing at a folder is answered
     * with the document inside it, and a url pointing nowhere is answered with the
     * page that says nothing was found.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller\Docs;
     *
     * // A request for /coherence/src/strings
     * (new Docs())->index(['coherence', 'src', 'strings']);
     *
     * // Prints the rendered docs/coherence/src/strings.md
     * ```
     *
     * @param string|array $page Url segments below the documentation folder, as the one
     *  segment or the list of them the router collected (default: [])
     * @return void
     */
    public function index(string|array $page = []): void
    {
        self::renderDocument(self::documentPath((array) $page));
    }

    /**
     * Works out the path of the document a url points at.
     *
     * Walks the segments of the url down the documentation folder and hands back
     * the document they land on. A url pointing at a folder is served by the
     * document inside it, and a url pointing at a page is served by whichever
     * format that page is written in.
     *
     * Nothing outside the documentation folder can be reached this way. A segment
     * that is not a plain file name is refused before it is ever looked for, and
     * the document that is found still has to sit inside the folder once every
     * symbolic link on the way has been followed.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller\Docs;
     *
     * $path = Docs::documentPath(['coherence']);
     *
     * // Returns the path of docs/coherence/readme.md
     * ```
     *
     * @param array $segments Url segments below the documentation folder
     * @return string|null Path of the document, or null when the url leads nowhere it may go
     * @see \Phuture\App\Controller\Docs::isInside()
     */
    protected static function documentPath(array $segments): ?string
    {
        $segments = array_values(array_filter($segments, fn ($segment) => $segment !== ''));

        if ($segments === []) {
            return null;
        }

        if (!array_all($segments, fn ($segment) => preg_match(self::SEGMENT_PATTERN, $segment) === 1)) {
            return null;
        }

        $path = DOCS_DIR . implode(DS, $segments);
        $name = array_pop($segments);

        $document = is_dir($path)
            ? Document::find($path, Document::fileNames(Document::DEFAULT_NAMES))
            : Document::find(dirname($path), Document::fileNames($name));

        $foundName = strtolower(pathinfo((string) $document, PATHINFO_FILENAME));

        if (in_array($foundName, Document::PROTECTED_NAMES, true)) {
            return null;
        }

        return self::isInside($document, DOCS_DIR) ? $document : null;
    }

    /**
     * Tells you whether a file really sits inside a directory.
     *
     * Both paths are resolved first, which follows every symbolic link and every
     * `..` on the way, so a file that only appears to be inside the directory is
     * turned down along with one that never was.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller\Docs;
     *
     * $isInside = Docs::isInside('/var/www/docs/coherence/readme.md', '/var/www/docs/');
     *
     * // Returns true
     * ```
     *
     * @param string|null $path Path of the file to check, which may be null when nothing was found
     * @param string $directory Directory the file has to live in
     * @return bool Returns true when the file sits inside the directory, false otherwise
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

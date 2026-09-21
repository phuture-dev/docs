<?php

namespace Phuture\App\Helper;

/**
 * Finds, reads and names the documents the site is made of.
 *
 * A document is a file of the documentation folder written in one of the formats a
 * page may be written in. Whatever is asked of one without regard for that format
 * lives here: finding it on disk, reading it, turning its path into the url it is
 * served at, and reading the title off it.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Document
{
    /**
     * Longest a title taken out of a document may be, in characters.
     *
     * A document with no heading falls back on its first words, which may run on
     * for a paragraph, so a limit is put on what ends up in the browser tab.
     *
     * @var int
     */
    public const TITLE_LENGTH = 255;

    /**
     * File extensions a page can be written in, in the order they are preferred.
     *
     * A url naming no extension is answered with the first of these that exists,
     * so a page written in both formats is served as markdown.
     *
     * @var array
     */
    public const EXTENSIONS = ['md', 'html'];

    /**
     * Names a document is served under when a url points at the folder holding it.
     *
     * A url such as `/coherence` names a folder rather than a page, and is answered
     * with the document inside it named one of these, in this order.
     *
     * @var array
     */
    public const DEFAULT_NAMES = ['index', 'readme'];

    /**
     * Names of the documents that drive the site rather than being pages of it.
     *
     * The navigation is written as a markdown document like any other, but it is
     * never served as a page and never answers a search.
     *
     * @var array
     */
    public const PROTECTED_NAMES = ['sidebar'];

    /**
     * Contents of the files read so far, keyed by the path they were read from.
     *
     * Filled the first time a file is read and reused for every later read in the
     * same request, because one document is often asked for its title, its text and
     * its html all at once.
     *
     * @var array
     */
    private static array $contents = [];

    /**
     * Finds the first of several file names present in a directory.
     *
     * The names are tried in the order they are given, so the first one that exists
     * wins. Matching ignores upper and lower case, which is what lets a url written
     * in lowercase find a file saved as `CONTRIBUTING.md`.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Document;
     *
     * $path = Document::find('/var/www/docs', ['index.md', 'readme.md']);
     *
     * // Returns '/var/www/docs/index.md' when that file exists
     * ```
     *
     * @param string $directory Directory to look inside
     * @param array $names File names to look for, in the order they are preferred
     * @return string|null Path of the first name that exists, or null when none of them do
     * @see \Phuture\App\Helper\Document::fileNames()
     */
    public static function find(string $directory, array $names): ?string
    {
        if (!is_dir($directory)) {
            return null;
        }

        $files = [];

        foreach ((array) scandir($directory) as $file) {
            if (is_file($directory . DS . $file)) {
                $files[strtolower($file)] = $file;
            }
        }

        foreach ($names as $name) {
            $name = strtolower($name);

            if (isset($files[$name])) {
                return $directory . DS . $files[$name];
            }
        }

        return null;
    }

    /**
     * Builds the file names a document could be saved under.
     *
     * Takes a name with no extension and hands back that name once per format a
     * page may be written in, in the order those formats are preferred. This is
     * what turns one url segment into the list of files worth looking for.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Document;
     *
     * $names = Document::fileNames('index');
     *
     * // Returns ['index.md', 'index.html']
     * ```
     *
     * @param string|array $names Names of the documents, without their extension
     * @return array Every name, once per format a page may be written in
     * @see \Phuture\App\Helper\Document::find()
     */
    public static function fileNames(string|array $names): array
    {
        $files = [];

        foreach ((array) $names as $name) {
            foreach (self::EXTENSIONS as $extension) {
                $files[] = $name . '.' . $extension;
            }
        }

        return $files;
    }

    /**
     * Reads a file exactly as it was written.
     *
     * The contents are kept for the rest of the request, so asking for the same
     * file again costs nothing. A file that is missing, or that the site is not
     * allowed to read, is answered with null rather than with an error.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Document;
     *
     * $markdown = Document::contents('/var/www/docs/index.md');
     *
     * // Returns '# Documentation\n\nWelcome...'
     * ```
     *
     * @param string $path Path of the file to read
     * @return string|null The contents of the file, or null when it cannot be read
     */
    public static function contents(string $path): ?string
    {
        if (isset(self::$contents[$path])) {
            return self::$contents[$path];
        }

        if (!is_file($path) || !is_readable($path)) {
            return null;
        }

        $contents = file_get_contents($path);

        return self::$contents[$path] = ($contents === false ? null : $contents);
    }

    /**
     * Reads a document and hands it back as the html of a page.
     *
     * Each format is read by the helper that knows it: markdown is converted, and
     * html is unwrapped so that only what lives inside its body ends up on the page.
     * Anything written in a format the site does not serve is answered with null.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Document;
     *
     * $html = Document::file('/var/www/docs/index.md');
     *
     * // Returns '<h1>Documentation</h1>...'
     * ```
     *
     * @param string $path Path of the document to read
     * @return string|null The html of the page, or null when the document cannot be read or parsed
     * @see \Phuture\App\Helper\Document::plain()
     */
    public static function file(string $path): ?string
    {
        return match (self::extension($path)) {
            'md' => Markdown::file($path),
            'html' => Html::file($path),
            default => null,
        };
    }

    /**
     * Reads a document and hands it back as plain text.
     *
     * Everything that is markup rather than words is taken away, leaving what a
     * reader would see on the page. This is what a search is answered out of, since
     * nobody searches for the angle brackets around a heading.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Document;
     *
     * $text = Document::plain('/var/www/docs/index.md');
     *
     * // Returns 'Documentation Welcome to the documentation...'
     * ```
     *
     * @param string $path Path of the document to read
     * @return string|null The text of the page, or null when the document cannot be read or parsed
     * @see \Phuture\App\Helper\Document::file()
     */
    public static function plain(string $path): ?string
    {
        return match (self::extension($path)) {
            'md' => Markdown::plain($path),
            'html' => Html::plain($path),
            default => null,
        };
    }

    /**
     * Works out the url a document inside the documentation is served under.
     *
     * The path below the documentation folder becomes the path of the url, with the
     * extension left off. A document named `index` or `readme` is what its folder is
     * served by, so its own name is left off too and the folder answers for it.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Document;
     *
     * $url = Document::url('/var/www/docs/coherence/readme.md');
     *
     * // Returns '/coherence'
     * ```
     *
     * @param string $path Path of the document on disk
     * @return string The url the document is served under, opening with a slash
     */
    public static function url(string $path): string
    {
        $root = rtrim((string) realpath(DOCS_DIR), DS);
        $segments = explode(DS, trim(substr($path, strlen($root)), DS));
        $name = strtolower(pathinfo((string) array_pop($segments), PATHINFO_FILENAME));

        if (!in_array($name, self::DEFAULT_NAMES, true)) {
            $segments[] = $name;
        }

        return '/' . implode('/', $segments);
    }

    /**
     * Reads the title of a document out of the document itself.
     *
     * Each format is read by the helper that knows it, and both take the first
     * heading of the page as its title, falling back on its first words when it
     * carries no heading at all.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Document;
     *
     * $title = Document::title('/var/www/docs/index.md');
     *
     * // Returns 'Documentation'
     * ```
     *
     * @param string $path Path of the document to read
     * @param int $length Longest the title may be, in characters (default: 255)
     * @return string|null The title of the page, or null when the document holds no text at all
     */
    public static function title(string $path, int $length = self::TITLE_LENGTH): ?string
    {
        return match (self::extension($path)) {
            'md' => Markdown::title($path, $length),
            'html' => Html::title($path, $length),
            default => null,
        };
    }

    /**
     * Reads the extension of a file, lowercased.
     *
     * The extension is the part of the name after its last dot, and lowercasing it
     * means a file saved as `README.MD` is read as the markdown it is.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Document;
     *
     * $extension = Document::extension('/var/www/docs/README.MD');
     *
     * // Returns 'md'
     * ```
     *
     * @param string $path Path of the file to read the extension of
     * @return string The extension without its dot, or an empty string when the name carries none
     */
    public static function extension(string $path): string
    {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * Cuts a title down to length without leaving half a word behind.
     *
     * Runs of spaces and line breaks are squeezed into single spaces first, so a
     * title taken off several lines reads as one. A title already short enough is
     * handed back as it is, and a longer one is cut at the last whole word that fits.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Document;
     *
     * $title = Document::shorten('A coherent collection of helpers', 20);
     *
     * // Returns 'A coherent'
     * ```
     *
     * @param string $title Title to shorten
     * @param int $length Longest the title may be, in characters (default: 255)
     * @return string The title, no longer than the length allowed and never cut mid-word
     */
    public static function shorten(string $title, int $length = self::TITLE_LENGTH): string
    {
        $title = trim(preg_replace('/\s+/u', ' ', $title) ?? $title);

        if (mb_strlen($title) <= $length) {
            return $title;
        }

        $title = mb_substr($title, 0, $length);
        $lastSpace = mb_strrpos($title, ' ');

        return rtrim($lastSpace !== false ? mb_substr($title, 0, $lastSpace) : $title);
    }
}

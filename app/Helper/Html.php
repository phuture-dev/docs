<?php

namespace Phuture\App\Helper;

use Throwable;
use Dom\HTMLDocument;

/**
 * Reads a page written as html.
 *
 * A document brought in from somebody else's repository is not always markdown.
 * What such a page holds is read the same way as any other: the html inside its
 * body, the text with the markup taken off, and the title it carries.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Html
{
    /**
     * Selectors a title is taken from, in the order they are tried.
     *
     * A heading is what a reader sees at the top of the page, so it is asked for
     * first. Failing that comes the title of the document itself, then its first
     * paragraph, and finally whatever text the page holds at all.
     *
     * @var array
     */
    protected const TITLE_ELEMENTS = ['h1, h2, h3, h4, h5, h6', 'title', 'p', 'body'];

    /**
     * Elements whose contents are code for the browser rather than words for a reader.
     *
     * Their contents are taken out before a page is read as plain text, which
     * keeps a stylesheet or a script out of what a search is answered with.
     *
     * @var string
     */
    protected const CODE_PATTERN = '#<(script|style)\b[^>]*>.*?</\1>#is';

    /**
     * Reads the contents of an html file.
     *
     * A whole document is unwrapped, so that only what lives inside its body ends
     * up on the page and its own head is left behind. A fragment, which is a piece
     * of html with no body of its own, is handed back as it was written.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Html;
     *
     * $contents = Html::file('/var/www/docs/notes.html');
     *
     * // Returns '<h1>Notes</h1>' for a file whose body holds that heading
     * ```
     *
     * @param string $path Path of the html file to read
     * @return string|null The html inside the body, or null when the file cannot be read
     * @see \Phuture\App\Helper\Html::plain()
     */
    public static function file(string $path): ?string
    {
        $html = Document::contents($path);

        if ($html === null) {
            return null;
        }

        if (preg_match('/<body\b[^>]*>(.*)<\/body>/is', $html, $body)) {
            return trim($body[1]);
        }

        return $html;
    }

    /**
     * Reads the contents of an html file as plain text.
     *
     * Everything between the angle brackets is taken away, leaving the words a
     * reader would see. Scripts and stylesheets go first, contents and all, so
     * that none of the code they hold is mistaken for text. Whatever the document
     * writes as an entity, such as `&amp;`, is turned back into the character it
     * stands for.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Html;
     *
     * $text = Html::plain('/var/www/docs/notes.html');
     *
     * // Returns 'Notes' for a file whose body holds '<h1>Notes</h1>'
     * ```
     *
     * @param string $path Path of the html file to read
     * @return string|null The text of the page, or null when the file cannot be read
     * @see \Phuture\App\Helper\Html::file()
     */
    public static function plain(string $path): ?string
    {
        $html = self::file($path);

        if ($html === null) {
            return null;
        }

        $text = strip_tags((string) preg_replace(self::CODE_PATTERN, ' ', $html));

        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Reads the title of an html file out of the file itself.
     *
     * The first heading of the page is taken as its title, because that is what a
     * reader sees at the top of it. A page with no heading falls back on its title
     * element, then on its first paragraph, and then on any text at all, and a
     * title longer than it is allowed to be is cut short at a word.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Html;
     *
     * $title = Html::title('/var/www/docs/notes.html');
     *
     * // Returns 'Notes' for a file whose body holds '<h1>Notes</h1>'
     * ```
     *
     * @param string $path Path of the html file to read
     * @param int $length Longest the title may be, in characters (default: 255)
     * @return string|null The title of the page, or null when the file holds no text at all
     * @see \Phuture\App\Helper\Markdown::title()
     */
    public static function title(string $path, int $length = Document::TITLE_LENGTH): ?string
    {
        $html = Document::contents($path);

        if ($html === null) {
            return null;
        }

        try {
            $document = HTMLDocument::createFromString($html, LIBXML_NOERROR);
        } catch (Throwable) {
            return null;
        }

        foreach (self::TITLE_ELEMENTS as $selector) {
            $title = Document::shorten((string) $document->querySelector($selector)?->textContent, $length);

            if ($title !== '') {
                return $title;
            }
        }

        return null;
    }
}

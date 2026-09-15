<?php

namespace Phuture\App\Helper;

use Throwable;
use Dom\HTMLDocument;

class Html
{
    /**
     * Selectors a title is taken from, in order of preference
     */
    protected const TITLE_ELEMENTS = ['h1, h2, h3, h4, h5, h6', 'title', 'p', 'body'];

    /**
     * Contents of an html file, or null when it cannot be read
     *
     * Whole documents are unwrapped, so only what lives inside the body ends up
     * on the page, while fragments are served as they are.
     *
     * @param string $path Path of the html file
     * @return string|null
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
     * Title of an html file, taken from its first heading, its title element, or its first line of text
     *
     * @param string $path Path of the html file
     * @param int $length Maximum length of the title
     * @return string|null
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

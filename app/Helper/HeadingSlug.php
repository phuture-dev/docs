<?php

namespace Phuture\App\Helper;

use League\CommonMark\Normalizer\TextNormalizerInterface;

/**
 * Gives a heading the anchor GitHub would have given it.
 *
 * Documents written for a repository link between their own headings by the
 * anchors GitHub builds, so the anchors here are built the same way: the text is
 * lowercased, punctuation and symbols are dropped, and every remaining space
 * becomes a dash.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class HeadingSlug implements TextNormalizerInterface
{
    /**
     * Characters a slug is allowed to keep.
     *
     * Letters, digits, marks, underscores, spaces and dashes of any alphabet.
     * Everything outside this set is punctuation, a symbol or an emoji, none of
     * which GitHub keeps in the anchor it gives a heading.
     *
     * @var string
     */
    protected const KEPT = '\p{L}\p{Nd}\p{Nl}\p{M}_\s-';

    /**
     * Turns the text of a heading into the anchor GitHub would have given it.
     *
     * An anchor is the part of a link after the hash sign, the piece that scrolls
     * the page to one heading rather than to its top. Documents written for a
     * repository link between their own headings that way, so the anchors here
     * are built the way GitHub builds them: the text is lowercased, punctuation
     * and symbols are dropped, and every remaining space becomes a dash.
     *
     * The order matters. Punctuation goes before the spaces are turned into
     * dashes, and a run of several spaces leaves a dash for each of them, which
     * is what the anchors already written in those documents were made against.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\HeadingSlug;
     *
     * $anchor = (new HeadingSlug())->normalize('What is not covered?');
     *
     * // Returns 'what-is-not-covered'
     * ```
     *
     * @param string $text Text of the heading to build an anchor out of
     * @param array $context Prefix, maximum length and node the text belongs to, as the
     *  caller knows them. A `prefix` is put in front of the text before anything else happens, and a
     *  `length` greater than zero cuts the finished anchor down to that many characters (default: [])
     * @return string The anchor the heading is reachable at, without its hash sign
     */
    public function normalize(string $text, array $context = []): string
    {
        $slug = ($context['prefix'] ?? '') . $text;
        $slug = mb_strtolower(trim($slug), 'UTF-8');
        $slug = preg_replace('/[^' . self::KEPT . ']+/u', '', $slug) ?? $slug;
        $slug = preg_replace('/\s/u', '-', $slug) ?? $slug;

        $length = $context['length'] ?? 0;

        return $length > 0 ? mb_substr($slug, 0, $length, 'UTF-8') : $slug;
    }
}

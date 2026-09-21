<?php

namespace Phuture\App\Helper;

use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Block\{ListBlock, ListItem};

/**
 * Reads the navigation of the site out of the markdown it is written in.
 *
 * The navigation is a document of nested links, kept beside the pages themselves
 * so that it is written and reviewed the way they are. It is read into the entries
 * a template walks, with the entry of the page being served, and every entry above
 * it, marked as the one a reader is on.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Sidebar
{
    /**
     * Name of the file the navigation is read from.
     *
     * A markdown document holding nothing but a list of links, nested as deep as
     * the navigation goes. It sits at the root of the documentation folder and is
     * never served as a page of its own.
     *
     * @var string
     */
    public const SOURCE = 'sidebar.md';

    /**
     * Navigation trees read so far, keyed by the path they were read from.
     *
     * Filled the first time a tree is asked for and reused for every later ask in
     * the same request, so the navigation file is parsed once however many times
     * the page needs it.
     *
     * @var array
     */
    private static array $trees = [];

    /**
     * Reads the navigation and marks the entry of the page being served.
     *
     * Hands back the whole navigation as a tree of entries, each with the text it
     * shows, the url it points at and the entries nested under it. The entry of the
     * page being served is marked as the current one, and every group holding it is
     * marked as open, which is what leaves the sidebar unfolded around it.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Sidebar;
     *
     * $tree = Sidebar::tree('/coherence/src/strings');
     *
     * // Returns the navigation, with the Coherence group marked open
     * ```
     *
     * @param string $currentPath Path of the request being served, such as `/coherence`
     * @param string|null $path Path of the markdown file the navigation is read from, or null for
     *  the one at the root of the documentation folder (default: null)
     * @return array The navigation, as entries holding `label`, `url`,
     *  `children`, `active` and `open`
     */
    public static function tree(string $currentPath, ?string $path = null): array
    {
        return self::markCurrent(self::items($path), rtrim($currentPath, '/') ?: '/');
    }

    /**
     * Reads the navigation out of a markdown list of links.
     *
     * The file is parsed once and kept for the rest of the request, because every
     * page of the site shows the same navigation and parsing it again would answer
     * the same way. A file that cannot be read leaves the navigation empty rather
     * than stopping the page.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Sidebar;
     *
     * $items = Sidebar::items();
     *
     * // Returns the navigation with no entry marked as current
     * ```
     *
     * @param string|null $path Path of the markdown file the navigation is read from, or null for
     *  the one at the root of the documentation folder (default: null)
     * @return array The navigation, as entries holding `label`, `url` and
     *  `children`
     */
    private static function items(?string $path = null): array
    {
        $path = $path ?? DOCS_DIR . self::SOURCE;

        if (isset(self::$trees[$path])) {
            return self::$trees[$path];
        }

        $document = Markdown::parse((string) Document::contents($path));
        $items = [];

        foreach ($document?->children() ?? [] as $node) {
            if ($node instanceof ListBlock) {
                $items = array_merge($items, self::parseList($node));
            }
        }

        return self::$trees[$path] = $items;
    }

    /**
     * Marks the entry of the page being served, and every group holding it.
     *
     * Walks the whole navigation and marks as active the one entry whose url is the
     * page being served. A group counts as open when it is that entry or when any
     * entry below it is open, so the sidebar arrives unfolded all the way down to
     * the page a reader is on.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Sidebar;
     *
     * $marked = Sidebar::markCurrent($items, '/coherence');
     *
     * // Returns the same entries, with the Coherence one marked active
     * ```
     *
     * @param array $items Entries of the navigation to mark
     * @param string $currentPath Path of the request being served, without its trailing slash
     * @return array The same entries, each carrying `active` and `open`
     */
    private static function markCurrent(array $items, string $currentPath): array
    {
        foreach ($items as &$item) {
            $item['children'] = self::markCurrent($item['children'], $currentPath);
            $item['active'] = $item['url'] !== '' && (rtrim($item['url'], '/') ?: '/') === $currentPath;
            $item['open'] = $item['active'] || array_any($item['children'], fn ($child) => $child['open']);
        }

        return $items;
    }

    /**
     * Turns a markdown list into navigation entries.
     *
     * Every item of the list becomes one entry, and an item holding nothing a
     * reader could see or follow is left out rather than shown as a blank row.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Sidebar;
     *
     * $items = Sidebar::parseList($listBlock);
     *
     * // Returns one entry for every item of the list
     * ```
     *
     * @param \League\CommonMark\Extension\CommonMark\Node\Block\ListBlock $list List to read from
     * @return array The entries the list holds
     * @see \Phuture\App\Helper\Sidebar::parseItem()
     */
    private static function parseList(ListBlock $list): array
    {
        $items = [];

        foreach ($list->children() as $item) {
            $entry = $item instanceof ListItem ? self::parseItem($item) : null;

            if ($entry !== null) {
                $items[] = $entry;
            }
        }

        return $items;
    }

    /**
     * Turns a single list item into one navigation entry.
     *
     * The link in the item gives the entry its url, and the text of that link gives
     * it the words a reader sees. An item with text but no link becomes an entry
     * that leads nowhere, which is how a heading in the navigation is written, and a
     * list nested under the item becomes the entries below it.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Sidebar;
     *
     * $entry = Sidebar::parseItem($listItem);
     *
     * // Returns ['label' => 'Coherence', 'url' => '/coherence', 'children' => [...]]
     * ```
     *
     * @param \League\CommonMark\Extension\CommonMark\Node\Block\ListItem $item List item to read from
     * @return array The entry holding `label`, `url` and `children`, or null when the item holds
     *  neither words nor entries below it
     * @see \Phuture\App\Helper\Sidebar::parseList()
     */
    private static function parseItem(ListItem $item): ?array
    {
        $entry = ['label' => '', 'url' => '', 'children' => []];

        foreach ($item->children() as $child) {
            if ($child instanceof ListBlock) {
                $entry['children'] = array_merge($entry['children'], self::parseList($child));

                continue;
            }

            if (!$child instanceof Paragraph) {
                continue;
            }

            $link = null;

            foreach ($child->children() as $inline) {
                if ($inline instanceof Link) {
                    $link = $inline;

                    break;
                }
            }

            $entry['url'] = $link instanceof Link ? $link->getUrl() : '';
            $entry['label'] = Markdown::text($link ?? $child) ?: $entry['url'];
        }

        return $entry['label'] === '' && $entry['children'] === [] ? null : $entry;
    }
}

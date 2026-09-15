<?php

namespace Phuture\App\Helper;

use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\CommonMark\Node\Block\{ListBlock, ListItem};

class Sidebar
{
    /**
     * File the navigation is read from
     */
    public const SOURCE = 'sidebar.md';

    /**
     * Navigation trees parsed so far, keyed by source file
     */
    private static array $trees = [];

    /**
     * Navigation tree flagged against the path being served
     *
     * @param string $currentPath Path of the request being served
     * @param string|null $path Path of the markdown file the navigation is read from
     * @return array
     */
    public static function tree(string $currentPath, ?string $path = null): array
    {
        return self::markCurrent(self::items($path), rtrim($currentPath, '/') ?: '/');
    }

    /**
     * Build the navigation tree out of a markdown list of links
     *
     * @param string|null $path Path of the markdown file the navigation is read from
     * @return array
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
     * Mark the entry of the current page, and every group holding it
     *
     * @param array $items Entries of the navigation tree
     * @param string $currentPath Path of the request being served
     * @return array
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
     * Turn a list block into label/url/children entries
     *
     * @param ListBlock $list List block to read the entries from
     * @return array
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
     * Turn a single list item into a label/url/children entry, or null when it holds neither
     *
     * @param ListItem $item List item to read the entry from
     * @return array|null
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

            $link = array_find($child->children(), fn ($inline) => $inline instanceof Link);

            $entry['url'] = $link instanceof Link ? $link->getUrl() : '';
            $entry['label'] = Markdown::text($link ?? $child) ?: $entry['url'];
        }

        return $entry['label'] === '' && $entry['children'] === [] ? null : $entry;
    }
}

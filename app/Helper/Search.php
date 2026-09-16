<?php

namespace Phuture\App\Helper;

use SplFileInfo;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;

class Search
{
    /**
     * File the index is kept in between requests
     */
    public const INDEX_FILE = 'search.json';

    /**
     * Shape of the index, bumped whenever what goes into it changes
     */
    public const INDEX_VERSION = 1;

    /**
     * Most results a search hands back
     */
    public const RESULTS = 20;

    /**
     * Longest piece of a document quoted around a match
     */
    public const SNIPPET_LENGTH = 180;

    /**
     * Shortest query worth looking anything up for
     */
    public const MINIMUM_LENGTH = 2;

    /**
     * Weight a match carries, by where in a document it was found
     */
    protected const WEIGHTS = ['title' => 100, 'heading' => 25, 'text' => 1];

    /**
     * Index read so far this request
     */
    private static ?array $index = null;

    /**
     * Documents matching a query, best match first
     *
     * Every word of the query has to show up somewhere in a document for it to
     * be an answer at all, and where each of them shows up is what orders the
     * answers: a title counts for more than a heading, and a heading for more
     * than a line somewhere down the page.
     *
     * @param string $query Words to look for
     * @param int $limit Most results to hand back
     * @return array
     */
    public static function results(string $query, int $limit = self::RESULTS): array
    {
        $words = self::words($query);

        if ($words === []) {
            return [];
        }

        $results = [];

        foreach (self::index() as $entry) {
            $score = self::score($entry, $words);

            if ($score > 0) {
                $entry['score'] = $score;
                $entry['anchor'] = self::anchor($entry, $words);
                $entry['snippet'] = self::snippet($entry['text'], $words);

                unset($entry['text']);

                $results[] = $entry;
            }
        }

        // Alike scores keep the order the documentation reads in
        usort($results, fn ($a, $b) => [$b['score'], $a['url']] <=> [$a['score'], $b['url']]);

        return array_slice($results, 0, max(1, $limit));
    }

    /**
     * Words a query is made of, lowercased
     *
     * @param string $query Query as it was typed
     * @return array
     */
    public static function words(string $query): array
    {
        $words = preg_split('/\s+/u', mb_strtolower(trim($query))) ?: [];

        return array_values(array_filter($words, fn ($word) => mb_strlen($word) >= self::MINIMUM_LENGTH));
    }

    /**
     * Index of every document the site serves, built once and kept on disk
     *
     * @return array
     */
    public static function index(): array
    {
        if (self::$index !== null) {
            return self::$index;
        }

        $documents = self::documents();
        $cache = CACHE_DIR . self::INDEX_FILE;
        $fingerprint = self::fingerprint($documents);
        $cached = is_file($cache) ? json_decode((string) file_get_contents($cache), true) : null;

        if (is_array($cached) && ($cached['fingerprint'] ?? null) === $fingerprint) {
            return self::$index = $cached['entries'];
        }

        $entries = array_values(array_filter(array_map(self::entry(...), $documents)));

        @file_put_contents($cache, (string) json_encode(['fingerprint' => $fingerprint, 'entries' => $entries]));

        return self::$index = $entries;
    }

    /**
     * Every document of the documentation, in the order their paths read
     *
     * @return array
     */
    protected static function documents(): array
    {
        if (!is_dir(DOCS_DIR)) {
            return [];
        }

        $documents = [];

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(rtrim(DOCS_DIR, DS), FilesystemIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $name = mb_strtolower($file->getBasename('.' . $file->getExtension()));

            if (!$file->isFile() || !in_array(mb_strtolower($file->getExtension()), Document::EXTENSIONS, true)) {
                continue;
            }

            // Whatever drives the site is no page of it, and has nothing to answer with
            if (!in_array($name, Document::PROTECTED_NAMES, true)) {
                $documents[] = $file->getPathname();
            }
        }

        sort($documents);

        return $documents;
    }

    /**
     * What the index holds about one document, or null when it cannot be read
     *
     * @param string $path Path of the document
     * @return array|null
     */
    protected static function entry(string $path): ?array
    {
        $text = Document::plain($path);

        if ($text === null) {
            return null;
        }

        return [
            'url' => Document::url($path),
            'title' => Document::title($path) ?? basename($path),
            'headings' => self::headings($path),
            'text' => trim((string) preg_replace('/\s+/u', ' ', $text)),
        ];
    }

    /**
     * Headings a document carries, each with the anchor it is reachable at
     *
     * @param string $path Path of the document
     * @return array
     */
    protected static function headings(string $path): array
    {
        $markdown = Document::extension($path) === 'md' ? Document::contents($path) : null;
        $document = $markdown === null ? null : Markdown::parse($markdown);
        $slug = new HeadingSlug();
        $headings = [];

        foreach ($document?->iterator() ?? [] as $node) {
            if (!$node instanceof Heading) {
                continue;
            }

            $text = Markdown::text($node);

            if ($text !== '') {
                $headings[] = ['text' => $text, 'anchor' => $slug->normalize($text)];
            }
        }

        return $headings;
    }

    /**
     * What a document is worth as an answer to a query, or zero when it is none
     *
     * @param array $entry Document as the index holds it
     * @param array $words Words of the query
     * @return int
     */
    protected static function score(array $entry, array $words): int
    {
        $title = mb_strtolower($entry['title']);
        $headings = mb_strtolower(implode("\n", array_column($entry['headings'], 'text')));
        $text = mb_strtolower($entry['text']);
        $score = 0;

        foreach ($words as $word) {
            $found = 0;

            foreach (['title' => $title, 'heading' => $headings, 'text' => $text] as $where => $haystack) {
                if (str_contains($haystack, $word)) {
                    $found += self::WEIGHTS[$where];
                }
            }

            // A word nowhere in the document leaves it no answer to the query
            if ($found === 0) {
                return 0;
            }

            $score += $found;
        }

        return $score;
    }

    /**
     * Anchor of the heading a query is answered under, or null when it is the page itself
     *
     * @param array $entry Document as the index holds it
     * @param array $words Words of the query
     * @return string|null
     */
    protected static function anchor(array $entry, array $words): ?string
    {
        foreach ($entry['headings'] as $heading) {
            $text = mb_strtolower($heading['text']);

            foreach ($words as $word) {
                if (!str_contains($text, $word)) {
                    continue 2;
                }
            }

            return $heading['anchor'];
        }

        return null;
    }

    /**
     * Piece of a document quoted around the first word of the query found in it
     *
     * @param string $text Text of the document, on one line
     * @param array $words Words of the query
     * @param int $length Longest piece to quote
     * @return string
     */
    protected static function snippet(string $text, array $words, int $length = self::SNIPPET_LENGTH): string
    {
        $at = null;

        foreach ($words as $word) {
            $found = mb_stripos($text, $word);

            if ($found !== false && ($at === null || $found < $at)) {
                $at = $found;
            }
        }

        if ($at === null) {
            return Document::shorten($text, $length);
        }

        // Opened a little ahead of the word, so that it is read in its sentence
        $start = max(0, $at - (int) ($length / 3));
        $piece = mb_substr($text, $start, $length);

        return ($start > 0 ? '…' : '') . trim($piece) . (mb_strlen($text) > $start + $length ? '…' : '');
    }

    /**
     * Mark of the documentation as it stands, which a stale index will not carry
     *
     * @param array $documents Paths of every document
     * @return string
     */
    protected static function fingerprint(array $documents): string
    {
        $marks = array_map(fn ($path) => $path . ':' . filemtime($path), $documents);

        return hash('xxh128', self::INDEX_VERSION . "\n" . implode("\n", $marks));
    }
}

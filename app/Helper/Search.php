<?php

namespace Phuture\App\Helper;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;

/**
 * Indexes the documentation and answers a search over it.
 *
 * Every document is read once into an index kept in the cache, holding the url,
 * the title, the headings and all of the text of each. A query is scored against
 * that index, and every result carries the heading to open the page at and a piece
 * of its text with the words that were looked for in it.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Search
{
    /**
     * Name of the file the index is kept in between requests.
     *
     * Reading every document of the site takes about a second, which is far too
     * long to do while a reader waits, so the finished index is written here and
     * read back on every later search.
     *
     * @var string
     */
    public const INDEX_FILE = 'search.json';

    /**
     * Shape of the index, raised whenever what goes into it changes.
     *
     * An index written by an older shape of this class holds different things and
     * cannot be trusted, even when not one document has changed since. Raising this
     * number leaves every such index stale and has it built again.
     *
     * @var int
     */
    public const INDEX_VERSION = 1;

    /**
     * Most results a search hands back.
     *
     * Nobody reads past the first page of results, and cutting the list short keeps
     * the answer small enough to send while a reader is still typing.
     *
     * @var int
     */
    public const RESULTS = 20;

    /**
     * Longest piece of a document quoted around a match, in characters.
     *
     * Long enough to read the matched word in its own sentence, short enough that
     * a result stays one or two lines on the page.
     *
     * @var int
     */
    public const SNIPPET_LENGTH = 180;

    /**
     * Shortest query worth looking anything up for, in characters.
     *
     * A single letter matches most of the documentation and answers with noise, so
     * nothing shorter than this is searched for at all.
     *
     * @var int
     */
    public const MINIMUM_LENGTH = 2;

    /**
     * What a match is worth, by where in a document it was found.
     *
     * A word in the title says the page is about that word, a word in a heading says
     * a section of it is, and a word in the text says only that it is mentioned. The
     * numbers are far apart so that one title match outweighs any amount of mentions.
     *
     * Kept as what a match is worth, keyed by `title`, `heading` and `text`.
     *
     * @var array
     */
    protected const WEIGHTS = ['title' => 100, 'heading' => 25, 'text' => 1];

    /**
     * Index read so far this request.
     *
     * Filled the first time a search is made and reused for every later one, so the
     * index file is read from disk once however many searches a request answers.
     *
     * Kept as the entries of the index, or null while it has not been read.
     *
     * @var array|null
     */
    private static ?array $index = null;

    /**
     * Finds the documents matching a query, best match first.
     *
     * Every word of the query has to show up somewhere in a document for it to be an
     * answer at all, so a search for two words finds the pages holding both rather
     * than the pages holding either. Where each word shows up is what orders the
     * answers: a title counts for more than a heading, and a heading for more than a
     * line somewhere down the page.
     *
     * Each result carries the anchor of the first heading matching the whole query,
     * which is what lets a result open the page at the section it was found in.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Search;
     *
     * $results = Search::results('random string', 3);
     *
     * // Returns the three pages best answering both words
     * ```
     *
     * @param string $query Words to look for, as the reader typed them
     * @param int $limit Most results to hand back (default: 20)
     * @return array The matching documents, each holding `url`, `title`, `headings`, `score`,
     *  `anchor` and `snippet`, best match first
     * @see \Phuture\App\Helper\Search::index()
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

        usort($results, fn ($first, $second) => [$second['score'], $first['url']] <=> [$first['score'], $second['url']]);

        return array_slice($results, 0, max(1, $limit));
    }

    /**
     * Breaks a query into the words it is made of.
     *
     * The query is lowercased, because a search for `Strings` should find `strings`,
     * and split on the spaces between its words. Anything too short to search for is
     * dropped rather than matching half the documentation.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Search;
     *
     * $words = Search::words('  Random   String a ');
     *
     * // Returns ['random', 'string']
     * ```
     *
     * @param string $query Query as the reader typed it
     * @return array The words worth looking for, as a list of lowercased words
     */
    public static function words(string $query): array
    {
        $words = preg_split('/\s+/u', mb_strtolower(trim($query))) ?: [];

        return array_values(array_filter($words, fn ($word) => mb_strlen($word) >= self::MINIMUM_LENGTH));
    }

    /**
     * Reads the index of every document the site serves, building it when it is stale.
     *
     * The index holds what each document is called, what it is about and where it
     * lives, which is everything a search needs and far less than the documents
     * themselves. It is kept on disk and rebuilt only when a document has changed
     * since it was written, so the cost of reading the whole site is paid once.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Search;
     *
     * $index = Search::index();
     *
     * // Returns one entry for every page of the site
     * ```
     *
     * @return array Every document, each holding `url`, `title`, `headings` and `text`
     * @see \Phuture\App\Helper\Search::results()
     */
    public static function index(): array
    {
        if (self::$index !== null) {
            return self::$index;
        }

        $documents = self::documents();
        $cachePath = CACHE_DIR . self::INDEX_FILE;
        $fingerprint = self::fingerprint($documents);
        $cached = is_file($cachePath) ? json_decode((string) file_get_contents($cachePath), true) : null;

        $entries = is_array($cached) && ($cached['fingerprint'] ?? null) === $fingerprint
            ? $cached['entries'] ?? null
            : null;

        if (is_array($entries)) {
            return self::$index = $entries;
        }

        $entries = array_values(array_filter(array_map(self::entry(...), $documents)));

        @file_put_contents($cachePath, (string) json_encode(['fingerprint' => $fingerprint, 'entries' => $entries]));

        return self::$index = $entries;
    }

    /**
     * Lists every document of the documentation, in the order their paths read.
     *
     * Walks the whole documentation folder, however deeply its folders nest, and
     * keeps the files written in a format the site serves. Whatever drives the site
     * rather than being a page of it, such as the navigation, is left out: it is no
     * page and has nothing to answer a search with.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Search;
     *
     * $documents = Search::documents();
     *
     * // Returns ['/var/www/docs/code_of_conduct.md', '/var/www/docs/coherence/readme.md', ...]
     * ```
     *
     * @return array Paths of every document worth indexing, as a sorted list of paths
     * @see \Phuture\App\Helper\Directory::files()
     */
    protected static function documents(): array
    {
        $documents = [];

        foreach (Directory::files(rtrim(DOCS_DIR, DS)) as $path) {
            $name = mb_strtolower(pathinfo($path, PATHINFO_FILENAME));

            if (!in_array(Document::extension($path), Document::EXTENSIONS, true)) {
                continue;
            }

            if (!in_array($name, Document::PROTECTED_NAMES, true)) {
                $documents[] = $path;
            }
        }

        return $documents;
    }

    /**
     * Reads what the index holds about one document.
     *
     * Everything a search needs: where the page lives, what it is called, the
     * headings it carries and all of its text on a single line. The text is squeezed
     * down to single spaces so that a piece quoted out of it reads as a sentence
     * rather than as the shape it had on the page.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Search;
     *
     * $entry = Search::entry('/var/www/docs/coherence/readme.md');
     *
     * // Returns ['url' => '/coherence', 'title' => 'Phuture Coherence', ...]
     * ```
     *
     * @param string $path Path of the document to read
     * @return array The entry holding `url`, `title`, `headings` and `text`, or null when the
     *  document cannot be read
     * @see \Phuture\App\Helper\Search::headings()
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
     * Lists the headings a document carries, each with the anchor it is reachable at.
     *
     * An anchor is the part of a link after the hash sign, the piece that opens a
     * page at one section rather than at its top. The anchors are built the way the
     * page builds its own, so a result can point straight at the section it matched.
     *
     * Only markdown is read for headings. A page written as html is still searched,
     * but its results open it at the top.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Search;
     *
     * $headings = Search::headings('/var/www/docs/coherence/readme.md');
     *
     * // Returns [['text' => 'Introduction', 'anchor' => 'introduction'], ...]
     * ```
     *
     * @param string $path Path of the document to read
     * @return array Every heading, each holding `text` and `anchor`
     * @see \Phuture\App\Helper\HeadingSlug::normalize()
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
     * Works out what a document is worth as an answer to a query.
     *
     * Each word of the query is looked for in the title, in the headings and in the
     * text, and what it is worth where is added up. A word found nowhere in the
     * document leaves it no answer at all, whatever the other words were worth,
     * which is what makes a search for several words narrow rather than widen it.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Search;
     *
     * $score = Search::score($entry, ['singleton']);
     *
     * // Returns 126 for a page whose title, headings and text all hold the word
     * ```
     *
     * @param array $entry Document as the index holds it, holding `url`, `title`, `headings` and `text`
     * @param array $words Words of the query, as a list of lowercased words
     * @return int What the document is worth as an answer, or zero when it is none
     */
    protected static function score(array $entry, array $words): int
    {
        $haystacks = [
            'title' => mb_strtolower($entry['title']),
            'heading' => mb_strtolower(implode("\n", array_column($entry['headings'], 'text'))),
            'text' => mb_strtolower($entry['text']),
        ];

        $score = 0;

        foreach ($words as $word) {
            $worth = 0;

            foreach ($haystacks as $where => $haystack) {
                if (str_contains($haystack, $word)) {
                    $worth += self::WEIGHTS[$where];
                }
            }

            if ($worth === 0) {
                return 0;
            }

            $score += $worth;
        }

        return $score;
    }

    /**
     * Finds the anchor of the heading a query is answered under.
     *
     * The first heading holding every word of the query is the section the reader is
     * after, so a result opens the page there. A page whose headings hold no such
     * section is opened at the top instead.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Search;
     *
     * $anchor = Search::anchor($entry, ['uuid']);
     *
     * // Returns 'isuuid' for a page carrying that heading
     * ```
     *
     * @param array $entry Document as the index holds it, holding `url`, `title`, `headings` and `text`
     * @param array $words Words of the query, as a list of lowercased words
     * @return string|null The anchor to open the page at, or null when it is the page itself
     */
    protected static function anchor(array $entry, array $words): ?string
    {
        foreach ($entry['headings'] as $heading) {
            $text = mb_strtolower($heading['text']);

            if (array_all($words, fn ($word) => str_contains($text, $word))) {
                return $heading['anchor'];
            }
        }

        return null;
    }

    /**
     * Quotes the piece of a document the query was found in.
     *
     * The quote opens a little ahead of the earliest word found, so that the word is
     * read in its own sentence rather than at the very start of the line. An ellipsis
     * is written wherever the quote opens or closes mid-sentence, and a document in
     * which none of the words can be found is quoted from its beginning.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Search;
     *
     * $snippet = Search::snippet('Generates a random string of characters', ['random'], 24);
     *
     * // Returns 'Generates a random strin…'
     * ```
     *
     * @param string $text Text of the document, squeezed onto one line
     * @param array $words Words of the query, as a list of lowercased words
     * @param int $length Longest the quote may be, in characters (default: 180)
     * @return string The piece of the document to show under the result
     */
    protected static function snippet(string $text, array $words, int $length = self::SNIPPET_LENGTH): string
    {
        $earliest = null;

        foreach ($words as $word) {
            $found = mb_stripos($text, $word);

            if ($found !== false && ($earliest === null || $found < $earliest)) {
                $earliest = $found;
            }
        }

        if ($earliest === null) {
            return Document::shorten($text, $length);
        }

        $start = max(0, $earliest - (int) ($length / 3));
        $piece = mb_substr($text, $start, $length);

        return ($start > 0 ? '…' : '') . trim($piece) . (mb_strlen($text) > $start + $length ? '…' : '');
    }

    /**
     * Takes a mark of the documentation as it stands.
     *
     * Every document and the moment it last changed are hashed together into one
     * short string, together with the path the site is served from, because an entry
     * holds the url of its document rather than only its path. An index carrying a
     * different mark was written against different documents, from a different place,
     * or by a different shape of this class, and is built again.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Search;
     *
     * $fingerprint = Search::fingerprint(['/var/www/docs/index.md']);
     *
     * // Returns 'b1b9e1c4...'
     * ```
     *
     * @param array $documents Paths of every document of the documentation, as a list of paths
     * @return string The mark, which changes whenever any document does
     */
    protected static function fingerprint(array $documents): string
    {
        $marks = array_map(fn ($path) => $path . ':' . filemtime($path), $documents);

        return hash('xxh128', self::INDEX_VERSION . "\n" . Url::base() . "\n" . implode("\n", $marks));
    }
}

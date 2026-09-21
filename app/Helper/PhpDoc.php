<?php

namespace Phuture\App\Helper;

/**
 * Reads a docblock as it was written.
 *
 * A docblock is the comment standing above a class or a member, holding a
 * description in plain words and the tags that say what goes in and what comes
 * out. What is read here is handed to the reference, which writes it out as the
 * prose of a page.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class PhpDoc
{
    /**
     * Prefixes of the tags written for a static analyser rather than for a reader.
     *
     * A static analyser is a tool that checks code without running it, and these
     * tags are notes left for one. They say nothing a reader of the documentation
     * would want, so they never reach the page.
     *
     * @var array
     */
    public const IGNORED_TAG_PREFIXES = ['phpstan-', 'psalm-', 'phan-'];

    /**
     * Lines a docblock opens an example with, lowercased.
     *
     * A docblock announces the code it is about to show on a line of its own.
     * Markdown has no reason to read that line as anything but more of the sentence
     * above it, so it is set in bold and reads as the heading it was meant to be.
     *
     * @var array
     */
    public const EXAMPLE_LABELS = ['example:', 'examples:'];

    /**
     * Shape of the line an example is fenced with.
     *
     * Three or more backticks or tildes, indented by no more than three spaces,
     * which is what markdown itself counts as the opening or closing of a block of
     * code.
     *
     * @var string
     */
    protected const FENCE = '/^\s{0,3}(`{3,}|~{3,})/';

    /**
     * Shape of the line a tag opens with.
     *
     * An at sign at the start of the line, then the name of the tag, then whatever
     * the tag has to say for itself.
     *
     * @var string
     */
    protected const TAG = '/^@([a-z][a-z0-9_-]*)[ \t]*(.*)$/i';

    /**
     * Reads the description and tags of a docblock, as it was written.
     *
     * A docblock is the comment written above a class or a method. It opens with a
     * description in plain words and goes on with tags, which are the lines opening
     * with an at sign that say what goes in and what comes out.
     *
     * An example inside the description is quoted wholesale, so that an at sign
     * written inside it is read as part of the example rather than as a tag of its
     * own. A tag runs on until the next one opens, because a reason may take more
     * than a line to give.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\PhpDoc;
     *
     * $doc = PhpDoc::parse("/**\n * Does a thing.\n *\n * @return bool Whether it worked\n *&#47;");
     *
     * // Returns ['description' => 'Does a thing.', 'tags' => [['name' => 'return', ...]]]
     * ```
     *
     * @param string|null $comment Docblock to read, or null when a member carries none
     * @return array The description in plain words and every tag it carries, as `description` and
     *  `tags`, where every tag holds `name` and `body`, in the order they were written
     * @see \Phuture\App\Helper\PhpDoc::parseAll()
     */
    public static function parse(?string $comment): array
    {
        if ($comment === null || trim($comment) === '') {
            return ['description' => '', 'tags' => []];
        }

        $description = [];
        $tags = [];
        $lastTag = -1;
        $fence = null;
        $isIgnoring = false;

        foreach ((array) preg_split('/\R/', self::unwrap($comment)) as $line) {
            $line = (string) $line;

            if (!self::isFence($line, $fence) && $fence === null && preg_match(self::TAG, $line, $matches) === 1) {
                $name = mb_strtolower($matches[1]);
                $isIgnoring = self::isIgnored($name);

                if ($isIgnoring) {
                    $lastTag = -1;

                    continue;
                }

                $tags[] = ['name' => $name, 'body' => $matches[2]];
                $lastTag = count($tags) - 1;

                continue;
            }

            if ($isIgnoring) {
                continue;
            }

            if ($lastTag >= 0) {
                $tags[$lastTag]['body'] = rtrim($tags[$lastTag]['body'] . "\n" . $line);

                continue;
            }

            $description[] = $line;
        }

        return ['description' => trim(implode("\n", self::labelled($description))), 'tags' => $tags];
    }

    /**
     * Reads every docblock standing before one declaration as the one docblock they stand in for.
     *
     * A class or a method may be written under more than one docblock, an annotation
     * on a line of its own above the block a reader is meant to see. They all belong
     * to the same declaration, so their descriptions are read one after another and
     * their tags are gathered together.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\PhpDoc;
     *
     * $doc = PhpDoc::parseAll(['/** Does a thing. *&#47;', '/** @phpstan-pure *&#47;']);
     *
     * // Returns ['description' => 'Does a thing.', 'tags' => []]
     * ```
     *
     * @param array $comments Docblocks to read, as a list of comments in the order they were written
     * @return array The descriptions read as paragraphs of one, and every tag all of them carry, as
     *  `description` and `tags`, where every tag holds `name` and `body`
     * @see \Phuture\App\Helper\PhpDoc::parse()
     */
    public static function parseAll(array $comments): array
    {
        $description = [];
        $tags = [];

        foreach ($comments as $comment) {
            $doc = self::parse($comment);

            if ($doc['description'] !== '') {
                $description[] = $doc['description'];
            }

            $tags = array_merge($tags, $doc['tags']);
        }

        return ['description' => implode("\n\n", $description), 'tags' => $tags];
    }

    /**
     * Sets the line opening an example in bold.
     *
     * Only a line that really opens one is touched: the words have to be an example
     * label, the line has to sit outside any block of code, and a block of code has
     * to follow it, whether straight away or after a blank line.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\PhpDoc;
     *
     * $lines = PhpDoc::labelled(['Example:', '```php', 'doThing();', '```']);
     *
     * // Returns ['**Example:**', '```php', 'doThing();', '```']
     * ```
     *
     * @param array $lines Lines of the description, as they were written
     * @return array The same lines, with any example label set in bold
     */
    protected static function labelled(array $lines): array
    {
        $count = count($lines);
        $fence = null;

        foreach ($lines as $index => $line) {
            if (self::isFence((string) $line, $fence)) {
                continue;
            }

            if ($fence !== null || !in_array(mb_strtolower(trim((string) $line)), self::EXAMPLE_LABELS, true)) {
                continue;
            }

            $next = $index + 1;

            while ($next < $count && trim((string) $lines[$next]) === '') {
                $next++;
            }

            if ($next < $count && preg_match(self::FENCE, (string) $lines[$next]) === 1) {
                $lines[$index] = preg_replace('/^(\s*)(\S.*?)\s*$/', '$1**$2**', (string) $line);
            }
        }

        return $lines;
    }

    /**
     * Tells you whether a line fences a block of code, keeping track of the block it opens or closes.
     *
     * A block of code is fenced by a line of backticks or tildes on either side of it,
     * and is closed only by the mark it was opened with, so that a block of backticks
     * may hold a line of tildes without ending there.
     *
     * The mark of the block being read is passed by reference and is kept up to date:
     * it becomes the mark a line opens a block with, and becomes null again once the
     * line closing that block comes around.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\PhpDoc;
     *
     * $fence = null;
     * $isFence = PhpDoc::isFence('```php', $fence);
     *
     * // Returns true, and leaves $fence holding '`'
     * ```
     *
     * @param string $line Line to read
     * @param string|null &$fence Mark the block being read is fenced with, or null while no block is
     *  open (this will be modified directly)
     * @return bool Returns true when the line fences a block, false when it is a line of the docblock
     */
    protected static function isFence(string $line, ?string &$fence): bool
    {
        if (preg_match(self::FENCE, $line, $matches) !== 1) {
            return false;
        }

        $mark = $matches[1][0];
        $fence = $fence === null ? $mark : ($mark === $fence ? null : $fence);

        return true;
    }

    /**
     * Tells you whether a tag is written for a static analyser.
     *
     * A static analyser is a tool that checks code without running it. Its tags are
     * notes left for the tool rather than for a reader, and are named after it, so
     * the name is all that has to be looked at.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\PhpDoc;
     *
     * $isIgnored = PhpDoc::isIgnored('phpstan-consistent-constructor');
     *
     * // Returns true
     * ```
     *
     * @param string $name Name of the tag, without its at sign
     * @return bool Returns true when the tag says nothing to a reader, false otherwise
     */
    protected static function isIgnored(string $name): bool
    {
        return array_any(
            self::IGNORED_TAG_PREFIXES,
            fn ($prefix) => str_starts_with($name, $prefix)
        );
    }

    /**
     * Lists the bodies of every tag of one name, in the order they were written.
     *
     * A docblock may carry the same tag several times, one `@param` for every
     * parameter a method takes, so every one of them is handed back rather than only
     * the first. A tag the docblock never carries answers with an empty list.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\PhpDoc;
     *
     * $bodies = PhpDoc::tagged($doc, 'param');
     *
     * // Returns ['string $name The name of the method', 'array $arguments The parameters']
     * ```
     *
     * @param array $doc Docblock as it was read, holding `description` and `tags`
     * @param string $name Name of the tag to look for, without its at sign
     * @return array What every tag of that name has to say, as a list of bodies in the order they
     *  were written
     * @see \Phuture\App\Helper\PhpDoc::parse()
     */
    public static function tagged(array $doc, string $name): array
    {
        $bodies = [];

        foreach ($doc['tags'] as $tag) {
            if ($tag['name'] === $name) {
                $bodies[] = $tag['body'];
            }
        }

        return $bodies;
    }

    /**
     * Reads the type, name and description a parameter tag names.
     *
     * A `@param` tag is written as a type, then the name of the parameter, then what
     * it is for. The type may be left out, and a parameter taking any number of
     * values is written with three dots in front of its name.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\PhpDoc;
     *
     * $parameter = PhpDoc::parameter('string $name The name to look up');
     *
     * // Returns ['type' => 'string', 'name' => '$name', 'description' => 'The name to look up']
     * ```
     *
     * @param string $body What the tag has to say, without its name
     * @return array The type, the name and what the parameter is for, as `type`, `name` and
     *  `description`, each empty when the tag does not name it
     * @see \Phuture\App\Helper\PhpDoc::typed()
     */
    public static function parameter(string $body): array
    {
        $pattern = '/^(?:(?<type>[^\s$]\S*)\s+)?(?<name>(?:\.\.\.)?\$[A-Za-z_]\w*)\s*(?<description>.*)$/s';

        if (preg_match($pattern, trim($body), $matches) !== 1) {
            return ['type' => '', 'name' => '', 'description' => trim($body)];
        }

        return [
            'type' => $matches['type'],
            'name' => $matches['name'],
            'description' => trim($matches['description']),
        ];
    }

    /**
     * Reads the type a tag opens with, and whatever it says after it.
     *
     * A `@return` or a `@throws` tag is written as a type and then what it means, so
     * the first word is the type and the rest is the description. A tag with nothing
     * to say answers with two empty strings.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\PhpDoc;
     *
     * $return = PhpDoc::typed('bool Whether the file was written');
     *
     * // Returns ['type' => 'bool', 'description' => 'Whether the file was written']
     * ```
     *
     * @param string $body What the tag has to say, without its name
     * @return array The type the tag opens with and what it means, as `type` and `description`
     * @see \Phuture\App\Helper\PhpDoc::parameter()
     */
    public static function typed(string $body): array
    {
        $body = trim($body);

        if ($body === '') {
            return ['type' => '', 'description' => ''];
        }

        $parts = preg_split('/\s+/', $body, 2) ?: [$body];

        return ['type' => $parts[0], 'description' => trim($parts[1] ?? '')];
    }

    /**
     * Strips a docblock of the framing it is written inside.
     *
     * The framing is the opening slash and asterisks, the asterisk down the side of
     * every line, and the closing asterisk and slash. One space behind the asterisk,
     * or behind the opening of a docblock written on a single line, belongs to that
     * framing. Every other space is indentation the author meant, which an example
     * needs to keep to stay an example.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\PhpDoc;
     *
     * $text = PhpDoc::unwrap("/**\n * Does a thing.\n *&#47;");
     *
     * // Returns 'Does a thing.'
     * ```
     *
     * @param string $comment Docblock to strip, as it was written
     * @return string What the docblock says, with its blank opening and closing lines taken off
     */
    protected static function unwrap(string $comment): string
    {
        $comment = (string) preg_replace('#^/\*\*+[ \t]?#', '', $comment);
        $comment = (string) preg_replace('#\s*\*+/\s*$#', '', $comment);

        $lines = [];

        foreach ((array) preg_split('/\R/', $comment) as $line) {
            $lines[] = (string) preg_replace('/^\s*\*[ ]?/', '', (string) $line);
        }

        while ($lines !== [] && trim($lines[0]) === '') {
            array_shift($lines);
        }

        while ($lines !== [] && trim((string) end($lines)) === '') {
            array_pop($lines);
        }

        return implode("\n", $lines);
    }
}

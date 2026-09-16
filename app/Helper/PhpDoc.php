<?php

namespace Phuture\App\Helper;

class PhpDoc
{
    /**
     * Prefixes of the tags written for a static analyser rather than for a reader
     */
    public const IGNORED_TAG_PREFIXES = ['phpstan-', 'psalm-', 'phan-'];

    /**
     * Lines a docblock opens an example with, lowercased
     */
    public const EXAMPLE_LABELS = ['example:', 'examples:'];

    /**
     * Line an example is fenced with
     */
    protected const FENCE = '/^\s{0,3}(`{3,}|~{3,})/';

    /**
     * Description and tags of a docblock, as it was written
     *
     * @param string|null $comment Docblock to read, or null when a member carries none
     * @return array
     */
    public static function parse(?string $comment): array
    {
        if ($comment === null || trim($comment) === '') {
            return ['description' => '', 'tags' => []];
        }

        $description = [];
        $tags = [];
        $last = -1;
        $fence = null;
        $ignoring = false;

        foreach ((array) preg_split('/\R/', self::unwrap($comment)) as $line) {
            $line = (string) $line;

            // An example is quoted wholesale, so that what it holds is never read as a tag of its own
            if (preg_match(self::FENCE, $line, $matches) === 1) {
                $mark = $matches[1][0];
                $fence = $fence === null ? $mark : ($mark === $fence ? null : $fence);
            } elseif ($fence === null && preg_match('/^@([a-z][a-z0-9_-]*)[ \t]*(.*)$/i', $line, $matches) === 1) {
                $name = mb_strtolower($matches[1]);
                $ignoring = self::isIgnored($name);

                if ($ignoring) {
                    $last = -1;

                    continue;
                }

                $tags[] = ['name' => $name, 'body' => $matches[2]];
                $last = count($tags) - 1;

                continue;
            }

            // What an ignored tag runs on to is left out with it
            if ($ignoring) {
                continue;
            }

            // A tag runs on until the next one opens, as a reason may take more than a line
            if ($last >= 0) {
                $tags[$last]['body'] = rtrim($tags[$last]['body'] . "\n" . $line);

                continue;
            }

            $description[] = $line;
        }

        return ['description' => trim(implode("\n", self::labelled($description))), 'tags' => $tags];
    }

    /**
     * Description with the line opening an example set in bold
     *
     * A docblock announces the code it goes on to show on a line of its own,
     * which markdown has no reason to read as anything but more of the sentence
     * above it. Set in bold, it reads as the heading of the example it opens.
     *
     * @param array $lines Lines of the description, as they were written
     * @return array
     */
    protected static function labelled(array $lines): array
    {
        $count = count($lines);
        $fence = null;

        foreach ($lines as $index => $line) {
            if (preg_match(self::FENCE, (string) $line, $matches) === 1) {
                $mark = $matches[1][0];
                $fence = $fence === null ? $mark : ($mark === $fence ? null : $fence);

                continue;
            }

            if ($fence !== null || !in_array(mb_strtolower(trim((string) $line)), self::EXAMPLE_LABELS, true)) {
                continue;
            }

            // Only where the example it announces does follow it
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
     * Description and tags of every docblock standing before one declaration
     *
     * A declaration may be written under more than one docblock, an annotation on
     * a line of its own above the block a reader is meant to see. They all belong
     * to the same declaration, so they are read as one.
     *
     * @param array $comments Docblocks to read, in the order they were written
     * @return array
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
     * Whether a tag is written for a static analyser, and says nothing to a reader
     *
     * @param string $name Name of the tag, without its at sign
     * @return bool
     */
    protected static function isIgnored(string $name): bool
    {
        return array_any(
            self::IGNORED_TAG_PREFIXES,
            fn ($prefix) => str_starts_with($name, $prefix)
        );
    }

    /**
     * Bodies of every tag of one name, in the order they were written
     *
     * @param array $doc Docblock as it was read
     * @param string $name Name of the tag, without its at sign
     * @return array
     */
    public static function tagged(array $doc, string $name): array
    {
        $bodies = [];

        foreach ($doc['tags'] ?? [] as $tag) {
            if ($tag['name'] === $name) {
                $bodies[] = $tag['body'];
            }
        }

        return $bodies;
    }

    /**
     * Type, name and description a parameter tag names
     *
     * @param string $body Body of the tag
     * @return array
     */
    public static function parameter(string $body): array
    {
        $pattern = '/^(?:(?<type>[^\s$]\S*)\s+)?(?<name>(?:\.\.\.)?\$[A-Za-z_]\w*)\s*(?<description>.*)$/s';

        if (preg_match($pattern, trim($body), $matches) !== 1) {
            return ['type' => '', 'name' => '', 'description' => trim($body)];
        }

        return [
            'type' => $matches['type'] ?? '',
            'name' => $matches['name'],
            'description' => trim($matches['description']),
        ];
    }

    /**
     * Type a tag opens with, and whatever it says after it
     *
     * @param string $body Body of the tag
     * @return array
     */
    public static function typed(string $body): array
    {
        $body = trim($body);

        if ($body === '') {
            return ['type' => '', 'description' => ''];
        }

        $parts = preg_split('/\s+/', $body, 2);

        return ['type' => $parts[0], 'description' => trim($parts[1] ?? '')];
    }

    /**
     * Docblock without the framing it is written inside
     *
     * One space behind the asterisk, or behind the opening of a docblock written on
     * a single line, belongs to the frame. Every other one is indentation the author
     * meant, which an example needs to keep to stay an example.
     *
     * @param string $comment Docblock to strip
     * @return string
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

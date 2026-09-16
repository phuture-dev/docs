<?php

namespace Phuture\App\Helper;

class PhpDoc
{
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

        foreach ((array) preg_split('/\R/', self::unwrap($comment)) as $line) {
            $line = (string) $line;

            // An example is quoted wholesale, so that what it holds is never read as a tag of its own
            if (preg_match('/^\s{0,3}(`{3,}|~{3,})/', $line, $matches) === 1) {
                $mark = $matches[1][0];
                $fence = $fence === null ? $mark : ($mark === $fence ? null : $fence);
            } elseif ($fence === null && preg_match('/^@([a-z][a-z0-9_-]*)[ \t]*(.*)$/i', $line, $matches) === 1) {
                $tags[] = ['name' => mb_strtolower($matches[1]), 'body' => $matches[2]];
                $last = count($tags) - 1;

                continue;
            }

            // A tag runs on until the next one opens, as a reason may take more than a line
            if ($last >= 0) {
                $tags[$last]['body'] = rtrim($tags[$last]['body'] . "\n" . $line);

                continue;
            }

            $description[] = $line;
        }

        return ['description' => trim(implode("\n", $description)), 'tags' => $tags];
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
     * One space behind the asterisk belongs to the frame, and every other one is
     * indentation the author meant, which an example needs to keep to stay an example.
     *
     * @param string $comment Docblock to strip
     * @return string
     */
    protected static function unwrap(string $comment): string
    {
        $comment = (string) preg_replace('#^/\*\*+#', '', $comment);
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

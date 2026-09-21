<?php

namespace Phuture\App\Helper;

/**
 * Writes the reference page of a php source as markdown.
 *
 * Takes the types a source declares, as they were read out of it, and writes them
 * out as a page: the signature of each, the prose of its docblock, and what its
 * tags have to say about what goes in, what comes out and what may be thrown.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Reference
{
    /**
     * Tags saying the same thing on every page of a package, which no page is better for repeating
     */
    protected const REPEATED = ['copyright', 'license', 'package', 'author'];

    /**
     * Groups a type is written out in, in the order they are written
     */
    protected const GROUPS = ['constant' => 'Constants', 'case' => 'Cases', 'method' => 'Methods'];

    /**
     * Page of a source, titled after the first type it declares
     *
     * @param array $parsed Namespace and types the source declares, as `namespace` and `types`
     * @return string
     */
    public static function page(array $parsed): string
    {
        $blocks = [];

        foreach ($parsed['types'] as $position => $type) {
            $blocks[] = self::type($type, $parsed['namespace'], $position === 0);
        }

        return self::join($blocks) . "\n";
    }

    /**
     * One type, as the heading of the page or as a section of its own
     *
     * The first type carries the only first level heading, which is the one the
     * site reads the title of the page from, and every member sits below the
     * second level so that the outline down the side has something to show.
     *
     * @param array $type Type to write out, holding `kind`, `name`, `header`, `doc` and `members`
     * @param string $namespace Namespace the type is declared in
     * @param bool $first Whether this is the type the page is named after
     * @return string
     */
    protected static function type(array $type, string $namespace, bool $first): string
    {
        $qualified = $namespace === '' ? $type['name'] : $namespace . '\\' . $type['name'];

        $blocks = [
            $first ? '# ' . $type['name'] : '## ' . ucfirst($type['kind']) . ' ' . $type['name'],
            '`' . $qualified . '`',
            self::fence($type['header']),
            self::deprecated($type['doc']),
            $type['doc']['description'],
            self::notes($type['doc']),
        ];

        foreach (self::GROUPS as $kind => $heading) {
            $members = array_values(array_filter($type['members'], fn ($member) => $member['kind'] === $kind));

            if ($members === []) {
                continue;
            }

            $blocks[] = ($first ? '## ' : '### ') . $heading;

            foreach ($members as $member) {
                $blocks[] = self::member($member, $first ? '###' : '####');
            }
        }

        return self::join($blocks);
    }

    /**
     * One constant, case or method
     *
     * @param array $member Member to write out, holding `kind`, `name`, `signature` and `doc`
     * @param string $level Heading the member is written under
     * @return string
     */
    protected static function member(array $member, string $level): string
    {
        $name = $member['kind'] === 'method' ? $member['name'] . '()' : $member['name'];

        return self::join([
            $level . ' `' . $name . '`',
            self::fence($member['signature']),
            self::deprecated($member['doc']),
            $member['doc']['description'],
            self::parameters($member['doc']),
            self::returns($member['doc']),
            self::throws($member['doc']),
            self::notes($member['doc']),
        ]);
    }

    /**
     * Table of the parameters a docblock names, or nothing when it names none
     *
     * @param array $doc Docblock as it was read, holding `description` and `tags`
     * @return string
     */
    protected static function parameters(array $doc): string
    {
        $rows = [];

        foreach (PhpDoc::tagged($doc, 'param') as $body) {
            $parameter = PhpDoc::parameter($body);

            if ($parameter['name'] === '') {
                continue;
            }

            $rows[] = '| `' . $parameter['name'] . '` | ' . self::code($parameter['type']) . ' | '
                . self::flatten($parameter['description']) . ' |';
        }

        if ($rows === []) {
            return '';
        }

        return "| Parameter | Type | Description |\n| --- | --- | --- |\n" . implode("\n", $rows);
    }

    /**
     * What a member hands back, as the docblock says it
     *
     * @param array $doc Docblock as it was read, holding `description` and `tags`
     * @return string
     */
    protected static function returns(array $doc): string
    {
        $bodies = PhpDoc::tagged($doc, 'return');

        if ($bodies === []) {
            return '';
        }

        return trim('**Returns** ' . self::described(PhpDoc::typed($bodies[0])));
    }

    /**
     * What a member throws, as a list, since a reason may run over more than a line
     *
     * @param array $doc Docblock as it was read, holding `description` and `tags`
     * @return string
     */
    protected static function throws(array $doc): string
    {
        $items = [];

        foreach (PhpDoc::tagged($doc, 'throws') as $body) {
            $items[] = '- ' . self::described(PhpDoc::typed($body));
        }

        return self::listed('Throws', $items);
    }

    /**
     * Links and cross references of a docblock
     *
     * @param array $doc Docblock as it was read, holding `description` and `tags`
     * @return string
     */
    protected static function notes(array $doc): string
    {
        $items = [];

        foreach ($doc['tags'] as $tag) {
            if (!in_array($tag['name'], ['see'], true)) {
                continue;
            }

            $note = PhpDoc::typed($tag['body']);
            $description = self::flatten($note['description']);

            // A link names somewhere to go, where a cross reference names something to look at
            if (str_starts_with($note['type'], 'http://') || str_starts_with($note['type'], 'https://')) {
                $items[] = '- [' . ($description === '' ? $note['type'] : $description) . '](' . $note['type'] . ')';

                continue;
            }

            $items[] = '- ' . self::described($note);
        }

        return self::listed('See also', $items);
    }

    /**
     * Notice that a member is on its way out, where a reader cannot miss it
     *
     * @param array $doc Docblock as it was read, holding `description` and `tags`
     * @return string
     */
    protected static function deprecated(array $doc): string
    {
        $bodies = PhpDoc::tagged($doc, 'deprecated');

        if ($bodies === []) {
            return '';
        }

        $description = self::flatten($bodies[0]);

        return trim('> **Deprecated.** ' . $description);
    }

    /**
     * Items of a docblock under the heading they belong to, or nothing when there are none
     *
     * @param string $heading Heading the items are written under
     * @param array $items Items to write out, as a list of the lines they are written on
     * @return string
     */
    protected static function listed(string $heading, array $items): string
    {
        return $items === [] ? '' : '**' . $heading . "**\n\n" . implode("\n", $items);
    }

    /**
     * Type of a tag and what it means, as one line
     *
     * @param array $typed Type and description of a tag as they were read, holding `type` and
     *  `description`
     * @return string
     */
    protected static function described(array $typed): string
    {
        $description = self::flatten($typed['description']);

        return trim(self::code($typed['type']) . ($description === '' ? '' : ' — ' . $description));
    }

    /**
     * A line of php in a fenced block
     *
     * @param string $code Line to fence
     * @return string
     */
    protected static function fence(string $code): string
    {
        return $code === '' ? '' : "```php\n" . $code . "\n```";
    }

    /**
     * Text as a code span, or nothing when there is no text to span
     *
     * @param string $text Text to span
     * @return string
     */
    protected static function code(string $text): string
    {
        return $text === '' ? '' : '`' . $text . '`';
    }

    /**
     * Text folded onto one line, for a cell that cannot hold more than one
     *
     * @param string $text Text to fold
     * @return string
     */
    protected static function flatten(string $text): string
    {
        $text = (string) preg_replace('/\s+/', ' ', $text);

        return trim(str_replace('|', '\\|', $text));
    }

    /**
     * Blocks of a page, with the blank lines between them that markdown reads by
     *
     * @param array $blocks Blocks to join, as a list of which the empty ones are left out
     * @return string
     */
    protected static function join(array $blocks): string
    {
        return implode("\n\n", array_filter($blocks, fn ($block) => trim((string) $block) !== ''));
    }
}

<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use Phuture\App\Helper\{PhpDoc, PhpSource, Reference};

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Helper\Reference.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class ReferenceTest extends TestCase
{
    /**
     * Builds the page of one type declared under one docblock.
     *
     * Saves every test from writing a whole source out: the docblock is the only
     * part most of them are about, so the type around it is always the same.
     *
     * Example:
     * ```php
     * $page = $this->page('/** Does a thing. *&#47;');
     *
     * // Returns the page of a class documented with that line
     * ```
     *
     * @param string $comment Docblock the type is declared under
     * @param string $members Members the type declares, as they would be written in the source
     * @return string The page the source is written as
     */
    private function page(string $comment, string $members = ''): string
    {
        $source = "<?php\n\nnamespace Acme;\n\n" . $comment . "\nclass Thing\n{\n" . $members . "}\n";

        return Reference::page((array) PhpSource::types($source));
    }

    public function testPageTitlesTheFirstTypeAndNamesItInFull(): void
    {
        $page = $this->page('/** Does a thing. */');

        Assert::same("# Thing\n", substr($page, 0, 8));
        Assert::contains('`Acme\Thing`', $page);
        Assert::contains("```php\nclass Thing\n```", $page);
        Assert::contains('Does a thing.', $page);

        // The first type carries the only first level heading, which is the one the title is read from
        Assert::same(1, substr_count("\n" . $page, "\n# "));
    }

    public function testPageWritesEveryTypeOfASourceOut(): void
    {
        $page = Reference::page((array) PhpSource::types(
            "<?php\n\nnamespace Acme;\n\nclass First\n{\n}\n\ninterface Second\n{\n}\n"
        ));

        Assert::contains('# First', $page);

        // A type the page is not named after is a section of it rather than the heading of it
        Assert::contains('## Interface Second', $page);
        Assert::contains('`Acme\Second`', $page);
    }

    public function testPageGroupsMembersUnderTheirOwnHeadings(): void
    {
        $page = $this->page('', "    public const ONE = 1;\n\n    public function run(): void\n    {\n    }\n");

        Assert::contains('## Constants', $page);
        Assert::contains('### `ONE`', $page);
        Assert::contains('## Methods', $page);

        // A method is named with the brackets that say it is one
        Assert::contains('### `run()`', $page);

        // A group no member belongs to is left out rather than written out empty
        Assert::notContains('Cases', $page);
    }

    public function testPageWritesParametersAsATable(): void
    {
        $doc = "    /**\n     * @param string \$name The name | its pipe\n     * @param int ...\$rest The rest\n     */\n";
        $page = $this->page('', $doc . "    public function run(string \$name, int ...\$rest): void\n    {\n    }\n");

        Assert::contains("| Parameter | Type | Description |\n| --- | --- | --- |", $page);

        // A pipe would open a column of its own, so it is written as itself instead
        Assert::contains('| `$name` | `string` | The name \| its pipe |', $page);
        Assert::contains('| `...$rest` | `int` | The rest |', $page);
    }

    public function testPageWritesWhatAMemberHandsBackAndThrows(): void
    {
        $doc = "    /**\n     * @return bool Whether it worked\n     * @throws \\RuntimeException When it did not\n"
            . "     * @throws \\LogicException\n     */\n";
        $page = $this->page('', $doc . "    public function run(): bool\n    {\n    }\n");

        Assert::contains('**Returns** `bool` — Whether it worked', $page);
        Assert::contains("**Throws**\n\n- `\\RuntimeException` — When it did not", $page);

        // A tag with nothing to say past its type is written as the type alone, with no dash left dangling
        Assert::contains('- `\LogicException`', $page);
        Assert::notContains('`\LogicException` —', $page);
    }

    public function testPageWritesNotesAndDeprecations(): void
    {
        $doc = "    /**\n     * @deprecated Use run() instead\n     * @see \\Acme\\Thing::run()\n"
            . "     * @see https://example.com/guide The guide\n     */\n";
        $page = $this->page('', $doc . "    public function old(): void\n    {\n    }\n");

        Assert::contains('> **Deprecated.** Use run() instead', $page);
        Assert::contains("**See also**\n\n- `\\Acme\\Thing::run()`", $page);

        // A link names somewhere to go, where a cross reference names something to look at
        Assert::contains('- [The guide](https://example.com/guide)', $page);
    }

    public function testPageLeavesOutWhatSaysNothing(): void
    {
        $page = $this->page('/** @package Acme */');

        // A tag saying the same thing on every page of a package is no better for being repeated
        Assert::notContains('Acme */', $page);

        // Blocks are held apart by the blank line markdown reads by, and the page ends with one line break
        Assert::notContains("\n\n\n", $page);
        Assert::same("\n", substr($page, -1));
        Assert::notSame("\n\n", substr($page, -2));
    }

    public function testPageOfASourceDeclaringNothing(): void
    {
        Assert::same("\n", Reference::page(['namespace' => '', 'types' => []]));
    }

    public function testPageReadsTheDocblockAsItWasWritten(): void
    {
        // The page is built out of what PhpDoc read, so the two agree on what a docblock says
        $doc = PhpDoc::parse("/**\n * Does a thing.\n *\n * @return bool Worked\n */");

        Assert::same('Does a thing.', $doc['description']);
        Assert::contains('Does a thing.', $this->page("/**\n * Does a thing.\n */"));
    }
}

(new ReferenceTest())->run();

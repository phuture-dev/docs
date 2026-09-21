<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use ReflectionMethod;
use Phuture\App\Helper\PhpDoc;

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Helper\PhpDoc.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class PhpDocTest extends TestCase
{
    /**
     * Calls a method the class keeps to itself.
     *
     * Each of them carries a rule of its own that is worth checking on its own,
     * rather than only through whichever public method happens to reach it.
     *
     * Example:
     * ```php
     * $text = $this->call('unwrap', ['/** Hi *&#47;']);
     *
     * // Returns 'Hi'
     * ```
     *
     * @param string $name Name of the method to call
     * @param array $arguments Arguments to call it with
     * @return mixed Whatever the method hands back
     */
    private function call(string $name, array $arguments): mixed
    {
        $method = new ReflectionMethod(PhpDoc::class, $name);
        $method->setAccessible(true);

        return $method->invokeArgs(null, $arguments);
    }

    public function testParse(): void
    {
        $doc = PhpDoc::parse("/**\n * Does a thing.\n *\n * @param string \$name The name\n * @return bool Worked\n */");

        Assert::same('Does a thing.', $doc['description']);
        Assert::same('param', $doc['tags'][0]['name']);
        Assert::same('string $name The name', $doc['tags'][0]['body']);
        Assert::same('return', $doc['tags'][1]['name']);

        // A reason may take more than a line, so a tag runs on until the next one opens
        $wrapped = PhpDoc::parse("/**\n * @return bool Worked,\n *  most of the time\n */");

        Assert::same("bool Worked,\n most of the time", $wrapped['tags'][0]['body']);

        // A tag written for a static analyser says nothing to a reader
        $ignored = PhpDoc::parse('/** @phpstan-consistent-constructor */');

        Assert::same('', $ignored['description']);
        Assert::same([], $ignored['tags']);

        // An at sign inside an example belongs to the example
        $example = PhpDoc::parse("/**\n * Text.\n *\n * ```\n * @param nothing\n * ```\n */");

        Assert::contains('@param nothing', $example['description']);
        Assert::same([], $example['tags']);

        Assert::same(['description' => '', 'tags' => []], PhpDoc::parse(null));
        Assert::same(['description' => '', 'tags' => []], PhpDoc::parse('   '));
    }

    public function testParseAll(): void
    {
        $doc = PhpDoc::parseAll(["/**\n * Does a thing.\n */", '/** @phpstan-pure */']);

        Assert::same('Does a thing.', $doc['description']);
        Assert::same([], $doc['tags']);

        $both = PhpDoc::parseAll(["/**\n * First.\n */", "/**\n * Second.\n *\n * @return bool Worked\n */"]);

        Assert::same("First.\n\nSecond.", $both['description']);
        Assert::count(1, $both['tags']);

        Assert::same(['description' => '', 'tags' => []], PhpDoc::parseAll([]));
    }

    public function testTagged(): void
    {
        $doc = PhpDoc::parse("/**\n * @param string \$one First\n * @param string \$two Second\n * @return bool Worked\n */");

        Assert::same(['string $one First', 'string $two Second'], PhpDoc::tagged($doc, 'param'));
        Assert::same(['bool Worked'], PhpDoc::tagged($doc, 'return'));
        Assert::same([], PhpDoc::tagged($doc, 'throws'));
    }

    public function testParameter(): void
    {
        Assert::same(
            ['type' => 'string', 'name' => '$name', 'description' => 'The name'],
            PhpDoc::parameter('string $name The name')
        );

        Assert::same('', PhpDoc::parameter('$name')['type']);
        Assert::same('...$values', PhpDoc::parameter('mixed ...$values Whatever')['name']);
        Assert::same('', PhpDoc::parameter('nonsense')['name']);
        Assert::same('nonsense', PhpDoc::parameter('nonsense')['description']);
    }

    public function testTyped(): void
    {
        Assert::same(['type' => 'bool', 'description' => 'Worked'], PhpDoc::typed('bool Worked'));
        Assert::same(['type' => 'bool', 'description' => ''], PhpDoc::typed('  bool  '));
        Assert::same(['type' => '', 'description' => ''], PhpDoc::typed('   '));
    }

    public function testLabelled(): void
    {
        Assert::same(
            ['**Example:**', '```php', 'x();', '```'],
            $this->call('labelled', [['Example:', '```php', 'x();', '```']])
        );

        // Blank lines may stand between the label and the example it opens
        Assert::same(
            ['**Examples:**', '', '```php', 'x();', '```'],
            $this->call('labelled', [['Examples:', '', '```php', 'x();', '```']])
        );

        // Nothing follows this one but prose, so it opens no example
        Assert::same(
            ['Example:', 'of how not to say it'],
            $this->call('labelled', [['Example:', 'of how not to say it']])
        );

        // Inside a block of code the words are code rather than a label
        Assert::same(
            ['```', 'Example:', '```'],
            $this->call('labelled', [['```', 'Example:', '```']])
        );
    }

    public function testIsIgnored(): void
    {
        Assert::true($this->call('isIgnored', ['phpstan-consistent-constructor']));
        Assert::true($this->call('isIgnored', ['psalm-return']));
        Assert::true($this->call('isIgnored', ['phan-suppress']));
        Assert::false($this->call('isIgnored', ['param']));
        Assert::false($this->call('isIgnored', ['return']));
    }

    public function testUnwrap(): void
    {
        Assert::same('Does a thing.', $this->call('unwrap', ["/**\n * Does a thing.\n */"]));

        // The one space behind the opening of a single-line docblock is framing too
        Assert::same('@phpstan-pure', $this->call('unwrap', ['/** @phpstan-pure */']));

        // Every other space is indentation the author meant
        Assert::same("Text.\n\n    indented", $this->call('unwrap', ["/**\n * Text.\n *\n *     indented\n */"]));

        Assert::same('', $this->call('unwrap', ["/**\n *\n */"]));
    }
}

(new PhpDocTest())->run();

<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use Phuture\App\Helper\PhpSource;

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Helper\PhpSource.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class PhpSourceTest extends TestCase
{
    /**
     * Reads the source every test of the whole file is made against.
     *
     * A single fixture carries one of everything the reader has a rule for: a
     * namespace, a class under a docblock and an attribute, constants written
     * several to a statement, a method holding a class of its own, an interface
     * and an enum.
     *
     * Example:
     * ```php
     * $parsed = $this->fixture();
     *
     * // Returns ['namespace' => 'Acme\Demo', 'types' => [...]]
     * ```
     *
     * @return array The source as it was read
     */
    private function fixture(): array
    {
        $parsed = PhpSource::types((string) file_get_contents(__DIR__ . '/fixtures/source/greeter.php'));

        Assert::notSame(null, $parsed);

        return (array) $parsed;
    }

    public function testTypesReadsEveryDeclaration(): void
    {
        $parsed = $this->fixture();

        Assert::same('Acme\Demo', $parsed['namespace']);
        Assert::count(3, $parsed['types']);
        Assert::same(['class', 'interface', 'enum'], array_column($parsed['types'], 'kind'));
        Assert::same(['Greeter', 'Contract', 'Suit'], array_column($parsed['types'], 'name'));

        // The declaration is written out on one line, whatever the source had wrapped, and without its attribute
        Assert::same('final class Greeter extends Base implements Contract', $parsed['types'][0]['header']);
        Assert::same('enum Suit: string', $parsed['types'][2]['header']);

        // A docblock standing before a declaration belongs to it, however many modifiers stand in between
        Assert::same('Greets somebody by name.', $parsed['types'][0]['doc']['description']);
    }

    public function testTypesReadsEveryMember(): void
    {
        $members = $this->fixture()['types'][0]['members'];

        Assert::same(
            ['GREETING', 'FIRST', 'SECOND', 'greet', 'items', '__construct'],
            array_column($members, 'name')
        );

        // One statement may declare several constants, and each of them is a member of its own
        Assert::same('constant', $members[1]['kind']);
        Assert::same('const FIRST = 1, SECOND = 2', $members[1]['signature']);
        Assert::same($members[1]['signature'], $members[2]['signature']);

        // A method is written out from its modifiers through to its return type
        Assert::same('method', $members[3]['kind']);
        Assert::same('public static function greet(string $name, int $times = 1): string', $members[3]['signature']);
        Assert::same('Says hello.', $members[3]['doc']['description']);
        Assert::same(['param', 'return'], array_column($members[3]['doc']['tags'], 'name'));

        // A method handing back a reference wears an ampersand where its name belongs
        Assert::same('items', $members[4]['name']);
        Assert::same('public function &items(): array', $members[4]['signature']);

        // A property promoted in the constructor is part of its signature rather than a member of its own
        Assert::same("public function __construct(private readonly string \$name = 'nobody')", $members[5]['signature']);
    }

    public function testTypesReadsCasesAndInheritedSignatures(): void
    {
        $types = $this->fixture()['types'];

        Assert::same(['case', 'case'], array_column($types[2]['members'], 'kind'));
        Assert::same(['HEARTS', 'SPADES'], array_column($types[2]['members'], 'name'));
        Assert::same("case HEARTS = 'H'", $types[2]['members'][0]['signature']);

        // A method with no body of its own ends at its semicolon rather than running on
        Assert::same('public function greet(string $name): string', $types[1]['members'][0]['signature']);
    }

    public function testTypesLeavesNestedDeclarationsAlone(): void
    {
        // Only what a type declares itself, rather than whatever the bodies below it hold
        $names = array_column($this->fixture()['types'][0]['members'], 'name');

        Assert::notContains('hidden', $names);

        // A class behind new has no name to be written under
        $anonymous = PhpSource::types('<?php $thing = new class {};');

        Assert::same([], $anonymous['types'] ?? null);
    }

    public function testTypesReadsASourceWithoutANamespace(): void
    {
        $parsed = PhpSource::types("<?php\n\nclass Loose\n{\n    public const ONE = 1;\n}\n");

        Assert::same('', $parsed['namespace'] ?? null);
        Assert::same('Loose', $parsed['types'][0]['name'] ?? null);
        Assert::same('class Loose', $parsed['types'][0]['header'] ?? null);
        Assert::same('ONE', $parsed['types'][0]['members'][0]['name'] ?? null);
    }

    public function testTypesRefusesWhatPhpWillNotRead(): void
    {
        Assert::null(PhpSource::types('<?php class {{{'));

        // A file holding no php at all parses, and declares nothing
        Assert::same([], PhpSource::types('Just some words.')['types'] ?? null);
    }

    public function testEncoding(): void
    {
        // A byte order mark belongs to the file rather than to the source it holds
        Assert::same('<?php', PhpSource::encoding("\u{FEFF}<?php"));

        $latin = mb_convert_encoding('<?php // café', 'ISO-8859-1', 'UTF-8');

        Assert::true(mb_check_encoding(PhpSource::encoding($latin), 'UTF-8'));
        Assert::same('<?php // café', PhpSource::encoding('<?php // café'));
    }
}

(new PhpSourceTest())->run();

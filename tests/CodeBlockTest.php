<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use ReflectionMethod;
use InvalidArgumentException;
use Phuture\App\Helper\Markdown;
use Phuture\App\Renderer\CodeBlock;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Renderer\HtmlRenderer;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Renderer\CodeBlock.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class CodeBlockTest extends TestCase
{
    /**
     * Calls a method the class keeps to itself.
     *
     * Reading the language off a fence and counting the lines of a block are both
     * rules worth checking on their own.
     *
     * @param string $name Name of the method to call
     * @param array $arguments Arguments to call it with
     * @return mixed Whatever the method hands back
     */
    private function call(string $name, array $arguments): mixed
    {
        $method = new ReflectionMethod(CodeBlock::class, $name);
        $method->setAccessible(true);

        return $method->invokeArgs(null, $arguments);
    }

    /**
     * Builds a fenced code node the way a parsed document carries one.
     *
     * @param string $info Word the block is fenced with, naming its language
     * @param string $code Code the block holds
     * @return \League\CommonMark\Extension\CommonMark\Node\Block\FencedCode The node to render
     */
    private function fence(string $info, string $code): FencedCode
    {
        $node = new FencedCode(3, '`', 0);
        $node->setInfo($info);
        $node->setLiteral($code);

        return $node;
    }

    public function testConstruct(): void
    {
        Assert::type(CodeBlock::class, new CodeBlock());
        Assert::same(2, CodeBlock::MINIMUM_LINES);
    }

    public function testRender(): void
    {
        $html = Markdown::render("```php\n\$a = 1;\n```");

        Assert::contains('<pre>', $html);
        Assert::contains('<code class="language-php"', $html);

        // A block naming no language carries no class of its own
        Assert::contains('<code>', Markdown::render("```\nplain\n```"));
    }

    public function testRenderRefusesOtherNodes(): void
    {
        $renderer = new HtmlRenderer(new Environment());

        Assert::exception(
            fn () => (new CodeBlock())->render(new Paragraph(), $renderer),
            InvalidArgumentException::class
        );
    }

    public function testLanguage(): void
    {
        Assert::same('php', $this->call('language', [$this->fence('php', 'x')]));
        Assert::same('php', $this->call('language', [$this->fence('PHP', 'x')]));

        // A fence may carry more than the name, and none of that is part of it
        Assert::same('php', $this->call('language', [$this->fence('php{5}', 'x')]));

        Assert::same('c++', $this->call('language', [$this->fence('c++', 'x')]));
        Assert::null($this->call('language', [$this->fence('', 'x')]));
    }

    public function testLines(): void
    {
        Assert::same(1, $this->call('lines', ["composer install\n"]));
        Assert::same(2, $this->call('lines', ["\$a = 1;\n\$b = 2;\n"]));
        Assert::same(1, $this->call('lines', ['']));
    }
}

(new CodeBlockTest())->run();

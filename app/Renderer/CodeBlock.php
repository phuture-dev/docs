<?php

namespace Phuture\App\Renderer;

use InvalidArgumentException;
use League\CommonMark\Node\Node;
use Tempest\Highlight\Highlighter;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Renderer\{ChildNodeRendererInterface, NodeRendererInterface};

/**
 * Renders a fenced block of code as the language it is written in.
 *
 * Every block is marked up by a highlighter, and a block running to more than a
 * single line is given a gutter, which puts the number of each line in front of
 * it. A fence naming a language nothing here knows is rendered as plain code
 * rather than refused.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class CodeBlock implements NodeRendererInterface
{
    /**
     * Lines a block has to run to before its lines are worth numbering.
     *
     * A block of a single line has nowhere to count to, and a lone number beside
     * an install command reads as clutter rather than as help.
     *
     * @var int
     */
    public const MINIMUM_LINES = 2;

    /**
     * Characters that end the name of a language in the line a block is fenced with.
     *
     * A fence may carry more than the name, such as the line number an example is
     * meant to start counting from, and none of that is part of the name itself.
     *
     * @var string
     */
    protected const LANGUAGE_PATTERN = '/[^a-z0-9#+_-].*$/i';

    /**
     * Highlighter a block of a single line is read by.
     *
     * Holds every language the site knows how to mark up. Kept for the whole life
     * of the renderer, because building one means registering all of them again.
     *
     * @var \Tempest\Highlight\Highlighter
     */
    protected Highlighter $highlighter;

    /**
     * Highlighter a block of several lines is read by, which numbers them.
     *
     * The same highlighter with a gutter added, which puts the number of each line
     * in front of it. Kept apart from the first so that the choice between them is
     * made once per block rather than built once per block.
     *
     * @var \Tempest\Highlight\Highlighter
     */
    protected Highlighter $numbered;

    /**
     * Builds a renderer that marks up fenced code as the language it is written in.
     *
     * Pass a highlighter of your own to mark code up differently, for instance one
     * carrying a language the site does not know by default. Left to itself the
     * renderer builds the standard one, which knows php, javascript, json, sql,
     * bash and a good many others.
     *
     * Example:
     * ```php
     * use Phuture\App\Renderer\CodeBlock;
     *
     * $renderer = new CodeBlock();
     *
     * // A renderer ready to be registered for fenced code
     * ```
     *
     * @param \Tempest\Highlight\Highlighter|null $highlighter Highlighter to read the code with, or
     *  null to build the standard one (default: null)
     */
    public function __construct(?Highlighter $highlighter = null)
    {
        $this->highlighter = $highlighter ?? new Highlighter();
        $this->numbered = $this->highlighter->withGutter();
    }

    /**
     * Renders fenced code as the block of marked up code it is served as.
     *
     * Every piece of the code is wrapped in a span saying what it is, a keyword or
     * a string or a comment, which is what lets the stylesheet give each of them a
     * colour of its own. The wrapping around the block is the one the renderer this
     * stands in for writes, so a block reads the same to the stylesheet whether its
     * language is one the highlighter knows or not.
     *
     * Lines are numbered once there is more than one of them. A language the
     * highlighter does not know is left as plain text rather than refused, so an
     * unknown fence still renders as a readable block.
     *
     * Example:
     * ```php
     * use Phuture\App\Renderer\CodeBlock;
     *
     * $html = (new CodeBlock())->render($fencedCode, $childRenderer);
     *
     * // Returns <pre><code class="language-php">...</code></pre>
     * ```
     *
     * @param \League\CommonMark\Node\Node $node Node to render, which has to be fenced code
     * @param \League\CommonMark\Renderer\ChildNodeRendererInterface $childRenderer Renderer of whatever
     *  the node holds, which fenced code never needs, its contents being code rather than more markdown
     * @return \League\CommonMark\Util\HtmlElement The block, ready to be written into the page
     * @throws InvalidArgumentException When the node handed over is not fenced code
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): HtmlElement
    {
        if (!$node instanceof FencedCode) {
            throw new InvalidArgumentException('Block must be an instance of ' . FencedCode::class);
        }

        $literal = $node->getLiteral();
        $language = self::language($node);
        $highlighter = self::lines($literal) < self::MINIMUM_LINES ? $this->highlighter : $this->numbered;
        $attributes = $language === null ? [] : ['class' => 'language-' . $language];
        $code = $highlighter->parse($literal, $language);

        return new HtmlElement('pre', [], new HtmlElement('code', $attributes, $code));
    }

    /**
     * Reads the language a block is fenced as.
     *
     * The language is the word written right after the opening fence, such as the
     * `php` in a fence opening with three backticks and that word. Anything written
     * behind it is an option rather than part of the name, and is left off.
     *
     * Example:
     * ```php
     * use Phuture\App\Renderer\CodeBlock;
     *
     * $language = CodeBlock::language($fencedCode);
     *
     * // Returns 'php' for a block fenced as php
     * ```
     *
     * @param \League\CommonMark\Extension\CommonMark\Node\Block\FencedCode $node Node to read from
     * @return string|null The name of the language, lowercased, or null when the fence names none
     */
    protected static function language(FencedCode $node): ?string
    {
        $word = $node->getInfoWords()[0] ?? '';
        $name = mb_strtolower((string) preg_replace(self::LANGUAGE_PATTERN, '', $word));

        return $name === '' ? null : $name;
    }

    /**
     * Counts the lines a block of code runs to.
     *
     * Counted the way the numbering down the side counts them, which leaves off the
     * empty line every fenced block ends with so that a block of one line is not
     * mistaken for a block of two.
     *
     * Example:
     * ```php
     * use Phuture\App\Renderer\CodeBlock;
     *
     * $lines = CodeBlock::lines("composer require phuture/coherence\n");
     *
     * // Returns 1
     * ```
     *
     * @param string $literal Code the block holds, as it was written
     * @return int How many lines of code the block holds
     */
    protected static function lines(string $literal): int
    {
        return count((array) preg_split('/\R/u', trim($literal, "\n")));
    }
}

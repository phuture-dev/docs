<?php

namespace Phuture\App\Renderer;

use InvalidArgumentException;
use League\CommonMark\Node\Node;
use Tempest\Highlight\Highlighter;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Renderer\{ChildNodeRendererInterface, NodeRendererInterface};

class CodeBlock implements NodeRendererInterface
{
    /**
     * Lines a block has to run to before its lines are worth numbering
     */
    public const MINIMUM_LINES = 2;

    /**
     * Highlighter a block of a single line is read by
     */
    protected Highlighter $highlighter;

    /**
     * Highlighter a block of several lines is read by, which numbers them
     */
    protected Highlighter $numbered;

    /**
     * Render fenced code with every token of it marked
     *
     * @param Highlighter|null $highlighter Highlighter to read the code with, or null for the default one
     */
    public function __construct(?Highlighter $highlighter = null)
    {
        $this->highlighter = $highlighter ?? new Highlighter();
        $this->numbered = $this->highlighter->withGutter();
    }

    /**
     * Fenced code as the block of marked up code it is served as
     *
     * The wrapping is the one the renderer this one stands in for writes, so that
     * a block reads the same to the stylesheet whether its language is one the
     * highlighter knows or not. A language it does not know is left as plain text.
     *
     * Lines are numbered once there is more than one of them, a block of a single
     * line having nowhere to count to.
     *
     * @param Node $node Node to render
     * @param ChildNodeRendererInterface $childRenderer Renderer of whatever the node holds
     * @return HtmlElement
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

        return new HtmlElement('pre', [], new HtmlElement('code', $attributes, $highlighter->parse($literal, $language)));
    }

    /**
     * Lines a block of code runs to, counted the way its numbering counts them
     *
     * @param string $literal Code the block holds
     * @return int
     */
    protected static function lines(string $literal): int
    {
        return count((array) preg_split('/\R/u', trim($literal, "\n")));
    }

    /**
     * Language a block is fenced as, or null when it is fenced as nothing in particular
     *
     * @param FencedCode $node Node to read the language from
     * @return string|null
     */
    protected static function language(FencedCode $node): ?string
    {
        $word = $node->getInfoWords()[0] ?? '';

        // Whatever a line number or another option is written behind it is not part of the name
        $name = mb_strtolower((string) preg_replace('/[^a-z0-9#+_-].*$/i', '', $word));

        return $name === '' ? null : $name;
    }
}

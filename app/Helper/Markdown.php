<?php

namespace Phuture\App\Helper;

use Throwable;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Environment\Environment;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Node\{Node, StringContainerInterface};
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\{Document as Ast, Paragraph};
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;

class Markdown
{
    /**
     * Converter shared by every conversion of this request
     */
    private static ?GithubFlavoredMarkdownConverter $converter = null;

    /**
     * Convert a markdown file to HTML, or null when it cannot be read or parsed
     *
     * @param string $path Path of the markdown file
     * @return string|null
     */
    public static function file(string $path): ?string
    {
        $markdown = Document::contents($path);

        if ($markdown === null) {
            return null;
        }

        try {
            return self::render($markdown);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Convert markdown to HTML
     *
     * @param string $markdown Markdown to convert
     * @return string
     */
    public static function render(string $markdown): string
    {
        return (string) self::converter()->convert($markdown);
    }

    /**
     * Converter of this request, giving every heading the anchor its own table of contents links to
     *
     * @return GithubFlavoredMarkdownConverter
     */
    protected static function converter(): GithubFlavoredMarkdownConverter
    {
        if (self::$converter !== null) {
            return self::$converter;
        }

        // Anchors are written bare, and sit on the heading itself, where the
        // stylesheet keeps them clear of the navbar once they are jumped to
        self::$converter = new GithubFlavoredMarkdownConverter([
            'slug_normalizer' => [
                'instance' => new HeadingSlug(),
            ],
            'heading_permalink' => [
                'id_prefix' => '',
                'fragment_prefix' => '',
                'apply_id_to_heading' => true,
                'symbol' => '#',
            ],
        ]);

        self::$converter->getEnvironment()->addExtension(new HeadingPermalinkExtension());

        return self::$converter;
    }

    /**
     * Parse markdown into its syntax tree, or null when it cannot be parsed
     *
     * @param string $markdown Markdown to parse
     * @return Ast|null
     */
    public static function parse(string $markdown): ?Ast
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());

        try {
            return (new MarkdownParser($environment))->parse($markdown);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Title of a markdown file, taken from its first heading, or from its first line of text
     *
     * @param string $path Path of the markdown file
     * @param int $length Maximum length of the title
     * @return string|null
     */
    public static function title(string $path, int $length = Document::TITLE_LENGTH): ?string
    {
        $markdown = Document::contents($path);

        if ($markdown === null) {
            return null;
        }

        $document = self::parse($markdown);

        if ($document === null) {
            return null;
        }

        $paragraph = null;

        foreach ($document->children() as $node) {
            if ($node instanceof Heading) {
                return Document::shorten(self::text($node), $length);
            }

            // Remember the first block of text, in case no heading shows up
            if ($paragraph === null && $node instanceof Paragraph) {
                $paragraph = $node;
            }
        }

        if ($paragraph === null) {
            return null;
        }

        // Only the first line of it, as a paragraph may run for several
        $title = Document::shorten(strtok(self::text($paragraph), "\n") ?: '', $length);

        return $title !== '' ? $title : null;
    }

    /**
     * Flatten every piece of text below a node, keeping its line breaks
     *
     * @param Node $node Node to read the text from
     * @return string
     */
    public static function text(Node $node): string
    {
        $text = '';

        foreach ($node->children() as $child) {
            if ($child instanceof Newline) {
                $text .= "\n";

                continue;
            }

            $text .= $child instanceof StringContainerInterface ? $child->getLiteral() : self::text($child);
        }

        return trim($text);
    }
}

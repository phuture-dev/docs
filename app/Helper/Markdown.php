<?php

namespace Phuture\App\Helper;

use Throwable;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlDecorator;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\{Node, StringContainerInterface};
use League\CommonMark\Extension\Table\{Table, TableRenderer};
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Block\{Document as Ast, Paragraph};
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;

class Markdown
{
    /**
     * Extensions of the documents a link is dropped for
     */
    public const DOCUMENT_EXTENSIONS = ['md'];

    /**
     * Converter shared by every conversion of this request
     */
    private static ?GithubFlavoredMarkdownConverter $converter = null;

    /**
     * Path of the document being converted, links are resolved against
     */
    private static ?string $document = null;

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
            return self::render($markdown, $path);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Convert markdown to HTML
     *
     * @param string $markdown Markdown to convert
     * @param string|null $path Path of the document the markdown comes from, links are resolved against
     * @return string
     */
    public static function render(string $markdown, ?string $path = null): string
    {
        self::$document = $path;

        try {
            return (string) self::converter()->convert($markdown);
        } finally {
            self::$document = null;
        }
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

        // A table is given a wrapper of its own, so that it can fill the column it
        // stands in and still be scrolled sideways where the column is too narrow for it
        self::$converter->getEnvironment()->addRenderer(
            Table::class,
            new HtmlDecorator(new TableRenderer(), 'div', ['class' => 'table-responsive']),
            10
        );
        self::$converter->getEnvironment()->addEventListener(DocumentParsedEvent::class, self::repointDocumentLinks(...));

        return self::$converter;
    }

    /**
     * Point every link a document makes to another document at the page it is served as
     *
     * Documents are written for the repository they come from, where a link to the
     * file next to them leads somewhere. Served as a page, the same file sits under
     * a url of its own, which the link is moved onto. A link leading nowhere on the
     * site is taken off instead, leaving only its label behind.
     *
     * @param DocumentParsedEvent $event Event carrying the document that was parsed
     * @return void
     */
    protected static function repointDocumentLinks(DocumentParsedEvent $event): void
    {
        $links = [];

        // Gathered first, as the tree is walked while it is still whole
        foreach ($event->getDocument()->iterator() as $node) {
            if ($node instanceof Link && self::isDocumentLink($node->getUrl())) {
                $links[] = $node;
            }
        }

        foreach ($links as $link) {
            $url = self::documentUrl($link->getUrl());

            if ($url !== null) {
                $link->setUrl($url);

                continue;
            }

            while (($child = $link->firstChild()) !== null) {
                $link->insertBefore($child);
            }

            $link->detach();
        }
    }

    /**
     * Whether a url points at a document sitting beside the one being rendered
     *
     * @param string $url Url the link carries
     * @return bool
     */
    protected static function isDocumentLink(string $url): bool
    {
        // Anything leading off the site is left alone, as it points at a page that exists
        if ($url === '' || preg_match('#^[a-z][a-z0-9+.-]*:|^//#i', $url)) {
            return false;
        }

        $path = preg_replace('/[?#].*$/', '', $url) ?? $url;

        return in_array(Document::extension($path), self::DOCUMENT_EXTENSIONS, true);
    }

    /**
     * Url of the page a document link is served as, or null when it leads nowhere
     *
     * @param string $url Url the link carries
     * @return string|null
     */
    protected static function documentUrl(string $url): ?string
    {
        if (self::$document === null || !preg_match('/^([^?#]*)(.*)$/', $url, $parts)) {
            return null;
        }

        $document = self::resolve($parts[1]);

        // Whatever the link carries past the file is kept, so it lands where it was aimed
        return $document === null ? null : self::pageUrl($document) . $parts[2];
    }

    /**
     * Path of the document a link points at, or null when there is no such document
     *
     * @param string $path Path the link carries, relative to the document holding it
     * @return string|null
     */
    protected static function resolve(string $path): ?string
    {
        $root = realpath(DOCS_DIR);

        if ($root === false) {
            return null;
        }

        $path = str_replace('/', DS, $path);
        $base = str_starts_with($path, DS) ? $root : dirname((string) self::$document);
        $directory = realpath(dirname($base . DS . ltrim($path, DS)));

        // Nothing outside the documentation, however far a link walks up
        if ($directory === false || !str_starts_with($directory . DS, rtrim($root, DS) . DS)) {
            return null;
        }

        return Document::find($directory, [basename($path)]);
    }

    /**
     * Url a document inside the documentation is served under
     *
     * @param string $document Path of the document
     * @return string
     */
    protected static function pageUrl(string $document): string
    {
        $root = rtrim((string) realpath(DOCS_DIR), DS);
        $segments = explode(DS, trim(substr($document, strlen($root)), DS));
        $name = strtolower(pathinfo((string) array_pop($segments), PATHINFO_FILENAME));

        // A folder is served by the document inside it, which is left off its url
        if (!in_array($name, Document::DEFAULT_NAMES, true)) {
            $segments[] = $name;
        }

        return '/' . implode('/', $segments);
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

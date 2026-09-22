<?php

namespace Phuture\App\Helper;

use Throwable;
use Phuture\App\Renderer\CodeBlock;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlDecorator;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\{Node, StringContainerInterface};
use League\CommonMark\Extension\Table\{Table, TableRenderer};
use League\CommonMark\Node\Block\{Document as Ast, Paragraph};
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\{FencedCode, Heading};

/**
 * Renders a page written as markdown, and reads what it holds.
 *
 * GitHub Flavored Markdown, rendered as the site serves it. A link from one
 * document to another is repointed at the url that document is served under, a
 * heading is given the anchor GitHub would have given it, and a fenced block of
 * code is marked up as the language it is written in, so that a document written
 * for a repository reads the same here as it does there.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Markdown
{
    /**
     * Extensions of the documents a link is pointed at a page for.
     *
     * A document written for a repository links to the file next to it, which is no
     * url on this site. Only links to files written in one of these formats are
     * pointed somewhere a reader can follow.
     *
     * @var array
     */
    public const DOCUMENT_EXTENSIONS = ['md'];

    /**
     * Converter shared by every conversion of this request.
     *
     * Built the first time markdown is converted and kept for the rest of the
     * request, because building one means registering every extension again and a
     * page holds many documents' worth of markdown.
     *
     * @var \League\CommonMark\GithubFlavoredMarkdownConverter|null
     */
    private static ?GithubFlavoredMarkdownConverter $converter = null;

    /**
     * Path of the document being converted.
     *
     * Set while a document is being converted and cleared as soon as it is done. A
     * link written as a relative path means nothing on its own, so it is resolved
     * against the document that carries it.
     *
     * @var string|null
     */
    private static ?string $document = null;

    /**
     * Converts a markdown file to the html of a page.
     *
     * The links the document makes to its neighbours are pointed at the pages those
     * neighbours are served as, its tables are wrapped so they can be scrolled, and
     * its code is marked up as the language it is written in. A file that cannot be
     * read, or that cannot be parsed, is answered with null rather than with an error.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Markdown;
     *
     * $html = Markdown::file('/var/www/docs/index.md');
     *
     * // Returns '<h1>Documentation</h1>...'
     * ```
     *
     * @param string $path Path of the markdown file to convert
     * @return string|null The html of the page, or null when the file cannot be read or parsed
     * @see \Phuture\App\Helper\Markdown::render()
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
     * Converts markdown to html.
     *
     * Pass the path of the document the markdown came from so that the links inside
     * it can be pointed at the pages they mean. Without it, a link written as a
     * relative path has nothing to be resolved against and is dropped instead.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Markdown;
     *
     * $html = Markdown::render('# Hello');
     *
     * // Returns '<h1>Hello</h1>'
     * ```
     *
     * @param string $markdown Markdown to convert
     * @param string|null $path Path of the document the markdown comes from, which its links are
     *  resolved against (default: null)
     * @return string The html of the page
     * @see \Phuture\App\Helper\Markdown::file()
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
     * Builds the converter every conversion of this request is made by.
     *
     * The converter is set up once with everything the site needs: an anchor on every
     * heading so a table of contents can link to it, code marked up as the language
     * it is written in, a wrapper around every table so a narrow screen can scroll
     * it, and links between documents pointed at the pages they are served as.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Markdown;
     *
     * $converter = Markdown::converter();
     *
     * // Returns the converter, built on the first call and kept for the rest
     * ```
     *
     * @return \League\CommonMark\GithubFlavoredMarkdownConverter The converter of this request
     */
    protected static function converter(): GithubFlavoredMarkdownConverter
    {
        if (self::$converter !== null) {
            return self::$converter;
        }

        // Anchors are written bare, and sit on the heading itself, where the
        // stylesheet keeps them clear of the navbar once they are jumped to
        self::$converter = new GithubFlavoredMarkdownConverter([
            // A document is written by whoever owns the repository it comes from, so the html it
            // carries of its own is taken out rather than passed on, and a link is only followed
            // where it leads somewhere a browser may safely go
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
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

        $environment = self::$converter->getEnvironment();
        $environment->addExtension(new HeadingPermalinkExtension());

        // Code is marked up as the language it is fenced as, rather than left as the
        // one colour a browser paints it
        $environment->addRenderer(FencedCode::class, new CodeBlock(), 10);

        // A table is given a wrapper of its own, so that it can fill the column it
        // stands in and still be scrolled sideways where the column is too narrow for it
        $environment->addRenderer(
            Table::class,
            new HtmlDecorator(new TableRenderer(), 'div', ['class' => 'table-responsive']),
            10
        );
        $environment->addEventListener(DocumentParsedEvent::class, self::repointDocumentLinks(...));

        return self::$converter;
    }

    /**
     * Points every link a document makes to another document at the page it is served as.
     *
     * Documents are written for the repository they come from, where a link to the
     * file next to them leads somewhere. Served as a page, the same file sits under a
     * url of its own, which the link is moved onto. A link leading nowhere on the site
     * is taken off instead, leaving only the words it was written on.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Markdown;
     *
     * Markdown::render('[Guide](CONTRIBUTING.md)', '/var/www/docs/coherence/readme.md');
     *
     * // Returns '<p><a href="/coherence/contributing">Guide</a></p>'
     * ```
     *
     * @param \League\CommonMark\Event\DocumentParsedEvent $event Event carrying the parsed document
     * @return void
     * @see \Phuture\App\Helper\Markdown::documentUrl()
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
     * Tells you whether a url points at a document sitting beside the one being rendered.
     *
     * Anything leading off the site is left alone, because it already points at a page
     * that exists. What is left is a relative path, and only one naming a document the
     * site serves is worth pointing anywhere else.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Markdown;
     *
     * $isDocument = Markdown::isDocumentLink('../CONTRIBUTING.md');
     *
     * // Returns true
     * ```
     *
     * @param string $url Url the link was written with
     * @return bool Returns true when the url names a document of this site, false otherwise
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
     * Works out the url of the page a document link is served as.
     *
     * Whatever the link carries past the file itself, such as the anchor naming a
     * heading, is kept and put back on the end, so a link aimed at one section of a
     * neighbouring document still lands there.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Markdown;
     *
     * $url = Markdown::documentUrl('WORKFLOW.md#branches');
     *
     * // Returns '/coherence/workflow#branches' while rendering a page of that folder
     * ```
     *
     * @param string $url Url the link was written with
     * @return string|null The url of the page, or null when the link leads nowhere on the site
     * @see \Phuture\App\Helper\Document::url()
     */
    protected static function documentUrl(string $url): ?string
    {
        if (self::$document === null || !preg_match('/^([^?#]*)(.*)$/', $url, $parts)) {
            return null;
        }

        $document = self::resolve($parts[1]);

        // Whatever the link carries past the file is kept, so it lands where it was aimed
        return $document === null ? null : Document::url($document) . $parts[2];
    }

    /**
     * Finds the document a link points at on disk.
     *
     * A path opening with a slash is counted from the root of the documentation, and
     * any other path from the folder of the document holding the link. Nothing outside
     * the documentation can be reached, however far a path walks up with `..`, and the
     * name is matched without regard for case so a link to `CONTRIBUTING.md` finds the
     * file saved as `contributing.md`.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Markdown;
     *
     * $path = Markdown::resolve('../readme.md');
     *
     * // Returns '/var/www/docs/coherence/readme.md' while rendering a page of that folder
     * ```
     *
     * @param string $path Path the link was written with, relative to the document holding it
     * @return string|null Path of the document on disk, or null when there is no such document
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
     * Parses markdown into the tree of nodes it is made of.
     *
     * The tree is what the markdown says rather than how it looks: headings, lists,
     * paragraphs and the text inside them, each as a node holding the ones below it.
     * It is what lets the navigation and the search read a document without first
     * turning it into a page.
     *
     * Only the core of markdown is understood here, without the tables, the anchors or
     * the marked up code a page is rendered with, because none of those change what a
     * document says.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Markdown;
     *
     * $document = Markdown::parse('# Hello');
     *
     * // Returns the tree holding one heading
     * ```
     *
     * @param string $markdown Markdown to parse
     * @return \League\CommonMark\Node\Block\Document|null The tree of nodes, or null when the
     *  markdown cannot be parsed
     * @see \Phuture\App\Helper\Markdown::text()
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
     * Reads the title of a markdown file out of the file itself.
     *
     * The first heading of the document is taken as its title, because that is what a
     * reader sees at the top of the page. A document with no heading falls back on its
     * first paragraph, of which only the first line is taken, since a paragraph may run
     * for several.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Markdown;
     *
     * $title = Markdown::title('/var/www/docs/index.md');
     *
     * // Returns 'Documentation'
     * ```
     *
     * @param string $path Path of the markdown file to read
     * @param int $length Longest the title may be, in characters (default: 255)
     * @return string|null The title of the page, or null when the document holds no text at all
     * @see \Phuture\App\Helper\Html::title()
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
     * Reads the contents of a markdown file as plain text.
     *
     * Everything that is markup rather than words is taken away, leaving what a reader
     * would see on the page. Every block opens a line of its own, so that a heading is
     * never read as the first word of the paragraph underneath it.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Markdown;
     *
     * $text = Markdown::plain('/var/www/docs/index.md');
     *
     * // Returns 'Documentation\nWelcome to the documentation...'
     * ```
     *
     * @param string $path Path of the markdown file to read
     * @return string|null The text of the page, or null when the file cannot be read or parsed
     * @see \Phuture\App\Helper\Markdown::text()
     */
    public static function plain(string $path): ?string
    {
        $markdown = Document::contents($path);
        $document = $markdown === null ? null : self::parse($markdown);

        if ($document === null) {
            return null;
        }

        $text = '';

        // Every block opens a line of its own, so that a heading is never read
        // as the first word of the paragraph underneath it
        foreach ($document->iterator() as $node) {
            if ($node instanceof AbstractBlock) {
                $text .= "\n";
            }

            if ($node instanceof StringContainerInterface) {
                $text .= $node->getLiteral();
            } elseif ($node instanceof Newline) {
                $text .= ' ';
            }
        }

        return trim($text);
    }

    /**
     * Gathers every piece of text below a node into one string.
     *
     * Walks whatever the node holds, however deeply it nests, and keeps only the words.
     * A line break written inside the node is kept as one, so a heading written across
     * two lines still reads as two.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Markdown;
     *
     * $text = Markdown::text(Markdown::parse('# Hello **there**'));
     *
     * // Returns 'Hello there'
     * ```
     *
     * @param \League\CommonMark\Node\Node $node Node to read the text of
     * @return string Every word below the node, with its own line breaks kept and the
     *  whitespace around it trimmed off
     * @see \Phuture\App\Helper\Markdown::plain()
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

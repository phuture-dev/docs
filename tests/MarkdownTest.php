<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use Phuture\App\Helper\Markdown;

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Helper\Markdown.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class MarkdownTest extends TestCase
{
    public function testRender(): void
    {
        Assert::contains('<h1', Markdown::render('# Hello'));
        Assert::contains('Hello', Markdown::render('# Hello'));
        Assert::same('', trim(Markdown::render('')));

        // Every heading carries the anchor its own table of contents links to
        Assert::contains('id="hello"', Markdown::render('# Hello'));

        // A table is wrapped so a narrow screen can scroll it
        Assert::contains('table-responsive', Markdown::render("| A | B |\n| --- | --- |\n| 1 | 2 |"));
    }

    public function testRenderMarksUpCode(): void
    {
        $html = Markdown::render("```php\n\$a = 1;\n\$b = 2;\n```");

        Assert::contains('<code class="language-php"', $html);
        Assert::contains('hl-variable', $html);

        // Lines are numbered once there is more than one of them
        Assert::contains('hl-gutter', $html);

        // A block of one line has nowhere to count to
        Assert::notContains('hl-gutter', Markdown::render("```bash\ncomposer install\n```"));

        // A language the highlighter does not know is left as plain text rather than refused
        Assert::contains('&lt;b&gt;', Markdown::render("```nosuchlang\n<b>x</b>\n```"));
    }

    public function testRenderPointsDocumentLinks(): void
    {
        $from = DOCS_DIR . 'package' . DS . 'readme.md';

        Assert::contains('href="/guide"', Markdown::render('[G](../guide.md)', $from));
        Assert::contains('href="/package/contributing"', Markdown::render('[C](CONTRIBUTING.md)', $from));

        // Whatever the link carries past the file is kept, so it lands where it was aimed
        Assert::contains('href="/guide#singletons"', Markdown::render('[G](../guide.md#singletons)', $from));

        // A link leading nowhere on the site is taken off, leaving the words it was written on
        Assert::same('<p>Missing</p>', trim(Markdown::render('[Missing](nowhere.md)', $from)));

        // Nothing outside the documentation can be reached, however far a link walks up
        Assert::same('<p>Out</p>', trim(Markdown::render('[Out](../../../composer.md)', $from)));

        // Anything leading off the site already points at a page that exists
        Assert::contains('href="https://example.com/x.md"', Markdown::render('[X](https://example.com/x.md)', $from));
        Assert::contains('href="#anchor"', Markdown::render('[A](#anchor)', $from));

        // With no document to resolve against, a relative link has nothing to be pointed at
        Assert::same('<p>G</p>', trim(Markdown::render('[G](../guide.md)')));
    }

    public function testFile(): void
    {
        Assert::contains('Fixture Guide', (string) Markdown::file(DOCS_DIR . 'guide.md'));
        Assert::null(Markdown::file(DOCS_DIR . 'nowhere.md'));
    }

    public function testPlain(): void
    {
        $text = (string) Markdown::plain(DOCS_DIR . 'guide.md');

        Assert::contains('Fixture Guide', $text);
        Assert::contains('Singletons', $text);
        Assert::notContains('#', $text);

        // A heading is never read as the first word of the paragraph underneath it
        Assert::notContains('Fixture GuideA', $text);

        Assert::null(Markdown::plain(DOCS_DIR . 'nowhere.md'));
    }

    public function testParse(): void
    {
        $document = Markdown::parse('# Hello');

        Assert::notNull($document);
        Assert::same('Hello', Markdown::text($document));
        Assert::notNull(Markdown::parse(''));
    }

    public function testTitle(): void
    {
        Assert::same('Fixture Guide', Markdown::title(DOCS_DIR . 'guide.md'));
        Assert::same('Fixture', Markdown::title(DOCS_DIR . 'guide.md', 10));
        Assert::null(Markdown::title(DOCS_DIR . 'nowhere.md'));
    }

    public function testText(): void
    {
        Assert::same('Hello there', Markdown::text(Markdown::parse('# Hello **there**')));

        // A line break written inside the node is kept as one
        Assert::same("One\nTwo", Markdown::text(Markdown::parse("One  \nTwo")));

        Assert::same('', Markdown::text(Markdown::parse('')));
    }
}

(new MarkdownTest())->run();

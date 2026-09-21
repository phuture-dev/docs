<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use Phuture\App\Helper\Document;

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Helper\Document.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class DocumentTest extends TestCase
{
    public function testFind(): void
    {
        $directory = DOCS_DIR . 'package';

        Assert::same($directory . DS . 'readme.md', Document::find($directory, ['readme.md']));
        Assert::same($directory . DS . 'readme.md', Document::find($directory, ['missing.md', 'readme.md']));

        // Matched without regard for case, so a lowercase file answers an uppercase name
        Assert::same($directory . DS . 'contributing.md', Document::find($directory, ['CONTRIBUTING.MD']));

        Assert::null(Document::find($directory, ['nowhere.md']));
        Assert::null(Document::find($directory, []));
        Assert::null(Document::find(DOCS_DIR . 'no-such-folder', ['readme.md']));
    }

    public function testFileNames(): void
    {
        Assert::same(['index.md', 'index.html'], Document::fileNames('index'));
        Assert::same(
            ['index.md', 'index.html', 'readme.md', 'readme.html'],
            Document::fileNames(['index', 'readme'])
        );
        Assert::same([], Document::fileNames([]));
    }

    public function testContents(): void
    {
        Assert::contains('# Fixture Guide', (string) Document::contents(DOCS_DIR . 'guide.md'));

        // Read again from the contents kept for the request rather than from disk
        Assert::contains('# Fixture Guide', (string) Document::contents(DOCS_DIR . 'guide.md'));

        Assert::null(Document::contents(DOCS_DIR . 'nowhere.md'));
        Assert::null(Document::contents(DOCS_DIR));
    }

    public function testFile(): void
    {
        Assert::contains('<h1', (string) Document::file(DOCS_DIR . 'guide.md'));
        Assert::contains('<h1>Fixture Page</h1>', (string) Document::file(DOCS_DIR . 'page.html'));
        Assert::null(Document::file(DOCS_DIR . 'sidebar.txt'));
    }

    public function testPlain(): void
    {
        $text = (string) Document::plain(DOCS_DIR . 'guide.md');

        Assert::contains('Fixture Guide', $text);
        Assert::notContains('<h1', $text);

        $html = (string) Document::plain(DOCS_DIR . 'page.html');

        Assert::contains('Fixture Page', $html);

        // A stylesheet is code for the browser rather than words for a reader
        Assert::notContains('color: red', $html);

        Assert::null(Document::plain(DOCS_DIR . 'sidebar.txt'));
    }

    public function testUrl(): void
    {
        Assert::same('/guide', Document::url(DOCS_DIR . 'guide.md'));
        Assert::same('/package', Document::url(DOCS_DIR . 'package' . DS . 'readme.md'));
        Assert::same('/package/contributing', Document::url(DOCS_DIR . 'package' . DS . 'contributing.md'));

        // A document named index is what its folder is served by, so the folder answers for it
        Assert::same('/', Document::url(DOCS_DIR . 'index.md'));
    }

    public function testTitle(): void
    {
        Assert::same('Fixture Guide', Document::title(DOCS_DIR . 'guide.md'));
        Assert::same('Fixture Page', Document::title(DOCS_DIR . 'page.html'));
        Assert::same('Fixture', Document::title(DOCS_DIR . 'guide.md', 10));
        Assert::null(Document::title(DOCS_DIR . 'sidebar.txt'));
    }

    public function testExtension(): void
    {
        Assert::same('md', Document::extension('/docs/readme.md'));
        Assert::same('md', Document::extension('/docs/README.MD'));
        Assert::same('html', Document::extension('/docs/page.html'));
        Assert::same('', Document::extension('/docs/readme'));
    }

    public function testShorten(): void
    {
        Assert::same('A short title', Document::shorten('A short title'));

        // Runs of whitespace are squeezed, so a title taken off several lines reads as one
        Assert::same('A short title', Document::shorten("A  short\n title "));

        Assert::same('A coherent', Document::shorten('A coherent collection of helpers', 20));
        Assert::same('', Document::shorten(''));

        // Nothing is cut mid-word, even when the first word is longer than the length allowed
        Assert::same('Extraordinari', Document::shorten('Extraordinarily', 13));
    }
}

(new DocumentTest())->run();

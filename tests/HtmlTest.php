<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use Phuture\App\Helper\Html;

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Helper\Html.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class HtmlTest extends TestCase
{
    public function testFile(): void
    {
        $html = (string) Html::file(DOCS_DIR . 'page.html');

        Assert::contains('<h1>Fixture Page</h1>', $html);

        // A whole document is unwrapped, so its own head is left behind
        Assert::notContains('<head>', $html);

        Assert::null(Html::file(DOCS_DIR . 'nowhere.html'));
    }

    public function testPlain(): void
    {
        $text = (string) Html::plain(DOCS_DIR . 'page.html');

        Assert::contains('Fixture Page', $text);
        Assert::notContains('<h1>', $text);
        Assert::null(Html::plain(DOCS_DIR . 'nowhere.html'));
    }

    public function testTitle(): void
    {
        // The first heading is what a reader sees at the top of the page
        Assert::same('Fixture Page', Html::title(DOCS_DIR . 'page.html'));

        Assert::same('Fixture', Html::title(DOCS_DIR . 'page.html', 10));
        Assert::null(Html::title(DOCS_DIR . 'nowhere.html'));
    }
}

(new HtmlTest())->run();

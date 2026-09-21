<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use Phuture\App\Helper\Sidebar;

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Helper\Sidebar.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class SidebarTest extends TestCase
{
    public function testTree(): void
    {
        $tree = Sidebar::tree('/package/contributing');

        Assert::same('Home', $tree[0]['label']);
        Assert::same('/', $tree[0]['url']);
        Assert::same([], $tree[0]['children']);

        $package = $tree[2];

        Assert::same('Package', $package['label']);
        Assert::count(2, $package['children']);

        // The group holding the page being served arrives unfolded around it
        Assert::true($package['open']);
        Assert::false($package['active']);

        Assert::true($package['children'][1]['active']);
        Assert::false($package['children'][0]['active']);
    }

    public function testTreeMarksNothingForAnUnknownPage(): void
    {
        $tree = Sidebar::tree('/nowhere');

        foreach ($tree as $item) {
            Assert::false($item['active']);
            Assert::false($item['open']);
        }
    }

    public function testTreeMatchesTheRootPath(): void
    {
        Assert::true(Sidebar::tree('/')[0]['active']);

        // A trailing slash names the same page as none at all
        Assert::true(Sidebar::tree('/guide/')[1]['active']);
    }

    public function testTreeReadsAnotherFile(): void
    {
        Assert::same([], Sidebar::tree('/', DOCS_DIR . 'nowhere.md'));
        Assert::same([], Sidebar::tree('/', DOCS_DIR . 'index.md'));
    }
}

(new SidebarTest())->run();

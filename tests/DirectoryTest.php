<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use Phuture\App\Helper\Directory;

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Helper\Directory.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class DirectoryTest extends TestCase
{
    public function testFilesWalksEveryFolderBelowTheOneGiven(): void
    {
        $files = Directory::files(rtrim(DOCS_DIR, DS));
        $relative = array_map(fn ($path) => str_replace(DS, '/', substr($path, strlen(rtrim(DOCS_DIR, DS)) + 1)), $files);

        Assert::contains('index.md', $relative);
        Assert::contains('page.html', $relative);

        // However deeply the folders nest
        Assert::contains('package/readme.md', $relative);

        // The folders themselves are not files, and neither are the two entries every folder holds
        Assert::notContains('package', $relative);
        Assert::notContains('.', $relative);
    }

    public function testFilesSortsWhatItHandsBack(): void
    {
        $files = Directory::files(rtrim(DOCS_DIR, DS));
        $sorted = $files;

        sort($sorted);

        Assert::same($sorted, $files);
    }

    public function testFilesOfAFolderThatIsNotThere(): void
    {
        Assert::same([], Directory::files(DOCS_DIR . 'nowhere'));

        // A file is no folder to walk, however readable it is
        Assert::same([], Directory::files(DOCS_DIR . 'index.md'));
    }
}

(new DirectoryTest())->run();

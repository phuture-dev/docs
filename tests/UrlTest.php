<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use Phuture\App\Helper\Url;

/**
 * The site of this test is served from a folder of a domain rather than from its root,
 * which is what a shared host gives a site installed beside another one. The constant
 * is defined before the bootstrap so that it is this value the helper reads.
 */
define('BASE_PATH', '/docs');

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Helper\Url.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class UrlTest extends TestCase
{
    public function testBaseIsThePathTheSiteIsServedFrom(): void
    {
        Assert::same('/docs', Url::base());
    }

    public function testToCarriesThePathTheSiteIsServedFrom(): void
    {
        Assert::same('/docs/coherence', Url::to('/coherence'));
        Assert::same('/docs/coherence/src/strings', Url::to('/coherence/src/strings'));
        Assert::same('/docs/assets/css/styles.css', Url::to('/assets/css/styles.css'));
    }

    public function testToReadsAPathWithOrWithoutItsOpeningSlash(): void
    {
        Assert::same(Url::to('/coherence'), Url::to('coherence'));
        Assert::same('/docs/search', Url::to('search'));
    }

    public function testToLeavesTheRootOfTheSiteWithoutATrailingSlash(): void
    {
        // The root of a site in a folder is that folder, which is what the navigation marks the
        // home page by and what the path of a request being served reads as
        Assert::same('/docs', Url::to('/'));
        Assert::same('/docs', Url::to(''));
    }
}

(new UrlTest())->run();

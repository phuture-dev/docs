<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use Phuture\App\Helper\HeadingSlug;

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Helper\HeadingSlug.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class HeadingSlugTest extends TestCase
{
    public function testNormalize(): void
    {
        $slug = new HeadingSlug();

        Assert::same('introduction', $slug->normalize('Introduction'));
        Assert::same('what-is-not-covered', $slug->normalize('What is not covered?'));

        // Punctuation goes before the spaces become dashes, so a run of them leaves a dash for each
        Assert::same('one--two', $slug->normalize('One:  Two'));

        Assert::same('isuuid', $slug->normalize('`isUuid()`'));
        // A letter of any alphabet is a letter, accent and all
        Assert::same('café-au-lait', $slug->normalize('Café au lait'));
        Assert::same('', $slug->normalize('!?#'));
        Assert::same('', $slug->normalize(''));

        // A prefix is put in front of the text before anything else happens
        Assert::same('user-content-introduction', $slug->normalize('Introduction', ['prefix' => 'user-content-']));

        Assert::same('intro', $slug->normalize('Introduction', ['length' => 5]));
        Assert::same('introduction', $slug->normalize('Introduction', ['length' => 0]));
    }
}

(new HeadingSlugTest())->run();

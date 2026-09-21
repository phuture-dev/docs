<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use Phuture\App\Enum\Schedule;

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Enum\Schedule.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class ScheduleTest extends TestCase
{
    public function testNext(): void
    {
        $from = strtotime('2026-01-01 09:00:00');

        Assert::same(strtotime('2026-01-01 10:00:00'), Schedule::HOURLY->next($from));
        Assert::same(strtotime('2026-01-02 09:00:00'), Schedule::DAILY->next($from));
        Assert::same(strtotime('2026-02-01 09:00:00'), Schedule::MONTHLY->next($from));

        // The last days of a long month have no answer in a short one, and are pulled back
        Assert::same(strtotime('2026-02-28 09:00:00'), Schedule::MONTHLY->next(strtotime('2026-01-31 09:00:00')));
        Assert::same(strtotime('2026-04-30 09:00:00'), Schedule::MONTHLY->next(strtotime('2026-03-31 09:00:00')));

        // A leap year has the twenty-ninth to land on
        Assert::same(strtotime('2024-02-29 09:00:00'), Schedule::MONTHLY->next(strtotime('2024-01-31 09:00:00')));
    }

    public function testDue(): void
    {
        $from = strtotime('2026-01-01 09:00:00');

        Assert::true(Schedule::DAILY->due($from, strtotime('2026-01-02 10:00:00')));
        Assert::false(Schedule::DAILY->due($from, strtotime('2026-01-02 08:00:00')));

        // Due on the very moment it falls due, rather than a second after it
        Assert::true(Schedule::DAILY->due($from, strtotime('2026-01-02 09:00:00')));

        Assert::true(Schedule::HOURLY->due($from, strtotime('2026-01-01 10:00:00')));
        Assert::false(Schedule::MONTHLY->due($from, strtotime('2026-01-15 09:00:00')));

        // Measured against this moment when no other is given
        Assert::true(Schedule::HOURLY->due(0));
    }
}

(new ScheduleTest())->run();

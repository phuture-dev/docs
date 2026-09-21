<?php

namespace Phuture\App\Enum;

use DateTimeImmutable;

/**
 * How often a command is allowed to run.
 *
 * Every command names one of these, and a run works out from the moment it last
 * ran whether it is due again. The step is taken over the calendar rather than
 * over a fixed number of seconds, so a day is still a day on the one that has an
 * hour taken off it for daylight saving.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
enum Schedule: string
{
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    case MONTHLY = 'monthly';

    /**
     * Works out when a run made at a given time is due to be made again.
     *
     * Takes the moment a command last ran and adds one turn of this schedule to
     * it, handing back the moment the command may run again. The step is taken
     * over the calendar rather than over a fixed number of seconds, so a day is
     * still a day on the one that has an hour taken off it for daylight saving.
     *
     * Example:
     * ```php
     * use Phuture\App\Enum\Schedule;
     *
     * $lastRun = strtotime('2026-01-31 09:00:00');
     * $dueAgain = Schedule::MONTHLY->next($lastRun);
     *
     * // Returns the timestamp of 2026-02-28 09:00:00
     * ```
     *
     * @param int $from Timestamp of the run being counted from
     * @return int Timestamp of the moment the next run is due
     * @see \Phuture\App\Enum\Schedule::due()
     */
    public function next(int $from): int
    {
        $date = (new DateTimeImmutable())->setTimestamp($from);

        return match ($this) {
            self::HOURLY => $date->modify('+1 hour')->getTimestamp(),
            self::DAILY => $date->modify('+1 day')->getTimestamp(),
            self::MONTHLY => self::month($date),
        };
    }

    /**
     * Tells you whether a run made at a given time is due to be made again.
     *
     * Compares the moment the next run falls due against the moment you are
     * asking about, which is this one unless you say otherwise. Passing a moment
     * of your own is what lets this be checked without waiting for the clock.
     *
     * Example:
     * ```php
     * use Phuture\App\Enum\Schedule;
     *
     * $lastRun = strtotime('2026-01-01 09:00:00');
     * $isDue = Schedule::DAILY->due($lastRun, strtotime('2026-01-02 10:00:00'));
     *
     * // Returns true
     * ```
     *
     * @param int $from Timestamp of the run being counted from
     * @param int|null $now Moment to measure against, defaulting to this one (default: null)
     * @return bool Returns true when the next run is due, false while it is still to come
     * @see \Phuture\App\Enum\Schedule::next()
     */
    public function due(int $from, ?int $now = null): bool
    {
        return ($now ?? time()) >= $this->next($from);
    }

    /**
     * Works out the moment one month on from a date, kept inside the month it lands in.
     *
     * Moving a month on from the last days of a long month has no answer in a
     * short one: there is no thirty-first of February. Rather than spilling into
     * the month after, the date is pulled back to the last day of the month it
     * lands in, and the time of day is left as it was.
     *
     * Example:
     * ```php
     * use Phuture\App\Enum\Schedule;
     *
     * $dueAgain = Schedule::MONTHLY->next(strtotime('2026-03-31 09:00:00'));
     *
     * // Returns the timestamp of 2026-04-30 09:00:00, April having no thirty-first
     * ```
     *
     * @param DateTimeImmutable $date Date being counted from
     * @return int Timestamp of the same day of the month after, or of its last day
     */
    private static function month(DateTimeImmutable $date): int
    {
        $month = $date->modify('first day of +1 month');
        $dayOfMonth = min((int) $date->format('j'), (int) $month->format('t'));

        return $month
            ->setDate((int) $month->format('Y'), (int) $month->format('n'), $dayOfMonth)
            ->getTimestamp();
    }
}

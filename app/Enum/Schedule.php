<?php

namespace Phuture\App\Enum;

use DateTimeImmutable;

enum Schedule: string
{
    case HOURLY = 'hourly';
    case DAILY = 'daily';
    case MONTHLY = 'monthly';

    /**
     * Moment a run made at the given time is due to be made again
     *
     * The step is taken over the calendar rather than over a fixed number of
     * seconds, so that a day is still a day on the one an hour is taken off it.
     *
     * @param int $from Timestamp of the run to count from
     * @return int
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
     * Whether a run made at the given time is due to be made again
     *
     * @param int $from Timestamp of the run to count from
     * @param int|null $now Moment to measure against, defaulting to this one
     * @return bool
     */
    public function due(int $from, ?int $now = null): bool
    {
        return ($now ?? time()) >= $this->next($from);
    }

    /**
     * Moment one month on from a date, kept inside the month it lands in
     *
     * @param DateTimeImmutable $date Date to count from
     * @return int
     */
    private static function month(DateTimeImmutable $date): int
    {
        $month = $date->modify('first day of +1 month');

        // The last days of a long month have no answer in a short one, and would otherwise spill into the one after
        $day = min((int) $date->format('j'), (int) $month->format('t'));

        return $month->setDate((int) $month->format('Y'), (int) $month->format('n'), $day)->getTimestamp();
    }
}

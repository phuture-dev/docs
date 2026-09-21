<?php

namespace Phuture\App\Command;

use Phuture\App\Command;
use Phuture\App\Enum\Schedule;

/**
 * Command that empties the workspace before anything is brought into it.
 *
 * Everything a source brings in is put in a folder of its own, so emptying those
 * folders leaves the pages written by hand at the root of the documentation
 * exactly where they were. It takes the lowest order of any command, because a run
 * should never read what an earlier one left behind.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class ClearWorkspace extends Command
{
    /**
     * How often this command may run.
     *
     * The workspace is cleared once a day, ahead of the commands that fill it
     * back up, so that a run never reads what an earlier one left behind.
     *
     * @var \Phuture\App\Enum\Schedule
     */
    protected const SCHEDULE = Schedule::DAILY;

    /**
     * Place this command takes in a run.
     *
     * The lowest number of any command, because the workspace has to be empty
     * before anything is brought into it.
     *
     * @var int
     */
    protected const ORDER = 10;

    /**
     * Clears the workspace, keeping the pages written at the root of the documentation.
     *
     * Everything a source brings in is put in a folder of its own, so emptying the
     * documentation of its folders leaves the pages of the site itself where they
     * are and takes away whatever a source has stopped carrying, or stopped being
     * read from at all. A folder that is really a link to somewhere else is left
     * alone, and so is whatever it points at.
     *
     * Unlike the other commands this one reads no entries from the source file:
     * there is only ever one workspace to clear.
     *
     * Example:
     * ```php
     * use Phuture\App\Command\ClearWorkspace;
     *
     * $exitCode = ClearWorkspace::run();
     *
     * // Returns 0, and prints a line for every folder taken away
     * ```
     *
     * @return int Exit code, zero when the workspace was cleared and one when a folder would not go
     */
    public static function run(): int
    {
        $docs = rtrim(self::docs(), DIRECTORY_SEPARATOR);

        if (!is_dir($docs)) {
            return self::abort('There is no documentation folder to clear.');
        }

        $cleared = 0;

        foreach (self::children($docs) as $path) {
            if (!is_dir($path) || is_link($path)) {
                continue;
            }

            self::remove($path);

            if (file_exists($path)) {
                return self::abort(self::relative($path) . ' could not be cleared.');
            }

            self::climate()->out('  <green>cleared</green> ' . self::relative($path));
            $cleared++;
        }

        if ($cleared === 0) {
            self::climate()->out('The workspace was already clear.');
        }

        self::ran();

        return 0;
    }
}

<?php

namespace Phuture\App\Command;

use Phuture\App\Command;
use Phuture\App\Enum\Schedule;

/**
 * Command that empties the folders a source fills, before anything is brought into them.
 *
 * Only the folders the source file names are emptied. Everything else under the
 * documentation was put there by somebody rather than by a run, and a run that took
 * it away would be taking away the only copy of it. It takes the lowest order of any
 * command, because a folder has to be empty before it is filled again.
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
     * The lowest number of any command, because a folder has to be empty before
     * anything is brought into it.
     *
     * @var int
     */
    protected const ORDER = 10;

    /**
     * Empties every folder of the documentation a source fills.
     *
     * Each entry of the source file names the folder its documents are written to,
     * and those are the folders emptied here, which takes away whatever a source has
     * stopped carrying. Nothing else is touched: a page written by hand, whether it
     * sits at the root of the documentation or in a folder of its own, is not
     * something any source can put back.
     *
     * The root of the documentation is never emptied, even where an entry writes a
     * single document to it, because emptying it would take the whole site with it.
     * The command a document belongs to clears that document itself, once it has the
     * new one in hand.
     *
     * Example:
     * ```php
     * use Phuture\App\Command\ClearWorkspace;
     *
     * $exitCode = ClearWorkspace::run();
     *
     * // Returns 0, and prints a line for every folder emptied
     * ```
     *
     * @return int Exit code, zero when the folders were emptied and one when one would not go
     * @see \Phuture\App\Command::directory()
     */
    public static function run(): int
    {
        $docs = rtrim(self::docs(), DIRECTORY_SEPARATOR);

        if (!is_dir($docs)) {
            return self::abort('There is no documentation folder to clear.');
        }

        $sourceFile = self::root() . self::SOURCE_FILE;
        $entries = self::entries($sourceFile);

        if ($entries === null) {
            return self::abort('Nothing could be read from ' . $sourceFile);
        }

        $destinations = [];

        foreach ($entries as $entry) {
            $directory = self::directory($entry['destination']);

            // The root holds the pages of the site itself, and a folder that is really a link is
            // left alone along with whatever it points at
            if ($directory === null || $directory === $docs || !is_dir($directory) || is_link($directory)) {
                continue;
            }

            $destinations[$directory] = $directory;
        }

        if ($destinations === []) {
            self::climate()->out('No folder of the documentation is filled by a source.');
            self::ran();

            return 0;
        }

        if (!self::clear($destinations)) {
            return self::abort('A folder of the documentation could not be emptied.');
        }

        foreach ($destinations as $destination) {
            self::climate()->out('  <green>cleared</green> ' . self::relative($destination));
        }

        self::ran();

        return 0;
    }
}

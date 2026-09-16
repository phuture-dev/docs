<?php

namespace Phuture\App\Command;

use Phuture\App\Command;
use Phuture\App\Enum\Schedule;

class ClearWorkspace extends Command
{
    /**
     * How often this command may run
     *
     * The workspace is cleared once a day, ahead of the commands that fill it back up.
     */
    protected const SCHEDULE = Schedule::DAILY;

    /**
     * Place this command takes in a run
     *
     * The workspace is emptied before anything is brought into it.
     */
    protected const ORDER = 10;

    /**
     * Clear the workspace, keeping the pages written at the root of the documentation
     *
     * Everything a source brings in is put in a folder of its own, so emptying
     * the documentation of its folders leaves the pages of the site itself and
     * takes away whatever a source has stopped carrying, or stopped being read
     * from at all. This command takes no entries of its own.
     *
     * @return int Exit code, zero when the workspace was cleared
     */
    public static function run(): int
    {
        $docs = rtrim(self::docs(), DIRECTORY_SEPARATOR);

        if (!is_dir($docs)) {
            return self::abort('There is no documentation folder to clear.');
        }

        $cleared = 0;

        foreach ((array) scandir($docs) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $path = $docs . DIRECTORY_SEPARATOR . $file;

            // A page of the site itself, and a link is left to whatever it was pointed at
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

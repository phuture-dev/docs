<?php

namespace Phuture\App\Helper;

use SplFileInfo;
use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Reads what a folder holds.
 *
 * Walks a folder and everything below it, handing back the files in an order that
 * is the same on every run rather than the one the filesystem happens to keep them
 * in.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Directory
{
    /**
     * Lists every file a folder holds, however deeply its folders nest.
     *
     * Walks the folder and everything below it, keeping the files and leaving the
     * folders themselves behind. The paths are sorted, because the order a
     * filesystem hands its files back in is its own and a run should read the same
     * every time. A folder that is not there is answered with an empty list rather
     * than with an error.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Directory;
     *
     * $files = Directory::files('/var/www/docs');
     *
     * // Returns ['/var/www/docs/index.md', '/var/www/docs/coherence/readme.md', ...]
     * ```
     *
     * @param string $directory Folder to walk
     * @return array Path of every file the folder holds, sorted
     */
    public static function files(string $directory): array
    {
        if (!is_dir($directory)) {
            return [];
        }

        $files = [];

        $found = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $file */
        foreach ($found as $file) {
            if ($file->isFile()) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }
}

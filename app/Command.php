<?php

namespace Phuture\App;

use Throwable;
use League\CLImate\CLImate;
use Phuture\App\Enum\Schedule;
use League\CLImate\TerminalObject\Dynamic\Progress;

abstract class Command
{
    /**
     * File the sources are read from, relative to the project root
     */
    public const SOURCE_FILE = 'source.json';

    /**
     * Type of entry this command takes care of, named by every command extending this one
     */
    public const TYPE = '';

    /**
     * Name of the staging directory a run downloads into before moving anything
     */
    protected const STAGING_DIRECTORY = '.update';

    /**
     * Extensions a file has to carry to be taken into the documentation
     */
    protected const EXTENSIONS = ['md', 'html', 'php'];

    /**
     * How often this command may run, named by every command that has its own pace
     */
    protected const SCHEDULE = Schedule::DAILY;

    /**
     * Place this command takes in a run, lowest first
     *
     * A command reading what another one brings in names a place after it. One
     * that has no reason to care is left at the end, where it can wait for the rest.
     */
    protected const ORDER = 100;

    /**
     * Folder inside the cache the runs of a command are remembered in
     */
    protected const SCHEDULE_DIRECTORY = 'schedule';

    /**
     * Console shared by every message of this run
     */
    private static ?CLImate $climate = null;

    /**
     * Entries of the source file this command takes care of, or null when the file cannot be used
     *
     * Entries are handed back as they were written, so that a command can read
     * the keys only its own type knows about.
     *
     * @param string $path Path of the source file
     * @return array|null
     */
    protected static function entries(string $path): ?array
    {
        if (!is_file($path) || !is_readable($path)) {
            return null;
        }

        try {
            $sources = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return null;
        }

        if (!is_array($sources)) {
            return null;
        }

        $entries = [];

        foreach ($sources as $source) {
            if (!is_array($source) || ($source['type'] ?? null) !== static::TYPE) {
                continue;
            }

            // An entry without both ends of the move has nothing to say
            if (!is_string($source['source'] ?? null) || !is_string($source['destination'] ?? null)) {
                continue;
            }

            $entries[] = $source;
        }

        return $entries;
    }

    /**
     * Place this command takes in a run, lowest first
     *
     * @return int
     */
    public static function order(): int
    {
        return static::ORDER;
    }

    /**
     * Whether enough time has passed since the last run for this command to run again
     *
     * A command that has never run is always ready, so that the first run of a
     * schedule is not held back by a moment it was never measured from.
     *
     * @param int|null $now Moment to measure against, defaulting to this one
     * @return bool
     */
    public static function timeToRun(?int $now = null): bool
    {
        $lastRun = self::lastRun();

        return $lastRun === null || static::SCHEDULE->due($lastRun, $now);
    }

    /**
     * Moment this command last finished a run, or null when it has not finished one yet
     *
     * @return int|null
     */
    public static function lastRun(): ?int
    {
        $marker = self::marker();

        if ($marker === null || !is_file($marker) || !is_readable($marker)) {
            return null;
        }

        $stamp = trim((string) @file_get_contents($marker));

        return ctype_digit($stamp) ? (int) $stamp : null;
    }

    /**
     * Moment this command is due to run again, or null when it is ready now
     *
     * @return int|null
     */
    public static function nextRun(): ?int
    {
        $lastRun = self::lastRun();

        return $lastRun === null || static::SCHEDULE->due($lastRun) ? null : static::SCHEDULE->next($lastRun);
    }

    /**
     * Remember that this command has just finished a run
     *
     * @return void
     */
    protected static function ran(): void
    {
        $marker = self::marker();

        if ($marker !== null) {
            @file_put_contents($marker, (string) time());
        }
    }

    /**
     * File the runs of this command are remembered in, or null when it cannot be kept
     *
     * @return string|null
     */
    protected static function marker(): ?string
    {
        $directory = self::cache() . static::SCHEDULE_DIRECTORY;

        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            return null;
        }

        // The class rather than the type, so that a command is remembered even before it has entries of its own
        $name = strtolower(basename(str_replace('\\', '/', static::class)));

        return $directory . DIRECTORY_SEPARATOR . $name;
    }

    /**
     * Whether a path is hidden, by its file name or by any of the folders it sits in
     *
     * A name opening with a dot is one the documentation has no url for, as a
     * url segment has to open with a letter or a digit to be served at all.
     *
     * @param string $path Path to read, relative to the documentation
     * @return bool
     */
    protected static function hidden(string $path): bool
    {
        foreach (explode('/', str_replace('\\', '/', $path)) as $segment) {
            if (str_starts_with($segment, '.')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether a file is written in one of the formats the documentation is made of
     *
     * @param string $path Path of the file
     * @return bool
     */
    protected static function supported(string $path): bool
    {
        return in_array(mb_strtolower(pathinfo($path, PATHINFO_EXTENSION)), static::EXTENSIONS, true);
    }

    /**
     * Move a downloaded file to where it belongs, over whatever is already there
     *
     * @param string $file Path of the downloaded file
     * @param string $destination Path the file belongs at
     * @return bool
     */
    protected static function install(string $file, string $destination): bool
    {
        $directory = dirname($destination);

        if (!is_dir($directory) && !@mkdir($directory, 0775, true) && !is_dir($directory)) {
            return false;
        }

        // A rename within one filesystem swaps the document in without a moment of it being half written
        if (@rename($file, $destination)) {
            return true;
        }

        // Another filesystem, so the copy is made next to the destination and swapped in from there
        $temporary = $destination . '.' . uniqid() . '.part';

        if (!@copy($file, $temporary)) {
            return false;
        }

        if (!@rename($temporary, $destination)) {
            @unlink($temporary);

            return false;
        }

        @unlink($file);

        return true;
    }

    /**
     * Empty the folders a run writes into, so that what a source no longer carries stops being served
     *
     * The documentation root is left alone, as the pages the site is built
     * around live there next to whatever a source brings in, owned by no entry.
     *
     * @param array $destinations Folders to empty, named as many times as the run writes into them
     * @return bool
     */
    protected static function clear(array $destinations): bool
    {
        $docs = rtrim(self::docs(), DIRECTORY_SEPARATOR);

        foreach (array_unique($destinations) as $destination) {
            $path = rtrim($destination, DIRECTORY_SEPARATOR);

            if ($path === $docs || !is_dir($path)) {
                continue;
            }

            foreach ((array) scandir($path) as $file) {
                if ($file === '.' || $file === '..') {
                    continue;
                }

                $child = $path . DIRECTORY_SEPARATOR . $file;

                is_dir($child) ? self::remove($child) : @unlink($child);

                // Something left behind would be served next to the documents that are about to come in
                if (file_exists($child)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Staging directory of this run, or null when it cannot be created
     *
     * It sits next to the documentation rather than in the system temp folder,
     * so that moving a document into place stays within one filesystem.
     *
     * @return string|null
     */
    protected static function staging(): ?string
    {
        $path = self::root() . static::STAGING_DIRECTORY . '-' . uniqid();

        if (!@mkdir($path, 0775, true) && !is_dir($path)) {
            return null;
        }

        return $path;
    }

    /**
     * Delete a directory and everything left in it
     *
     * @param string $path Path of the directory
     * @return void
     */
    protected static function remove(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        foreach ((array) scandir($path) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $child = $path . DIRECTORY_SEPARATOR . $file;

            // A link is taken away itself, rather than being walked down into whatever it points at
            is_dir($child) && !is_link($child) ? self::remove($child) : @unlink($child);
        }

        @rmdir($path);
    }

    /**
     * Root of the project, with a trailing separator
     *
     * @return string
     */
    protected static function root(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }

    /**
     * Folder the documentation lives in, with a trailing separator
     *
     * @return string
     */
    protected static function docs(): string
    {
        return defined('DOCS_DIR') ? DOCS_DIR : self::root() . 'docs' . DIRECTORY_SEPARATOR;
    }

    /**
     * Folder the cache lives in, with a trailing separator
     *
     * @return string
     */
    protected static function cache(): string
    {
        return defined('CACHE_DIR') ? CACHE_DIR : self::root() . 'cache' . DIRECTORY_SEPARATOR;
    }

    /**
     * Path as it reads from the project root, for the sake of the output
     *
     * @param string $path Path to shorten
     * @return string
     */
    protected static function relative(string $path): string
    {
        return str_starts_with($path, self::root()) ? substr($path, strlen(self::root())) : $path;
    }

    /**
     * Progress bar of this run, or null when there is no terminal to draw one on
     *
     * A progress bar redraws itself with control characters, which only belong on a terminal.
     *
     * @param int $total Number of steps the run is made of
     * @return Progress|null
     */
    protected static function progress(int $total): ?Progress
    {
        if (!defined('STDOUT') || !stream_isatty(STDOUT)) {
            return null;
        }

        return self::climate()->progress()->total($total);
    }

    /**
     * Console every message of this run is written through
     *
     * @return CLImate
     */
    protected static function climate(): CLImate
    {
        return self::$climate ??= new CLImate();
    }

    /**
     * Report what went wrong, clear away the staging directory, and hand back the exit code to leave with
     *
     * @param string $message Message to report
     * @param string|null $staging Staging directory of the run, when there is one to clear away
     * @return int
     */
    protected static function abort(string $message, ?string $staging = null): int
    {
        if ($staging !== null) {
            self::remove($staging);
        }

        self::climate()->to('error')->error($message);

        return 1;
    }
}

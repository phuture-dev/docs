<?php

namespace Phuture\App\Command;

use SplFileInfo;
use FilesystemIterator;
use Phuture\App\Command;
use Phuture\App\Enum\Schedule;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Phuture\App\Helper\{PhpSource, Reference};

class UpdateReference extends Command
{
    /**
     * How often this command may run
     *
     * The sources it reads are brought in once a day, so there is nothing new to read before then.
     */
    protected const SCHEDULE = Schedule::DAILY;

    /**
     * Place this command takes in a run
     *
     * The sources are read once the repositories carrying them have been brought in.
     */
    protected const ORDER = 40;

    /**
     * Extensions a file has to carry to be read as a source
     */
    protected const EXTENSIONS = ['php'];

    /**
     * Name of the staging directory a run writes into before moving anything
     */
    protected const STAGING_DIRECTORY = '.update-reference';

    /**
     * Write a page for every source in the documentation, and take the sources away
     *
     * A repository is brought in whole, so it arrives carrying the code behind
     * what it documents. The site has no way of serving that code, and the
     * docblocks in it say everything a reader would open it for, so each source
     * is read for what it declares and left as a page. One declaring nothing is
     * taken away, having nothing to say and no url to say it at.
     *
     * A source that cannot be read is left where it is rather than stopping the
     * run, so that one file nobody can parse does not cost every other page.
     *
     * @return int Exit code, zero when every source was read
     */
    public static function run(): int
    {
        $sources = self::sources(rtrim(self::docs(), DIRECTORY_SEPARATOR));

        if ($sources === []) {
            self::climate()->out('There is nothing to write a reference from.');
            self::ran();

            return 0;
        }

        $staging = self::staging();

        if ($staging === null) {
            return self::abort('The staging directory could not be created.');
        }

        $progress = self::progress(count($sources));
        $skipped = [];

        foreach ($sources as $index => $source) {
            $progress?->current($index, 'Reading ' . self::relative($source));

            $parsed = PhpSource::types((string) @file_get_contents($source));

            // Somebody else's file that php itself will not read, which is not this run's to fix
            if ($parsed === null) {
                $skipped[] = $source;

                continue;
            }

            if ($parsed['types'] === []) {
                @unlink($source);
                self::climate()->out('  <yellow>removed</yellow> ' . self::relative($source) . ' (it declares nothing)');

                continue;
            }

            $page = $staging . DIRECTORY_SEPARATOR . $index . '.md';

            if (@file_put_contents($page, Reference::page($parsed)) === false) {
                return self::abort('The page for ' . self::relative($source) . ' could not be written.', $staging);
            }

            $destination = self::destination($source);

            if (!self::install($page, $destination)) {
                return self::abort('Moving ' . basename($destination) . ' into place failed.', $staging);
            }

            // Only once the page is in place, so that nothing is lost to a run that stops halfway
            @unlink($source);

            self::climate()->out('  <green>written</green> ' . self::relative($destination));
        }

        $progress?->current(count($sources), 'Read');

        self::remove($staging);
        self::ran();

        foreach ($skipped as $source) {
            self::climate()->to('error')->out('  <red>skipped</red> ' . self::relative($source) . ' (it is not valid php)');
        }

        return $skipped === [] ? 0 : 1;
    }

    /**
     * Every source in the documentation, in the order their paths read
     *
     * @param string $directory Folder to read
     * @return array
     */
    protected static function sources(string $directory): array
    {
        if (!is_dir($directory)) {
            return [];
        }

        $sources = [];

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            // A link is never followed, as what it points at may not be the documentation at all
            if (!$file->isFile() || $file->isLink() || !self::supported($file->getPathname())) {
                continue;
            }

            $path = str_replace('\\', '/', substr($file->getPathname(), strlen($directory) + 1));

            if (!self::hidden($path)) {
                $sources[] = $file->getPathname();
            }
        }

        // The order a filesystem hands its files back in is its own, and a run should read the same every time
        sort($sources);

        return $sources;
    }

    /**
     * Path of the page a source is written to, under the name the source had
     *
     * @param string $path Path of the source
     * @return string
     */
    protected static function destination(string $path): string
    {
        return substr($path, 0, -strlen(pathinfo($path, PATHINFO_EXTENSION))) . 'md';
    }
}

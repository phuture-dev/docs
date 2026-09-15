<?php

namespace Phuture\App\Command;

use Throwable;
use League\CLImate\CLImate;

class UpdateDocs
{
    /**
     * File the sources are read from, relative to the project root
     */
    public const SOURCE_FILE = 'source.json';

    /**
     * Type of entry this command takes care of
     */
    public const TYPE = 'doc';

    /**
     * Name of the staging directory a run downloads into before moving anything
     */
    protected const STAGING_DIRECTORY = '.update-docs';

    /**
     * Seconds a single download is given before it is given up on
     */
    protected const TIMEOUT = 30;

    /**
     * Console shared by every message of this run
     */
    private static ?CLImate $climate = null;

    /**
     * Update every document listed in the source file
     *
     * Nothing is moved until every download has succeeded, so a run either
     * brings all of the documents in or leaves the ones on disk untouched.
     *
     * @param string|null $sourceFile Path of the source file, defaulting to the one in the project root
     * @return int Exit code, zero when every document was updated
     */
    public static function run(?string $sourceFile = null): int
    {
        $sourceFile = $sourceFile ?? self::root() . self::SOURCE_FILE;
        $entries = self::entries($sourceFile);

        if ($entries === null) {
            return self::abort('Nothing could be read from ' . $sourceFile);
        }

        if ($entries === []) {
            self::climate()->out('No <bold>' . self::TYPE . '</bold> entries in ' . basename($sourceFile) . '.');

            return 0;
        }

        $staging = self::staging();

        if ($staging === null) {
            return self::abort('The staging directory could not be created.');
        }

        // A progress bar redraws itself with control characters, which only belong on a terminal
        $progress = defined('STDOUT') && stream_isatty(STDOUT)
            ? self::climate()->progress()->total(count($entries))
            : null;

        $downloaded = [];

        foreach ($entries as $index => $entry) {
            $progress?->current($index, 'Downloading ' . $entry['source']);

            $destination = self::destination($entry['source'], $entry['destination']);

            if ($destination === null) {
                return self::abort('Entry ' . $index . ' points at a destination outside the documentation.', $staging);
            }

            $file = $staging . DIRECTORY_SEPARATOR . $index . '-' . basename($destination);

            if (!self::download($entry['source'], $file)) {
                return self::abort('Downloading ' . $entry['source'] . ' failed, nothing was updated.', $staging);
            }

            $downloaded[$file] = $destination;
        }

        $progress?->current(count($entries), 'Downloaded');

        // Every download is in, so the documents can be put in place
        foreach ($downloaded as $file => $destination) {
            if (!self::install($file, $destination)) {
                return self::abort('Moving ' . basename($destination) . ' into place failed.', $staging);
            }

            self::climate()->out('  <green>updated</green> ' . self::relative($destination));
        }

        self::remove($staging);

        return 0;
    }

    /**
     * Entries of the source file this command takes care of, or null when the file cannot be used
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
            if (!is_array($source) || ($source['type'] ?? null) !== self::TYPE) {
                continue;
            }

            // An entry without both ends of the move has nothing to say
            if (!is_string($source['source'] ?? null) || !is_string($source['destination'] ?? null)) {
                continue;
            }

            $entries[] = ['source' => $source['source'], 'destination' => $source['destination']];
        }

        return $entries;
    }

    /**
     * Path a document is written to, or null when it would land outside the documentation
     *
     * A destination naming a folder takes the file name the url ends with, and
     * every document is written under a lowercase name whichever it comes from.
     *
     * @param string $url Url the document comes from
     * @param string $destination Destination as the source file writes it
     * @return string|null
     */
    protected static function destination(string $url, string $destination): ?string
    {
        $docs = rtrim(self::docs(), DIRECTORY_SEPARATOR);
        $destination = str_replace('\\', '/', trim($destination));

        // A path walking up cannot be resolved against a folder that is not there yet
        if ($destination === '' || preg_match('#(^|/)\.\.(/|$)#', $destination)) {
            return null;
        }

        $path = $docs . DIRECTORY_SEPARATOR . trim($destination, '/');

        // A folder is named either by the trailing slash or by already being one
        if (str_ends_with($destination, '/') || is_dir($path)) {
            $name = self::fileName($url);

            if ($name === null) {
                return null;
            }

            $path .= DIRECTORY_SEPARATOR . $name;
        }

        if (!str_starts_with($path, $docs . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return dirname($path) . DIRECTORY_SEPARATOR . mb_strtolower(basename($path));
    }

    /**
     * File name a url ends with, or null when it ends with nothing usable
     *
     * @param string $url Url the document comes from
     * @return string|null
     */
    protected static function fileName(string $url): ?string
    {
        $name = basename((string) parse_url($url, PHP_URL_PATH));

        return preg_match('/^[a-z0-9][a-z0-9._-]*$/i', $name) ? $name : null;
    }

    /**
     * Download a url into a file, replacing whatever the file held
     *
     * @param string $url Url to download
     * @param string $path Path of the file to write
     * @return bool
     */
    protected static function download(string $url, string $path): bool
    {
        $file = @fopen($path, 'wb');

        if ($file === false) {
            return false;
        }

        $downloaded = extension_loaded('curl')
            ? self::downloadWithCurl($url, $file)
            : self::downloadWithStream($url, $file);

        fclose($file);

        // An empty answer would wipe a document that is still fine on disk
        if (!$downloaded || filesize($path) === 0) {
            @unlink($path);

            return false;
        }

        return true;
    }

    /**
     * Download a url into an open file through curl
     *
     * @param string $url Url to download
     * @param resource $file File to write to
     * @return bool
     */
    protected static function downloadWithCurl(string $url, $file): bool
    {
        $curl = curl_init($url);

        if ($curl === false) {
            return false;
        }

        curl_setopt_array($curl, [
            CURLOPT_FILE => $file,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => self::TIMEOUT,
            CURLOPT_FAILONERROR => true,
            CURLOPT_USERAGENT => 'UpdateDocs',
        ]);

        $downloaded = curl_exec($curl);
        curl_close($curl);

        return $downloaded !== false;
    }

    /**
     * Download a url into an open file through the stream wrapper, for a php without curl
     *
     * @param string $url Url to download
     * @param resource $file File to write to
     * @return bool
     */
    protected static function downloadWithStream(string $url, $file): bool
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => self::TIMEOUT,
                'follow_location' => 1,
                'max_redirects' => 5,
                'user_agent' => 'UpdateDocs',
                'ignore_errors' => false,
            ],
        ]);

        $source = @fopen($url, 'rb', false, $context);

        if ($source === false) {
            return false;
        }

        $copied = @stream_copy_to_stream($source, $file);
        fclose($source);

        return $copied !== false;
    }

    /**
     * Move a downloaded document to where it belongs, over whatever is already there
     *
     * @param string $file Path of the downloaded file
     * @param string $destination Path the document belongs at
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
     * Staging directory of this run, or null when it cannot be created
     *
     * It sits next to the documentation rather than in the system temp folder,
     * so that moving a document into place stays within one filesystem.
     *
     * @return string|null
     */
    protected static function staging(): ?string
    {
        $path = self::root() . self::STAGING_DIRECTORY . '-' . uniqid();

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

            is_dir($child) ? self::remove($child) : @unlink($child);
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
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
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

<?php

namespace Phuture\App\Command;

use Phuture\App\Command;
use Phuture\App\Enum\Schedule;

/**
 * Command that brings in every single document the source file names.
 *
 * An entry of type `doc` names one file to download, such as a readme kept in
 * somebody else's repository, and the folder of the documentation it is written
 * into. A single document is a cheap thing to fetch, so these are kept close to
 * their source and refreshed by the hour.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class UpdateDocs extends Command
{
    /**
     * Type of entry this command takes care of
     */
    public const TYPE = 'doc';

    /**
     * Name of the staging directory a run downloads into before moving anything
     */
    protected const STAGING_DIRECTORY = '.update-docs';

    /**
     * How often this command may run
     *
     * A single document is a cheap thing to fetch, so it is kept close to its source.
     */
    protected const SCHEDULE = Schedule::HOURLY;

    /**
     * Place this command takes in a run
     *
     * Single documents are brought in once the workspace is clear.
     */
    protected const ORDER = 20;

    /**
     * Seconds a single download is given before it is given up on
     */
    protected const TIMEOUT = 30;

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

        $progress = self::progress(count($entries));
        $downloaded = [];
        $directories = [];

        foreach ($entries as $index => $entry) {
            $progress?->current($index, 'Downloading ' . $entry['source']);

            $destination = self::destination($entry['source'], $entry['destination']);

            if ($destination === null) {
                return self::abort('Entry ' . $index . ' points at a destination outside the documentation.', $staging);
            }

            if (self::hidden($entry['destination']) || self::hidden(basename($destination))) {
                return self::abort('Entry ' . $index . ' names a hidden document, which has no url to be served at.', $staging);
            }

            if (!self::supported($destination)) {
                return self::abort(
                    'Entry ' . $index . ' names a document the documentation is not made of, which is one of '
                    . implode(', ', self::EXTENSIONS) . '.',
                    $staging
                );
            }

            $file = $staging . DIRECTORY_SEPARATOR . $index . '-' . basename($destination);

            if (!self::download($entry['source'], $file)) {
                return self::abort('Downloading ' . $entry['source'] . ' failed, nothing was updated.', $staging);
            }

            $downloaded[$file] = $destination;
            $directories[] = dirname($destination);
        }

        $progress?->current(count($entries), 'Downloaded');

        // Every download is in, so whatever the destinations were holding can go
        if (!self::clear($directories)) {
            return self::abort('A destination could not be cleared, the documentation may be incomplete.', $staging);
        }

        // The destinations are empty, so the documents can be put in place
        foreach ($downloaded as $file => $destination) {
            if (!self::install($file, $destination)) {
                return self::abort('Moving ' . basename($destination) . ' into place failed.', $staging);
            }

            self::climate()->out('  <green>updated</green> ' . self::relative($destination));
        }

        self::remove($staging);
        self::ran();

        return 0;
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
}

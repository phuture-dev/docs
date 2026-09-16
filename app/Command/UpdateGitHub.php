<?php

namespace Phuture\App\Command;

use Throwable;
use ZipArchive;
use SplFileInfo;
use Milo\Github\Api;
use FilesystemIterator;
use Phuture\App\Command;
use Milo\Github\OAuth\Token;
use Phuture\App\Enum\Schedule;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Milo\Github\Http\{CurlClient, StreamClient};

class UpdateGitHub extends Command
{
    /**
     * Type of entry this command takes care of
     */
    public const TYPE = 'github';

    /**
     * Name of the staging directory a run downloads into before moving anything
     */
    protected const STAGING_DIRECTORY = '.update-github';

    /**
     * How often this command may run
     *
     * A whole repository is downloaded for every entry, which is not worth doing by the hour.
     */
    protected const SCHEDULE = Schedule::DAILY;

    /**
     * Place this command takes in a run
     *
     * Repositories are brought in once the single documents are in place.
     */
    protected const ORDER = 30;

    /**
     * Folders left behind whichever repository they turn up in
     */
    protected const SKIPPED_DIRECTORIES = ['tests'];

    /**
     * Environment variables an access token is looked for in, in this order
     */
    protected const TOKEN_VARIABLES = ['GITHUB_TOKEN', 'GH_TOKEN'];

    /**
     * Name this command introduces itself to GitHub under, which the api asks every caller for
     */
    protected const USER_AGENT = 'UpdateGitHub';

    /**
     * Seconds a single request is given before it is given up on
     */
    protected const TIMEOUT = 60;

    /**
     * Client every request of this run goes through
     */
    private static ?Api $api = null;

    /**
     * Update the documentation of every repository listed in the source file
     *
     * Each repository is downloaded whole, and the documents it carries are
     * written under its destination in the folders they sat in upstream.
     * Nothing is moved until every repository has been read, so a run either
     * brings all of the documents in or leaves the ones on disk untouched.
     *
     * @param string|null $sourceFile Path of the source file, defaulting to the one in the project root
     * @return int Exit code, zero when every repository was updated
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
        $documents = [];
        $directories = [];

        foreach ($entries as $index => $entry) {
            $repository = self::repository($entry);

            if ($repository === null) {
                return self::abort('Entry ' . $index . ' does not name a GitHub repository.', $staging);
            }

            $directory = self::directory($entry['destination']);

            if ($directory === null) {
                return self::abort('Entry ' . $index . ' points at a destination outside the documentation.', $staging);
            }

            $directories[] = $directory;

            $progress?->current($index, 'Downloading ' . self::name($repository));

            $checkout = self::checkout($repository, $staging . DIRECTORY_SEPARATOR . $index);

            if ($checkout === null) {
                return self::abort('Downloading ' . self::name($repository) . ' failed, nothing was updated.', $staging);
            }

            $found = self::documents($checkout, self::exclusions($entry));

            if ($found === []) {
                self::climate()->out('  <yellow>no documents</yellow> in ' . self::name($repository));
            }

            foreach ($found as $path => $file) {
                $destination = $directory . DIRECTORY_SEPARATOR . self::nested($path);
                $label = self::name($repository) . '/' . $path;

                // Two paths that differ only in case are one path here, so the run stops rather than choosing for the reader
                if (isset($documents[$destination])) {
                    return self::abort(
                        'Both ' . $documents[$destination]['label'] . ' and ' . $label . ' would be written to '
                        . self::relative($destination) . ', exclude one of them.',
                        $staging
                    );
                }

                $documents[$destination] = ['file' => $file, 'label' => $label];
            }
        }

        $progress?->current(count($entries), 'Downloaded');

        // Every repository is in, so whatever the destinations were holding can go
        if (!self::clear($directories)) {
            return self::abort('A destination could not be cleared, the documentation may be incomplete.', $staging);
        }

        // The destinations are empty, so the documents can be put in place
        foreach ($documents as $destination => $document) {
            if (!self::install($document['file'], $destination)) {
                return self::abort('Moving ' . basename($destination) . ' into place failed.', $staging);
            }

            self::climate()->out('  <green>updated</green> ' . self::relative($destination));
        }

        self::remove($staging);
        self::ran();

        return 0;
    }

    /**
     * Repository an entry names, or null when it names none
     *
     * The source is read both as a GitHub url and as the owner/name shorthand,
     * and the branch, tag or commit comes either from the entry or from the url.
     *
     * @param array $entry Entry as the source file writes it
     * @return array|null Owner, name and reference of the repository
     */
    protected static function repository(array $entry): ?array
    {
        $source = trim($entry['source']);

        // A url is cut down to the path it carries, so that it and the shorthand are read the same way
        if (preg_match('#^(?:[a-z][a-z0-9+.-]*://)?(?:www\.)?github\.com/(.*)$#i', $source, $matches)) {
            $source = $matches[1];
        }

        $segments = explode('/', trim(preg_replace('/\.git$/i', '', $source), '/'));

        if (count($segments) < 2) {
            return null;
        }

        $owner = array_shift($segments);
        $name = array_shift($segments);

        if (!preg_match('/^[a-z0-9._-]+$/i', $owner) || !preg_match('/^[a-z0-9._-]+$/i', $name)) {
            return null;
        }

        $reference = is_string($entry['ref'] ?? null) && trim($entry['ref']) !== '' ? trim($entry['ref']) : null;

        // GitHub writes a branch into the url as /tree/<branch>, and a branch name may hold slashes of its own
        if ($reference === null && ($segments[0] ?? null) === 'tree' && count($segments) > 1) {
            array_shift($segments);
            $reference = implode('/', $segments);
        }

        return ['owner' => $owner, 'name' => $name, 'reference' => $reference];
    }

    /**
     * Patterns an entry excludes, lowercased so that they match however they were written
     *
     * @param array $entry Entry as the source file writes it
     * @return array
     */
    protected static function exclusions(array $entry): array
    {
        $exclusions = [];

        foreach ((array) ($entry['exclude'] ?? []) as $exclusion) {
            if (is_string($exclusion) && trim($exclusion) !== '') {
                $exclusions[] = mb_strtolower(str_replace('\\', '/', trim(trim($exclusion), '/')));
            }
        }

        return $exclusions;
    }

    /**
     * Folder the documents of a repository are written to, or null when it would land outside the documentation
     *
     * @param string $destination Destination as the source file writes it
     * @return string|null
     */
    protected static function directory(string $destination): ?string
    {
        $docs = rtrim(self::docs(), DIRECTORY_SEPARATOR);
        $destination = trim(str_replace('\\', '/', trim($destination)), '/');

        // A path walking up cannot be resolved against a folder that is not there yet
        if (preg_match('#(^|/)\.\.(/|$)#', $destination)) {
            return null;
        }

        // The documentation root is a destination of its own, written as / or left empty
        if ($destination === '') {
            return $docs;
        }

        $path = $docs . DIRECTORY_SEPARATOR . $destination;

        return str_starts_with($path, $docs . DIRECTORY_SEPARATOR) ? $path : null;
    }

    /**
     * Download a repository and unpack it, handing back the folder it was unpacked into, or null when that fails
     *
     * @param array $repository Repository to download
     * @param string $path Path of the folder to unpack into
     * @return string|null
     */
    protected static function checkout(array $repository, string $path): ?string
    {
        $archive = $path . '.zip';

        if (!self::archive($repository, $archive)) {
            return null;
        }

        $zip = new ZipArchive();

        if ($zip->open($archive) !== true) {
            @unlink($archive);

            return null;
        }

        $unpacked = $zip->extractTo($path);
        $zip->close();

        // The archive is of no use once it is unpacked, and it is the bulkiest thing a run leaves lying around
        @unlink($archive);

        return $unpacked ? self::unwrap($path) : null;
    }

    /**
     * Download the archive of a repository into a file
     *
     * @param array $repository Repository to download
     * @param string $path Path of the file to write
     * @return bool
     */
    protected static function archive(array $repository, string $path): bool
    {
        $url = '/repos/:owner/:repo/zipball';

        // The reference is put in by hand, as a branch name may hold slashes that stand for themselves
        if ($repository['reference'] !== null) {
            $url .= '/' . implode('/', array_map('rawurlencode', explode('/', $repository['reference'])));
        }

        try {
            $response = self::api()->get(
                $url,
                ['owner' => $repository['owner'], 'repo' => $repository['name']],
                ['User-Agent' => self::USER_AGENT]
            );

            $content = self::api()->decode($response);
        } catch (Throwable $exception) {
            self::climate()->to('error')->out('  <red>' . $exception->getMessage() . '</red>');

            return false;
        }

        // An empty answer would unpack into nothing and quietly empty a destination
        if (!is_string($content) || $content === '') {
            return false;
        }

        return @file_put_contents($path, $content) !== false;
    }

    /**
     * Folder a repository was unpacked into, stepping into the one folder GitHub wraps an archive in
     *
     * @param string $path Path the archive was unpacked into
     * @return string
     */
    protected static function unwrap(string $path): string
    {
        $children = array_values(array_diff((array) scandir($path), ['.', '..']));

        if (count($children) === 1 && is_dir($path . DIRECTORY_SEPARATOR . $children[0])) {
            return $path . DIRECTORY_SEPARATOR . $children[0];
        }

        return $path;
    }

    /**
     * Documents a checkout carries, as paths within it against the file they sit at
     *
     * What a repository keeps under a dotted name or in a folder of its tests is
     * its own business rather than documentation, and is left behind along with
     * what an entry excludes.
     *
     * @param string $checkout Folder the repository was unpacked into
     * @param array $exclusions Patterns of the files to leave behind
     * @return array
     */
    protected static function documents(string $checkout, array $exclusions): array
    {
        $documents = [];

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($checkout, FilesystemIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if (!$file->isFile() || !self::supported($file->getPathname())) {
                continue;
            }

            $path = str_replace('\\', '/', substr($file->getPathname(), strlen($checkout) + 1));

            if (self::hidden($path) || self::skipped($path) || self::excluded($path, $exclusions)) {
                continue;
            }

            $documents[$path] = $file->getPathname();
        }

        // The order a filesystem hands its files back in is its own, and a run should read the same every time
        ksort($documents);

        return $documents;
    }

    /**
     * Path of a document within its destination, as the documentation writes it
     *
     * The folders a document sits in are kept, under lowercase names like the
     * document itself, so that a url reads the same whichever repository it came from.
     *
     * @param string $path Path of the document within the checkout
     * @return string
     */
    protected static function nested(string $path): string
    {
        return str_replace('/', DIRECTORY_SEPARATOR, mb_strtolower($path));
    }

    /**
     * Whether a document sits in one of the folders no repository has documentation in
     *
     * Only the folders on the way to a document are read, so that a document
     * named after one of them is still a document.
     *
     * @param string $path Path of the document within the checkout
     * @return bool
     */
    protected static function skipped(string $path): bool
    {
        $folders = explode('/', $path);
        array_pop($folders);

        foreach ($folders as $folder) {
            if (in_array(mb_strtolower($folder), static::SKIPPED_DIRECTORIES, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether a document is one of those an entry leaves behind
     *
     * @param string $path Path of the document within the checkout
     * @param array $exclusions Patterns of the files to leave behind
     * @return bool
     */
    protected static function excluded(string $path, array $exclusions): bool
    {
        $path = mb_strtolower($path);
        $name = basename($path);

        foreach ($exclusions as $exclusion) {
            // A pattern is read against the whole path as well as the bare name, so both ways of naming a file work
            if (fnmatch($exclusion, $path) || fnmatch($exclusion, $name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Repository as it reads in a message
     *
     * @param array $repository Repository to name
     * @return string
     */
    protected static function name(array $repository): string
    {
        $name = $repository['owner'] . '/' . $repository['name'];

        return $repository['reference'] === null ? $name : $name . '@' . $repository['reference'];
    }

    /**
     * Client every request of this run goes through
     *
     * @return Api
     */
    protected static function api(): Api
    {
        if (self::$api !== null) {
            return self::$api;
        }

        $options = [CURLOPT_TIMEOUT => self::TIMEOUT, CURLOPT_CONNECTTIMEOUT => self::TIMEOUT];
        $certificates = self::certificates();

        // The client pins a handful of authorities of its own, too few for the hosts an archive is served from
        if ($certificates !== null) {
            $options[CURLOPT_CAINFO] = $certificates;
        }

        $client = extension_loaded('curl') ? new CurlClient($options) : new StreamClient();

        self::$api = new Api($client);
        $token = self::token();

        // Without a token GitHub answers sixty requests an hour and nothing from a private repository
        if ($token !== null) {
            self::$api->setToken(new Token($token));
        }

        return self::$api;
    }

    /**
     * Bundle of certificate authorities this machine trusts, or null when it names none
     *
     * @return string|null
     */
    protected static function certificates(): ?string
    {
        $locations = openssl_get_cert_locations();

        $candidates = [
            ini_get('curl.cainfo'),
            ini_get('openssl.cafile'),
            $locations['ini_cafile'] ?? null,
            $locations['default_cert_file'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '' && is_file($candidate) && is_readable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Access token the environment carries, or null when it carries none
     *
     * @return string|null
     */
    protected static function token(): ?string
    {
        foreach (self::TOKEN_VARIABLES as $variable) {
            $token = getenv($variable);

            if (is_string($token) && trim($token) !== '') {
                return trim($token);
            }
        }

        return null;
    }
}

<?php

namespace Phuture\App;

use Throwable;
use Latte\Engine;
use Pop\Controller\AbstractController;
use Phuture\App\Helper\{Document, Sidebar};

/**
 * Base of every controller a request is answered with.
 *
 * Holds what every page has in common: the template engine, the data shared by
 * every view such as the navigation and the path being served, and the ways a
 * request may be answered, whether as a rendered view, as a document of the
 * documentation, as json, or as the page that says nothing was found.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Controller extends AbstractController
{
    /**
     * View rendered when the one asked for is missing or unusable.
     *
     * Named without its extension, like every other view. It is what a reader is
     * shown for a url leading nowhere, and for a page the site knows about but
     * cannot render.
     *
     * @var string
     */
    protected const NOT_FOUND_VIEW = '404';

    /**
     * Template engine shared by every render of this request.
     *
     * Built the first time a view is rendered and kept for the rest of the request,
     * because one request may render a page and then the navigation around it.
     *
     * @var \Latte\Engine|null
     */
    private static ?Engine $engine = null;

    /**
     * Renders a view and prints it.
     *
     * The variables you pass are laid over the ones every view is given, such as the
     * navigation, so a view can override them where it needs to. A view that is missing,
     * or that breaks halfway through being rendered, is answered with the page that says
     * nothing was found rather than with half a page saying nothing.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller;
     *
     * Controller::render('search', ['title' => 'Search', 'query' => 'uuid']);
     *
     * // Prints the rendered search page
     * ```
     *
     * @param string|null $view Name of the view, without its .latte extension (default: null)
     * @param array $data Variables for the template, merged over the data shared by
     *  every view (default: [])
     * @return void
     * @see \Phuture\App\Controller::output()
     */
    protected static function render(?string $view = null, array $data = []): void
    {
        if ($view === null || $view === '' || !self::output($view, $data)) {
            self::renderNotFound();
        }
    }

    /**
     * Renders a document as a page and prints it.
     *
     * The document gives the page both its contents and its title, and a document
     * that cannot be read is answered with the page that says nothing was found.
     * A document with no title of its own borrows the name of the site.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller;
     *
     * Controller::renderDocument('/var/www/docs/index.md');
     *
     * // Prints the rendered docs/index.md
     * ```
     *
     * @param string|null $path Path of the document to render (default: null)
     * @return void
     * @see \Phuture\App\Controller::render()
     */
    protected static function renderDocument(?string $path = null): void
    {
        $content = $path !== null ? Document::file($path) : null;

        if ($content === null) {
            self::renderNotFound();

            return;
        }

        self::render('content', [
            'title' => Document::title($path) ?? APP_NAME,
            'content' => $content,
        ]);
    }

    /**
     * Answers with data rather than with a page.
     *
     * Used by the parts of a page that ask the site something while a reader is
     * still on it, such as the search bar looking results up as they type. Slashes
     * and accented letters are written as themselves rather than escaped, which
     * keeps a url in the answer readable.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller;
     *
     * Controller::renderJson(['query' => 'uuid', 'results' => []]);
     *
     * // Prints {"query":"uuid","results":[]}
     * ```
     *
     * @param array $data Data to answer with
     * @return void
     */
    protected static function renderJson(array $data): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }

        echo (string) json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Sends the page that says nothing was found.
     *
     * The status is set first, so a browser and a search engine are told the page is
     * missing whether or not there is anything to show them. When even that view
     * cannot be rendered the status goes out on its own rather than with an error.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller;
     *
     * Controller::renderNotFound();
     *
     * // Answers 404 and prints the page saying so
     * ```
     *
     * @return void
     */
    protected static function renderNotFound(): void
    {
        if (!headers_sent()) {
            http_response_code(404);
        }

        // Nothing is left to say when even this view cannot be rendered, so the status goes out on its own
        self::output(self::NOT_FOUND_VIEW, [
            'title' => 'Page not found',
            'heading' => 'Page not found',
            'description' => 'The page you are looking for does not exist or has been moved.',
        ]);
    }

    /**
     * Renders a view and prints it, saying whether there was anything to print.
     *
     * The variables you pass are laid over the ones every view is given, such as the
     * navigation, and the name of the site is laid over both so that no view can lose
     * it. The page is rendered to a string before any of it is printed, which means a
     * template that breaks halfway prints nothing at all and says so, rather than
     * leaving half a page behind for its caller to answer after.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller;
     *
     * $printed = Controller::output('search', ['title' => 'Search']);
     *
     * // Returns true, having printed the rendered search page
     * ```
     *
     * @param string $view Name of the view, without its .latte extension
     * @param array $data Variables for the template, merged over the data shared by
     *  every view
     * @return bool Returns true when the page was printed, false when the view is missing or broken
     * @see \Phuture\App\Controller::render()
     */
    private static function output(string $view, array $data): bool
    {
        $template = VIEWS_DIR . $view . '.latte';

        if (!is_file($template)) {
            return false;
        }

        $data = array_merge(self::sharedData(), $data, ['app_name' => APP_NAME]);

        try {
            $output = self::engine()->renderToString($template, $data);
        } catch (Throwable) {
            return false;
        }

        echo $output;

        return true;
    }

    /**
     * Builds the template engine every render of this request goes through.
     *
     * A template is compiled into php the first time it is rendered, and the result
     * is kept in the cache folder so that later requests skip the compiling. Where
     * that folder cannot be written to, the engine is used without it and compiles
     * each template again on every request.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller;
     *
     * $engine = Controller::engine();
     *
     * // Returns the engine, built on the first call and kept for the rest
     * ```
     *
     * @return \Latte\Engine The template engine of this request
     */
    protected static function engine(): Engine
    {
        if (self::$engine !== null) {
            return self::$engine;
        }

        self::$engine = new Engine();
        $cacheDirectory = self::cacheDirectory();

        if ($cacheDirectory !== null) {
            self::$engine->setCacheDirectory($cacheDirectory);
        }

        return self::$engine;
    }

    /**
     * Finds the directory the compiled templates are written to.
     *
     * The folder is created when it is missing, because a fresh checkout of the site
     * has no cache folder yet. A folder that cannot be created, or that cannot be
     * written to, is answered with null rather than with an error, and the site runs
     * on without a cache.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller;
     *
     * $directory = Controller::cacheDirectory();
     *
     * // Returns '/var/www/cache/' when that folder can be written to
     * ```
     *
     * @return string|null Path of the folder, or null when it cannot be used
     */
    protected static function cacheDirectory(): ?string
    {
        if (!is_dir(CACHE_DIR)) {
            @mkdir(CACHE_DIR, 0775, true);
        }

        return is_dir(CACHE_DIR) && is_writable(CACHE_DIR) ? CACHE_DIR : null;
    }

    /**
     * Gathers the data every view is given.
     *
     * Every page of the site is drawn inside the same navigation, so the navigation
     * and the path of the request being served are handed to every view rather than
     * to each of them by name. A view is free to override either of them.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller;
     *
     * $data = Controller::sharedData();
     *
     * // Returns ['sidebar' => [...], 'currentPath' => '/coherence']
     * ```
     *
     * @return array The navigation and the path of the request being served
     * @see \Phuture\App\Helper\Sidebar::tree()
     */
    protected static function sharedData(): array
    {
        $currentPath = self::currentPath();

        return [
            'sidebar' => Sidebar::tree($currentPath),
            'currentPath' => $currentPath,
        ];
    }

    /**
     * Reads the path of the request being served.
     *
     * Only the path is kept: whatever the url carries after a question mark says what
     * the page is being asked for rather than which page it is. A trailing slash is
     * taken off so that one page is never two, and the site root stays a lone slash.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller;
     *
     * // While serving /coherence/?q=uuid
     * $path = Controller::currentPath();
     *
     * // Returns '/coherence'
     * ```
     *
     * @return string The path of the request, without its query string or trailing slash
     */
    protected static function currentPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        return rtrim((string) $path, '/') ?: '/';
    }
}

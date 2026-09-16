<?php

namespace Phuture\App;

use Throwable;
use Latte\Engine;
use Pop\Controller\AbstractController;
use Phuture\App\Helper\{Document, Sidebar};

class Controller extends AbstractController
{
    /**
     * View rendered when the requested one is missing or unusable
     */
    protected const NOT_FOUND_VIEW = '404';

    /**
     * Template engine shared by every render of this request
     */
    private static ?Engine $engine = null;

    /**
     * Render a view, falling back to the 404 page when it cannot be rendered
     *
     * @param string|null $view Name of the view inside VIEWS_DIR, without the .latte extension
     * @param array $data Variables for the template, merged over the data shared by every view
     * @return void
     */
    protected static function render(?string $view = null, array $data = []): void
    {
        $template = VIEWS_DIR . $view . '.latte';

        if ($view === null || $view === '' || !is_file($template)) {
            self::renderNotFound();

            return;
        }

        // Set default data
        $data['app_name'] = APP_NAME;

        // Render to a string first, so a broken template does not leave half a page behind
        try {
            $output = self::engine()->renderToString($template, array_merge(self::sharedData(), $data));
        } catch (Throwable) {
            self::renderNotFound();

            return;
        }

        echo $output;
    }

    /**
     * Render a document as a page, falling back to the 404 page when there is none
     *
     * @param string|null $path Path of the document
     * @return void
     */
    protected static function renderDocument(?string $path = null): void
    {
        $content = $path !== null ? Document::file($path) : null;

        // Nothing to show without a document
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
     * Answer with json rather than a page, for whatever on the page is asking
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
     * Send the 404 page, or nothing but the status code when that view is unusable too
     *
     * @return void
     */
    protected static function renderNotFound(): void
    {
        if (!headers_sent()) {
            http_response_code(404);
        }

        $template = VIEWS_DIR . self::NOT_FOUND_VIEW . '.latte';

        if (!is_file($template)) {
            return;
        }

        $data = array_merge(self::sharedData(), [
            'app_name' => APP_NAME,
            'title' => 'Page not found',
            'heading' => 'Page not found',
            'description' => 'The page you are looking for does not exist or has been moved.',
        ]);

        try {
            echo self::engine()->renderToString($template, $data);
        } catch (Throwable) {
            // Nothing left to render with
        }
    }

    /**
     * Template engine, caching its compiled templates whenever it is allowed to
     *
     * @return Engine
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
     * Directory the compiled templates are written to, creating it when missing, or null when it cannot be used
     *
     * @return string|null
     */
    protected static function cacheDirectory(): ?string
    {
        if (!is_dir(CACHE_DIR)) {
            @mkdir(CACHE_DIR, 0775, true);
        }

        return is_dir(CACHE_DIR) && is_writable(CACHE_DIR) ? CACHE_DIR : null;
    }

    /**
     * Data shared by every view, overridable by the caller
     *
     * @return array
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
     * Path of the request being served, without query string or trailing slash
     *
     * @return string
     */
    protected static function currentPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        return rtrim((string) $path, '/') ?: '/';
    }
}

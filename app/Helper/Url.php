<?php

namespace Phuture\App\Helper;

/**
 * Writes the urls of the site under the path it is served from.
 *
 * A site is not always served from the root of a domain. Where it sits in a folder
 * of one, every url it writes has to carry that folder, or a link written for the
 * root of the domain leads away from the site entirely. The folder is worked out
 * once by the front controller and every url is written through here.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Url
{
    /**
     * Path the site is served from, without a slash of its own at the end.
     *
     * Empty while the site is served from the root of a domain, which is what a
     * document root pointing at the public folder gives, and the folder below it
     * otherwise, such as `/docs` for a site served from a folder of that name.
     *
     * @return string The path the site is served from, or an empty string at the root
     */
    public static function base(): string
    {
        return defined('BASE_PATH') ? rtrim((string) BASE_PATH, '/') : '';
    }

    /**
     * Writes a url of the site under the path it is served from.
     *
     * Takes a path counted from the root of the site and hands back the url a
     * browser is to ask for. The root of the site stays a lone slash whether or not
     * it sits in a folder of a domain.
     *
     * Example:
     * ```php
     * use Phuture\App\Helper\Url;
     *
     * $url = Url::to('/coherence');
     *
     * // Returns '/docs/coherence' while the site is served from /docs
     * ```
     *
     * @param string $path Path counted from the root of the site
     * @return string The url a browser is to ask for, opening with a slash
     */
    public static function to(string $path): string
    {
        return rtrim(self::base() . '/' . ltrim($path, '/'), '/') ?: '/';
    }
}

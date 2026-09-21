<?php

namespace Phuture\App\Controller;

use Phuture\App\{Controller, Helper\Search as SearchIndex};

/**
 * Controller answering a search over the documentation.
 *
 * Reads the words to look for out of the url and hands them to the index. The
 * results are sent back as a page of their own, or as data when the bar in the
 * navbar asks for them while a reader is still typing.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class Search extends Controller
{
    /**
     * Format asked for by the search bar in the navbar.
     *
     * The bar looks results up while a reader is still typing, and wants them as
     * data rather than as a page. Any other value, including none at all, is
     * answered with the page of results.
     *
     * @var string
     */
    protected const JSON_FORMAT = 'json';

    /**
     * Answers a search, as a page of results or as data for the bar in the navbar.
     *
     * Reads the words to look for out of the `q` parameter of the url and hands
     * them to the index. An empty query is not looked up at all: the page is sent
     * back with nothing on it but an invitation to type something.
     *
     * Example:
     * ```php
     * use Phuture\App\Controller\Search;
     *
     * // A request for /search?q=singleton
     * (new Search())->index();
     *
     * // Prints the page of results for the word 'singleton'
     * ```
     *
     * @return void
     * @see \Phuture\App\Helper\Search::results()
     */
    public function index(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        $results = $query === '' ? [] : SearchIndex::results($query);

        if (($_GET['format'] ?? '') === self::JSON_FORMAT) {
            self::renderJson(['query' => $query, 'results' => $results]);

            return;
        }

        self::render('search', [
            'title' => $query === '' ? 'Search' : 'Search for ' . $query,
            'query' => $query,
            'results' => $results,
        ]);
    }
}

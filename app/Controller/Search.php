<?php

namespace Phuture\App\Controller;

use Phuture\App\{Controller, Helper\Search as Index};

class Search extends Controller
{
    /**
     * Format the page asks for when it is looking things up for a reader as they type
     */
    protected const JSON_FORMAT = 'json';

    /**
     * Answer a search, as a page of results or as json for the bar in the navbar
     *
     * @return void
     */
    public function index(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        $results = $query === '' ? [] : Index::results($query);

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

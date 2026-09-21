<?php

namespace Phuture\App\Tests;

use Tester\Assert;
use Tester\TestCase;
use ReflectionMethod;
use Phuture\App\Helper\Search;

require __DIR__ . '/bootstrap.php';

/**
 * Tests of \Phuture\App\Helper\Search.
 *
 * @copyright Copyright (c) 2026, Advandz Technologies, LLC
 * @license https://opensource.org/licenses/MIT MIT License
 * @link https://www.phuture.dev/ Phuture
 */
class SearchTest extends TestCase
{
    /**
     * Calls a method the class keeps to itself.
     *
     * The index is built out of several of them, and each carries a rule worth
     * checking on its own rather than only through a whole search.
     *
     * Example:
     * ```php
     * $words = $this->call('score', [$entry, ['singleton']]);
     *
     * // Returns what the entry is worth as an answer
     * ```
     *
     * @param string $name Name of the method to call
     * @param array $arguments Arguments to call it with
     * @return mixed Whatever the method hands back
     */
    private function call(string $name, array $arguments): mixed
    {
        $method = new ReflectionMethod(Search::class, $name);
        $method->setAccessible(true);

        return $method->invokeArgs(null, $arguments);
    }

    public function setUp(): void
    {
        @unlink(CACHE_DIR . Search::INDEX_FILE);
    }

    public function testWords(): void
    {
        Assert::same(['random', 'string'], Search::words('  Random   String '));

        // Nothing shorter than two characters is worth looking up
        Assert::same(['random'], Search::words('Random a'));

        Assert::same([], Search::words('   '));
        Assert::same([], Search::words('a'));
    }

    public function testIndex(): void
    {
        $index = Search::index();
        $urls = array_column($index, 'url');

        Assert::contains('/guide', $urls);
        Assert::contains('/package', $urls);
        Assert::contains('/page', $urls);

        // The navigation drives the site rather than being a page of it
        Assert::notContains('/sidebar', $urls);

        Assert::true(is_file(CACHE_DIR . Search::INDEX_FILE));
    }

    public function testResults(): void
    {
        $results = Search::results('singleton');

        Assert::count(1, $results);
        Assert::same('/guide', $results[0]['url']);
        Assert::same('singletons', $results[0]['anchor']);
        Assert::contains('singleton', mb_strtolower($results[0]['snippet']));

        // The text a result is quoted from is not carried back with it
        Assert::false(isset($results[0]['text']));

        // Every word has to show up, so a second word narrows rather than widens
        Assert::count(0, Search::results('singleton nowhere'));

        Assert::same([], Search::results(''));
        Assert::same([], Search::results('a'));
        Assert::count(1, Search::results('fixture', 1));
    }

    public function testDocuments(): void
    {
        $documents = $this->call('documents', []);

        Assert::contains(DOCS_DIR . 'guide.md', $documents);
        Assert::contains(DOCS_DIR . 'page.html', $documents);
        Assert::notContains(DOCS_DIR . 'sidebar.md', $documents);

        // A run reads the same order every time, whatever order the filesystem holds
        $sorted = $documents;
        sort($sorted);

        Assert::same($sorted, $documents);
    }

    public function testEntry(): void
    {
        $entry = $this->call('entry', [DOCS_DIR . 'guide.md']);

        Assert::same('/guide', $entry['url']);
        Assert::same('Fixture Guide', $entry['title']);
        Assert::contains('singleton', mb_strtolower($entry['text']));

        // Squeezed onto one line, so a quote out of it reads as a sentence
        Assert::notContains("\n", $entry['text']);

        Assert::null($this->call('entry', [DOCS_DIR . 'nowhere.md']));
    }

    public function testHeadings(): void
    {
        $headings = $this->call('headings', [DOCS_DIR . 'guide.md']);

        Assert::same('Fixture Guide', $headings[0]['text']);
        Assert::same('fixture-guide', $headings[0]['anchor']);
        Assert::same('Singletons', $headings[1]['text']);
        Assert::same('singletons', $headings[1]['anchor']);

        // A page written as html is searched, but its results open it at the top
        Assert::same([], $this->call('headings', [DOCS_DIR . 'page.html']));
    }

    public function testScore(): void
    {
        $entry = [
            'title' => 'Strings',
            'headings' => [['text' => 'Random', 'anchor' => 'random']],
            'text' => 'Generates a random string',
        ];

        // The heading and the text hold it, the title does not
        Assert::same(26, $this->call('score', [$entry, ['random']]));

        // Only the title holds it, which is worth more on its own than either of the others
        Assert::same(100, $this->call('score', [$entry, ['strings']]));

        // Held in the title, in a heading and in the text, and worth all three together
        $everywhere = ['title' => 'Random', 'headings' => [['text' => 'Random']], 'text' => 'random'];

        Assert::same(126, $this->call('score', [$everywhere, ['random']]));

        // A word found nowhere leaves the document no answer at all
        Assert::same(0, $this->call('score', [$entry, ['random', 'nowhere']]));
    }

    public function testAnchor(): void
    {
        $entry = [
            'headings' => [
                ['text' => 'Introduction', 'anchor' => 'introduction'],
                ['text' => 'Random strings', 'anchor' => 'random-strings'],
            ],
        ];

        Assert::same('random-strings', $this->call('anchor', [$entry, ['random']]));
        Assert::same('random-strings', $this->call('anchor', [$entry, ['random', 'strings']]));
        Assert::null($this->call('anchor', [$entry, ['nowhere']]));
        Assert::null($this->call('anchor', [['headings' => []], ['random']]));
    }

    public function testSnippet(): void
    {
        $text = 'Generates a cryptographically secure random string of characters';

        // Opened a little ahead of the word, so it is read in its sentence
        Assert::contains('random', $this->call('snippet', [$text, ['random'], 40]));

        // Nothing found, so the quote opens at the beginning
        Assert::same('Generates a', $this->call('snippet', [$text, ['nowhere'], 12]));

        Assert::same($text, $this->call('snippet', [$text, ['generates'], 400]));
    }

    public function testFingerprint(): void
    {
        $one = $this->call('fingerprint', [[DOCS_DIR . 'guide.md']]);
        $two = $this->call('fingerprint', [[DOCS_DIR . 'guide.md']]);

        Assert::same($one, $two);
        Assert::notSame($one, $this->call('fingerprint', [[DOCS_DIR . 'index.md']]));
        Assert::same(32, strlen($one));
    }
}

(new SearchTest())->run();

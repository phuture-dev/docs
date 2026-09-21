# Strings

`Phuture\Coherence\Type\Strings`

```php
class Strings extends FluentClass implements Stringable, \Stringable
```

A fluent wrapper around the Strings utility class for chainable string manipulation.

Each method delegates to the corresponding static method on `\Phuture\Coherence\Strings`, stores the
result internally, and returns `$this` to enable method chaining. Retrieve the final
value by calling `get()` or invoking the object directly.

**Example:**
```php
use Phuture\Coherence\Type\Strings;

$result = Strings::from('  hello_world  ')
    ->trim()
    ->replace('_', ' ')
    ->upper()
    ->take(5)
    ->get();
// 'HELLO'
```

## Methods

### `__toString()`

```php
public function __toString(): string
```

Returns the string representation of the wrapped value.

**Returns** `string` — The wrapped string value

### `addCSlashes()`

```php
public function addCSlashes(string $characters): self
```

Escapes specific characters using C-style backslash notation.

| Parameter | Type | Description |
| --- | --- | --- |
| `$characters` | `string` | The list of characters to escape |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::addCSlashes()`

### `addSlashes()`

```php
public function addSlashes(): self
```

Escapes single quotes, double quotes, backslashes, and NUL bytes.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::addSlashes()`

### `after()`

```php
public function after(string $search): self
```

Returns the portion of the string after the first occurrence of a search value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$search` | `string` | The value to search for |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::after()`

### `afterLast()`

```php
public function afterLast(string $search): self
```

Returns the portion of the string after the last occurrence of a search value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$search` | `string` | The value to search for |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::afterLast()`

### `ascii()`

```php
public function ascii(string $language = 'en'): self
```

Transliterates the string to its ASCII representation.

| Parameter | Type | Description |
| --- | --- | --- |
| `$language` | `string` | The language code for locale-specific rules (default: 'en') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::ascii()`

### `before()`

```php
public function before(string $search): self
```

Returns the portion of the string before the first occurrence of a search value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$search` | `string` | The value to search for |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::before()`

### `beforeLast()`

```php
public function beforeLast(string $search): self
```

Returns the portion of the string before the last occurrence of a search value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$search` | `string` | The value to search for |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::beforeLast()`

### `between()`

```php
public function between(string $start, string $end): self
```

Returns the portion of the string between two delimiter values.

| Parameter | Type | Description |
| --- | --- | --- |
| `$start` | `string` | The opening delimiter |
| `$end` | `string` | The closing delimiter |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::between()`

### `camel()`

```php
public function camel(): self
```

Converts the string to camelCase.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::camel()`

### `censor()`

```php
public function censor(array $bannedWords, string $replacement = '***'): self
```

Censors all occurrences of banned words by replacing them with a substitution.

| Parameter | Type | Description |
| --- | --- | --- |
| `$bannedWords` | `array` | List of word strings to replace; each element must be a string |
| `$replacement` | `string` | The string to substitute for each matched word (default: '***') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::censor()`

### `charAt()`

```php
public function charAt(int $index): self
```

Returns the character at the given index position.

| Parameter | Type | Description |
| --- | --- | --- |
| `$index` | `int` | The zero-based character index (negative counts from the end) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::charAt()`

### `dedupe()`

```php
public function dedupe(string $character = ' '): self
```

Collapses consecutive duplicate occurrences of a character.

| Parameter | Type | Description |
| --- | --- | --- |
| `$character` | `string` | The character to collapse (default: space) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::dedupe()`

### `distance()`

```php
public function distance(string $other): self
```

Calculates the Levenshtein edit distance between the string and another string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$other` | `string` | The string to compare against |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::distance()`

### `entityDecode()`

```php
public function entityDecode(int $flags = ENT_QUOTES | ENT_SUBSTITUTE): self
```

Converts HTML entities back to their corresponding characters.

| Parameter | Type | Description |
| --- | --- | --- |
| `$flags` | `int` | Bitmask of ENT_* constants (default: ENT_QUOTES \| ENT_SUBSTITUTE) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::entityDecode()`

### `entityEncode()`

```php
public function entityEncode(int $flags = ENT_QUOTES | ENT_SUBSTITUTE): self
```

Converts all applicable characters to HTML entities.

| Parameter | Type | Description |
| --- | --- | --- |
| `$flags` | `int` | Bitmask of ENT_* constants (default: ENT_QUOTES \| ENT_SUBSTITUTE) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::entityEncode()`

### `excerpt()`

```php
public function excerpt(string $phrase, int $radius = 100, string $omission = '...'): self
```

Extracts a contextual excerpt of the string around a given phrase.

| Parameter | Type | Description |
| --- | --- | --- |
| `$phrase` | `string` | The phrase to center the excerpt around |
| `$radius` | `int` | The number of characters to include on each side (default: 100) |
| `$omission` | `string` | The string to append at truncated ends (default: '...') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::excerpt()`

### `finish()`

```php
public function finish(string $suffix): self
```

Ensures the string ends with exactly one occurrence of the given suffix.

| Parameter | Type | Description |
| --- | --- | --- |
| `$suffix` | `string` | The suffix to ensure is present exactly once |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::finish()`

### `first()`

```php
public function first(int $count = 1): self
```

Returns the first N characters of the string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$count` | `int` | The number of characters to return (default: 1) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::first()`

### `fixEncoding()`

```php
public function fixEncoding(): self
```

Fixes invalid byte sequences in the string using the current encoding.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::fixEncoding()`

### `fromBase64()`

```php
public function fromBase64(): self
```

Decodes a Base64-encoded string.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::fromBase64()`

### `fromHex()`

```php
public function fromHex(): self
```

Decodes a hex-encoded binary string.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::fromHex()`

### `hamming()`

```php
public function hamming(string $other): self
```

Calculates the Hamming distance between the string and another string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$other` | `string` | The string to compare against; must have the same character length |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::hamming()`

### `headline()`

```php
public function headline(): self
```

Converts the string to a human-readable headline format.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::headline()`

### `highlight()`

```php
public function highlight(string $phrase, string $tagOpen = '<mark>', string $tagClose = '</mark>'): self
```

Highlights all occurrences of a phrase by wrapping them in tags.

| Parameter | Type | Description |
| --- | --- | --- |
| `$phrase` | `string` | The phrase to highlight |
| `$tagOpen` | `string` | The opening tag (default: '<mark>') |
| `$tagClose` | `string` | The closing tag (default: '</mark>') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::highlight()`

### `indent()`

```php
public function indent(int $level = 1, string $indentChar = "\t"): self
```

Adds indentation to each line of the string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$level` | `int` | The number of times to repeat the indent character (default: 1) |
| `$indentChar` | `string` | The character(s) used for one level of indentation (default: "\t") |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::indent()`

### `insert()`

```php
public function insert(string $substring, int $index): self
```

Inserts a substring into the string at the given index position.

| Parameter | Type | Description |
| --- | --- | --- |
| `$substring` | `string` | The substring to insert |
| `$index` | `int` | The zero-based position to insert at (negative counts from the end) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::insert()`

### `jaro()`

```php
public function jaro(string $other): self
```

Calculates the Jaro similarity between the string and another string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$other` | `string` | The string to compare against |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::jaro()`

### `jaroWinkler()`

```php
public function jaroWinkler(string $other, float $prefixScale = 0.1): self
```

Calculates the Jaro-Winkler similarity between the string and another string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$other` | `string` | The string to compare against |
| `$prefixScale` | `float` | How much weight to give the common prefix; must not exceed 0.25 (default: 0.1) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::jaroWinkler()`

### `kebab()`

```php
public function kebab(): self
```

Converts the string to kebab-case.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::kebab()`

### `last()`

```php
public function last(int $count = 1): self
```

Returns the last N characters of the string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$count` | `int` | The number of characters to return (default: 1) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::last()`

### `limit()`

```php
public function limit(int $limit, string $end = ''): self
```

Limits the string to a given number of characters, appending an omission marker.

| Parameter | Type | Description |
| --- | --- | --- |
| `$limit` | `int` | The maximum number of characters before truncation |
| `$end` | `string` | The string to append after truncation (default: '...') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::limit()`

### `lower()`

```php
public function lower(): self
```

Converts the string to lowercase.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::lower()`

### `lowerFirst()`

```php
public function lowerFirst(): self
```

Converts only the first character of the string to lowercase.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::lowerFirst()`

### `mask()`

```php
public function mask(string $mask = '*', int $offset = 0, ?int $length = null): self
```

Masks a portion of the string with a repeated mask character.

| Parameter | Type | Description |
| --- | --- | --- |
| `$mask` | `string` | The mask character to use (default: '*') |
| `$offset` | `int` | The start position to begin masking |
| `$length` | `int|null` | The number of characters to mask (null masks to the end) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::mask()`

### `metaphone()`

```php
public function metaphone(int $maxPhonemes = 0): self
```

Calculates the metaphone phonetic key of the string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$maxPhonemes` | `int` | The maximum number of phonemes to return; 0 means no limit (default: 0) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::metaphone()`

### `nl2br()`

```php
public function nl2br(bool $useXhtml = true): self
```

Inserts HTML line breaks before all newlines.

| Parameter | Type | Description |
| --- | --- | --- |
| `$useXhtml` | `bool` | Whether to use XHTML-compatible tags (default: true) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::nl2br()`

### `normalizeNewLines()`

```php
public function normalizeNewLines(): self
```

Normalizes line endings to Unix-style `\n`.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::normalizeNewLines()`

### `pad()`

```php
public function pad(int $length, string $padString = ' ', PadDirection $direction = PadDirection::Right): self
```

Pads the string to a given length using a pad string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$length` | `int` | The target total length in characters |
| `$padString` | `string` | The string to pad with (default: space) |
| `$direction` | `\Phuture\Coherence\Enum\PadDirection` | Which side to pad — Right, Left, or Both (default: PadDirection::Right) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::pad()`
- `\Phuture\Coherence\Enum\PadDirection`

### `pascal()`

```php
public function pascal(): self
```

Converts the string to PascalCase (StudlyCase).

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::pascal()`

### `quoteMeta()`

```php
public function quoteMeta(): self
```

Escapes regular expression meta-characters.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::quoteMeta()`

### `remove()`

```php
public function remove(string $search, bool $caseSensitive = true): self
```

Removes all occurrences of a search value from the string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$search` | `string` | The value to remove |
| `$caseSensitive` | `bool` | Whether the removal is case-sensitive (default: true) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::remove()`

### `repeat()`

```php
public function repeat(int $times): self
```

Repeats the string a given number of times.

| Parameter | Type | Description |
| --- | --- | --- |
| `$times` | `int` | The number of repetitions (must be zero or greater) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::repeat()`

### `replace()`

```php
public function replace(string $search, string $replace, bool $caseSensitive = true): self
```

Replaces all occurrences of a search value with a replacement.

| Parameter | Type | Description |
| --- | --- | --- |
| `$search` | `string` | The value to search for |
| `$replace` | `string` | The replacement value |
| `$caseSensitive` | `bool` | Whether the replacement is case-sensitive (default: true) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::replace()`

### `replaceArray()`

```php
public function replaceArray(string $search, array $replacements): self
```

Replaces successive occurrences of a search value using values from an array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$search` | `string` | The value to search for |
| `$replacements` | `array` | Ordered list of string replacement values |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::replaceArray()`

### `replaceAt()`

```php
public function replaceAt(string $replacement, int $position, ?int $length = null): self
```

Replaces a portion of the string starting at a given character position.

| Parameter | Type | Description |
| --- | --- | --- |
| `$replacement` | `string` | The text to insert at the given position |
| `$position` | `int` | The character index at which to begin replacement (negative counts from end) |
| `$length` | `int|null` | The number of characters to replace (null replaces to end of string) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::replaceAt()`

### `replaceFirst()`

```php
public function replaceFirst(string $search, string $replace): self
```

Replaces the first occurrence of a search value with a replacement.

| Parameter | Type | Description |
| --- | --- | --- |
| `$search` | `string` | The value to search for |
| `$replace` | `string` | The replacement value |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::replaceFirst()`

### `replaceLast()`

```php
public function replaceLast(string $search, string $replace): self
```

Replaces the last occurrence of a search value with a replacement.

| Parameter | Type | Description |
| --- | --- | --- |
| `$search` | `string` | The value to search for |
| `$replace` | `string` | The replacement value |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::replaceLast()`

### `reverse()`

```php
public function reverse(): self
```

Reverses the string character by character.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::reverse()`

### `rot13()`

```php
public function rot13(): self
```

Applies the ROT13 encoding to the string.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::rot13()`

### `scrub()`

```php
public function scrub(): self
```

Removes dangerous control characters from the string.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::scrub()`

### `search()`

```php
public function search(string $search, bool $beforeNeedle = false, bool $caseSensitive = true): self
```

Returns the portion of the string from the first occurrence of a search value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$search` | `string` | The value to search for |
| `$beforeNeedle` | `bool` | Return the part before the search value (default: false) |
| `$caseSensitive` | `bool` | Whether the search is case-sensitive (default: true) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::search()`

### `setEncoding()`

```php
public function setEncoding(string $encoding): self
```

Sets the character encoding used for all multibyte operations in the chain.

Changes the encoding applied by every subsequent method call on this instance.
The default encoding is 'UTF-8'.

**Example:**
```php
use Phuture\Coherence\Type\Strings;

$result = Strings::from('héllo')
    ->setEncoding('UTF-8')
    ->upper()
    ->get();
// Returns 'HÉLLO'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$encoding` | `string` | The character encoding to use (e.g. 'UTF-8', 'ISO-8859-1') |

**Returns** `self` — Returns the current instance for method chaining

### `shuffle()`

```php
public function shuffle(): self
```

Randomly shuffles the characters in the string.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::shuffle()`

### `similar()`

```php
public function similar(string $other): self
```

Calculates the similarity percentage between the string and another string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$other` | `string` | The string to compare against |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::similar()`

### `slice()`

```php
public function slice(int $start, ?int $length = null): self
```

Extracts a portion of the string by start position and optional length.

| Parameter | Type | Description |
| --- | --- | --- |
| `$start` | `int` | The starting position (negative counts from the end) |
| `$length` | `int|null` | The number of characters to return (null returns to the end) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::slice()`

### `slug()`

```php
public function slug(string $separator = '-', string $language = 'en'): self
```

Generates a URL-friendly slug from the string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$separator` | `string` | The separator character between words (default: '-') |
| `$language` | `string` | The language code for transliteration (default: 'en') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::slug()`

### `snake()`

```php
public function snake(string $delimiter = '_'): self
```

Converts the string to snake_case with a configurable delimiter.

| Parameter | Type | Description |
| --- | --- | --- |
| `$delimiter` | `string` | The word separator character (default: '_') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::snake()`

### `soundex()`

```php
public function soundex(): self
```

Calculates the soundex phonetic key of the string.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::soundex()`

### `squish()`

```php
public function squish(): self
```

Collapses all whitespace sequences into a single space and trims the result.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::squish()`

### `start()`

```php
public function start(string $prefix): self
```

Ensures the string begins with exactly one occurrence of the given prefix.

| Parameter | Type | Description |
| --- | --- | --- |
| `$prefix` | `string` | The prefix to ensure is present exactly once |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::start()`

### `strip()`

```php
public function strip(string $allowedTags = ''): self
```

Strips HTML and PHP tags from the string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$allowedTags` | `string` | HTML tags to preserve (default: '' = strip all) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::strip()`

### `stripCSlashes()`

```php
public function stripCSlashes(): self
```

Removes C-style backslash escapes.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::stripCSlashes()`

### `stripSlashes()`

```php
public function stripSlashes(): self
```

Removes backslash escapes added by addSlashes.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::stripSlashes()`

### `swap()`

```php
public function swap(array $replacements): self
```

Performs multiple simultaneous search-and-replace operations.

| Parameter | Type | Description |
| --- | --- | --- |
| `$replacements` | `array` | An associative array where each key is the text to find and each value is the text to substitute |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::swap()`

### `take()`

```php
public function take(int $count): self
```

Returns the first or last N characters based on the sign of count.

| Parameter | Type | Description |
| --- | --- | --- |
| `$count` | `int` | Positive returns first N; negative returns last N characters |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::take()`

### `title()`

```php
public function title(): self
```

Converts every word in the string to Title Case.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::title()`

### `toBase64()`

```php
public function toBase64(): self
```

Encodes the string to its Base64 representation.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::toBase64()`

### `toHex()`

```php
public function toHex(): self
```

Converts the string to its hexadecimal representation.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::toHex()`

### `toString()`

```php
public function toString(): string
```

Converts the wrapped value to a string.

**Returns** `string` — The wrapped string value

### `trim()`

```php
public function trim(?string $characters = null): self
```

Strips whitespace (or given characters) from both ends of the string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$characters` | `string|null` | The characters to strip (default: whitespace) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::trim()`

### `trimLeft()`

```php
public function trimLeft(?string $characters = null): self
```

Strips whitespace (or given characters) from the beginning of the string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$characters` | `string|null` | The characters to strip (default: whitespace) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::trimLeft()`

### `trimRight()`

```php
public function trimRight(?string $characters = null): self
```

Strips whitespace (or given characters) from the end of the string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$characters` | `string|null` | The characters to strip (default: whitespace) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::trimRight()`

### `unwrap()`

```php
public function unwrap(string $wrapper): self
```

Removes a surrounding wrapper string from both ends of the string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$wrapper` | `string` | The wrapper string to remove from both ends |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::unwrap()`

### `upper()`

```php
public function upper(): self
```

Converts the string to uppercase.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::upper()`

### `upperFirst()`

```php
public function upperFirst(): self
```

Converts only the first character of the string to uppercase.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::upperFirst()`

### `wordWrap()`

```php
public function wordWrap(int $width = 75, string $break = "\n", bool $cutLongWords = false): self
```

Wraps the string at a given number of characters.

| Parameter | Type | Description |
| --- | --- | --- |
| `$width` | `int` | The number of characters at which to wrap (default: 75) |
| `$break` | `string` | The line break string to insert (default: "\n") |
| `$cutLongWords` | `bool` | Whether to cut words longer than width (default: false) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::wordWrap()`

### `wrap()`

```php
public function wrap(string $wrapper): self
```

Wraps the string with a given wrapper string on both sides.

| Parameter | Type | Description |
| --- | --- | --- |
| `$wrapper` | `string` | The string to prepend and append |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::wrap()`

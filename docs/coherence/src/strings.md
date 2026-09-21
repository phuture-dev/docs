# Strings

`Phuture\Coherence\Strings`

```php
class Strings extends StaticClass
```

Comprehensive string manipulation utility class with multibyte-safe operations.

This utility class provides a complete toolkit for string manipulation, including case
conversion, pattern matching, extraction, modification, splitting, joining, validation,
truncation, encoding conversion, and miscellaneous string operations.

Key features:

- **Core Operations**: Length, case conversion, reversal, and repetition
- **Search & Matching**: Find substrings, check for containment, and locate positions
- **Extraction**: Extract portions before, after, or between delimiters
- **Modification**: Replace, remove, trim, pad, and insert substrings
- **Case Conversion**: camel, snake, kebab, pascal, headline
- **Splitting & Joining**: Split strings into arrays by patterns or delimiters
- **Counting & Comparison**: Count occurrences, compare strings, check equality
- **Truncation & Wrapping**: Limit length, wrap text, and extract excerpts
- **Testing & Checking**: Validate URLs, emails, UUIDs, ASCII, JSON, and more
- **Encoding & Conversion**: Transliterate to ASCII, generate slugs
- **Miscellaneous**: Mask, random strings, UUIDs, chunking, and swapping

## Constants

### `MAX_DECIMALS`

```php
const MAX_DECIMALS = 100
```

Highest number of decimal places accepted by `numberFormat()`.

Formatting with an arbitrarily large number of decimals allocates a string of that length,
which exhausts memory long before the result is useful. PHP 8.6 rejects out-of-range values
outright, so the limit is enforced here to keep the behaviour identical across versions.

## Methods

### `addCSlashes()`

```php
public static function addCSlashes(string $string, string $characters): string
```

Escapes specific characters in a string using C-style backslash notation.

Wraps PHP's native `addcslashes()`. Characters listed in `$characters` are
escaped with backslashes. Supports ranges like `\n..\r` and `\0..\31`.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::addCSlashes('hello world', 'aeiou'); // 'h\\ell\\o w\\orld'
Strings::addCSlashes("hello\x00world", "\x00"); // 'hello\0world'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to escape |
| `$characters` | `string` | The list of characters to escape |

**Returns** `string` — The C-style escaped string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$characters` is empty

**See also**

- `\Phuture\Coherence\Strings::stripCSlashes()`

### `addSlashes()`

```php
public static function addSlashes(string $string): string
```

Escapes single quotes, double quotes, backslashes, and NUL bytes in a string.

Wraps PHP's native `addslashes()`. Useful for preparing strings for database
queries or other contexts that require backslash escaping.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::addSlashes("hello 'world'"); // "hello \\'world\\'"
Strings::addSlashes('path\\to\\file'); // 'path\\\\to\\\\file'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to escape |

**Returns** `string` — The escaped string

**See also**

- `\Phuture\Coherence\Strings::stripSlashes()`

### `after()`

```php
public static function after(string $string, string $search, string $encoding = 'UTF-8'): string
```

Returns the portion of the string after the first occurrence of a search value.

Searches for the first occurrence of `$search` in `$string` and returns everything
that follows it. Returns an empty string when the search value is not found.
Returns the original string unchanged when `$search` is an empty string.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::after('user@example.com', '@'); // 'example.com'
Strings::after('2023-12-25', '-'); // '12-25'
Strings::after('hello', 'x'); // ''
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$search` | `string` | The value to search for |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The substring after the first occurrence, or an empty string if not found

**See also**

- `\Phuture\Coherence\Strings::afterLast()`
- `\Phuture\Coherence\Strings::before()`

### `afterLast()`

```php
public static function afterLast(string $string, string $search, string $encoding = 'UTF-8'): string
```

Returns the portion of the string after the last occurrence of a search value.

Searches for the last occurrence of `$search` in `$string` and returns everything
that follows it. Returns an empty string when the search value is not found.
Returns the original string unchanged when `$search` is an empty string.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::afterLast('path/to/file.txt', '/'); // 'file.txt'
Strings::afterLast('a.b.c', '.'); // 'c'
Strings::afterLast('hello', 'x'); // ''
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$search` | `string` | The value to search for |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The substring after the last occurrence, or an empty string if not found

**See also**

- `\Phuture\Coherence\Strings::after()`
- `\Phuture\Coherence\Strings::beforeLast()`

### `ascii()`

```php
public static function ascii(string $string, string $language = 'en'): string
```

Transliterates a string to its ASCII representation.

Converts accented and non-ASCII characters to their closest ASCII equivalents,
then strips any remaining non-printable ASCII characters. Supports language-specific
transliteration rules (e.g., German umlauts).

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::ascii('héllo'); // 'hello'
Strings::ascii('ñaño'); // 'nano'
Strings::ascii('über', 'de'); // 'ueber'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to transliterate |
| `$language` | `string` | The language code for locale-specific rules (default: 'en') |

**Returns** `string` — The ASCII-safe string

**See also**

- `\Phuture\Coherence\Strings::slug()`

### `asciiArt()`

```php
public static function asciiArt(string $text, string $font = 'block', string $encoding = 'UTF-8'): string
```

Generates an ASCII art representation of the given text using a block font.

Renders each character of `$text` as a 5-row tall block-style ASCII art figure.
Supports uppercase and lowercase letters A–Z (normalised to uppercase), digits 0–9,
and common punctuation. Unsupported characters are rendered as blank columns.

**Example:**
```php
use Phuture\Coherence\Strings;

echo Strings::asciiArt('Hi');
// #    # ######
// #    #   ##
// ######   ##
// #    #   ##
// #    # ######
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$text` | `string` | The text to render as ASCII art |
| `$font` | `string` | The font name to use — currently only 'block' is supported |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The multi-line ASCII art string

**See also**

- `\Phuture\Coherence\Strings::ascii()`

### `before()`

```php
public static function before(string $string, string $search, string $encoding = 'UTF-8'): string
```

Returns the portion of the string before the first occurrence of a search value.

Searches for the first occurrence of `$search` in `$string` and returns everything
that precedes it. Returns the original string when the search value is not found
or when `$search` is an empty string.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::before('user@example.com', '@'); // 'user'
Strings::before('2023-12-25', '-'); // '2023'
Strings::before('hello', 'x'); // 'hello'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$search` | `string` | The value to search for |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The substring before the first occurrence, or the original string if not found

**See also**

- `\Phuture\Coherence\Strings::beforeLast()`
- `\Phuture\Coherence\Strings::after()`

### `beforeLast()`

```php
public static function beforeLast(string $string, string $search, string $encoding = 'UTF-8'): string
```

Returns the portion of the string before the last occurrence of a search value.

Searches for the last occurrence of `$search` in `$string` and returns everything
that precedes it. Returns the original string when the search value is not found
or when `$search` is an empty string.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::beforeLast('path/to/file.txt', '/'); // 'path/to'
Strings::beforeLast('a.b.c', '.'); // 'a.b'
Strings::beforeLast('hello', 'x'); // 'hello'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$search` | `string` | The value to search for |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The substring before the last occurrence, or the original string if not found

**See also**

- `\Phuture\Coherence\Strings::before()`
- `\Phuture\Coherence\Strings::afterLast()`

### `between()`

```php
public static function between(string $string, string $start, string $end, string $encoding = 'UTF-8'): string
```

Returns the portion of the string between two delimiter values.

Finds the first occurrence of `$start` and the first occurrence of `$end` after it,
and returns everything in between. Returns the original string when either delimiter
is empty or not found.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::between('[hello]', '[', ']'); // 'hello'
Strings::between('user@example.com', '@', '.'); // 'example'
Strings::between('hello', '{', '}'); // 'hello'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$start` | `string` | The opening delimiter |
| `$end` | `string` | The closing delimiter |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The substring between the delimiters, or the original string if not found

**See also**

- `\Phuture\Coherence\Strings::before()`
- `\Phuture\Coherence\Strings::after()`

### `camel()`

```php
public static function camel(string $string): string
```

Converts a string to camelCase.

Words separated by spaces, hyphens, underscores, or CamelCase boundaries are joined
together with each word (except the first) capitalised. The result starts with a
lowercase letter.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::camel('hello world'); // 'helloWorld'
Strings::camel('hello_world'); // 'helloWorld'
Strings::camel('hello-world'); // 'helloWorld'
Strings::camel('HelloWorld'); // 'helloWorld'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to convert |

**Returns** `string` — The camelCase version of the string

**See also**

- `\Phuture\Coherence\Strings::pascal()`
- `\Phuture\Coherence\Strings::snake()`

### `censor()`

```php
public static function censor(string $string, array $bannedWords, string $replacement = '***'): string
```

Censors all occurrences of banned words in a string by replacing them with a substitution.

Matching is case-insensitive. Each matched word is replaced with `$replacement` in full,
regardless of the matched word's length. The string is returned unchanged when `$bannedWords`
is empty.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::censor('This is bad and awful', ['bad', 'awful']); // 'This is *** and ***'
Strings::censor('BAD language', ['bad'], '####'); // '#### language'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to censor |
| `$bannedWords` | `array` | List of word strings to replace |
| `$replacement` | `string` | The string to substitute for each matched word (default: '***') |

**Returns** `string` — The censored string

**See also**

- `\Phuture\Coherence\Strings::replace()`

### `charAt()`

```php
public static function charAt(string $string, int $index, string $encoding = 'UTF-8'): string
```

Returns the character at the given index position.

Supports negative indices to count from the end of the string.
Returns an empty string when the index is out of bounds.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::charAt('hello', 0); // 'h'
Strings::charAt('hello', -1); // 'o'
Strings::charAt('hello', 10); // ''
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to index into |
| `$index` | `int` | The zero-based character index (negative counts from the end) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The character at the given position, or an empty string if out of bounds

**See also**

- `\Phuture\Coherence\Strings::slice()`

### `charCounts()`

```php
public static function charCounts(string $string, CharCountMode $mode = CharCountMode::All): array|int|string
```

Returns information about the byte values used in a string.

`CharCountMode::All` returns an array with all 256 possible byte values as keys and
their frequency as values. `CharCountMode::Present` returns only byte values with a
count greater than zero. `CharCountMode::Absent` returns only byte values with a
count of zero. `CharCountMode::Unique` returns a string containing all unique byte
values found.

**Example:**
```php
use Phuture\Coherence\Strings;
use Phuture\Coherence\Enum\CharCountMode;

$counts = Strings::charCounts('hello', CharCountMode::Present);
// $counts[104] is 1 (one 'h'), $counts[108] is 2 (two 'l's)

$unique = Strings::charCounts('hello', CharCountMode::Unique);
// 'ehlo' — unique bytes sorted
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to analyze |
| `$mode` | `\Phuture\Coherence\Enum\CharCountMode` | The return mode — All, Present, Absent or Unique (default: CharCountMode::All) |

**Returns** `array|int|string` — The result depends on `$mode`

**See also**

- `\Phuture\Coherence\Enum\CharCountMode`

### `chunk()`

```php
public static function chunk(string $string, int $size, string $encoding = 'UTF-8'): array
```

Splits the string into an array of chunks of the given size.

Divides the string into sequential chunks of `$size` characters each. The last
chunk may be shorter if the string length is not evenly divisible by `$size`.
Throws when `$size` is less than or equal to zero.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::chunk('abcdef', 2); // ['ab', 'cd', 'ef']
Strings::chunk('hello', 3); // ['hel', 'lo']
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to split |
| `$size` | `int` | The number of characters per chunk |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `array` — Array of string chunks, indexed sequentially from zero

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$size` is less than or equal to zero

**See also**

- `\Phuture\Coherence\Strings::split()`

### `compare()`

```php
public static function compare(string $string, string $other, bool $caseSensitive = true, string $encoding = 'UTF-8'): int
```

Compares two strings lexicographically.

Returns a negative integer, zero, or a positive integer depending on whether
the first string is less than, equal to, or greater than the second string.
Supports optional case-insensitive comparison.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::compare('apple', 'banana'); // negative
Strings::compare('banana', 'apple'); // positive
Strings::compare('hello', 'hello'); // 0
Strings::compare('Hello', 'hello', false); // 0
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The first string to compare |
| `$other` | `string` | The second string to compare against |
| `$caseSensitive` | `bool` | Whether the comparison is case-sensitive (default: true) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `int` — Negative if less than, 0 if equal, positive if greater than

**See also**

- `\Phuture\Coherence\Strings::equals()`

### `compareNatural()`

```php
public static function compareNatural(string $string, string $other, bool $caseSensitive = true): int
```

Compares two strings using a "natural order" algorithm.

Natural order comparison arranges strings the way a human would. For example,
"img2" comes before "img10" in natural order (unlike lexicographic order).
Returns a negative integer, zero, or a positive integer depending on whether
the first string is less than, equal to, or greater than the second string.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::compareNatural('img2', 'img10'); // negative (img2 < img10)
Strings::compareNatural('img10', 'img2'); // positive (img10 > img2)
Strings::compareNatural('hello', 'hello'); // 0
Strings::compareNatural('Hello', 'hello', false); // 0
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The first string to compare |
| `$other` | `string` | The second string to compare against |
| `$caseSensitive` | `bool` | Whether the comparison is case-sensitive (default: true) |

**Returns** `int` — Negative if less than, 0 if equal, positive if greater than

**See also**

- `\Phuture\Coherence\Strings::compare()`

### `contains()`

```php
public static function contains(string $string, string $search, bool $caseSensitive = true, string $encoding = 'UTF-8'): bool
```

Determines whether a string contains a given search value.

An empty `$search` always returns true. Supports optional case-insensitive matching.
This method wraps PHP's native `str_contains()` with multibyte-safe handling
and additional case-insensitive support.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::contains('hello world', 'world'); // true
Strings::contains('hello world', 'World', false); // true (case-insensitive)
Strings::contains('hello world', 'xyz'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$search` | `string` | The value to look for |
| `$caseSensitive` | `bool` | Whether the search is case-sensitive (default: true) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `bool` — True when the string contains the search value

**See also**

- `\Phuture\Coherence\Strings::containsAll()`
- `\Phuture\Coherence\Strings::containsNone()`

### `containsAll()`

```php
public static function containsAll(string $string, array $searches, bool $caseSensitive = true): bool
```

Determines whether a string contains all of the given search values.

Returns true only when every value in `$searches` is found within `$string`.
An empty `$searches` array always returns true.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::containsAll('hello world', ['hello', 'world']); // true
Strings::containsAll('hello world', ['hello', 'xyz']); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$searches` | `array` | The values to look for; each element must be a string |
| `$caseSensitive` | `bool` | Whether the searches are case-sensitive (default: true) |

**Returns** `bool` — True when all search values are found

**See also**

- `\Phuture\Coherence\Strings::contains()`
- `\Phuture\Coherence\Strings::containsNone()`

### `containsNone()`

```php
public static function containsNone(string $string, array $searches, bool $caseSensitive = true): bool
```

Determines whether a string contains none of the given search values.

Returns true only when every value in `$searches` is absent from `$string`.
An empty `$searches` array always returns true.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::containsNone('hello world', ['foo', 'bar']); // true
Strings::containsNone('hello world', ['hello', 'bar']); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$searches` | `array` | The values to check for absence; each element must be a string |
| `$caseSensitive` | `bool` | Whether the searches are case-sensitive (default: true) |

**Returns** `bool` — True when none of the search values are found

**See also**

- `\Phuture\Coherence\Strings::contains()`
- `\Phuture\Coherence\Strings::containsAll()`

### `countOccurrences()`

```php
public static function countOccurrences(string $string, string $search, string $encoding = 'UTF-8'): int
```

Counts the number of non-overlapping times a given text appears in a string.

Returns zero when `$search` is an empty string or is not found in `$string`.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::countOccurrences('hello world hello', 'hello'); // 2
Strings::countOccurrences('aaaa', 'aa'); // 2
Strings::countOccurrences('hello', 'xyz'); // 0
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$search` | `string` | The text to count |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `int` — The number of non-overlapping times the text appears

**See also**

- `\Phuture\Coherence\Strings::contains()`

### `dedupe()`

```php
public static function dedupe(string $string, string $character = ' '): string
```

Removes duplicate consecutive occurrences of a character from the string.

Replaces runs of two or more adjacent occurrences of `$character` with a single
occurrence. Returns the string unchanged when `$character` is an empty string.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::dedupe('hello    world'); // 'hello world'
Strings::dedupe('a,,,b,,,c', ','); // 'a,b,c'
Strings::dedupe('---test---', '-'); // '-test-'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to deduplicate |
| `$character` | `string` | The character to collapse (default: space) |

**Returns** `string` — The string with consecutive duplicate characters collapsed

**See also**

- `\Phuture\Coherence\Strings::squish()`

### `distance()`

```php
public static function distance(string $string, string $other): int
```

Calculates the Levenshtein edit distance between two strings.

The edit distance is the minimum number of single-character edits (insertions,
replacements, or deletions) required to transform one string into the other.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::distance('hello', 'hello'); // 0
Strings::distance('hello', 'hallo'); // 1
Strings::distance('kitten', 'sitting'); // 3
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The first string |
| `$other` | `string` | The second string |

**Returns** `int` — The minimum number of edits needed to transform one string into the other

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When either string exceeds 255 bytes

**See also**

- `\Phuture\Coherence\Strings::similar()`

### `endsWith()`

```php
public static function endsWith(string $string, string $search, string $encoding = 'UTF-8'): bool
```

Determines whether a string ends with a given search value.

Returns true when `$string` ends with exactly `$search`. An empty `$search`
always returns true.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::endsWith('image.jpg', '.jpg'); // true
Strings::endsWith('hello world', 'world'); // true
Strings::endsWith('hello', 'Hello'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to check |
| `$search` | `string` | The expected suffix |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `bool` — True when the string ends with the search value

**See also**

- `\Phuture\Coherence\Strings::startsWith()`

### `entityDecode()`

```php
public static function entityDecode(string $string, int $flags = ENT_QUOTES | ENT_SUBSTITUTE, string $encoding = 'UTF-8'): string
```

Converts HTML entities back to their corresponding characters.

Reverses the encoding performed by `entityEncode()`.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::entityDecode('hello &amp; &quot;world&quot;'); // 'hello & "world"'
Strings::entityDecode('caf&eacute;'); // 'café'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The HTML-entity-encoded string to decode |
| `$flags` | `int` | Bitmask of ENT_* constants (default: ENT_QUOTES \| ENT_SUBSTITUTE) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The decoded string

**See also**

- `\Phuture\Coherence\Strings::entityEncode()`

### `entityEncode()`

```php
public static function entityEncode(string $string, int $flags = ENT_QUOTES | ENT_SUBSTITUTE, string $encoding = 'UTF-8'): string
```

Converts all applicable characters to HTML entities.

Translates characters that have HTML entity equivalents (like `&`, `<`, `>`,
accented characters, etc.) into their entity representations.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::entityEncode('hello & "world"'); // 'hello &amp; &quot;world&quot;'
Strings::entityEncode('café'); // 'caf&eacute;'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to encode |
| `$flags` | `int` | Bitmask of ENT_* constants (default: ENT_QUOTES \| ENT_SUBSTITUTE) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The HTML-entity-encoded string

**See also**

- `\Phuture\Coherence\Strings::entityDecode()`

### `equals()`

```php
public static function equals(string $string, string $other, bool $caseSensitive = true): bool
```

Determines whether two strings are equal.

Supports optional case-insensitive comparison using multibyte-safe lowercasing.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::equals('hello', 'hello'); // true
Strings::equals('Hello', 'hello'); // false
Strings::equals('Hello', 'hello', false); // true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The first string to compare |
| `$other` | `string` | The second string to compare against |
| `$caseSensitive` | `bool` | Whether the comparison is case-sensitive (default: true) |

**Returns** `bool` — True when the strings are equal

**See also**

- `\Phuture\Coherence\Strings::compare()`

### `excerpt()`

```php
public static function excerpt(string $string, string $phrase, int $radius = 100, string $omission = '...', string $encoding = 'UTF-8'): string
```

Extracts a contextual excerpt of a string around a given phrase.

Finds the first occurrence of `$phrase` (case-insensitive) and returns a surrounding
excerpt bounded by `$radius` characters on each side. Truncated ends are indicated
by `$omission`. When `$phrase` is empty, returns the beginning of the string up to
`$radius * 2` characters.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::excerpt('The quick brown fox jumps', 'fox', 5);
// '...wn fox ju...'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to excerpt from |
| `$phrase` | `string` | The phrase to centre the excerpt around |
| `$radius` | `int` | The number of characters to include on each side; must be zero or greater (default: 100) |
| `$omission` | `string` | The string to append at truncated ends (default: '...') |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The contextual excerpt

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$radius` is negative

**See also**

- `\Phuture\Coherence\Strings::limit()`
- `\Phuture\Coherence\Strings::limitWords()`

### `explode()`

```php
public static function explode(string $string, string $delimiter, int $limit = PHP_INT_MAX): array
```

Splits a string into an array using a delimiter.

Wraps PHP's native `explode()` with optional limit support. Throws when `$delimiter`
is an empty string.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::explode('a,b,c', ','); // ['a', 'b', 'c']
Strings::explode('a,b,c', ',', 2); // ['a', 'b,c']
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to split |
| `$delimiter` | `string` | The boundary string |
| `$limit` | `int` | Maximum number of returned elements (default: PHP_INT_MAX) |

**Returns** `array` — Array of substrings, indexed sequentially from zero

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$delimiter` is an empty string

**See also**

- `\Phuture\Coherence\Strings::split()`

### `finish()`

```php
public static function finish(string $string, string $suffix): string
```

Ensures a string ends with exactly one occurrence of the given suffix.

If `$string` already ends with one or more occurrences of `$suffix`, they are
removed before the suffix is appended once. Returns the string unchanged when
`$suffix` is empty.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::finish('path/to', '/'); // 'path/to/'
Strings::finish('path/to/', '/'); // 'path/to/'
Strings::finish('path/to///', '/'); // 'path/to/'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$suffix` | `string` | The suffix to ensure is present exactly once |

**Returns** `string` — The string guaranteed to end with the suffix

**See also**

- `\Phuture\Coherence\Strings::start()`

### `first()`

```php
public static function first(string $string, int $count = 1, string $encoding = 'UTF-8'): string
```

Returns the first N characters of a string.

Returns an empty string when `$count` is zero or less, or when the input is empty.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::first('hello', 3); // 'hel'
Strings::first('ñaño', 2); // 'ña'
Strings::first('hello'); // 'h'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$count` | `int` | The number of characters to return (default: 1) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The first N characters

**See also**

- `\Phuture\Coherence\Strings::last()`
- `\Phuture\Coherence\Strings::take()`

### `fixEncoding()`

```php
public static function fixEncoding(string $string, string $encoding = 'UTF-8'): string
```

Fixes invalid UTF-8 byte sequences in a string.

Removes or replaces any byte sequences that are not valid UTF-8. The result is
guaranteed to be valid UTF-8.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::fixEncoding("hello\xc0world"); // 'helloworld' (invalid byte removed)
Strings::fixEncoding('valid utf-8 ñoño'); // 'valid utf-8 ñoño'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string that may contain invalid encoding sequences |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — A valid string with invalid byte sequences removed

**See also**

- `\Phuture\Coherence\Strings::scrub()`

### `format()`

```php
public static function format(string $format, mixed ...$args): string
```

Returns a formatted string using sprintf semantics.

Replaces placeholders in `$format` with the provided arguments. Supports
all standard sprintf format specifiers (`%s`, `%d`, `%f`, `%02d`, etc.).

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::format('Hello, %s!', 'World'); // 'Hello, World!'
Strings::format('%04d-%02d-%02d', 2026, 5, 1); // '2026-05-01'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$format` | `string` | The format string containing placeholders |
| `...$args` | `mixed` | The values to substitute into the placeholders |

**Returns** `string` — The formatted string

### `fromBase64()`

```php
public static function fromBase64(string $string): string
```

Decodes a Base64-encoded string.

Returns an empty string when the input is not valid Base64.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::fromBase64('aGVsbG8='); // 'hello'
Strings::fromBase64('not-base64!!!'); // ''
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The Base64-encoded string to decode |

**Returns** `string` — The decoded string, or an empty string when decoding fails

**See also**

- `\Phuture\Coherence\Strings::toBase64()`

### `fromHex()`

```php
public static function fromHex(string $string): string|false
```

Decodes a hex-encoded binary string.

Reverses the encoding performed by `toHex()`. The input must contain only
valid hexadecimal characters (0-9, a-f, A-F) and must have an even length.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::fromHex('68656c6c6f'); // 'hello'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The hexadecimal string to decode |

**Returns** `string|false` — The decoded binary string, or false when decoding fails

**See also**

- `\Phuture\Coherence\Strings::toHex()`

### `hamming()`

```php
public static function hamming(string $string, string $other, string $encoding = 'UTF-8'): int
```

Calculates the Hamming distance between two strings.

The Hamming distance is the number of positions at which the corresponding
characters are different. Think of it as counting the minimum number of
character substitutions needed to turn one string into the other.

Both strings must have the same number of characters. This method is
multibyte-safe and works correctly with accented characters and other
Unicode text — each Unicode character counts as one unit regardless of
how many bytes it uses.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::hamming('karolin', 'kathrin'); // 3
Strings::hamming('hello', 'hello');     // 0
Strings::hamming('', '');               // 0
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The first string to compare |
| `$other` | `string` | The second string to compare against |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `int` — The number of positions where the characters differ

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the strings have different character lengths

**See also**

- `\Phuture\Coherence\Strings::distance()`
- `\Phuture\Coherence\Strings::jaro()`

### `headline()`

```php
public static function headline(string $string): string
```

Converts a string to a human-readable headline format.

Splits on spaces, hyphens, and underscores, capitalises each word, and joins them
with single spaces. Useful for converting identifiers into display labels.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::headline('hello_world'); // 'Hello World'
Strings::headline('foo-bar-baz'); // 'Foo Bar Baz'
Strings::headline('hello world'); // 'Hello World'
Strings::headline('helloWorld'); // 'Hello World'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to convert |

**Returns** `string` — The headline-formatted string

**See also**

- `\Phuture\Coherence\Strings::pascal()`
- `\Phuture\Coherence\Strings::title()`

### `highlight()`

```php
public static function highlight(string $string, string $phrase, string $tagOpen = '<mark>', string $tagClose = '</mark>'): string
```

Highlights all occurrences of a phrase within a string by wrapping them in tags.

Matching is case-insensitive. The original casing of the matched text is preserved
inside the tags. Returns the string unchanged when `$phrase` is empty.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::highlight('The quick brown fox', 'quick'); // 'The <mark>quick</mark> brown fox'
Strings::highlight('Hello World', 'world', '<b>', '</b>'); // 'Hello <b>World</b>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$phrase` | `string` | The phrase to highlight |
| `$tagOpen` | `string` | The opening tag to insert before each match (default: '<mark>') |
| `$tagClose` | `string` | The closing tag to insert after each match (default: '</mark>') |

**Returns** `string` — The string with all occurrences of `$phrase` wrapped in the given tags

**See also**

- `\Phuture\Coherence\Strings::replace()`

### `indent()`

```php
public static function indent(string $string, int $level = 1, string $indentChar = "\t"): string
```

Adds indentation to each line of a string.

Prepends `$indentChar` repeated `$level` times to every line. A line is defined
as any sequence ending with `\n`. Throws when `$level` is negative.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::indent("line1\nline2"); // "\tline1\n\tline2"
Strings::indent("line1\nline2", 2); // "\t\tline1\n\t\tline2"
Strings::indent("line1\nline2", 1, '  '); // "  line1\n  line2"
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to indent |
| `$level` | `int` | The number of times to repeat the indent character; must be zero or greater (default: 1) |
| `$indentChar` | `string` | The character(s) used for one level of indentation (default: "\t") |

**Returns** `string` — The indented string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$level` is negative

### `insert()`

```php
public static function insert(string $string, string $substring, int $index, string $encoding = 'UTF-8'): string
```

Inserts a substring into a string at the given index position.

Supports negative indices to insert relative to the end of the string.
When `$index` is beyond the end, the substring is appended. When `$index`
is before the start (after negative adjustment), the substring is prepended.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::insert('hello world', '!', 5); // 'hello! world'
Strings::insert('hello world', '!', -1); // 'hello worl!d'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to insert into |
| `$substring` | `string` | The substring to insert |
| `$index` | `int` | The zero-based position to insert at (negative counts from the end) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The string with the substring inserted

**See also**

- `\Phuture\Coherence\Strings::slice()`

### `is()`

```php
public static function is(string $string, string $pattern): bool
```

Determines whether a string matches a wildcard pattern.

The `*` character acts as a wildcard matching zero or more characters.
All other characters are treated as literals.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::is('user_123', 'user_*'); // true
Strings::is('photo.jpg', '*.jpg'); // true
Strings::is('test.jpg', '*.*'); // true
Strings::is('admin', 'user_*'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |
| `$pattern` | `string` | The wildcard pattern (use `*` as wildcard) |

**Returns** `bool` — True when the string matches the pattern

**See also**

- `\Phuture\Coherence\Strings::matches()`

### `isAlpha()`

```php
public static function isAlpha(string $string): bool
```

Determines whether a string contains only alphabetic characters.

Returns false for empty strings. Supports multibyte Unicode letters via the `\p{L}`
character class.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isAlpha('hello'); // true
Strings::isAlpha('héllo'); // true
Strings::isAlpha('hello1'); // false
Strings::isAlpha(''); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |

**Returns** `bool` — True when the string contains only Unicode letters

**See also**

- `\Phuture\Coherence\Strings::isAlphanumeric()`
- `\Phuture\Coherence\Strings::isNumeric()`

### `isAlphanumeric()`

```php
public static function isAlphanumeric(string $string): bool
```

Determines whether a string contains only alphanumeric characters.

Returns false for empty strings. Supports multibyte Unicode letters and numbers
via the `\p{L}\p{N}` character classes.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isAlphanumeric('hello123'); // true
Strings::isAlphanumeric('hello'); // true
Strings::isAlphanumeric('hello!'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |

**Returns** `bool` — True when the string contains only Unicode letters and numbers

**See also**

- `\Phuture\Coherence\Strings::isAlpha()`
- `\Phuture\Coherence\Strings::isNumeric()`

### `isAscii()`

```php
public static function isAscii(string $string): bool
```

Determines whether a string contains only ASCII characters.

Returns true for empty strings. A string is ASCII-only when all of its bytes
fall within the 0x00–0x7F range.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isAscii('hello'); // true
Strings::isAscii('héllo'); // false
Strings::isAscii(''); // true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |

**Returns** `bool` — True when the string contains only ASCII characters

**See also**

- `\Phuture\Coherence\Strings::ascii()`

### `isBlank()`

```php
public static function isBlank(string $string): bool
```

Determines whether a string contains only whitespace characters (or is empty).

Uses PHP's native `trim()` to detect blank strings. Returns true for the empty
string as well as strings containing only spaces, tabs, and newlines.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isBlank(''); // true
Strings::isBlank('   '); // true
Strings::isBlank("\t\n"); // true
Strings::isBlank('hello'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |

**Returns** `bool` — True when the string is blank

**See also**

- `\Phuture\Coherence\Strings::isEmpty()`
- `\Phuture\Coherence\Strings::isFilled()`

### `isEmail()`

```php
public static function isEmail(string $string): bool
```

Determines whether a string is a valid email address.

Delegates to PHP's `filter_var()` with `FILTER_VALIDATE_EMAIL`.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isEmail('user@example.com'); // true
Strings::isEmail('not-an-email'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to validate |

**Returns** `bool` — True when the string is a valid email address

**See also**

- `\Phuture\Coherence\Strings::isUrl()`

### `isEmpty()`

```php
public static function isEmpty(string $string): bool
```

Determines whether a string is exactly empty (zero-length).

A string consisting only of whitespace is NOT considered empty. Use `isBlank()`
to check for whitespace-only strings.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isEmpty(''); // true
Strings::isEmpty('0'); // false
Strings::isEmpty(' '); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |

**Returns** `bool` — True when the string has zero length

**See also**

- `\Phuture\Coherence\Strings::isNotEmpty()`
- `\Phuture\Coherence\Strings::isBlank()`

### `isFilled()`

```php
public static function isFilled(string $string): bool
```

Determines whether a string is non-empty and contains at least one non-whitespace character.

Returns false for empty strings and for strings that consist only of whitespace.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isFilled('hello'); // true
Strings::isFilled(' hello '); // true
Strings::isFilled(''); // false
Strings::isFilled('   '); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |

**Returns** `bool` — True when the string contains at least one non-whitespace character

**See also**

- `\Phuture\Coherence\Strings::isBlank()`
- `\Phuture\Coherence\Strings::isEmpty()`

### `isJson()`

```php
public static function isJson(string $string): bool
```

Determines whether a string is valid JSON.

Returns false for empty strings and any string that is not valid JSON.
Uses PHP 8.3's `json_validate()` when available, falling back to
`json_decode()` on older versions.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isJson('{"name":"John"}'); // true
Strings::isJson('["a", "b"]'); // true
Strings::isJson('not json'); // false
Strings::isJson(''); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to validate |

**Returns** `bool` — True when the string is valid JSON

### `isLower()`

```php
public static function isLower(string $string): bool
```

Determines whether a string is entirely lowercase.

Compares the lowercased version of the string against itself using multibyte-safe
lowercasing.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isLower('hello'); // true
Strings::isLower('Hello'); // false
Strings::isLower(''); // true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |

**Returns** `bool` — True when the string is entirely lowercase

**See also**

- `\Phuture\Coherence\Strings::isUpper()`
- `\Phuture\Coherence\Strings::lower()`

### `isNotEmpty()`

```php
public static function isNotEmpty(string $string): bool
```

Determines whether a string is not empty (has at least one character).

The inverse of `isEmpty()`. A string consisting only of whitespace is NOT empty.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isNotEmpty('hello'); // true
Strings::isNotEmpty(' '); // true
Strings::isNotEmpty(''); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |

**Returns** `bool` — True when the string is not empty

**See also**

- `\Phuture\Coherence\Strings::isEmpty()`

### `isNumeric()`

```php
public static function isNumeric(string $string): bool
```

Determines whether a string represents a numeric value.

Returns false for empty strings. Accepts optional leading minus sign and an
optional decimal point.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isNumeric('123'); // true
Strings::isNumeric('-45.6'); // true
Strings::isNumeric('abc'); // false
Strings::isNumeric(''); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |

**Returns** `bool` — True when the string is numeric

**See also**

- `\Phuture\Coherence\Strings::isAlpha()`
- `\Phuture\Coherence\Strings::isAlphanumeric()`

### `isUlid()`

```php
public static function isUlid(string $string): bool
```

Determines whether a string is a valid ULID (Universally Unique Lexicographically Sortable Identifier).

A ULID is 26 characters long and uses Crockford's Base32 character set (0-9 and A-Z
excluding I, L, O, U). Matching is case-insensitive.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isUlid('01ARZ3NDEKTSV4RRFFQ69G5FAV'); // true
Strings::isUlid('not-a-ulid'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to validate |

**Returns** `bool` — True when the string is a valid ULID

**See also**

- `\Phuture\Coherence\Strings::isUuid()`

### `isUpper()`

```php
public static function isUpper(string $string): bool
```

Determines whether a string is entirely uppercase.

Compares the uppercased version of the string against itself using multibyte-safe
uppercasing.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isUpper('HELLO'); // true
Strings::isUpper('Hello'); // false
Strings::isUpper(''); // true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |

**Returns** `bool` — True when the string is entirely uppercase

**See also**

- `\Phuture\Coherence\Strings::isLower()`
- `\Phuture\Coherence\Strings::upper()`

### `isUrl()`

```php
public static function isUrl(string $string): bool
```

Determines whether a string is a valid URL.

Delegates to PHP's `filter_var()` with `FILTER_VALIDATE_URL`.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isUrl('https://example.com'); // true
Strings::isUrl('not-a-url'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to validate |

**Returns** `bool` — True when the string is a valid URL

**See also**

- `\Phuture\Coherence\Strings::isEmail()`

### `isUuid()`

```php
public static function isUuid(string $string): bool
```

Determines whether a string is a valid UUID (version 1–5).

Validates the standard 8-4-4-4-12 hexadecimal format using a case-insensitive
regular expression.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::isUuid('550e8400-e29b-41d4-a716-446655440000'); // true
Strings::isUuid('not-a-uuid'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to validate |

**Returns** `bool` — True when the string is a valid UUID

**See also**

- `\Phuture\Coherence\Strings::uuid()`

### `jaro()`

```php
public static function jaro(string $string, string $other, string $encoding = 'UTF-8'): float
```

Calculates the Jaro similarity between two strings.

The Jaro similarity is a measure of how alike two strings are. It returns
a number between 0.0 (completely different) and 1.0 (identical). The
algorithm considers two characters to be a "match" when they appear within
a certain distance of each other in both strings.

This method is multibyte-safe and works correctly with accented characters,
emoji, and other Unicode text — each Unicode character counts as one unit.

Returns 1.0 when both strings are empty (they are identical). Returns 0.0
when one string is empty and the other is not.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::jaro('martha', 'marhta'); // ~0.9444
Strings::jaro('hello', 'hello'); // 1.0
Strings::jaro('foo', 'bar'); // 0.0
Strings::jaro('', ''); // 1.0
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The first string to compare |
| `$other` | `string` | The second string to compare against |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `float` — The Jaro similarity score from 0.0 (different) to 1.0 (identical)

**See also**

- `\Phuture\Coherence\Strings::jaroWinkler()`
- `\Phuture\Coherence\Strings::distance()`
- `\Phuture\Coherence\Strings::similar()`

### `jaroWinkler()`

```php
public static function jaroWinkler(string $string, string $other, float $prefixScale = 0.1, string $encoding = 'UTF-8'): float
```

Calculates the Jaro-Winkler similarity between two strings.

Jaro-Winkler is an extension of the Jaro similarity that gives extra weight
to strings sharing a common prefix. A longer shared prefix results in a
higher similarity score. This makes it particularly useful for comparing
names or words where the beginning matters most.

The prefix scale controls how much the shared prefix boosts the score. The
standard value is 0.1 and it must not exceed 0.25, otherwise the result
could fall outside the valid 0.0 to 1.0 range.

This method is multibyte-safe and works correctly with accented characters,
emoji, and other Unicode text.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::jaroWinkler('martha', 'marhta'); // ~0.9611
Strings::jaroWinkler('hello', 'hello'); // 1.0
Strings::jaroWinkler('hello', 'helo', 0.0); // same as jaro()
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The first string to compare |
| `$other` | `string` | The second string to compare against |
| `$prefixScale` | `float` | How much weight to give the common prefix; must not exceed 0.25 (default: 0.1) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `float` — The Jaro-Winkler similarity score from 0.0 (different) to 1.0 (identical)

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$prefixScale` exceeds 0.25

**See also**

- `\Phuture\Coherence\Strings::jaro()`

### `kebab()`

```php
public static function kebab(string $string): string
```

Converts a string to kebab-case.

Words are lowercased and joined with hyphens. Delegates to `snake()` with a
hyphen delimiter.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::kebab('helloWorld'); // 'hello-world'
Strings::kebab('UserProfileData'); // 'user-profile-data'
Strings::kebab('hello world'); // 'hello-world'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to convert |

**Returns** `string` — The kebab-case version of the string

**See also**

- `\Phuture\Coherence\Strings::snake()`
- `\Phuture\Coherence\Strings::camel()`

### `last()`

```php
public static function last(string $string, int $count = 1, string $encoding = 'UTF-8'): string
```

Returns the last N characters of a string.

Returns an empty string when `$count` is zero or less, or when the input is empty.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::last('hello', 3); // 'llo'
Strings::last('ñaño', 2); // 'ño'
Strings::last('hello'); // 'o'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$count` | `int` | The number of characters to return (default: 1) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The last N characters

**See also**

- `\Phuture\Coherence\Strings::first()`

### `lastPosition()`

```php
public static function lastPosition(string $string, string $search, int $offset = 0, bool $caseSensitive = true, string $encoding = 'UTF-8'): int|false
```

Returns the position of the last occurrence of a search value.

Returns false when the search value is not found. Supports optional
case-insensitive matching.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::lastPosition('hello world hello', 'hello'); // 12
Strings::lastPosition('hello', 'xyz'); // false
Strings::lastPosition('Hello World', 'world', 0, false); // 6
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$search` | `string` | The value to search for |
| `$offset` | `int` | The offset from the start to begin searching (default: 0) |
| `$caseSensitive` | `bool` | Whether the search is case-sensitive (default: true) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `int|false` — The position of the last occurrence, or false if not found

**See also**

- `\Phuture\Coherence\Strings::position()`

### `length()`

```php
public static function length(string $string, string $encoding = 'UTF-8'): int
```

Returns the number of characters in a string.

Multibyte-safe: counts Unicode code points, not bytes.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::length('hello'); // 5
Strings::length('ñaño'); // 4
Strings::length('你好'); // 2
Strings::length(''); // 0
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to measure |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `int` — The number of characters

**See also**

- `\Phuture\Coherence\Strings::wordCount()`

### `limit()`

```php
public static function limit(string $string, int $limit, string $end = '', string $encoding = 'UTF-8'): string
```

Limits the string to a given number of characters, appending an omission marker.

Returns the string unchanged when its length is within the limit.
Trailing whitespace is trimmed before the omission marker is appended.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::limit('Hello World', 5); // 'Hello'
Strings::limit('Hello World', 5, '...'); // 'Hello...'
Strings::limit('Hello World', 5, ' [+]'); // 'Hello [+]'
Strings::limit('Hi', 5); // 'Hi'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to limit |
| `$limit` | `int` | The maximum number of characters before truncation; must be zero or greater |
| `$end` | `string` | The string to append after truncation (default: '') |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The limited string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$limit` is negative

**See also**

- `\Phuture\Coherence\Strings::limitWords()`
- `\Phuture\Coherence\Strings::excerpt()`

### `limitWords()`

```php
public static function limitWords(string $string, int $limit, string $end = ''): string
```

Limits the number of words in a string, appending an end marker when truncated.

Splits the string into words, keeps only the first `$limit` words, and joins them
back with spaces. Returns the string unchanged when the word count is within the limit.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::limitWords('The quick brown fox jumps', 3); // 'The quick brown'
Strings::limitWords('The quick brown fox jumps', 3, '...'); // 'The quick brown...'
Strings::limitWords('Hi there', 5); // 'Hi there' (no truncation, original string returned unchanged)
Strings::limitWords('Hi there', 5, '...'); // 'Hi there' (no truncation, end marker not appended)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to limit |
| `$limit` | `int` | The maximum number of words to keep; must be greater than zero |
| `$end` | `string` | The string to append after truncation (default: '') |

**Returns** `string` — The word-limited string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$limit` is less than or equal to zero

**See also**

- `\Phuture\Coherence\Strings::limit()`
- `\Phuture\Coherence\Strings::words()`

### `lower()`

```php
public static function lower(string $string, string $encoding = 'UTF-8'): string
```

Converts a string to lowercase.

Multibyte-safe: uses `mb_strtolower()` with UTF-8 encoding.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::lower('HELLO'); // 'hello'
Strings::lower('ÑOÑO'); // 'ñoño'
Strings::lower('ÄÖÜ'); // 'äöü'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to lowercase |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The lowercased string

**See also**

- `\Phuture\Coherence\Strings::upper()`
- `\Phuture\Coherence\Strings::title()`

### `lowerFirst()`

```php
public static function lowerFirst(string $string, string $encoding = 'UTF-8'): string
```

Converts only the first character of a string to lowercase.

The remainder of the string is left unchanged. Multibyte-safe.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::lowerFirst('Hello World'); // 'hello World'
Strings::lowerFirst('HELLO'); // 'hELLO'
Strings::lowerFirst('Ñoño'); // 'ñoño'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The string with its first character lowercased

**See also**

- `\Phuture\Coherence\Strings::lower()`
- `\Phuture\Coherence\Strings::title()`

### `mask()`

```php
public static function mask(string $string, string $mask = '*', int $offset = 0, ?int $length = null, string $encoding = 'UTF-8'): string
```

Masks a portion of a string with a repeated mask character.

Replaces characters at positions `($offset, $offset + $length)` with `$mask`.
Supports negative offsets to count from the end of the string. When `$length`
is null, all characters from `$offset` onwards are masked.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::mask('1234567890'); // '**********'
Strings::mask('1234567890', '*', 3); // '123*******'
Strings::mask('1234567890', '*', 3, 4); // '123****890'
Strings::mask('1234567890', '*', -4); // '123456****'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to mask |
| `$mask` | `string` | The mask character to use (default: '*') |
| `$offset` | `int` | The start position to begin masking (negative counts from the end) |
| `$length` | `int|null` | The number of characters to mask; must be zero or greater (null masks to the end) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The masked string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$length` is negative

**See also**

- `\Phuture\Coherence\Strings::limit()`

### `matches()`

```php
public static function matches(string $string, string $pattern): bool
```

Determines whether a string matches a regular expression pattern.

The `$pattern` must include delimiters (e.g., `/^user_\d+$/`). Returns true when
the pattern matches the string.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::matches('user_123', '/^user_\d+$/'); // true
Strings::matches('user_abc', '/^user_\d+$/'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to test |
| `$pattern` | `string` | The full regular expression pattern including delimiters |

**Returns** `bool` — True when the pattern matches

**See also**

- `\Phuture\Coherence\Strings::is()`

### `metaphone()`

```php
public static function metaphone(string $string, int $maxPhonemes = 0): string
```

Calculates the metaphone key of a string.

Metaphone is a phonetic algorithm that encodes words based on how they
sound in English. Unlike soundex, metaphone produces keys of variable
length and is generally more accurate for English pronunciation. Two
words that sound the same will produce the same key.

The string is transliterated to ASCII before processing, making this
method safe to use with accented or non-Latin characters — for example,
"héllo" is treated the same as "hello".

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::metaphone('World'); // 'WRLT'
Strings::metaphone('Thompson'); // '0MPSN'
Strings::metaphone('Smith'); // 'SM0'
Strings::metaphone('Smythe'); // 'SM0'
Strings::metaphone('héllo'); // 'HL'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to compute the metaphone key for |
| `$maxPhonemes` | `int` | The maximum number of phonemes to return; 0 means no limit (default: 0) |

**Returns** `string` — The metaphone phonetic key

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$string` is empty or `$maxPhonemes` is negative

**See also**

- `\Phuture\Coherence\Strings::soundex()`
- `\Phuture\Coherence\Strings::ascii()`

### `nl2br()`

```php
public static function nl2br(string $string, bool $useXhtml = true): string
```

Inserts HTML line breaks before all newlines in a string.

Converts newline characters (`\n`) to `<br>` tags. When `$useXhtml` is true,
produces `<br />` instead of `<br>`.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::nl2br("hello\nworld"); // 'hello<br />\nworld'
Strings::nl2br("hello\nworld", false); // 'hello<br>\nworld'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string containing newlines |
| `$useXhtml` | `bool` | Whether to use XHTML-compatible `<br />` tags (default: true) |

**Returns** `string` — The string with HTML line breaks inserted before newlines

### `normalizeNewLines()`

```php
public static function normalizeNewLines(string $string): string
```

Normalizes line endings to Unix-style `\n`.

Converts Windows-style `\r\n` and old Mac-style `\r` to `\n`. The string is
returned unchanged when it contains no line endings.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::normalizeNewLines("line1\r\nline2\rline3"); // "line1\nline2\nline3"
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string whose line endings are to be normalized |

**Returns** `string` — The string with all line endings replaced by `\n`

**See also**

- `\Phuture\Coherence\Strings::strip()`

### `numberFormat()`

```php
public static function numberFormat(float|int $number, int $decimals = 0, string $decimalSeparator = '.', string $thousandsSeparator = ', '): string
```

Formats a number with grouped thousands and configurable separators.

Rounds the number to `$decimals` decimal places and inserts `$thousandsSeparator`
between every group of three digits.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::numberFormat(1234.5678, 2); // '1,234.57'
Strings::numberFormat(1234.5678, 2, ',', '.'); // '1.234,57'
Strings::numberFormat(1000000); // '1,000,000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `float|int` | The number to format |
| `$decimals` | `int` | The number of decimal places (default: 0) |
| `$decimalSeparator` | `string` | The character for the decimal point (default: '.') |
| `$thousandsSeparator` | `string` | The character for thousands grouping (default: ',') |

**Returns** `string` — The formatted number string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$decimals` is negative or greater than MAX_DECIMALS

### `of()`

```php
public static function of(string $string): Type\Strings
```

Creates a fluent wrapper around the given string for method chaining.

Returns a `Type\Strings` instance that wraps the provided string value and
exposes every string-returning method as a chainable call.

**Example:**
```php
use Phuture\Coherence\Strings;

$result = Strings::of('  hello world  ')
    ->trim()
    ->upper()
    ->get();
// 'HELLO WORLD'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The string to wrap for fluent operations |

**Returns** `Type\Strings` — A fluent wrapper instance that enables method chaining

**See also**

- `\Phuture\Coherence\Type\Strings` — For the fluent wrapper implementation

### `pad()`

```php
public static function pad(string $string, int $length, string $padString = ' ', PadDirection $direction = PadDirection::Right, string $encoding = 'UTF-8'): string
```

Pads a string to a given length using a pad string.

Extends the string with the `$padString` on the side specified by `$direction`
until the total character count reaches `$length`. When the string is already
at or beyond `$length`, or when `$padString` is empty, it is returned unchanged.
For `PadDirection::Both`, padding is split evenly; when the total padding is odd
the extra character goes to the right side.

**Example:**
```php
use Phuture\Coherence\Strings;
use Phuture\Coherence\Enum\PadDirection;

Strings::pad('hello', 10); // 'hello     '
Strings::pad('hello', 10, '-'); // 'hello-----'
Strings::pad('hello', 10, ' ', PadDirection::Left); // '     hello'
Strings::pad('5', 5, '0', PadDirection::Left); // '00005'
Strings::pad('hello', 11, '-', PadDirection::Both); // '---hello---'
Strings::pad('hello', 10, '-', PadDirection::Both); // '--hello---'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to pad |
| `$length` | `int` | The target total length in characters |
| `$padString` | `string` | The string to pad with (default: space) |
| `$direction` | `\Phuture\Coherence\Enum\PadDirection` | Which side to pad — Right, Left, or Both (default: PadDirection::Right) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The padded string

**See also**

- `\Phuture\Coherence\Enum\PadDirection`

### `pascal()`

```php
public static function pascal(string $string): string
```

Converts a string to PascalCase (StudlyCase).

Words separated by spaces, hyphens, underscores, or CamelCase boundaries are
joined together with each word capitalised (including the first).

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::pascal('hello world'); // 'HelloWorld'
Strings::pascal('hello_world'); // 'HelloWorld'
Strings::pascal('hello-world'); // 'HelloWorld'
Strings::pascal('helloWorld'); // 'HelloWorld'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to convert |

**Returns** `string` — The PascalCase version of the string

**See also**

- `\Phuture\Coherence\Strings::camel()`
- `\Phuture\Coherence\Strings::snake()`

### `password()`

```php
public static function password(int $length = 16, bool $includeUppercase = true, bool $includeLowercase = true, bool $includeDigits = true, string $includeSpecialCharacters = '!@#$%^&*()-_=+[]{}|;:, .<>?'): string
```

Generates a cryptographically secure password with configurable character requirements.

Produces a random password that is guaranteed to contain at least one character from
each enabled character pool. The character pools are: uppercase letters, lowercase
letters, digits, and special characters. Throws when `$length` is too short to
satisfy all enabled requirements.

**Example:**
```php
use Phuture\Coherence\Strings;

$pw = Strings::password(16);
$pw = Strings::password(20, includeSpecialCharacters: '!@#$%^&*');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$length` | `int` | The total length of the password; must be greater than zero (default: 16) |
| `$includeUppercase` | `bool` | Whether at least one uppercase letter is included (default: true) |
| `$includeLowercase` | `bool` | Whether at least one lowercase letter is included (default: true) |
| `$includeDigits` | `bool` | Whether at least one digit is included (default: true) |
| `$includeSpecialCharacters` | `string` | The set of special characters to include (default: '!@#$%^&*()-_=+[]{}\|;:,.<>?') |

**Returns** `string` — The generated password

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$length` is too short for the enabled requirements
- `\Random\RandomException` — If the system entropy source is unavailable

**See also**

- `\Phuture\Coherence\Strings::random()`

### `position()`

```php
public static function position(string $string, string $search, int $offset = 0, bool $caseSensitive = true, string $encoding = 'UTF-8'): int|false
```

Returns the position of the first occurrence of a search value.

Returns false when the search value is not found. Supports optional
case-insensitive matching.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::position('hello world', 'world'); // 6
Strings::position('hello world', 'xyz'); // false
Strings::position('hello hello', 'hello', 3); // 6
Strings::position('Hello World', 'world', 0, false); // 6
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$search` | `string` | The value to search for |
| `$offset` | `int` | The offset from the start to begin searching (default: 0) |
| `$caseSensitive` | `bool` | Whether the search is case-sensitive (default: true) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `int|false` — The position of the first occurrence, or false if not found

**See also**

- `\Phuture\Coherence\Strings::lastPosition()`

### `quoteMeta()`

```php
public static function quoteMeta(string $string): string
```

Escapes regular expression meta-characters in a string.

Adds a backslash before each of the characters: `. \ + * ? [ ^ ] ( $ )`.
Useful for preparing a literal string for use in a regular expression pattern.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::quoteMeta('hello (world)'); // 'hello \(world\)'
Strings::quoteMeta('price: $10.00'); // 'price: \$10\.00'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to escape |

**Returns** `string` — The string with meta-characters escaped

### `random()`

```php
public static function random(int $length = 16): string
```

Generates a cryptographically random alphanumeric string.

Uses `random_int()` for all character selection. Throws `RandomException`
if the system entropy source fails. The character pool is `[0-9a-zA-Z]` (62 characters).

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::random(16); // e.g. 'aB3xK9mNpQ2rZ5wY'
Strings::random(8); // e.g. 'a1B2c3D4'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$length` | `int` | The length of the random string to generate; must be greater than zero (default: 16) |

**Returns** `string` — The random alphanumeric string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$length` is less than or equal to zero
- `\Random\RandomException` — If the system entropy source is unavailable

**See also**

- `\Phuture\Coherence\Strings::uuid()`

### `remove()`

```php
public static function remove(string $string, string $search, bool $caseSensitive = true): string
```

Removes all occurrences of a search value from a string.

Delegates to `replace()` with an empty replacement string.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::remove('hello world', 'o'); // 'hell wrld'
Strings::remove('Hello World', 'world', false); // 'Hello '
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$search` | `string` | The value to remove |
| `$caseSensitive` | `bool` | Whether the removal is case-sensitive (default: true) |

**Returns** `string` — The string with all occurrences removed

**See also**

- `\Phuture\Coherence\Strings::replace()`

### `repeat()`

```php
public static function repeat(string $string, int $times): string
```

Repeats a string a given number of times.

Returns an empty string when `$times` is zero. Throws when `$times` is negative.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::repeat('ab', 3); // 'ababab'
Strings::repeat('ha', 0); // ''
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to repeat |
| `$times` | `int` | The number of repetitions (must be zero or greater) |

**Returns** `string` — The repeated string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$times` is negative

### `replace()`

```php
public static function replace(string $string, string $search, string $replace, bool $caseSensitive = true): string
```

Replaces all occurrences of a search value with a replacement.

Returns the string unchanged when `$search` is empty. Supports optional
case-insensitive replacement.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::replace('hello world', 'world', 'PHP'); // 'hello PHP'
Strings::replace('Hello World', 'world', 'PHP', false); // 'Hello PHP'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$search` | `string` | The value to search for |
| `$replace` | `string` | The replacement value |
| `$caseSensitive` | `bool` | Whether the replacement is case-sensitive (default: true) |

**Returns** `string` — The string with all occurrences replaced

**See also**

- `\Phuture\Coherence\Strings::replaceFirst()`
- `\Phuture\Coherence\Strings::replaceLast()`
- `\Phuture\Coherence\Strings::remove()`

### `replaceArray()`

```php
public static function replaceArray(string $string, string $search, array $replacements, string $encoding = 'UTF-8'): string
```

Replaces successive occurrences of a search value using values from an array.

Each time `$search` is found, it is replaced with the next value from `$replacements`.
When the replacements array is exhausted, remaining occurrences are replaced with an
empty string. Returns the string unchanged when `$search` is empty or `$replacements`
is empty.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::replaceArray('Year: ?, Month: ?', '?', ['2026', 'April']); // 'Year: 2026, Month: April'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to perform replacements on |
| `$search` | `string` | The value to search for |
| `$replacements` | `array` | Ordered list of string replacement values |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The string with successive occurrences replaced

**See also**

- `\Phuture\Coherence\Strings::replace()`

### `replaceAt()`

```php
public static function replaceAt(string $string, string $replacement, int $position, ?int $length = null, string $encoding = 'UTF-8'): string
```

Replaces a portion of a string starting at a given character position.

When `$length` is null, replaces from `$position` to the end of the string.
A negative `$position` counts from the end of the string. A negative `$length`
stops that many characters before the end of the string.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::replaceAt('hello world', 'PHP', 6); // 'hello PHP'
Strings::replaceAt('hello world', 'PHP', 6, 5); // 'hello PHP'
Strings::replaceAt('hello world', '', 5, 6); // 'hello'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to modify |
| `$replacement` | `string` | The text to insert at the given position |
| `$position` | `int` | The character index at which to begin replacement (negative counts from end) |
| `$length` | `int|null` | The number of characters to replace (null replaces to end of string) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The modified string

**See also**

- `\Phuture\Coherence\Strings::insert()`
- `\Phuture\Coherence\Strings::slice()`

### `replaceFirst()`

```php
public static function replaceFirst(string $string, string $search, string $replace, string $encoding = 'UTF-8'): string
```

Replaces the first occurrence of a search value with a replacement.

Returns the string unchanged when `$search` is empty or not found.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::replaceFirst('hello hello', 'hello', 'world'); // 'world hello'
Strings::replaceFirst('hello', 'xyz', 'world'); // 'hello'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$search` | `string` | The value to search for |
| `$replace` | `string` | The replacement value |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The string with the first occurrence replaced

**See also**

- `\Phuture\Coherence\Strings::replaceLast()`
- `\Phuture\Coherence\Strings::replace()`

### `replaceLast()`

```php
public static function replaceLast(string $string, string $search, string $replace, string $encoding = 'UTF-8'): string
```

Replaces the last occurrence of a search value with a replacement.

Returns the string unchanged when `$search` is empty or not found.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::replaceLast('hello hello', 'hello', 'world'); // 'hello world'
Strings::replaceLast('hello', 'xyz', 'world'); // 'hello'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$search` | `string` | The value to search for |
| `$replace` | `string` | The replacement value |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The string with the last occurrence replaced

**See also**

- `\Phuture\Coherence\Strings::replaceFirst()`
- `\Phuture\Coherence\Strings::replace()`

### `reverse()`

```php
public static function reverse(string $string): string
```

Reverses a string character by character.

Multibyte-safe: splits on Unicode code points before reversing.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::reverse('hello'); // 'olleh'
Strings::reverse('ñaño'); // 'oñañ'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to reverse |

**Returns** `string` — The reversed string

**See also**

- `\Phuture\Coherence\Strings::swap()`

### `rot13()`

```php
public static function rot13(string $string): string
```

Applies the ROT13 encoding to a string.

ROT13 shifts every ASCII letter by 13 positions, wrapping around the alphabet.
Applying it twice returns the original string. Non-alphabetic characters are
left unchanged.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::rot13('hello'); // 'uryyb'
Strings::rot13('uryyb'); // 'hello'
Strings::rot13('Hello World!'); // 'Uryyb Jbeyq!'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to encode |

**Returns** `string` — The ROT13-encoded string

### `scrub()`

```php
public static function scrub(string $string): string
```

Removes dangerous control characters from a string.

Strips bytes in the ranges 0x00–0x08, 0x0B, 0x0C, 0x0E–0x1F, and 0x7F
(all ASCII control characters except tab 0x09, LF 0x0A, and CR 0x0D).

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::scrub("hello\x00world"); // 'helloworld'
Strings::scrub("clean text"); // 'clean text'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to clean |

**Returns** `string` — The string with control characters removed

**See also**

- `\Phuture\Coherence\Strings::fixEncoding()`

### `search()`

```php
public static function search(string $string, string $search, bool $beforeNeedle = false, bool $caseSensitive = true, string $encoding = 'UTF-8'): string|false
```

Returns the portion of the string from the first occurrence of a search value.

Searches for the first occurrence of `$search` in `$string` and returns the
portion from that position to the end (or everything before it if `$beforeNeedle`
is true). Returns `false` when `$search` is not found.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::search('user@example.com', '@'); // '@example.com'
Strings::search('user@example.com', '@', true); // 'user'
Strings::search('Hello World', 'world', false, false); // 'World'
Strings::search('hello', 'xyz'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to search within |
| `$search` | `string` | The value to search for |
| `$beforeNeedle` | `bool` | Return the part before the search value instead of after (default: false) |
| `$caseSensitive` | `bool` | Whether the search is case-sensitive (default: true) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string|false` — The portion from the first occurrence, or false if not found

**See also**

- `\Phuture\Coherence\Strings::position()`

### `shuffle()`

```php
public static function shuffle(string $string): string
```

Randomly shuffles the characters in a string.

Multibyte-safe: splits on Unicode code points before shuffling.
Returns an empty string when the input is empty.

**Example:**
```php
use Phuture\Coherence\Strings;

$shuffled = Strings::shuffle('hello'); // e.g. 'lleoh'
$shuffled = Strings::shuffle('ñaño'); // multibyte-safe shuffle
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to shuffle |

**Returns** `string` — The shuffled string

### `similar()`

```php
public static function similar(string $string, string $other): float
```

Calculates the similarity between two strings as a percentage.

Returns a value between 0.0 and 100.0 indicating how similar the two strings are.
A value of 100.0 means the strings are identical. Returns 0.0 when either string
is empty.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::similar('hello', 'hello'); // 100.0
Strings::similar('hello', 'hallo'); // ~80.0
Strings::similar('hello', ''); // 0.0
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The first string to compare |
| `$other` | `string` | The second string to compare against |

**Returns** `float` — The similarity percentage from 0.0 to 100.0

**See also**

- `\Phuture\Coherence\Strings::distance()`

### `slice()`

```php
public static function slice(string $string, int $start, ?int $length = null, string $encoding = 'UTF-8'): string
```

Extracts a portion of a string by start position and optional length.

Supports negative `$start` to count from the end of the string. When `$length`
is null, returns all characters from `$start` to the end.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::slice('hello world', 0, 5); // 'hello'
Strings::slice('hello world', 6); // 'world'
Strings::slice('hello world', -5); // 'world'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to slice |
| `$start` | `int` | The starting position (negative counts from the end) |
| `$length` | `int|null` | The number of characters to return (null returns to the end) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The extracted substring

**See also**

- `\Phuture\Coherence\Strings::first()`
- `\Phuture\Coherence\Strings::last()`
- `\Phuture\Coherence\Strings::charAt()`

### `slug()`

```php
public static function slug(string $string, string $separator = '-', string $language = 'en'): string
```

Generates a URL-friendly slug from a string.

Transliterates non-ASCII characters, strips non-word characters, collapses
separators, and lowercases the result.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::slug('Hello World'); // 'hello-world'
Strings::slug('Hello World', '_'); // 'hello_world'
Strings::slug('héllo wörld'); // 'hello-world'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to slugify |
| `$separator` | `string` | The separator character between words (default: '-') |
| `$language` | `string` | The language code for transliteration (default: 'en') |

**Returns** `string` — The URL-friendly slug

**See also**

- `\Phuture\Coherence\Strings::ascii()`

### `snake()`

```php
public static function snake(string $string, string $delimiter = '_'): string
```

Converts a string to snake_case with a configurable delimiter.

Words separated by spaces, hyphens, underscores, or CamelCase boundaries are
lowercased and joined with the given `$delimiter` (default: underscore).

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::snake('helloWorld'); // 'hello_world'
Strings::snake('HelloWorld', '-'); // 'hello-world'
Strings::snake('hello world'); // 'hello_world'
Strings::snake('XMLParser'); // 'xml_parser'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to convert |
| `$delimiter` | `string` | The word separator character (default: '_') |

**Returns** `string` — The snake_case version of the string

**See also**

- `\Phuture\Coherence\Strings::kebab()`
- `\Phuture\Coherence\Strings::camel()`

### `soundex()`

```php
public static function soundex(string $string): string
```

Calculates the soundex key of a string.

Soundex is a phonetic algorithm that indexes names by their English pronunciation.
The result is a 4-character string starting with a letter followed by three digits.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::soundex('Euler'); // 'E460'
Strings::soundex('Ellery'); // 'E460'
Strings::soundex('Knuth'); // 'K530'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to compute the soundex key for |

**Returns** `string` — The 4-character soundex key

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$string` is empty

### `split()`

```php
public static function split(string $string, string $pattern, int $limit = -1): array
```

Splits a string into an array by a literal pattern.

The `$pattern` is treated as a literal string (not a regex). Returns an array
containing the original string when `$pattern` is empty. The `$limit` parameter
controls the maximum number of elements returned.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::split('a.b.c', '.'); // ['a', 'b', 'c']
Strings::split('a.b.c', '.', 2); // ['a', 'b.c']
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to split |
| `$pattern` | `string` | The literal separator to split on |
| `$limit` | `int` | Maximum number of elements to return (default: -1 = no limit) |

**Returns** `array` — Array of substrings, indexed sequentially from zero

**See also**

- `\Phuture\Coherence\Strings::explode()`

### `squish()`

```php
public static function squish(string $string): string
```

Collapses all whitespace sequences into a single space and trims the result.

Replaces any sequence of one or more whitespace characters (including tabs and
newlines) with a single space, then trims the leading and trailing whitespace.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::squish('hello    world'); // 'hello world'
Strings::squish("  hello   \n   world  "); // 'hello world'
Strings::squish("a\t\tb\n\nc"); // 'a b c'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to squish |

**Returns** `string` — The string with collapsed whitespace

**See also**

- `\Phuture\Coherence\Strings::trim()`
- `\Phuture\Coherence\Strings::dedupe()`

### `start()`

```php
public static function start(string $string, string $prefix): string
```

Ensures a string begins with exactly one occurrence of the given prefix.

If `$string` already begins with one or more occurrences of `$prefix`, they are
removed before the prefix is prepended once. Returns the string unchanged when
`$prefix` is empty.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::start('/path/to', '/'); // '/path/to'
Strings::start('path/to', '/'); // '/path/to'
Strings::start('///path/to', '/'); // '/path/to'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$prefix` | `string` | The prefix to ensure is present exactly once |

**Returns** `string` — The string guaranteed to begin with the prefix

**See also**

- `\Phuture\Coherence\Strings::finish()`

### `startsWith()`

```php
public static function startsWith(string $string, string $search, string $encoding = 'UTF-8'): bool
```

Determines whether a string begins with a given search value.

Returns true when `$string` starts with exactly `$search`. An empty `$search`
always returns true.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::startsWith('hello world', 'hello'); // true
Strings::startsWith('https://example.com', 'https://'); // true
Strings::startsWith('hello', 'Hello'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to check |
| `$search` | `string` | The expected prefix |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `bool` — True when the string begins with the search value

**See also**

- `\Phuture\Coherence\Strings::endsWith()`

### `strip()`

```php
public static function strip(string $string, string $allowedTags = ''): string
```

Strips HTML and PHP tags from a string.

Delegates to PHP's native `strip_tags()`. An optional list of allowed tags
can be provided to preserve specific tags.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::strip('<p>Hello <b>world</b></p>'); // 'Hello world'
Strings::strip('<p>Hello</p>', '<p>'); // '<p>Hello</p>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to strip |
| `$allowedTags` | `string` | HTML tags to preserve (default: '' = strip all) |

**Returns** `string` — The string with HTML/PHP tags removed

**See also**

- `\Phuture\Coherence\Strings::normalizeNewLines()`
- `\Phuture\Coherence\Strings::trim()`

### `stripCSlashes()`

```php
public static function stripCSlashes(string $string): string
```

Removes C-style backslash escapes from a string.

Reverses the escaping performed by `addCSlashes()`, recognizing C-style
escape sequences like `\n`, `\r`, `\t`, `\0`, and octal/hex notations.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::stripCSlashes('h\\ell\\o'); // 'hello'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The C-style escaped string to unescape |

**Returns** `string` — The unescaped string

**See also**

- `\Phuture\Coherence\Strings::addCSlashes()`

### `stripSlashes()`

```php
public static function stripSlashes(string $string): string
```

Removes backslash escapes added by `addSlashes()`.

Reverses the escaping performed by `addSlashes()`, removing backslashes
before single quotes, double quotes, backslashes, and NUL bytes.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::stripSlashes("hello \\'world\\'"); // "hello 'world'"
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The escaped string to unescape |

**Returns** `string` — The unescaped string

**See also**

- `\Phuture\Coherence\Strings::addSlashes()`

### `swap()`

```php
public static function swap(string $string, array $replacements): string
```

Performs multiple simultaneous search-and-replace operations.

Keys of `$replacements` are searched for and replaced with their corresponding
values. All replacements happen in a single pass.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::swap('hello world', ['hello' => 'hi', 'world' => 'earth']);
// 'hi earth'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$replacements` | `array` | A map of string search keys to string replacement values |

**Returns** `string` — The string with all swaps applied

**See also**

- `\Phuture\Coherence\Strings::replace()`

### `take()`

```php
public static function take(string $string, int $count): string
```

Returns the first or last N characters of a string based on the sign of `$count`.

A positive `$count` returns the first N characters; a negative `$count` returns
the last N characters (using the absolute value).

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::take('hello world', 5); // 'hello'
Strings::take('hello world', -5); // 'world'
Strings::take('hello world', 0); // ''
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$count` | `int` | The number of characters (negative returns from the end) |

**Returns** `string` — The extracted characters

**See also**

- `\Phuture\Coherence\Strings::first()`
- `\Phuture\Coherence\Strings::last()`

### `title()`

```php
public static function title(string $string, string $encoding = 'UTF-8'): string
```

Converts every word in a string to Title Case.

Multibyte-safe: uses `mb_convert_case()` with `MB_CASE_TITLE` and UTF-8 encoding.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::title('hello world'); // 'Hello World'
Strings::title('HELLO WORLD'); // 'Hello World'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to convert |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The title-cased string

**See also**

- `\Phuture\Coherence\Strings::title()`
- `\Phuture\Coherence\Strings::upper()`

### `toArray()`

```php
public static function toArray(string $string): array
```

Converts a string to an array of individual characters.

Returns an empty array for an empty string. Each element of the returned array
is a single Unicode code point. Multibyte-safe.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::toArray('hello'); // ['h', 'e', 'l', 'l', 'o']
Strings::toArray('ñaño'); // ['ñ', 'a', 'ñ', 'o']
Strings::toArray(''); // []
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to convert |

**Returns** `array` — Array of individual Unicode characters, indexed sequentially from zero

**See also**

- `\Phuture\Coherence\Strings::split()`
- `\Phuture\Coherence\Strings::chunk()`

### `toBase64()`

```php
public static function toBase64(string $string): string
```

Encodes a string to its Base64 representation.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::toBase64('hello'); // 'aGVsbG8='
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to encode |

**Returns** `string` — The Base64-encoded string

**See also**

- `\Phuture\Coherence\Strings::fromBase64()`

### `toHex()`

```php
public static function toHex(string $string): string
```

Converts binary data into its hexadecimal representation.

Generates a hex string (lowercase) where each byte of the input is represented
by two hexadecimal digits.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::toHex('hello'); // '68656c6c6f'
Strings::toHex("\x00\xFF"); // '00ff'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to convert |

**Returns** `string` — The hexadecimal representation

**See also**

- `\Phuture\Coherence\Strings::fromHex()`

### `trim()`

```php
public static function trim(string $string, ?string $characters = null): string
```

Strips whitespace (or given characters) from the beginning and end of a string.

Delegates to PHP's native `mb_trim()`. The `$characters` parameter specifies the
characters to strip (default: standard whitespace characters).

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::trim('  hello  '); // 'hello'
Strings::trim('***hello***', '*'); // 'hello'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to trim |
| `$characters` | `string|null` | The characters to strip (default: whitespace) |

**Returns** `string` — The trimmed string

**See also**

- `\Phuture\Coherence\Strings::trimLeft()`
- `\Phuture\Coherence\Strings::trimRight()`
- `\Phuture\Coherence\Strings::squish()`

### `trimLeft()`

```php
public static function trimLeft(string $string, ?string $characters = null): string
```

Strips whitespace (or given characters) from the beginning of a string.

Delegates to PHP's native `mb_ltrim()`.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::trimLeft('  hello  '); // 'hello  '
Strings::trimLeft('***hello***', '*'); // 'hello***'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to trim |
| `$characters` | `string|null` | The characters to strip (default: whitespace) |

**Returns** `string` — The left-trimmed string

**See also**

- `\Phuture\Coherence\Strings::trimRight()`
- `\Phuture\Coherence\Strings::trim()`

### `trimRight()`

```php
public static function trimRight(string $string, ?string $characters = null): string
```

Strips whitespace (or given characters) from the end of a string.

Delegates to PHP's native `mb_rtrim()`.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::trimRight('  hello  '); // '  hello'
Strings::trimRight('***hello***', '*'); // '***hello'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to trim |
| `$characters` | `string|null` | The characters to strip (default: whitespace) |

**Returns** `string` — The right-trimmed string

**See also**

- `\Phuture\Coherence\Strings::trimLeft()`
- `\Phuture\Coherence\Strings::trim()`

### `unwrap()`

```php
public static function unwrap(string $string, string $wrapper, string $encoding = 'UTF-8'): string
```

Removes a surrounding wrapper string from both ends of a string.

Only removes the wrapper when `$string` starts AND ends with `$wrapper`. Returns
the string unchanged when `$wrapper` is empty or the string is too short to be
wrapped.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::unwrap('"hello"', '"'); // 'hello'
Strings::unwrap('[hello]', '['); // '[hello]' (no matching end)
Strings::unwrap('hello', '"'); // 'hello'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to unwrap |
| `$wrapper` | `string` | The wrapper string to remove from both ends |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The unwrapped string

**See also**

- `\Phuture\Coherence\Strings::wrap()`

### `upper()`

```php
public static function upper(string $string, string $encoding = 'UTF-8'): string
```

Converts a string to uppercase.

Multibyte-safe: uses `mb_strtoupper()` with UTF-8 encoding.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::upper('hello'); // 'HELLO'
Strings::upper('ñoño'); // 'ÑOÑO'
Strings::upper('äöü'); // 'ÄÖÜ'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to uppercase |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The uppercased string

**See also**

- `\Phuture\Coherence\Strings::lower()`
- `\Phuture\Coherence\Strings::title()`

### `upperFirst()`

```php
public static function upperFirst(string $string, string $encoding = 'UTF-8'): string
```

Converts only the first character of a string to uppercase.

The remainder of the string is left unchanged. Multibyte-safe.
This is the counterpart to `lowerFirst()`.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::upperFirst('hello World'); // 'Hello World'
Strings::upperFirst('HELLO'); // 'HELLO'
Strings::upperFirst('ñoño'); // 'Ñoño'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The string with its first character uppercased

**See also**

- `\Phuture\Coherence\Strings::lowerFirst()`
- `\Phuture\Coherence\Strings::upper()`

### `uuid()`

```php
public static function uuid(UuidVersion $version = UuidVersion::V4): string
```

Generates a UUID (Universally Unique Identifier).

Generates a UUID using cryptographically secure random data. Supports version 4
(fully random, default) and version 7 (time-ordered). The version and variant
bits are set according to RFC 4122.

**Example:**
```php
use Phuture\Coherence\Strings;
use Phuture\Coherence\Enum\UuidVersion;

Strings::uuid(); // e.g. 'a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d'
Strings::uuid(UuidVersion::V4); // e.g. 'f47ac10b-58cc-4372-a567-0e02b2c3d479'
Strings::uuid(UuidVersion::V7); // e.g. '019f3e7a-9b2c-7d4e-a5f6-7890123456ab'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$version` | `\Phuture\Coherence\Enum\UuidVersion` | The UUID version to generate (default: V4) |

**Returns** `string` — The generated UUID string

**Throws**

- `\Random\RandomException` — If the system entropy source is unavailable

**See also**

- `\Phuture\Coherence\Strings::random()`
- `\Phuture\Coherence\Strings::isUuid()`
- `\Phuture\Coherence\Enum\UuidVersion`

### `wordCount()`

```php
public static function wordCount(string $string): int
```

Returns the number of words in a string.

A word is any sequence of Unicode letters and numbers. Returns zero for blank strings.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::wordCount('hello world'); // 2
Strings::wordCount('  '); // 0
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to count words in |

**Returns** `int` — The number of words

**See also**

- `\Phuture\Coherence\Strings::words()`

### `words()`

```php
public static function words(string $string, int $limit = -1, string $end = ''): array
```

Extracts the words from a string into an array.

A word is any sequence of Unicode letters, numbers, and apostrophes. Returns an
empty array for blank strings. When `$limit` is non-negative, only the first
`$limit` words are returned, and `$end` is appended as a final element if provided.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::words("hello world"); // ['hello', 'world']
Strings::words("it's a test", 2, '…'); // ['it\'s', 'a', '…']
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to extract words from |
| `$limit` | `int` | Maximum number of words to return, -1 = no limit (default: -1) |
| `$end` | `string` | String appended after the word list when limited (default: '') |

**Returns** `array` — Array of word strings, indexed sequentially from zero

**See also**

- `\Phuture\Coherence\Strings::wordCount()`
- `\Phuture\Coherence\Strings::split()`

### `wordWrap()`

```php
public static function wordWrap(string $string, int $width = 75, string $break = "\n", bool $cutLongWords = false, string $encoding = 'UTF-8'): string
```

Wraps a string at a given number of characters, inserting a break string.

Delegates to PHP's native `wordwrap()`. When `$cutLongWords` is true, words
longer than `$width` characters are broken at exactly `$width`.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::wordWrap('The quick brown fox', 10);
// "The quick\nbrown fox"
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to wrap |
| `$width` | `int` | The number of characters at which to wrap; must be greater than zero (default: 75) |
| `$break` | `string` | The line break string to insert (default: "\n") |
| `$cutLongWords` | `bool` | Whether to cut words longer than `$width` (default: false) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The word-wrapped string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$width` is less than or equal to zero

**See also**

- `\Phuture\Coherence\Strings::limit()`
- `\Phuture\Coherence\Strings::limitWords()`

### `wrap()`

```php
public static function wrap(string $string, string $wrapper): string
```

Wraps a string with a given wrapper string on both sides.

**Example:**
```php
use Phuture\Coherence\Strings;

Strings::wrap('hello', '"'); // '"hello"'
Strings::wrap('hello', '[]'); // '[]hello[]'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to wrap |
| `$wrapper` | `string` | The string to prepend and append |

**Returns** `string` — The wrapped string

**See also**

- `\Phuture\Coherence\Strings::unwrap()`

### `getAsciiFontMap()`

```php
private static function getAsciiFontMap(string $font): array
```

Returns the character map for the given ASCII art font.

Each character is represented as an array of exactly 5 strings of equal width,
forming a 5-row block glyph rendered using `#` and space characters.

| Parameter | Type | Description |
| --- | --- | --- |
| `$font` | `string` | The font name (currently only 'block' is supported) |

**Returns** `array` — Map of single-character string keys to arrays of exactly five 6-column strings

### `hasMetaphoneTrait()`

```php
private static function hasMetaphoneTrait(string $letter, int $trait): bool
```

Tests an upper-cased letter against a metaphone character class.

The classes are the bit flags of the algorithm's lookup table: 1 marks a
vowel (AEIOU), 2 a letter passed through unchanged (FJMNR), 4 a letter
forming a diphthong before H (CGPST), 8 a letter making C and G soft
(EIY), and 16 a letter preventing GH from becoming F (BDH).

| Parameter | Type | Description |
| --- | --- | --- |
| `$letter` | `string` | The upper-cased letter to test |
| `$trait` | `int` | The character class bit flag to test for |

**Returns** `bool` — True when `$letter` belongs to the given class

### `isMetaphoneLetter()`

```php
private static function isMetaphoneLetter(string $byte): bool
```

Determines whether a byte is an ASCII letter.

| Parameter | Type | Description |
| --- | --- | --- |
| `$byte` | `string` | The single byte to test |

**Returns** `bool` — True when the byte is in A-Z or a-z

### `lookAheadMetaphone()`

```php
private static function lookAheadMetaphone(string $word, int $index, int $distance): string
```

Looks ahead a fixed number of bytes from an offset, stopping at the string end.

| Parameter | Type | Description |
| --- | --- | --- |
| `$word` | `string` | The ASCII string being encoded |
| `$index` | `int` | The byte offset to look ahead from |
| `$distance` | `int` | The number of bytes to look ahead |

**Returns** `string` — The upper-cased byte found, or "\0" when the string ends first

### `phonizeMetaphone()`

```php
private static function phonizeMetaphone(string $word, int $maxPhonemes): string
```

Computes the metaphone key of an ASCII string.

Pure PHP implementation of the traditional metaphone algorithm, kept
byte-for-byte compatible with PHP's native `metaphone()`, which is
deprecated as of PHP 8.6. The input is treated as single-byte ASCII and
terminates at the first NUL byte, matching the native implementation.

| Parameter | Type | Description |
| --- | --- | --- |
| `$word` | `string` | The ASCII string to encode |
| `$maxPhonemes` | `int` | The maximum number of phonemes to return; 0 means no limit |

**Returns** `string` — The metaphone phonetic key

### `readMetaphoneByte()`

```php
private static function readMetaphoneByte(string $word, int $index): string
```

Reads a single raw byte from a metaphone input string.

Returns a NUL byte for out-of-range offsets, mirroring the NUL-terminated
string the native implementation walks.

| Parameter | Type | Description |
| --- | --- | --- |
| `$word` | `string` | The ASCII string being encoded |
| `$index` | `int` | The byte offset to read |

**Returns** `string` — The byte at `$index`, or "\0" when out of range

### `readMetaphoneLetter()`

```php
private static function readMetaphoneLetter(string $word, int $index): string
```

Reads a single byte from a metaphone input string, upper-cased.

| Parameter | Type | Description |
| --- | --- | --- |
| `$word` | `string` | The ASCII string being encoded |
| `$index` | `int` | The byte offset to read |

**Returns** `string` — The upper-cased byte at `$index`, or "\0" when out of range

### `transliterateToAscii()`

```php
private static function transliterateToAscii(string $string, string $language = 'en'): string
```

Transliterates accented and non-ASCII characters to their ASCII equivalents.

Applies a language-specific substitution table. Currently supports 'en' (default)
and 'de' (German umlaut expansion: ä → ae, ö → oe, ü → ue).

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The input string to transliterate |
| `$language` | `string` | The language code for locale-specific rules |

**Returns** `string` — The transliterated string (may still contain non-ASCII characters)

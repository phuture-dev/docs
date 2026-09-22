# Html

`Phuture\Coherence\Type\Html`

```php
class Html extends FluentClass implements Htmlable
```

A fluent wrapper around the Html utility class for chainable HTML manipulation.

Each method delegates to the corresponding static method on `\Phuture\Coherence\Html`,
stores the result internally, and returns `$this` to enable method chaining. Retrieve
the final value by calling `get()`, `toString()`, or invoking the object directly.

The wrapped value is always HTML. To build HTML from plain text or Markdown first,
use the static `\Phuture\Coherence\Html::toHtml()` and wrap the result, since on this
class `toHtml()` hands back the markup being held rather than converting anything.

**Example:**
```php
use Phuture\Coherence\Html;

$result = Html::of('<p onclick="steal()">Hello <script>alert(1)</script>world</p>')
    ->sanitize()
    ->truncate(8)
    ->toString();
// '<p>Hello wo...</p>'

$text = Html::of('<p>Hello <b>world</b></p>')->toText();
// 'Hello world'
```

## Methods

### `__toString()`

```php
public function __toString(): string
```

Returns the string representation of the wrapped HTML.

**Returns** `string` — The wrapped HTML markup

### `decode()`

```php
public function decode(EncodingMode $mode = EncodingMode::All, int $flags = ENT_QUOTES, string $encoding = 'UTF-8'): self
```

Decodes HTML entities in the wrapped value back to their characters.

| Parameter | Type | Description |
| --- | --- | --- |
| `$mode` | `EncodingMode` | The decoding mode (default: EncodingMode::All) |
| `$flags` | `int` | The bitmask of entity conversion flags (default: ENT_QUOTES) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Html::decode()`

### `encode()`

```php
public function encode(EncodingMode $mode = EncodingMode::All, int $flags = ENT_QUOTES, string $encoding = 'UTF-8'): self
```

Encodes characters in the wrapped value as HTML entities.

| Parameter | Type | Description |
| --- | --- | --- |
| `$mode` | `EncodingMode` | The encoding mode (default: EncodingMode::All) |
| `$flags` | `int` | The bitmask of entity conversion flags (default: ENT_QUOTES) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Html::encode()`

### `images()`

```php
public function images(): array
```

Lists the addresses of every image in the wrapped HTML.

**Returns** `array` — The image addresses in the order they appear, without repeats

**See also**

- `\Phuture\Coherence\Html::images()`

### `isSanitized()`

```php
public function isSanitized(array $allowedTags = [], array $allowedSchemes = ['http', 'https', 'mailto', 'tel'], int $maxLength = 20000): bool
```

Checks whether the wrapped HTML is already exactly what cleaning would produce.

| Parameter | Type | Description |
| --- | --- | --- |
| `$allowedTags` | `array` | The tag names to keep, or an empty array to keep every safe tag (default: []) |
| `$allowedSchemes` | `array` | The kinds of address allowed in links and images (default: ['http', 'https', 'mailto', 'tel']) |
| `$maxLength` | `int` | How many bytes of input to read, or -1 to read all of it (default: 20000) |

**Returns** `bool` — True when cleaning would change nothing, false when it would change something

**See also**

- `\Phuture\Coherence\Html::isSanitized()`

### `links()`

```php
public function links(): array
```

Lists the addresses of every link in the wrapped HTML.

**Returns** `array` — The link addresses in the order they appear, without repeats

**See also**

- `\Phuture\Coherence\Html::links()`

### `minify()`

```php
public function minify(): self
```

Makes the wrapped HTML smaller without changing how it looks.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Html::minify()`

### `sanitize()`

```php
public function sanitize(array $allowedTags = [], array $allowedSchemes = ['http', 'https', 'mailto', 'tel'], int $maxLength = 20000): self
```

Removes dangerous code from the wrapped HTML.

| Parameter | Type | Description |
| --- | --- | --- |
| `$allowedTags` | `array` | The tag names to keep, or an empty array to keep every safe tag (default: []) |
| `$allowedSchemes` | `array` | The kinds of address allowed in links and images (default: ['http', 'https', 'mailto', 'tel']) |
| `$maxLength` | `int` | How many bytes of input to read, or -1 to read all of it (default: 20000) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Html::sanitize()`

### `secureLinks()`

```php
public function secureLinks(string $rel = 'noopener noreferrer', bool $forceHttps = false): self
```

Cleans the wrapped HTML and hardens every link it contains.

| Parameter | Type | Description |
| --- | --- | --- |
| `$rel` | `string` | The relationship value to put on every link (default: 'noopener noreferrer') |
| `$forceHttps` | `bool` | Whether to rewrite insecure addresses to their secure form (default: false) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Html::secureLinks()`

### `stripComments()`

```php
public function stripComments(): self
```

Removes comments from the wrapped HTML, leaving the rest of the markup alone.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Html::stripComments()`

### `stripTags()`

```php
public function stripTags(array $allowedTags = []): self
```

Removes tags from the wrapped HTML, optionally keeping some of them.

| Parameter | Type | Description |
| --- | --- | --- |
| `$allowedTags` | `array` | A list of tag names to keep (without angle brackets, default: []) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Html::stripTags()`

### `tags()`

```php
public function tags(): array
```

Lists the tag names used in the wrapped HTML.

**Returns** `array` — The tag names in the order they appear, without repeats

**See also**

- `\Phuture\Coherence\Html::tags()`

### `toHtml()`

```php
public function toHtml(): string
```

Returns the wrapped HTML markup.

This method hands back the markup itself, unchanged. It returns the same
value as `toString()`; both exist so that code reading HTML can say so by
name, while anything expecting a plain string still works.

**Returns** `string` — The wrapped HTML markup

**See also**

- `\Phuture\Coherence\Type\Html::toString()`

### `toMarkdown()`

```php
public function toMarkdown(): string
```

Returns the wrapped HTML converted to Markdown.

**Returns** `string` — The content written as Markdown

**See also**

- `\Phuture\Coherence\Html::toMarkdown()`

### `toString()`

```php
public function toString(): string
```

Returns the wrapped HTML as a string.

**Returns** `string` — The wrapped HTML markup

### `toText()`

```php
public function toText(): string
```

Returns just the readable text of the wrapped HTML, with the markup removed.

**Returns** `string` — The readable text without any markup

**See also**

- `\Phuture\Coherence\Html::toText()`

### `truncate()`

```php
public function truncate(int $limit, string $end = '...'): self
```

Shortens the wrapped HTML to a number of visible characters.

| Parameter | Type | Description |
| --- | --- | --- |
| `$limit` | `int` | The maximum number of visible characters to keep |
| `$end` | `string` | The string to append when shortening occurs (default: '...') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Html::truncate()`

### `truncateWords()`

```php
public function truncateWords(int $limit, string $end = '...'): self
```

Shortens the wrapped HTML to a number of visible words.

| Parameter | Type | Description |
| --- | --- | --- |
| `$limit` | `int` | The maximum number of visible words to keep |
| `$end` | `string` | The string to append when shortening occurs (default: '...') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Html::truncateWords()`

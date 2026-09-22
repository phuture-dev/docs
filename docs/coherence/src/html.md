# Html

`Phuture\Coherence\Html`

```php
class Html extends StaticClass
```

Comprehensive HTML manipulation utility class.

This utility class provides a complete toolkit for HTML generation, conversion,
encoding, decoding, and text extraction. It supports conversions between HTML,
plain text, and Markdown, as well as HTML tag generation from individual tags
or structured arrays.

Key features:

- **Sanitization**: Remove scripts and unsafe markup from untrusted HTML
- **Text Extraction**: Strip HTML tags and decode entities back to readable text
- **HTML Conversion**: Convert plain text or Markdown to HTML
- **Markdown Conversion**: Convert HTML to Markdown
- **Encoding & Decoding**: Encode and decode HTML entities with mode selection via enum
- **Tag Generation**: Generate arbitrary HTML tags with attributes
- **Structured Building**: Generate complex HTML trees from multi-dimensional arrays
- **Inspection**: List the links, images and tag names a document contains
- **Size Reduction**: Shrink markup and remove comments without changing how it renders
- **Fluent Interface**: Chain operations with `of()` for readable transformations

## Constants

### `VOID_ELEMENTS`

```php
const VOID_ELEMENTS = [ 'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr', ]
```

HTML void elements that cannot have closing tags.

These elements are rendered without a closing tag (e.g., `<br>` instead of
`<br></br>`). The content parameter is ignored for void elements.

## Methods

### `attributes()`

```php
public static function attributes(array $attributes): string
```

Turns a list of attributes into a string you can drop inside a tag.

Each key becomes the attribute name and each value becomes the attribute
value, escaped so that quotes and angle brackets cannot break out of the
tag. A value of `true` writes the name on its own, which is how HTML marks
on/off options such as `disabled`. A value of `false` or `null` leaves the
attribute out altogether.

When there is at least one attribute the result begins with a space, so it
can be placed straight after a tag name without adding one yourself. Note
that attribute names are written out as given, so they must come from your
own code rather than from user input.

**Example:**
```php
use Phuture\Coherence\Html;

Html::attributes(['class' => 'btn', 'id' => 'save']);
// Returns: ' class="btn" id="save"'

Html::attributes(['disabled' => true, 'hidden' => false]);
// Returns: ' disabled'

Html::attributes([]);
// Returns: ''
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$attributes` | `array` | The attribute names and the values to give them |

**Returns** `string` — The attributes as text beginning with a space, or an empty string when there are none

**See also**

- `\Phuture\Coherence\Html::tag()`
- `\Phuture\Coherence\Html::build()`

### `build()`

```php
public static function build(array $elements): string
```

Builds HTML from a multi-dimensional array structure.

Recursively generates HTML elements from an array of tag definitions.
Each element in the array must contain a `tag` key with the tag name,
and may optionally contain `attributes` (an associative array) and
`content` (either a string or another tag definition array for nesting).

When `content` is an array matching the tag definition format, it is
recursively processed. When `content` is a plain array of tag definitions,
each element is processed and concatenated.

Void elements (such as `<br>`, `<img>`, `<input>`, `<hr>`, `<meta>`, etc.)
are rendered without a closing tag and without any content, regardless of
whether a `content` key is provided — it is silently ignored.

**Example:**
```php
use Phuture\Coherence\Html;

Html::build([
    ['tag' => 'div', 'attributes' => ['class' => 'container'], 'content' => [
        ['tag' => 'h1', 'content' => 'Title'],
        ['tag' => 'p', 'content' => 'Paragraph text'],
    ]],
]);
// '<div class="container"><h1>Title</h1><p>Paragraph text</p></div>'

Html::build([
    ['tag' => 'ul', 'content' => [
        ['tag' => 'li', 'content' => 'Item 1'],
        ['tag' => 'li', 'content' => 'Item 2'],
    ]],
]);
// '<ul><li>Item 1</li><li>Item 2</li></ul>'

Html::build([
    ['tag' => 'p', 'content' => 'Line one'],
    ['tag' => 'br'],
    ['tag' => 'img', 'attributes' => ['src' => 'photo.jpg', 'alt' => 'Photo']],
]);
// '<p>Line one</p><br><img src="photo.jpg" alt="Photo">'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$elements` | `array` | An array of tag definition arrays to build |

**Returns** `string` — The concatenated HTML string for all elements

**See also**

- `\Phuture\Coherence\Html::tag()`

### `comment()`

```php
public static function comment(string $content): string
```

Wraps text in an HTML comment.

A comment is a note in the markup that the browser does not display. Any
run of two dashes inside the text is broken apart first, because `-->` and
`--!>` both end a comment early, which would let whatever follows run as
real markup.

**Example:**
```php
use Phuture\Coherence\Html;

Html::comment('Section starts here');
// Returns: '<!-- Section starts here -->'

Html::comment('a --> b');
// Returns: '<!-- a - -> b -->'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$content` | `string` | The text to place inside the comment |

**Returns** `string` — The text wrapped in comment markers

**See also**

- `\Phuture\Coherence\Html::stripComments()`

### `decode()`

```php
public static function decode(string $string, EncodingMode $mode = EncodingMode::All, int $flags = ENT_QUOTES, string $encoding = 'UTF-8'): string
```

Decodes HTML entities back to their original characters.

The decoding behaviour is controlled by the `$mode` parameter using the
`\Phuture\Coherence\Enum\EncodingMode` enum. When set to `All`, all HTML entities
are decoded using `html_entity_decode()`. When set to `SpecialChars`, only the
special HTML character entities (`&amp;`, `&quot;`, `&#039;`, `&lt;`, `&gt;`)
are decoded using `htmlspecialchars_decode()`.

**Example:**
```php
use Phuture\Coherence\Html;
use Phuture\Coherence\Enum\EncodingMode;

Html::decode('Tom &amp; Jerry'); // 'Tom & Jerry'
Html::decode('&lt;p&gt;Hello&lt;/p&gt;'); // '<p>Hello</p>'
Html::decode('Tom &amp; Jerry', EncodingMode::SpecialChars); // 'Tom & Jerry'
Html::decode('caf&eacute;', EncodingMode::All); // 'café'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The string to decode |
| `$mode` | `\Phuture\Coherence\Enum\EncodingMode` | The decoding mode (default: EncodingMode::All) |
| `$flags` | `int` | The bitmask of entity conversion flags (default: ENT_QUOTES) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The decoded string with HTML entities restored to characters

**See also**

- `\Phuture\Coherence\Html::encode()`
- `\Phuture\Coherence\Enum\EncodingMode`

### `encode()`

```php
public static function encode(string $string, EncodingMode $mode = EncodingMode::All, int $flags = ENT_QUOTES, string $encoding = 'UTF-8'): string
```

Encodes characters in a string to their HTML entity equivalents.

The encoding behaviour is controlled by the `$mode` parameter using the
`\Phuture\Coherence\Enum\EncodingMode` enum. When set to `All`, every character
that has an HTML entity equivalent is converted using `htmlentities()`. When set
to `SpecialChars`, only the five characters with special meaning in HTML
(`&`, `"`, `'`, `<`, `>`) are encoded using `htmlspecialchars()`.

**Example:**
```php
use Phuture\Coherence\Html;
use Phuture\Coherence\Enum\EncodingMode;

Html::encode('Tom & Jerry'); // 'Tom &amp; Jerry'
Html::encode('<p>Hello</p>'); // '&lt;p&gt;Hello&lt;/p&gt;'
Html::encode('Tom & Jerry', EncodingMode::SpecialChars); // 'Tom &amp; Jerry'
Html::encode('café', EncodingMode::All); // 'caf&eacute;'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$string` | `string` | The string to encode |
| `$mode` | `\Phuture\Coherence\Enum\EncodingMode` | The encoding mode (default: EncodingMode::All) |
| `$flags` | `int` | The bitmask of entity conversion flags (default: ENT_QUOTES) |
| `$encoding` | `string` | The character encoding to use (default: 'UTF-8') |

**Returns** `string` — The encoded string with characters replaced by HTML entities

**See also**

- `\Phuture\Coherence\Html::decode()`
- `\Phuture\Coherence\Enum\EncodingMode`

### `images()`

```php
public static function images(string $html): array
```

Lists the addresses of every image in the HTML.

The HTML is cleaned first, so anything hidden inside a comment or a script
is not reported, and addresses that cleaning rejects, such as
`javascript:` ones, are left out. Each value appears once, in the order it
first shows up.

**Example:**
```php
use Phuture\Coherence\Html;

Html::images('<img src="a.png"><p>x</p><img src="b.png">');
// Returns: ['a.png', 'b.png']

Html::images('<p>No pictures here</p>');
// Returns: []
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML to read the image addresses from |

**Returns** `array` — The image addresses in the order they appear, without repeats

**See also**

- `\Phuture\Coherence\Html::links()`
- `\Phuture\Coherence\Html::sanitize()`

### `isSanitized()`

```php
public static function isSanitized(string $html, array $allowedTags = [], array $allowedSchemes = ['http', 'https', 'mailto', 'tel'], int $maxLength = 20000): bool
```

Checks whether HTML is already exactly what cleaning would produce.

Returns true only when `sanitize()` would leave the HTML untouched. A
false result does **not** mean the HTML is dangerous: cleaning also tidies
harmless things, so `<p class="x">y</p>`, `<br>` and `Tom & Jerry` all
come back as false simply because cleaning would rewrite them. Use it to
tell whether stored HTML still matches what cleaning produces today, not
as a test for whether something is safe.

The options are the same as `sanitize()` and must match the ones used to
clean the HTML in the first place, otherwise the answer is meaningless.

**Example:**
```php
use Phuture\Coherence\Html;

Html::isSanitized('<p>Hello</p>');
// Returns: true

Html::isSanitized('<p onclick="steal()">Hello</p>');
// Returns: false

Html::isSanitized('<p class="lead">Hello</p>');
// Returns: false, because cleaning removes the class
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML to check |
| `$allowedTags` | `array` | The tag names to keep, or an empty array to keep every safe tag (default: []) |
| `$allowedSchemes` | `array` | The kinds of address allowed in links and images (default: ['http', 'https', 'mailto', 'tel']) |
| `$maxLength` | `int` | How many bytes of input to read, or -1 to read all of it (default: 20000) |

**Returns** `bool` — True when cleaning would change nothing, false when it would change something

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the maximum length is below -1

**See also**

- `\Phuture\Coherence\Html::sanitize()`

### `link()`

```php
public static function link(string|array $href, string $rel = 'stylesheet', string $type = 'text/css', string $title = '', string $media = '', string $hreflang = ''): string
```

Generates an HTML link element.

Creates a `<link>` tag, typically used for stylesheets, favicons, and other
resource links. When using the array form, you have full control over all
attributes. When using the string form, sensible defaults are applied for
stylesheets.

**Example:**
```php
use Phuture\Coherence\Html;

Html::link('styles.css');
// '<link href="styles.css" rel="stylesheet" type="text/css">'

Html::link('favicon.ico', 'shortcut icon', 'image/ico');
// '<link href="favicon.ico" rel="shortcut icon" type="image/ico">'

Html::link(['href' => 'print.css', 'rel' => 'stylesheet', 'media' => 'print']);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$href` | `string\|array` | The URL of the linked resource, or an associative array of attributes |
| `$rel` | `string` | The relationship type (default: 'stylesheet') |
| `$type` | `string` | The content type (default: 'text/css') |
| `$title` | `string` | The title of the link (default: '') |
| `$media` | `string` | The media type the link applies to (default: '') |
| `$hreflang` | `string` | The language of the linked resource (default: '') |

**Returns** `string` — The generated `<link>` element

**See also**

- `\Phuture\Coherence\Html::tag()`
- `\Phuture\Coherence\Html::script()`

### `links()`

```php
public static function links(string $html): array
```

Lists the addresses of every link in the HTML.

The HTML is cleaned first, so anything hidden inside a comment or a script
is not reported, and addresses that cleaning rejects, such as
`javascript:` ones, are left out. Each value appears once, in the order it
first shows up.

**Example:**
```php
use Phuture\Coherence\Html;

Html::links('<a href="/about">About</a> and <a href="https://e.com">E</a>');
// Returns: ['/about', 'https://e.com']

Html::links('<a href="javascript:alert(1)">Bad</a>');
// Returns: []
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML to read the link addresses from |

**Returns** `array` — The link addresses in the order they appear, without repeats

**See also**

- `\Phuture\Coherence\Html::images()`
- `\Phuture\Coherence\Html::sanitize()`

### `minify()`

```php
public static function minify(string $html): string
```

Makes HTML smaller without changing how it looks.

Squeezes every run of spaces, tabs and newlines down to a single space and
drops comments. A browser already treats any run of spacing as one space,
so the page renders exactly as before — the spacing is never removed
outright, because deleting the space in `<b>a</b> <b>b</b>` would join the
two words together.

Everything inside `<pre>`, `<textarea>`, `<script>` and `<style>` is left
exactly as it was, since spacing matters there. Spacing is still reduced
if your stylesheet makes some other element preserve it.

**Example:**
```php
use Phuture\Coherence\Html;

Html::minify("<div>\n    <p>Hello</p>\n</div>");
// Returns: '<div> <p>Hello</p> </div>'

Html::minify('<p>a</p><!-- note --><p>b</p>');
// Returns: '<p>a</p><p>b</p>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML to shrink |

**Returns** `string` — The HTML with spacing reduced and comments removed

**See also**

- `\Phuture\Coherence\Html::stripComments()`

### `of()`

```php
public static function of(string $html): Type\Html
```

Creates a fluent wrapper around the given HTML for method chaining.

Returns a `Type\Html` instance that wraps the provided markup and exposes
the HTML-returning methods as chainable calls.

**Example:**
```php
use Phuture\Coherence\Html;

$result = Html::of('<p onclick="steal()">Hello</p>')
    ->sanitize()
    ->toString();
// '<p>Hello</p>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML to wrap for fluent operations |

**Returns** `Type\Html` — A fluent wrapper instance that enables method chaining

**See also**

- `\Phuture\Coherence\Type\Html` — For the fluent wrapper implementation

### `safeTags()`

```php
public static function safeTags(): array
```

Lists the tag names that cleaning treats as safe.

These are the names `sanitize()` keeps when you do not narrow the list
yourself, useful for building a tag picker or explaining to someone why
their markup disappeared. The names are returned in alphabetical order.

Four of them describe the head of a page rather than its content: `head`,
`link`, `meta` and `title`. Those never survive `sanitize()`, which treats
its input as page content, so treat this as the list of names the cleaner
recognises rather than a promise that each one survives.

**Example:**
```php
use Phuture\Coherence\Html;

in_array('em', Html::safeTags(), true);
// Returns: true

in_array('script', Html::safeTags(), true);
// Returns: false
```

**Returns** `array` — The safe tag names in alphabetical order

**See also**

- `\Phuture\Coherence\Html::sanitize()`

### `sanitize()`

```php
public static function sanitize(string $html, array $allowedTags = [], array $allowedSchemes = ['http', 'https', 'mailto', 'tel'], int $maxLength = 20000): string
```

Removes dangerous code from HTML that came from an untrusted source.

Takes HTML written by someone you do not trust, such as a comment or a
profile description, and returns a version that is safe to put on a page.
Scripts, click handlers like `onclick`, and links that try to run code are
removed. Broken or half-written HTML is repaired instead of rejected.

By default every tag considered safe is kept, which is what you want for
text a user has formatted themselves. Attributes are filtered the same
way, and two common ones are not on the safe list: `class` and `style`
are always removed, because either can be used to cover the page with an
invisible clickable layer. Plan for styling by tag name, not by class.

Pass a list of tag names in
`$allowedTags` to keep fewer: the other safe tags are removed but their
text stays, the same way `stripTags()` behaves. Tags that are never safe,
such as `script` and `style`, are always removed together with everything
inside them, even when you list them.

A link or image keeps its address only when the address starts with one of
the `$allowedSchemes`, which is what stops `javascript:` links from working.
Addresses with no scheme, such as `/about`, are always kept, and the ones
that are kept may come back with characters like `@` written as `&#64;`,
which a browser displays the same way.

Input longer than `$maxLength` bytes is cut before anything is read, so a
huge page cannot be used to slow the server down. Pass `-1` to read it all.

**Example:**
```php
use Phuture\Coherence\Html;

Html::sanitize('<p onclick="steal()">Hello <script>alert(1)</script>world</p>');
// Returns: '<p>Hello world</p>'

Html::sanitize('<a href="javascript:alert(1)">Click</a>');
// Returns: '<a>Click</a>'

Html::sanitize('<p>Keep <em>this</em> and <b>that</b></p>', ['em']);
// Returns: 'Keep <em>this</em> and that'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The untrusted HTML to clean |
| `$allowedTags` | `array` | The tag names to keep, written without angle brackets, or an empty array to keep every safe tag (default: []) |
| `$allowedSchemes` | `array` | The kinds of address allowed in links and images (default: ['http', 'https', 'mailto', 'tel']) |
| `$maxLength` | `int` | How many bytes of input to read, or -1 to read all of it (default: 20000) |

**Returns** `string` — The cleaned HTML, safe to display on a page

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the maximum length is below -1

**See also**

- `\Phuture\Coherence\Html::stripTags()`
- `\Phuture\Coherence\Html::encode()`

### `script()`

```php
public static function script(?string $src, string $content = '', array $attributes = []): string
```

Generates an HTML script element.

Creates a `<script>` tag. When a source URL is provided, the script
references an external file. When null is passed as the source, inline
content can be provided instead. Only one of `src` or `content` should
be used at a time.

**Example:**
```php
use Phuture\Coherence\Html;

Html::script('app.js'); // '<script src="app.js"></script>'
Html::script('app.js', '', ['defer' => true]); // '<script src="app.js" defer></script>'
Html::script(null, 'alert("hi");'); // '<script>alert("hi");</script>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$src` | `string\|null` | The script source URL, or null for inline scripts |
| `$content` | `string` | The inline script content (default: '') |
| `$attributes` | `array` | Additional HTML attributes (default: []) |

**Returns** `string` — The generated `<script>` element

**See also**

- `\Phuture\Coherence\Html::tag()`
- `\Phuture\Coherence\Html::link()`

### `secureLinks()`

```php
public static function secureLinks(string $html, string $rel = 'noopener noreferrer', bool $forceHttps = false): string
```

Cleans HTML and hardens every link it contains.

This does everything `sanitize()` does — the whole document is cleaned, so
scripts go, and `class` and `style` are removed along with them — and then
adds a `rel` attribute to every link. The usual value, `noopener
noreferrer`, stops a page you link to from reaching back into the page
that opened it and hides where the visitor came from.

Turning on `$forceHttps` rewrites `http://` addresses to `https://` in
links and images alike. Anchors without an address, such as in-page jump
targets, still receive the `rel` attribute.

**Example:**
```php
use Phuture\Coherence\Html;

Html::secureLinks('<a href="https://example.com">Visit</a>');
// Returns: '<a href="https://example.com" rel="noopener noreferrer">Visit</a>'

Html::secureLinks('<a href="http://example.com">Visit</a>', 'nofollow', true);
// Returns: '<a href="https://example.com" rel="nofollow">Visit</a>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The untrusted HTML to clean and harden |
| `$rel` | `string` | The relationship value to put on every link, which tells the browser how the linked page relates to this one (default: 'noopener noreferrer') |
| `$forceHttps` | `bool` | Whether to rewrite insecure addresses to their secure form (default: false) |

**Returns** `string` — The cleaned HTML with every link hardened

**See also**

- `\Phuture\Coherence\Html::sanitize()`

### `stripComments()`

```php
public static function stripComments(string $html): string
```

Removes HTML comments, leaving the rest of the markup alone.

Comments are the notes between `<!--` and `-->` that a browser does not
display. Unlike `stripTags()` and `sanitize()`, which also drop comments
but change the surrounding markup too, this touches nothing else — useful
for trimming trusted templates you do not want otherwise rewritten.

A `<!--` with no closing `-->` is left in place rather than swallowing the
rest of the document.

**Example:**
```php
use Phuture\Coherence\Html;

Html::stripComments('<p class="lead">Hello<!-- note --></p>');
// Returns: '<p class="lead">Hello</p>'

Html::stripComments('<p>Kept<!-- unfinished');
// Returns: '<p>Kept<!-- unfinished'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML to remove comments from |

**Returns** `string` — The HTML without its comments

**See also**

- `\Phuture\Coherence\Html::comment()`
- `\Phuture\Coherence\Html::minify()`

### `stripTags()`

```php
public static function stripTags(string $html, array $allowedTags = []): string
```

Removes HTML tags from a string while optionally preserving specific allowed tags.

This method strips all HTML and PHP tags from the given string. You can provide
a list of tag names (without angle brackets) that should be kept in the result.
Any attributes on the allowed tags are preserved as-is.

Note: HTML comments and PHP tags are always removed, even if not explicitly listed.

**Example:**
```php
use Phuture\Coherence\Html;

Html::stripTags('<p>Hello <b>world</b></p>');
// Returns: 'Hello world'

Html::stripTags('<p>Hello <b>world</b></p>', ['b']);
// Returns: 'Hello <b>world</b>'

Html::stripTags('<div><a href="#">Link</a> text</div>', ['a']);
// Returns: '<a href="#">Link</a> text'

Html::stripTags('<p>Keep <em>this</em> <strong>bold</strong></p>', ['em', 'strong']);
// Returns: 'Keep <em>this</em> <strong>bold</strong>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML string to strip tags from |
| `$allowedTags` | `array` | A list of tag names to keep (without angle brackets, default: []) |

**Returns** `string` — The string with HTML tags removed, keeping only the allowed tags

**See also**

- `\Phuture\Coherence\Html::toText()`

### `table()`

```php
public static function table(array $rows, array $headers = [], array $attributes = []): string
```

Generates an HTML table from a two-dimensional array.

Converts a list of rows (each row being an array of cell values) into a
complete HTML `<table>` element. If column headers are provided, they are
rendered as `<th>` cells inside a `<thead>` section; all data rows are
wrapped in a `<tbody>`. Cell values are cast to string before output.

Every inner array must have the same number of elements. If a column headers
array is given, it should also match that length — extra or missing headers
are not validated and will produce misaligned columns.

**Example:**
```php
use Phuture\Coherence\Html;

$rows = [
    ['Alice', 30, 'Engineer'],
    ['Bob', 25, 'Designer'],
];

Html::table($rows);
// '<table><tbody><tr><td>Alice</td><td>30</td><td>Engineer</td></tr>
//  <tr><td>Bob</td><td>25</td><td>Designer</td></tr></tbody></table>'

Html::table($rows, ['Name', 'Age', 'Role'], ['class' => 'data-table']);
// '<table class="data-table"><thead><tr><th>Name</th><th>Age</th><th>Role</th></tr></thead>
//  <tbody><tr><td>Alice</td>...</td></tr></tbody></table>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$rows` | `array` | A list of rows, where each row is an array of cell values |
| `$headers` | `array` | Optional column header labels rendered as `<th>` cells in a `<thead>` (default: []) |
| `$attributes` | `array` | Optional HTML attributes applied to the `<table>` element (default: []) |

**Returns** `string` — The generated HTML table string

**See also**

- `\Phuture\Coherence\Html::tag()`
- `\Phuture\Coherence\Html::build()`

### `tag()`

```php
public static function tag(string $name, string $content = '', array $attributes = []): string
```

Generates an HTML tag with attributes.

Creates an opening and closing tag pair with the given content and attributes.
Void elements (like `<img>`, `<br>`, `<hr>`) are rendered without a closing
tag and the content parameter is ignored. Boolean attributes (where the value
is `true`) are rendered as just the attribute name, and attributes with a
`false` or `null` value are omitted entirely.

**Example:**
```php
use Phuture\Coherence\Html;

Html::tag('p', 'Hello'); // '<p>Hello</p>'
Html::tag('p', 'Hello', ['class' => 'greeting']); // '<p class="greeting">Hello</p>'
Html::tag('br'); // '<br>'
Html::tag('input', '', ['type' => 'text', 'required' => true]); // '<input type="text" required>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$name` | `string` | The tag name (e.g., 'p', 'div', 'img') |
| `$content` | `string` | The inner content of the tag (default: '') |
| `$attributes` | `array` | Associative array of HTML attributes (default: []) |

**Returns** `string` — The generated HTML tag

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the tag name is empty

**See also**

- `\Phuture\Coherence\Html::build()`
- `\Phuture\Coherence\Html::link()`
- `\Phuture\Coherence\Html::script()`

### `tags()`

```php
public static function tags(string $html): array
```

Lists the tag names used in the HTML.

The HTML is cleaned first, so anything hidden inside a comment or a script
is not reported, and addresses that cleaning rejects, such as
`javascript:` ones, are left out. Each value appears once, in the order it
first shows up.
Opening and closing tags of the same element count once, and the names
come back in lower case.

**Example:**
```php
use Phuture\Coherence\Html;

Html::tags('<div><p>Hi</p><p>There</p></div>');
// Returns: ['div', 'p']

Html::tags('Just text');
// Returns: []
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML to read the tag names from |

**Returns** `array` — The tag names in the order they appear, without repeats

**See also**

- `\Phuture\Coherence\Html::safeTags()`
- `\Phuture\Coherence\Html::sanitize()`

### `toHtml()`

```php
public static function toHtml(string $content, ?bool $isMarkdown = null): string
```

Converts plain text or Markdown to HTML.

When `$isMarkdown` is `true`, the content is converted using the CommonMark parser.
When `$isMarkdown` is `false`, the content is treated as plain text. When `$isMarkdown`
is `null` (the default), Markdown is detected automatically by looking for common
Markdown syntax patterns such as headings (#), emphasis (*, _), links ([text](url)),
images (![alt](src)), lists (-, *), code blocks (```), and blockquotes (>).

Plain text is HTML-encoded and newlines are converted to `<br>` tags.

**Example:**
```php
use Phuture\Coherence\Html;

Html::toHtml('Hello & goodbye'); // 'Hello &amp; goodbye'
Html::toHtml("Line 1\nLine 2"); // 'Line 1<br>\nLine 2'
Html::toHtml('# Heading'); // '<h1>Heading</h1>'
Html::toHtml('**bold**'); // '<p><strong>bold</strong></p>'
Html::toHtml('# Not Markdown', false); // '# Not Markdown'
Html::toHtml('**bold**', true); // '<p><strong>bold</strong></p>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$content` | `string` | The plain text or Markdown content to convert |
| `$isMarkdown` | `bool\|null` | Force Markdown (true), force plain text (false), or auto-detect (null, default) |

**Returns** `string` — The resulting HTML string

**See also**

- `\Phuture\Coherence\Html::toText()`
- `\Phuture\Coherence\Html::toMarkdown()`

### `toMarkdown()`

```php
public static function toMarkdown(string $html): string
```

Converts HTML to Markdown.

Uses the league/html-to-markdown library to convert the given HTML string
into its Markdown representation. This is useful for generating Markdown
from rich HTML content.

**Example:**
```php
use Phuture\Coherence\Html;

Html::toMarkdown('<h1>Heading</h1>'); // '# Heading'
Html::toMarkdown('<p><strong>bold</strong></p>'); // '**bold**'
Html::toMarkdown('<a href="https://example.com">Link</a>'); // '[Link](https://example.com)'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML string to convert to Markdown |

**Returns** `string` — The Markdown representation of the HTML

**See also**

- `\Phuture\Coherence\Html::toHtml()`

### `toText()`

```php
public static function toText(string $html): string
```

Converts HTML to plain text.

Removes all HTML tags and returns readable text. The `<br>` tag is
converted to a newline character before stripping, and all HTML entities
are decoded. If the content between block-level tags appears on separate
lines, the spacing is preserved for readability.

**Example:**
```php
use Phuture\Coherence\Html;

Html::toText('<p>Hello <b>world</b></p>'); // 'Hello world'
Html::toText('Line 1<br>Line 2'); // "Line 1\nLine 2"
Html::toText('Tom &amp; Jerry'); // 'Tom & Jerry'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML string to convert to plain text |

**Returns** `string` — The plain text with all tags removed and entities decoded

**See also**

- `\Phuture\Coherence\Html::toHtml()`
- `\Phuture\Coherence\Html::decode()`

### `truncate()`

```php
public static function truncate(string $html, int $limit, string $end = '...'): string
```

Truncates an HTML string to a given number of visible characters.

Counts only the characters that a reader can see — HTML tags and their
angle brackets do not count toward the limit. HTML entities like `&amp;`
or `&eacute;` each count as one visible character, because they display
as a single character in the browser.

When the text is cut short, any HTML tags that were left open are
automatically closed so the returned string is always valid HTML. The
suffix is appended after all closing tags.

Returns the original string unchanged when the visible text is already
within the limit. The suffix is only appended when truncation actually occurs.

Note: HTML comments containing a `>` character inside them may not be
handled correctly.

**Example:**
```php
use Phuture\Coherence\Html;

Html::truncate('<p>Hello <b>world</b></p>', 7);
// Returns: '<p>Hello <b>w...</b></p>'

Html::truncate('<p>Hello</p>', 10);
// Returns: '<p>Hello</p>'

Html::truncate('<p>Tom &amp; Jerry</p>', 5);
// Returns: '<p>Tom &amp;...</p>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML string to truncate |
| `$limit` | `int` | The maximum number of visible characters to keep |
| `$end` | `string` | The string to append when truncation occurs (default: '...') |

**Returns** `string` — The truncated HTML string with all open tags properly closed

**See also**

- `\Phuture\Coherence\Html::truncateWords()`
- `\Phuture\Coherence\Html::toText()`

### `truncateWords()`

```php
public static function truncateWords(string $html, int $limit, string $end = '...'): string
```

Truncates an HTML string to a given number of visible words.

Counts only the words that a reader can see — HTML tags do not count
toward the limit. A word is any sequence of non-whitespace characters.
HTML entities like `&amp;` are treated as word characters.

When the text is cut short, any HTML tags that were left open are
automatically closed so the returned string is always valid HTML. The
suffix is appended after all closing tags.

Returns the original string unchanged when the visible word count is
already within the limit. The suffix is only appended when truncation
actually occurs.

Note: HTML comments containing a `>` character inside them may not be
handled correctly.

**Example:**
```php
use Phuture\Coherence\Html;

Html::truncateWords('<p>One Two Three Four</p>', 2);
// Returns: '<p>One Two...</p>'

Html::truncateWords('<p>Hello World</p>', 5);
// Returns: '<p>Hello World</p>'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML string to truncate |
| `$limit` | `int` | The maximum number of visible words to keep |
| `$end` | `string` | The string to append when truncation occurs (default: '...') |

**Returns** `string` — The truncated HTML string with all open tags properly closed

**See also**

- `\Phuture\Coherence\Html::truncate()`
- `\Phuture\Coherence\Html::toText()`

### `buildSanitizerConfig()`

```php
private static function buildSanitizerConfig(array $allowedTags, array $allowedSchemes, int $maxLength): HtmlSanitizerConfig
```

Builds the sanitizer configuration shared by every cleaning method.

| Parameter | Type | Description |
| --- | --- | --- |
| `$allowedTags` | `array` | The tag names to keep, or an empty array for every safe tag |
| `$allowedSchemes` | `array` | The kinds of address allowed in links and images |
| `$maxLength` | `int` | The maximum input length in bytes, or -1 for no limit |

**Returns** `HtmlSanitizerConfig` — The configuration to hand to the sanitizer

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the maximum length is below -1

### `closeOpenTags()`

```php
private static function closeOpenTags(array $openTags): string
```

Serialises the open-tag stack as a sequence of closing tags.

| Parameter | Type | Description |
| --- | --- | --- |
| `$openTags` | `array` | The stack of unclosed tag names (innermost last) |

**Returns** `string` — A string of closing tags in reverse order, or empty string when none

### `convertMarkdownToHtml()`

```php
private static function convertMarkdownToHtml(string $markdown): string
```

Converts Markdown content to HTML using the CommonMark parser.

| Parameter | Type | Description |
| --- | --- | --- |
| `$markdown` | `string` | The Markdown string to convert |

**Returns** `string` — The resulting HTML

### `extractAttributeValues()`

```php
private static function extractAttributeValues(string $html, string $tag, string $attribute): array
```

Collects one attribute's values from every matching tag in cleaned HTML.

| Parameter | Type | Description |
| --- | --- | --- |
| `$html` | `string` | The HTML to read |
| `$tag` | `string` | The tag name to look for |
| `$attribute` | `string` | The attribute whose value to collect |

**Returns** `array` — The attribute values in document order, without repeats

### `isMarkdown()`

```php
private static function isMarkdown(string $content): bool
```

Determines whether the given content appears to be Markdown.

Checks for common Markdown syntax patterns that would not typically
appear in plain text. Returns false when no Markdown patterns are found,
indicating the content should be treated as plain text.

| Parameter | Type | Description |
| --- | --- | --- |
| `$content` | `string` | The content to inspect |

**Returns** `bool` — True when the content appears to contain Markdown syntax

### `updateOpenTagStack()`

```php
private static function updateOpenTagStack(string $tag, array &$openTags): void
```

Updates the open-tag stack based on a parsed HTML tag token.

| Parameter | Type | Description |
| --- | --- | --- |
| `$tag` | `string` | The raw HTML tag string (e.g. '<p>', '</p>', '<br />') |

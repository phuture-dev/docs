# Htmlable

`Phuture\Coherence\Interface\Htmlable`

```php
interface Htmlable extends Stringable
```

Interface for objects that hold HTML and can be shown in other forms.

This interface provides a standardized way for objects that carry HTML to
hand it over, either as the markup itself or converted into Markdown or
readable text. Any class that wraps or represents HTML should implement this
interface so that consumers can reliably obtain it in the form they need.

## Methods

### `toHtml()`

```php
public function toHtml(): string
```

Returns the HTML held by the object.

This method hands back the markup itself, unchanged. It returns the same
value as `toString()`; both exist so that code reading HTML can say so by
name, while anything expecting a plain string still works.

**Returns** `string` — The HTML markup

### `toMarkdown()`

```php
public function toMarkdown(): string
```

Returns the HTML converted to Markdown.

Markdown is a plain-text way of writing formatted documents, where a
heading is written with `#` and bold text with `**stars**`. This is useful
for storing content in a form people can edit by hand.

**Returns** `string` — The content written as Markdown

### `toString()`

```php
public function toString(): string
```

Returns the HTML as a string.

This method provides the object's default string form, which is the markup
itself. It returns the same value as `toHtml()`.

**Returns** `string` — The HTML markup

### `toText()`

```php
public function toText(): string
```

Returns just the readable text, with the markup removed.

Tags are stripped and entities such as `&amp;` are turned back into the
characters they stand for, leaving what a reader would see on the page.

**Returns** `string` — The readable text without any markup

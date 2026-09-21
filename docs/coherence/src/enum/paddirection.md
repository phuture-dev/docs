# PadDirection

`Phuture\Coherence\Enum\PadDirection`

```php
enum PadDirection
```

Enumeration for string padding direction.

Controls which side of a string padding characters are added to.
Use this enum with \Phuture\Coherence\Strings::pad() to specify
where the pad string is applied.

## Cases

### `Both`

```php
case Both
```

Padding is added to both sides of the string equally.

When the required padding is an odd number, the extra character
is added to the right side.

### `Left`

```php
case Left
```

Padding is prepended to the left (start) of the string.

Characters are added before the first character of the string
until the target length is reached.

### `Right`

```php
case Right
```

Padding is appended to the right (end) of the string.

This is the default direction. Characters are added after the last
character of the string until the target length is reached.

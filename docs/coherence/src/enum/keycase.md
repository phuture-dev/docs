# KeyCase

`Phuture\Coherence\Enum\KeyCase`

```php
enum KeyCase
```

Enumeration for array key letter casing.

Controls the case that string keys are converted to. Use this enum with
\Phuture\Coherence\Arrays::changeKeyCase() to specify whether keys are
normalized to lowercase or uppercase.

## Cases

### `Lower`

```php
case Lower
```

Convert string keys to lowercase.

This is the default case. Every alphabetic character in a string key is
lowercased, while numeric keys are left untouched.

### `Upper`

```php
case Upper
```

Convert string keys to uppercase.

Every alphabetic character in a string key is uppercased, while numeric
keys are left untouched.

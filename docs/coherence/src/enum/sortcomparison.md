# SortComparison

`Phuture\Coherence\Enum\SortComparison`

```php
enum SortComparison
```

Enumeration for value comparison strategies during sorting.

Determines how two values are compared to each other when an operation has to decide
whether they are equivalent. Use this enum with \Phuture\Coherence\Arrays::unique()
to control how duplicate values are recognised. Exactly one strategy applies to any
given operation; these are alternatives rather than flags to be combined.

## Cases

### `LocaleString`

```php
case LocaleString
```

Compare values as strings using the current locale's collation rules.

Behaves like String comparison, except that the ordering and equivalence of
characters follow the locale set through the LC_COLLATE category, which makes the
outcome dependent on the runtime environment.

### `Numeric`

```php
case Numeric
```

Compare values numerically.

Both values are interpreted as numbers before being compared, so strings holding
the same quantity in different notations are treated as equal. Values that are
not numeric are interpreted as zero.

### `Regular`

```php
case Regular
```

Compare values by their native type without converting them first.

Values are compared using PHP's standard comparison semantics for the types
involved, so integers are compared as integers, strings as strings, and mixed
pairs follow PHP's own type juggling rules.

### `String`

```php
case String
```

Compare values as strings.

This is the default strategy. Both values are cast to strings before comparison,
so two values that render identically are treated as equal even when their
original types differ.

# ArrayComparator

`Phuture\Coherence\Enum\ArrayComparator`

```php
enum ArrayComparator
```

Enumeration for array comparison strategies.

This enum defines different ways to compare arrays when performing operations
like checking equality, differences, or intersections. Each case represents
a specific comparison focus. Use this enum with \Phuture\Coherence\Arrays::difference()
and \Phuture\Coherence\Arrays::intersection() to control whether comparisons are
performed on keys, values, or both.

## Cases

### `Both`

```php
case Both
```

Compare arrays based on both keys and values.

When using this comparator, both the keys and their associated values are
considered during comparison operations. This provides the most comprehensive
comparison, requiring both the structure (keys) and content (values) to match.

### `Key`

```php
case Key
```

Compare arrays based on their keys only.

When using this comparator, only the keys of the arrays are considered
during comparison operations. Values associated with those keys are ignored.

### `Value`

```php
case Value
```

Compare arrays based on their values only.

When using this comparator, only the values within the arrays are considered
during comparison operations. The order and keys of those values may or may not
be relevant depending on the specific operation.

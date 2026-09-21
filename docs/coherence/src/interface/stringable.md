# Stringable

`Phuture\Coherence\Interface\Stringable`

```php
interface Stringable
```

Interface for objects that can be converted to strings.

This interface provides a standardized way for objects to be converted
to their string representation, making it easier to display, log, or
serialize objects as text.

## Methods

### `toString()`

```php
public function toString(): string
```

Convert the object to a string representation.

This method should return a meaningful string representation
of the object's data or state. The implementation should be
consistent and provide useful information for debugging,
logging, or display purposes.

**Returns** `string` — The string representation of the object

# OutOfBoundsException

`Phuture\Coherence\Exception\OutOfBoundsException`

```php
class OutOfBoundsException extends \OutOfBoundsException implements Exception
```

Exception thrown when a value is not a valid key or when accessing an array
with an invalid offset or index.

Common scenarios where this exception is thrown:
- Accessing an array with an offset that doesn't exist
- Using negative indices with collections that don't support them
- Attempting to access an element beyond the collection's size
- Providing an invalid key type for indexed collections

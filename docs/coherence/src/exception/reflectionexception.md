# ReflectionException

`Phuture\Coherence\Exception\ReflectionException`

```php
class ReflectionException extends \ReflectionException implements Exception
```

Exception thrown for reflection errors.
This exception is used when reflection operations fail, such as trying to
access non-existent classes, methods, or properties.

Common scenarios:
- Attempting to reflect on a non-existent class or method
- Trying to access inaccessible properties or methods through reflection
- Invalid parameter types passed to reflection methods

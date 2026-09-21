# InvalidArgumentException

`Phuture\Coherence\Exception\InvalidArgumentException`

```php
class InvalidArgumentException extends \InvalidArgumentException implements Exception
```

Exception thrown when an invalid argument is provided to a method.

Common scenarios where this exception is thrown:
- A required parameter is missing, and no default value was provided
- An argument value is outside the expected range or format
- A parameter fails validation checks

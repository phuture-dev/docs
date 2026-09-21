# BadMethodCallException

`Phuture\Coherence\Exception\BadMethodCallException`

```php
class BadMethodCallException extends \BadMethodCallException implements Exception
```

Exception thrown when a call to a method is not valid.
This exception is used when attempting to call a method that doesn't exist,
is not accessible, or when method arguments are invalid.

Common scenarios:
- Calling a method that doesn't exist on the object
- Calling methods with an incorrect number or type of arguments

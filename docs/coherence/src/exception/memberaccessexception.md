# MemberAccessException

`Phuture\Coherence\Exception\MemberAccessException`

```php
class MemberAccessException extends BadMethodCallException implements Exception
```

Exception thrown when attempting to access a class member (method or property) that is not accessible.

Common scenarios where this exception is thrown:
- Attempting to call a private or protected method from outside the class
- Attempting to access a class member that should not be publicly accessible

# LogicException

`Phuture\Coherence\Exception\LogicException`

```php
class LogicException extends \LogicException implements Exception
```

Exception thrown when there is a logical error in the program flow.
This exception is used when code is called inappropriately or when
the program reaches a state that should not be possible. These errors typically
indicate bugs in the code or incorrect usage of the library's API.

Common scenarios:
- Calling methods in invalid states or sequences
- Providing invalid parameters that violate logic
- Attempting operations that are not allowed in the current context

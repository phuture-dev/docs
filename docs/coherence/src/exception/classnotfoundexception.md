# ClassNotFoundException

`Phuture\Coherence\Exception\ClassNotFoundException`

```php
class ClassNotFoundException extends LogicException implements Exception
```

Exception thrown when a class cannot be found or loaded.
This exception indicates that an attempt was made to use a class that does
not exist, cannot be loaded, or is not available in the current execution context.

Common scenarios where this exception is thrown:
- Attempting to instantiate a non-existent class
- Autoloader fails to locate a class file
- Missing dependencies or required extensions

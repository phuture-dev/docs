# Exception

`Phuture\Coherence\Interface\Exception`

```php
interface Exception extends Throwable
```

Interface for all library exceptions.

This interface serves as a contract that all exception classes in the
library namespace should implement. It extends PHP's native Throwable
interface to maintain compatibility with standard exception handling while
providing a common type for catching library-specific exceptions.

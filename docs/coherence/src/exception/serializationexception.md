# SerializationException

`Phuture\Coherence\Exception\SerializationException`

```php
class SerializationException extends LogicException implements Exception
```

Exception thrown when serialization or deserialization operations fail.

Common scenarios where this exception is thrown:
- PHP serialization encounters non-serializable objects
- Unsupported data types cannot be serialized
- Malformed serialized data cannot be deserialized

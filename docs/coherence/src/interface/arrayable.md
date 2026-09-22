# Arrayable

`Phuture\Coherence\Interface\Arrayable`

```php
interface Arrayable extends ArrayAccess
```

Interface for objects that can be converted to arrays.

This interface provides a standardized way for objects to be converted
to their array representation, making it easier to work with different
data types consistently.

## Methods

### `toArray()`

```php
public function toArray(): array
```

Convert the object to an array representation.

This method should return a native PHP array that represents
the object's data in a serializable format.

**Returns** `array` — The array representation of the object

### `toJson()`

```php
public function toJson(): string
```

Convert the object to a JSON string representation.

This method should return a JSON-encoded string that represents
the object's data in a format suitable for storage or transmission.

**Returns** `string` — The JSON representation of the object

### `toObject()`

```php
public function toObject(): object
```

Convert the object to a generic object representation.

This method should return a plain PHP object (stdClass) that represents
the object's data, making it easier to access properties dynamically.

**Returns** `object` — The object representation of the object

# Numberable

`Phuture\Coherence\Interface\Numberable`

```php
interface Numberable
```

Interface for objects that can be converted to numeric types.

This interface provides a standardized way for objects to be converted
to different numeric representations. Classes implementing this interface
can be treated as numbers in various contexts, allowing for flexible
type conversion and numeric operations.

## Methods

### `toFloat()`

```php
public function toFloat(): float
```

Converts the object to a float.

This method should convert the object to a floating-point number.
Use this method when you need decimal precision or want to ensure
the result can contain fractional parts.

**Example:**
```php
$number = new SomeNumberableObject();
$value = $number->toFloat(); // Returns: 42.0 or 42.5
```

**Returns** `float` — The float representation of the object

### `toInt()`

```php
public function toInt(): int
```

Converts the object to an integer.

This method should convert the object to an integer by truncating
any decimal portion if necessary. Use this method when you need
the whole number representation of the object.

**Example:**
```php
$number = new SomeNumberableObject();
$value = $number->toInt(); // Returns: 42 (even if original was 42.9)
```

**Returns** `int` — The integer representation of the object

### `toString()`

```php
public function toString(): string
```

Converts the object to a string.

This method should convert the object to its string representation.
Use this method when you need to display the number as text or
concatenate it with other strings.

**Example:**
```php
$number = new SomeNumberableObject();
$value = $number->toString(); // Returns: "42" or "42.5"
```

**Returns** `string` — The string representation of the object

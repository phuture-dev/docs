# Numbers

`Phuture\Coherence\Type\Numbers`

```php
class Numbers extends FluentClass implements Numberable
```

A fluent wrapper around the Numbers utility class for chainable number manipulation.

Each method delegates to the corresponding static method on `Numbers`, stores the
result internally, and returns `$this` to enable method chaining. Retrieve the final
value by calling `get()`, `toFloat()`, `toInt()`, or `toNumber()`.

Arithmetic methods (add, subtract, multiply, divide, modulus, squareRoot) use BCMath
internally and store their result as a string. Subsequent operations that require a
numeric value will cast the stored string automatically.

**Example:**
```php
use Phuture\Coherence\Type\Numbers;

$result = Numbers::from(10)
    ->add(5)
    ->multiply(2)
    ->subtract(3)
    ->round(0)
    ->get();
// 27.0
```

## Methods

### `absolute()`

```php
public function absolute(): self
```

Returns the absolute (non-negative) value of the wrapped number.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::absolute()`

### `add()`

```php
public function add(int|float $value): self
```

Adds a number to the wrapped value using BCMath for precision.

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float` | The value to add |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::add()`

### `addPercentage()`

```php
public function addPercentage(int|float $percentage): self
```

Adds a percentage of the wrapped value to itself using BCMath for precision.

| Parameter | Type | Description |
| --- | --- | --- |
| `$percentage` | `int\|float` | The percentage to add (e.g. 20 for 20%) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::addPercentage()`

### `ceil()`

```php
public function ceil(): self
```

Returns the smallest integer value greater than or equal to the wrapped number.

**Returns** `self` — Returns the current instance for method chaining (stores result as a BCMath string)

**See also**

- `Transformer::ceil()`

### `celsiusToFahrenheit()`

```php
public function celsiusToFahrenheit(): self
```

Converts the wrapped Celsius temperature to Fahrenheit using BCMath for precision.

The conversion result replaces the wrapped value, enabling further
chaining with arithmetic or formatting methods.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::celsiusToFahrenheit()`

### `celsiusToRankine()`

```php
public function celsiusToRankine(): self
```

Converts the wrapped Celsius temperature to Rankine using BCMath for precision.

The conversion result replaces the wrapped value, enabling further
chaining with arithmetic or formatting methods.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::celsiusToRankine()`

### `clamp()`

```php
public function clamp(int|float $min, int|float $max): self
```

Restricts the wrapped number to be within the given minimum and maximum bounds.

| Parameter | Type | Description |
| --- | --- | --- |
| `$min` | `int\|float` | The lower bound |
| `$max` | `int\|float` | The upper bound |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::clamp()`

### `compare()`

```php
public function compare(int|float $value): int
```

Compares the wrapped number with another and returns their relative order.

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float` | The value to compare against |

**Returns** `int` — -1 when wrapped < $value, 0 when equal, 1 when wrapped > $value

**See also**

- `Transformer::compare()`

### `convert()`

```php
public function convert(Unit $from, Unit $to): self
```

Converts the wrapped value from one unit of measurement to another.

The conversion result replaces the wrapped value, enabling further
chaining with arithmetic or formatting methods.

**Example:**
```php
use Phuture\Coherence\Enum\Unit;
use Phuture\Coherence\Type\Numbers;

$fahrenheit = Numbers::from(100)
    ->convert(Unit::Celsius, Unit::Fahrenheit)
    ->get();
// '212.0000000000'

$miles = Numbers::from(5)
    ->convert(Unit::Kilometer, Unit::Mile)
    ->round(2)
    ->get();
// '3.1100000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$from` | `Unit` | The source unit to convert from |
| `$to` | `Unit` | The target unit to convert to |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::convert()` — For the static conversion method
- `\Phuture\Coherence\Enum\Unit` — For all available unit cases

### `divide()`

```php
public function divide(int|float $value): self
```

Divides the wrapped number by another using BCMath for precision.

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float` | The divisor (must not be zero) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::divide()`

### `fahrenheitToCelsius()`

```php
public function fahrenheitToCelsius(): self
```

Converts the wrapped Fahrenheit temperature to Celsius using BCMath for precision.

The conversion result replaces the wrapped value, enabling further
chaining with arithmetic or formatting methods.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::fahrenheitToCelsius()`

### `fileSize()`

```php
public function fileSize(int $precision = 0, ByteBase $base = ByteBase::Binary): string
```

Converts the wrapped byte count into a human-readable file size string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$precision` | `int` | The number of decimal places to show (default: 0) |
| `$base` | `\Phuture\Coherence\Enum\ByteBase` | The base for unit conversion — Binary (1024) or Decimal (1000) (default: ByteBase::Binary) |

**Returns** `string` — The human-readable file size string

**See also**

- `Transformer::fileSize()`
- `\Phuture\Coherence\Enum\ByteBase`

### `floor()`

```php
public function floor(): self
```

Returns the largest integer value less than or equal to the wrapped number.

**Returns** `self` — Returns the current instance for method chaining (stores result as a BCMath string)

**See also**

- `Transformer::floor()`

### `forHumans()`

```php
public function forHumans(int $precision = 1): string
```

Converts the wrapped number into a human-readable string with unit names.

| Parameter | Type | Description |
| --- | --- | --- |
| `$precision` | `int` | The number of decimal places to keep (default: 1) |

**Returns** `string` — The human-readable number string

**See also**

- `Transformer::forHumans()`

### `format()`

```php
public function format(?int $precision = null): string
```

Formats the wrapped number with grouped thousands and a specified precision.

| Parameter | Type | Description |
| --- | --- | --- |
| `$precision` | `int\|null` | The number of decimal places (default: null — preserve original) |

**Returns** `string` — The formatted number string

**See also**

- `Transformer::format()`

### `formatPercentage()`

```php
public function formatPercentage(int $precision = 1, int $multiplicand = 100): string
```

Converts the wrapped number into a human-readable percentage string.

| Parameter | Type | Description |
| --- | --- | --- |
| `$precision` | `int` | The number of decimal places (default: 1) |
| `$multiplicand` | `int` | The value to multiply by before formatting (default: 100) |

**Returns** `string` — The formatted percentage string

**See also**

- `Transformer::formatPercentage()`

### `max()`

```php
public function max(int|float $value): self
```

Returns the higher of the wrapped number and another.

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float` | The value to compare against |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::max()`

### `min()`

```php
public function min(int|float $value): self
```

Returns the lower of the wrapped number and another.

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float` | The value to compare against |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::min()`

### `modulus()`

```php
public function modulus(int|float $value): self
```

Computes the modulus of the wrapped number divided by another using BCMath.

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float` | The divisor (must not be zero) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::modulus()`

### `multiply()`

```php
public function multiply(int|float $value): self
```

Multiplies the wrapped number by another using BCMath for precision.

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float` | The value to multiply by |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::multiply()`

### `opposite()`

```php
public function opposite(): self
```

Returns the arithmetic opposite (negation) of the wrapped number.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::opposite()`

### `ordinal()`

```php
public function ordinal(): string
```

Converts the wrapped integer to its ordinal string representation.

**Returns** `string` — The ordinal string with the appropriate suffix

**See also**

- `Transformer::ordinal()`

### `percentage()`

```php
public function percentage(int|float $percentage): self
```

Calculates the given percentage of the wrapped value using BCMath for precision.

Replaces the wrapped value with the computed percentage amount.

| Parameter | Type | Description |
| --- | --- | --- |
| `$percentage` | `int\|float` | The percentage to calculate (e.g. 20 for 20%) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::percentage()`

### `rankineToCelsius()`

```php
public function rankineToCelsius(): self
```

Converts the wrapped Rankine temperature to Celsius using BCMath for precision.

The conversion result replaces the wrapped value, enabling further
chaining with arithmetic or formatting methods.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::rankineToCelsius()`

### `round()`

```php
public function round(int $precision = 0, RoundingMode $mode = RoundingMode::HalfAwayFromZero): self
```

Rounds the wrapped number to the specified precision using the given rounding mode.

| Parameter | Type | Description |
| --- | --- | --- |
| `$precision` | `int` | The number of decimal places (default: 0) |
| `$mode` | `RoundingMode` | The rounding mode (default: \RoundingMode::HalfAwayFromZero) |

**Returns** `self` — Returns the current instance for method chaining (stores result as a BCMath string)

**See also**

- `Transformer::round()`

### `squareRoot()`

```php
public function squareRoot(int $scale = 10): self
```

Computes the square root of the wrapped number using BCMath for precision.

| Parameter | Type | Description |
| --- | --- | --- |
| `$scale` | `int` | The number of decimal places in the result (default: 10) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::squareRoot()`

### `subtract()`

```php
public function subtract(int|float $value): self
```

Subtracts a number from the wrapped value using BCMath for precision.

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float` | The value to subtract |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::subtract()`

### `subtractPercentage()`

```php
public function subtractPercentage(int|float $percentage): self
```

Subtracts a percentage of the wrapped value from itself using BCMath for precision.

| Parameter | Type | Description |
| --- | --- | --- |
| `$percentage` | `int\|float` | The percentage to subtract (e.g. 20 for 20%) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::subtractPercentage()`

### `toFloat()`

```php
public function toFloat(): float
```

Converts the wrapped value to a float.

**Returns** `float` — The float representation of the wrapped value

### `toInt()`

```php
public function toInt(): int
```

Converts the wrapped value to an integer.

**Returns** `int` — The integer representation of the wrapped value

### `toNumber()`

```php
public function toNumber(): self
```

Normalizes the wrapped value to its most appropriate numeric type.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `Transformer::toNumber()`

### `toString()`

```php
public function toString(): string
```

Converts the wrapped value to a string.

**Returns** `string` — The string representation of the wrapped value

# Numbers

`Phuture\Coherence\Numbers`

```php
class Numbers extends StaticClass
```

Comprehensive number manipulation utility class with precise arithmetic and formatting.

This utility class provides a complete toolkit for working with numbers, combining
float comparison with epsilon tolerance, precise arithmetic via BCMath, number
formatting, and human-readable output into a single cohesive interface.

Key features:

- **BCMath Comparison**: Compare numbers with BCMath precision to avoid floating-point errors
- **Precise Arithmetic**: Add, subtract, multiply, divide, and compute modulus using BCMath strings
- **State & Validation**: Check if a number is zero, positive, negative, or an integer
- **Clamping & Limits**: Constrain numbers to a minimum, maximum, or both
- **Formatting**: Abbreviate numbers, format file sizes, percentages, ordinals, and more
- **Human-Readable Output**: Convert numbers into readable strings like "1.5K" or "2.5 MB"
- **Unit Conversion**: Convert between units of temperature, distance, mass, volume, time, area, speed,
  pressure, energy, power, force, electric potential, electric current, and luminosity
- **Statistical Functions**: Compute mean, median, mode, variance, standard deviation, and percentiles

## Constants

### `DEFAULT_SCALE`

```php
const DEFAULT_SCALE = 10
```

Number of decimal places used by default in BCMath arithmetic operations.

### `MAX_PRECISION`

```php
const MAX_PRECISION = 100
```

Highest number of decimal places accepted by the formatting methods.

Formatting with an arbitrarily large precision allocates a string of that length, which
exhausts memory long before the result is useful. PHP 8.6 rejects out-of-range values
outright, so the limit is enforced here to keep the behaviour identical across versions.

## Methods

### `abbreviate()`

```php
public static function abbreviate(int|float|string $number, int $precision = 1): string
```

Abbreviates a number using suffix letters (K, M, B, T).

Converts large numbers into shorter human-readable strings by dividing
the value and appending a suffix. For example, 1500 becomes "1.5K".

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::abbreviate(1500); // '1.5K'
Numbers::abbreviate(1000000); // '1.0M'
Numbers::abbreviate(123456789); // '123.5M'
Numbers::abbreviate(1500, 2); // '1.50K'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to abbreviate |
| `$precision` | `int` | The number of decimal places to keep (default: 1) |

**Returns** `string` — The abbreviated number string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$precision` is negative or greater than MAX_PRECISION

**See also**

- `\Phuture\Coherence\Numbers::forHumans()`

### `absolute()`

```php
public static function absolute(int|float|string $number): int|float|string
```

Returns the absolute (non-negative) value of a number.

Converts negative numbers to their positive equivalent. Positive numbers
and zero are returned unchanged.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::absolute(-5); // 5
Numbers::absolute(3.14); // 3.14
Numbers::absolute(0); // 0
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to convert |

**Returns** `int|float|string` — The non-negative value of the number

**See also**

- `\Phuture\Coherence\Numbers::opposite()`

### `add()`

```php
public static function add(int|float|string $left, int|float|string $right): string
```

Adds two numbers using BCMath for precision and returns the result as a string.

Both values are converted to strings and added using BCMath to avoid
floating-point precision loss.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::add(0.1, 0.2); // '0.3000000000'
Numbers::add(100, 200); // '300.0000000000'
Numbers::add(1.5, 2.5); // '4.0000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$left` | `int\|float\|string` | The first addend |
| `$right` | `int\|float\|string` | The second addend |

**Returns** `string` — The sum as a string

**See also**

- `\Phuture\Coherence\Numbers::subtract()`

### `addPercentage()`

```php
public static function addPercentage(int|float|string $number, int|float|string $percentage): string
```

Increases a number by a given percentage and returns the result.

Computes the percentage of the number and adds it to the original value
using BCMath to avoid floating-point precision loss.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::addPercentage(100, 20); // '120.0000000000'  (100 + 20%)
Numbers::addPercentage(50, 10); // '55.0000000000'   (50 + 10%)
Numbers::addPercentage(200, 5); // '210.0000000000'  (200 + 5%)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The base number |
| `$percentage` | `int\|float\|string` | The percentage to add (e.g. 20 means 20%) |

**Returns** `string` — The increased value as a BCMath string

**See also**

- `\Phuture\Coherence\Numbers::subtractPercentage()`
- `\Phuture\Coherence\Numbers::percentage()`
- `\Phuture\Coherence\Numbers::add()`

### `areEqual()`

```php
public static function areEqual(int|float|string $left, int|float|string $right): bool
```

Determines whether two numbers are equal at BCMath precision.

Compares two numbers using BCMath at the default scale.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::areEqual(0.1 + 0.2, 0.3); // true
Numbers::areEqual(10, 10.0); // true
Numbers::areEqual(1.0, 2.0); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$left` | `int\|float\|string` | The first value to compare |
| `$right` | `int\|float\|string` | The second value to compare |

**Returns** `bool` — True when both values are equal at BCMath precision

**Throws**

- `\Phuture\Coherence\Exception\LogicException` — When either value is NAN

**See also**

- `\Phuture\Coherence\Numbers::compare()`
- `\Phuture\Coherence\Numbers::isZero()`

### `ceil()`

```php
public static function ceil(int|float|string $number): string
```

Returns the smallest integer value greater than or equal to the given number.

Rounds up to the nearest integer using BCMath for precision. For example,
3.2 becomes "4" and -3.2 becomes "-3".

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::ceil(3.2); // '4'
Numbers::ceil(-1.1); // '-1'
Numbers::ceil(5.0); // '5'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to round up |

**Returns** `string` — The smallest integer greater than or equal to the number as a BCMath string

**See also**

- `\Phuture\Coherence\Numbers::floor()`
- `\Phuture\Coherence\Numbers::round()`

### `celsiusToFahrenheit()`

```php
public static function celsiusToFahrenheit(string $value): string
```

Converts a Celsius temperature to Fahrenheit using BCMath for precision.

Applies the formula: Fahrenheit = (Celsius / 5) * 9 + 32

**Example:**
```php
use Phuture\Coherence\Numbers;

$fahrenheit = Numbers::celsiusToFahrenheit('100');

// Returns: '212.0000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `string` | The Celsius temperature as a numeric string |

**Returns** `string` — The Fahrenheit temperature as a BCMath string

**See also**

- `\Phuture\Coherence\Numbers::fahrenheitToCelsius()`

### `celsiusToRankine()`

```php
public static function celsiusToRankine(string $value): string
```

Converts a Celsius temperature to Rankine using BCMath for precision.

Applies the formula: Rankine = (Celsius / 5) * 9 + 491.67

**Example:**
```php
use Phuture\Coherence\Numbers;

$rankine = Numbers::celsiusToRankine('100');

// Returns: '671.6700000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `string` | The Celsius temperature as a numeric string |

**Returns** `string` — The Rankine temperature as a BCMath string

**See also**

- `\Phuture\Coherence\Numbers::rankineToCelsius()`

### `clamp()`

```php
public static function clamp(int|float|string $number, int|float|string $min, int|float|string $max): int|float|string
```

Restricts a number to be within the given minimum and maximum bounds.

When the number is below `$min`, `$min` is returned. When the number is
above `$max`, `$max` is returned. Otherwise, the number itself is returned.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::clamp(5, 1, 10); // 5
Numbers::clamp(-3, 0, 100); // 0
Numbers::clamp(150, 0, 100); // 100
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to restrict |
| `$min` | `int\|float\|string` | The lower bound |
| `$max` | `int\|float\|string` | The upper bound |

**Returns** `int|float|string` — The clamped value

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When min is greater than max

**See also**

- `\Phuture\Coherence\Numbers::max()`
- `\Phuture\Coherence\Numbers::min()`

### `compare()`

```php
public static function compare(int|float|string $left, int|float|string $right): int
```

Compares two numbers and returns their relative order.

Returns -1 when `$a` is less than `$b`, 0 when they are equal at BCMath precision, and 1
when `$a` is greater than `$b`. Suitable for use with sorting functions like `usort()`.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::compare(1.0, 2.0); // -1
Numbers::compare(2.0, 1.0); // 1
Numbers::compare(1.0, 1.0); // 0

$arr = [3, 1, 2];
usort($arr, [Numbers::class, 'compare']); // [1, 2, 3]
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$left` | `int\|float\|string` | The first value to compare |
| `$right` | `int\|float\|string` | The second value to compare |

**Returns** `int` — -1 when $left < $right, 0 when equal, 1 when $left > $right

**Throws**

- `\Phuture\Coherence\Exception\LogicException` — When either value is NAN

**See also**

- `\Phuture\Coherence\Numbers::areEqual()`

### `conversionUnits()`

```php
public static function conversionUnits(): array
```

Returns all supported units grouped by measurement category.

This method returns an associative array where each key is a measurement
category name and the value is an array of Unit enum cases that belong to
that category. This is useful for building user interfaces that let users
pick units from a dropdown.

**Example:**
```php
use Phuture\Coherence\Numbers;

$units = Numbers::conversionUnits();
// Returns: [
//     'temperature' => [Unit::Celsius, Unit::Fahrenheit, ...],
//     'distance' => [Unit::Meter, Unit::Millimeter, ...],
//     ...
// ]
```

**Returns** `array` — An associative array mapping category names to arrays of Unit enum cases

**See also**

- `\Phuture\Coherence\Enum\Unit`
- `\Phuture\Coherence\Numbers::convert()`

### `convert()`

```php
public static function convert(int|float|string $value, Unit $from, Unit $to): string
```

Converts a numeric value from one unit of measurement to another.

This method converts a value between units within the same measurement category.
Both units must belong to the same category (for example, both must be temperature
units or both must be distance units). The conversion uses BCMath for precise
decimal arithmetic.

Use the Unit enum to specify the source and target units. See
\Phuture\Coherence\Enum\Unit for the full list of supported units organized
by category (temperature, distance, mass, volume, time, area, speed, pressure,
energy, power, force, electric potential, electric current, and luminous intensity).

**Example:**
```php
use Phuture\Coherence\Enum\Unit;
use Phuture\Coherence\Numbers;

Numbers::convert(100, Unit::Celsius, Unit::Fahrenheit);
// Returns: '212.0000000000'

Numbers::convert(1, Unit::Kilometer, Unit::Mile);
// Returns: '0.6213711922'

Numbers::convert(1, Unit::GallonUs, Unit::Liter);
// Returns: '3.7854117840'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float\|string` | The numeric value to convert |
| `$from` | `Unit` | The source unit to convert from |
| `$to` | `Unit` | The target unit to convert to |

**Returns** `string` — The converted value as a BCMath string with up to 10 decimal places

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When units belong to different categories

**See also**

- `\Phuture\Coherence\Enum\Unit` — For all available unit cases
- `\Phuture\Coherence\Type\Numbers::convert()` — For the fluent equivalent

### `divide()`

```php
public static function divide(int|float|string $dividend, int|float|string $divisor): string
```

Divides the first number by the second using BCMath for precision.

Both values are converted to strings and divided using BCMath to avoid
floating-point precision loss. Throws when dividing by zero.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::divide(10, 3); // '3.3333333333'
Numbers::divide(100, 4); // '25.0000000000'
Numbers::divide(1, 3); // '0.3333333333'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$dividend` | `int\|float\|string` | The dividend |
| `$divisor` | `int\|float\|string` | The divisor (must not be zero) |

**Returns** `string` — The quotient as a string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the divisor is zero

**See also**

- `\Phuture\Coherence\Numbers::multiply()`

### `fahrenheitToCelsius()`

```php
public static function fahrenheitToCelsius(string $value): string
```

Converts a Fahrenheit temperature to Celsius using BCMath for precision.

Applies the formula: Celsius = (Fahrenheit - 32) * 5 / 9

**Example:**
```php
use Phuture\Coherence\Numbers;

$celsius = Numbers::fahrenheitToCelsius('212');

// Returns: '100.0000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `string` | The Fahrenheit temperature as a numeric string |

**Returns** `string` — The Celsius temperature as a BCMath string

**See also**

- `\Phuture\Coherence\Numbers::celsiusToFahrenheit()`

### `fileSize()`

```php
public static function fileSize(int|float|string $bytes, int $precision = 0, ByteBase $base = ByteBase::Binary): string
```

Converts a byte count into a human-readable file size string.

Expresses the byte count using the largest appropriate unit (B, KB, MB, GB, TB, PB).
Uses base 1024 by default (binary prefixes). Use base 1000 for decimal prefixes.

**Example:**
```php
use Phuture\Coherence\Numbers;
use Phuture\Coherence\Enum\ByteBase;

Numbers::fileSize(500); // '500 B'
Numbers::fileSize(1024); // '1 KB'
Numbers::fileSize(1048576); // '1 MB'
Numbers::fileSize(1073741824); // '1 GB'
Numbers::fileSize(1500, 2); // '1.46 KB'
Numbers::fileSize(1000, 0, ByteBase::Decimal); // '1 KB'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$bytes` | `int\|float\|string` | The file size in bytes |
| `$precision` | `int` | The number of decimal places to show (default: 0) |
| `$base` | `\Phuture\Coherence\Enum\ByteBase` | The base for unit conversion — Binary (1024) or Decimal (1000) (default: ByteBase::Binary) |

**Returns** `string` — The human-readable file size string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$precision` is negative or greater than MAX_PRECISION

**See also**

- `\Phuture\Coherence\Numbers::forHumans()`
- `\Phuture\Coherence\Enum\ByteBase`

### `floor()`

```php
public static function floor(int|float|string $number): string
```

Returns the largest integer value less than or equal to the given number.

Rounds down to the nearest integer using BCMath for precision. For example,
3.8 becomes "3" and -3.8 becomes "-4".

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::floor(3.8); // '3'
Numbers::floor(-1.1); // '-2'
Numbers::floor(5.0); // '5'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to round down |

**Returns** `string` — The largest integer less than or equal to the number as a BCMath string

**See also**

- `\Phuture\Coherence\Numbers::ceil()`
- `\Phuture\Coherence\Numbers::round()`

### `forHumans()`

```php
public static function forHumans(int|float|string $number, int $precision = 1): string
```

Converts a number into a human-readable string with unit suffixes.

Similar to `abbreviate()` but uses full unit names instead of suffix
letters. For example, 1500 becomes "1.5 thousand".

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::forHumans(1500); // '1.5 thousand'
Numbers::forHumans(1000000); // '1.0 million'
Numbers::forHumans(1234, 2); // '1.23 thousand'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to format |
| `$precision` | `int` | The number of decimal places to keep (default: 1) |

**Returns** `string` — The human-readable number string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$precision` is negative or greater than MAX_PRECISION

**See also**

- `\Phuture\Coherence\Numbers::abbreviate()`

### `format()`

```php
public static function format(int|float|string $number, ?int $precision = null): string
```

Formats a number with grouped thousands and a specified number of decimal places.

Wraps PHP's `number_format()` to produce locale-independent formatted strings.
When `$precision` is null, the original precision of the number is preserved.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::format(1234567.8912, 2); // '1,234,567.89'
Numbers::format(1234567, 0); // '1,234,567'
Numbers::format(1234.5678, 4); // '1,234.5678'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to format |
| `$precision` | `int\|null` | The number of decimal places (default: null — preserve original) |

**Returns** `string` — The formatted number string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$precision` is explicitly given and is negative or greater than MAX_PRECISION

**See also**

- `\Phuture\Coherence\Numbers::formatPercentage()`
- `\Phuture\Coherence\Numbers::abbreviate()`

### `formatPercentage()`

```php
public static function formatPercentage(int|float|string $number, int $precision = 1, int $multiplicand = 100): string
```

Converts a number into a human-readable percentage string.

Multiplies the number by the given multiplicand (default 100) and appends
the percent sign. Useful for displaying ratios as percentages.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::formatPercentage(0.75); // '75.0%'
Numbers::formatPercentage(0.75, 2); // '75.00%'
Numbers::formatPercentage(1.5, 1); // '150.0%'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to convert to a percentage |
| `$precision` | `int` | The number of decimal places (default: 1) |
| `$multiplicand` | `int` | The value to multiply by before formatting (default: 100) |

**Returns** `string` — The formatted percentage string with a percent sign

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When `$precision` is negative or greater than MAX_PRECISION

**See also**

- `\Phuture\Coherence\Numbers::format()`
- `\Phuture\Coherence\Numbers::percentage()`
- `\Phuture\Coherence\Numbers::addPercentage()`
- `\Phuture\Coherence\Numbers::subtractPercentage()`

### `isFloat()`

```php
public static function isFloat(int|float|string $value): bool
```

Determines whether a value is a floating-point number (has a fractional part).

Returns true when the value is a finite float that is not a whole number.
Integer values, infinity, and NAN return false. This is the logical
inverse of `isInteger()` for finite numeric values.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::isFloat(3.14); // true
Numbers::isFloat(0.5); // true
Numbers::isFloat(5); // false
Numbers::isFloat(5.0); // false
Numbers::isFloat(INF); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float\|string` | The value to check |

**Returns** `bool` — True when the value is a float with a fractional part

**See also**

- `\Phuture\Coherence\Numbers::isInteger()`

### `isGreaterThan()`

```php
public static function isGreaterThan(int|float|string $left, int|float|string $right): bool
```

Determines whether a number is greater than another at BCMath precision.

Returns true when `$left` is strictly greater than `$right`.

Throws a `\Phuture\Coherence\Exception\LogicException` when either value is `NAN`.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::isGreaterThan(10.0, 5.0); // true
Numbers::isGreaterThan(5.0, 10.0); // false
Numbers::isGreaterThan(10.0, 10.0); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$left` | `int\|float\|string` | The value to test |
| `$right` | `int\|float\|string` | The value to compare against |

**Returns** `bool` — True when $left is strictly greater than $right

**Throws**

- `\Phuture\Coherence\Exception\LogicException` — When either value is NAN

**See also**

- `\Phuture\Coherence\Numbers::isGreaterThanOrEqualTo()`
- `\Phuture\Coherence\Numbers::isLessThan()`

### `isGreaterThanOrEqualTo()`

```php
public static function isGreaterThanOrEqualTo(int|float|string $left, int|float|string $right): bool
```

Determines whether a number is greater than or equal to another at BCMath precision.

Returns true when `$left` is greater than or equal to `$right`.

Throws a `\Phuture\Coherence\Exception\LogicException` when either value is `NAN`.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::isGreaterThanOrEqualTo(10.0, 5.0); // true
Numbers::isGreaterThanOrEqualTo(10.0, 10.0); // true
Numbers::isGreaterThanOrEqualTo(5.0, 10.0); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$left` | `int\|float\|string` | The value to test |
| `$right` | `int\|float\|string` | The value to compare against |

**Returns** `bool` — True when $left is greater than or equal to $right

**Throws**

- `\Phuture\Coherence\Exception\LogicException` — When either value is NAN

**See also**

- `\Phuture\Coherence\Numbers::isGreaterThan()`
- `\Phuture\Coherence\Numbers::isLessThanOrEqualTo()`

### `isInteger()`

```php
public static function isInteger(int|float|string $value): bool
```

Determines whether a number is an integer (has no fractional part).

Returns true when the value is a whole number with no decimal component.
Returns false for infinity and NAN.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::isInteger(5); // true
Numbers::isInteger(5.0); // true
Numbers::isInteger(-3.0); // true
Numbers::isInteger(3.14); // false
Numbers::isInteger(INF); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float\|string` | The value to check |

**Returns** `bool` — True when the value has no fractional part

**See also**

- `\Phuture\Coherence\Numbers::isZero()`

### `isLessThan()`

```php
public static function isLessThan(int|float|string $left, int|float|string $right): bool
```

Determines whether a number is less than another at BCMath precision.

Returns true when `$left` is strictly less than `$right`.

Throws a `\Phuture\Coherence\Exception\LogicException` when either value is `NAN`.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::isLessThan(5.0, 10.0); // true
Numbers::isLessThan(10.0, 5.0); // false
Numbers::isLessThan(10.0, 10.0); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$left` | `int\|float\|string` | The value to test |
| `$right` | `int\|float\|string` | The value to compare against |

**Returns** `bool` — True when $left is strictly less than $right

**Throws**

- `\Phuture\Coherence\Exception\LogicException` — When either value is NAN

**See also**

- `\Phuture\Coherence\Numbers::isLessThanOrEqualTo()`
- `\Phuture\Coherence\Numbers::isGreaterThan()`

### `isLessThanOrEqualTo()`

```php
public static function isLessThanOrEqualTo(int|float|string $left, int|float|string $right): bool
```

Determines whether a number is less than or equal to another at BCMath precision.

Returns true when `$left` is less than or equal to `$right`.

Throws a `\Phuture\Coherence\Exception\LogicException` when either value is `NAN`.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::isLessThanOrEqualTo(5.0, 10.0); // true
Numbers::isLessThanOrEqualTo(10.0, 10.0); // true
Numbers::isLessThanOrEqualTo(15.0, 10.0); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$left` | `int\|float\|string` | The value to test |
| `$right` | `int\|float\|string` | The value to compare against |

**Returns** `bool` — True when $left is less than or equal to $right

**Throws**

- `\Phuture\Coherence\Exception\LogicException` — When either value is NAN

**See also**

- `\Phuture\Coherence\Numbers::isLessThan()`
- `\Phuture\Coherence\Numbers::isGreaterThanOrEqualTo()`

### `isNegative()`

```php
public static function isNegative(int|float|string $number): bool
```

Determines whether a number is negative (strictly less than zero).

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::isNegative(-5); // true
Numbers::isNegative(-0.1); // true
Numbers::isNegative(0); // false
Numbers::isNegative(3); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to check |

**Returns** `bool` — True when the number is strictly less than zero

**See also**

- `\Phuture\Coherence\Numbers::isPositive()`
- `\Phuture\Coherence\Numbers::isZero()`

### `isNumber()`

```php
public static function isNumber(mixed $value): bool
```

Determines whether a value is a valid numeric representation.

Accepts integers, floats, and numeric strings. Returns false for
non-numeric strings, NAN, infinity, arrays, objects, and null.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::isNumber(42); // true
Numbers::isNumber(3.14); // true
Numbers::isNumber('100'); // true
Numbers::isNumber('abc'); // false
Numbers::isNumber(null); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `mixed` | The value to check |

**Returns** `bool` — True when the value is a valid number or numeric string

**See also**

- `\Phuture\Coherence\Numbers::parseInt()`
- `\Phuture\Coherence\Numbers::parseFloat()`

### `isPositive()`

```php
public static function isPositive(int|float|string $number): bool
```

Determines whether a number is positive (strictly greater than zero).

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::isPositive(5); // true
Numbers::isPositive(0.1); // true
Numbers::isPositive(0); // false
Numbers::isPositive(-3); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to check |

**Returns** `bool` — True when the number is strictly greater than zero

**See also**

- `\Phuture\Coherence\Numbers::isNegative()`
- `\Phuture\Coherence\Numbers::isZero()`

### `isZero()`

```php
public static function isZero(int|float|string $number): bool
```

Determines whether a number is equal to zero at BCMath precision.

Compares the value against zero using BCMath at the default scale.
Values smaller than the default scale are treated as zero.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::isZero(0); // true
Numbers::isZero(0.0); // true
Numbers::isZero(0.5); // false
Numbers::isZero('0.0000000000'); // true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to check |

**Returns** `bool` — True when the number is zero at BCMath precision

**See also**

- `\Phuture\Coherence\Numbers::isPositive()`
- `\Phuture\Coherence\Numbers::isNegative()`

### `max()`

```php
public static function max(int|float|string $first, int|float|string $second): int|float|string
```

Returns the higher of two numbers.

Compares two numbers and returns the one with the higher value.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::max(3, 7); // 7
Numbers::max(-5, -2); // -2
Numbers::max(3.14, 2.7); // 3.14
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$first` | `int\|float\|string` | The first number |
| `$second` | `int\|float\|string` | The second number |

**Returns** `int|float|string` — The higher of the two numbers

**See also**

- `\Phuture\Coherence\Numbers::min()`
- `\Phuture\Coherence\Numbers::clamp()`

### `mean()`

```php
public static function mean(array $values): string
```

Computes the arithmetic mean (average) of a list of numbers.

This method calculates the average by summing all values and dividing
by the count. It uses BCMath for precision, returning a string result.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::mean([2, 4, 6, 8]);
// Returns: '5.0000000000'

Numbers::mean([1.5, 2.5, 3.5]);
// Returns: '2.5000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$values` | `array` | The list of numbers to average |

**Returns** `string` — The arithmetic mean as a BCMath string

**Throws**

- `InvalidArgumentException` — When the values array is empty

**See also**

- `\Phuture\Coherence\Numbers::median()`
- `\Phuture\Coherence\Numbers::mode()`

### `median()`

```php
public static function median(array $values): string
```

Computes the median (middle value) of a list of numbers.

This method sorts the values and returns the middle value for odd-count
arrays, or the average of the two middle values for even-count arrays.
Returns a BCMath string for precision.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::median([1, 3, 5]);
// Returns: '3.0000000000'

Numbers::median([1, 3, 5, 7]);
// Returns: '4.0000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$values` | `array` | The list of numbers to find the median of |

**Returns** `string` — The median as a BCMath string

**Throws**

- `InvalidArgumentException` — When the values array is empty

**See also**

- `\Phuture\Coherence\Numbers::mean()`
- `\Phuture\Coherence\Numbers::percentile()`

### `min()`

```php
public static function min(int|float|string $first, int|float|string $second): int|float|string
```

Returns the lower of two numbers.

Compares two numbers and returns the one with the lower value.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::min(3, 7); // 3
Numbers::min(-5, -2); // -5
Numbers::min(3.14, 2.7); // 2.7
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$first` | `int\|float\|string` | The first number |
| `$second` | `int\|float\|string` | The second number |

**Returns** `int|float|string` — The lower of the two numbers

**See also**

- `\Phuture\Coherence\Numbers::max()`
- `\Phuture\Coherence\Numbers::clamp()`

### `mode()`

```php
public static function mode(array $values): array
```

Finds the mode (most frequently occurring value) of a list of numbers.

This method returns the value that appears most often. When multiple
values share the highest frequency, all of them are returned. The result
is an array of the mode values, preserving their original types.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::mode([1, 2, 2, 3, 3, 3]);
// Returns: [3]

Numbers::mode([1, 1, 2, 2, 3]);
// Returns: [1, 2]

Numbers::mode([5]);
// Returns: [5]
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$values` | `array` | The list of numbers to find the mode of |

**Returns** `array` — An array containing the most frequently occurring value(s)

**Throws**

- `InvalidArgumentException` — When the values array is empty

**See also**

- `\Phuture\Coherence\Numbers::mean()`
- `\Phuture\Coherence\Numbers::median()`

### `modulus()`

```php
public static function modulus(int|float|string $dividend, int|float|string $divisor): string
```

Computes the modulus (remainder) of dividing the first number by the second using BCMath.

Returns the remainder of `$a` divided by `$b`. Throws when the divisor is zero.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::modulus(10, 3); // '1.0000000000'
Numbers::modulus(10, 2); // '0.0000000000'
Numbers::modulus(7.5, 2); // '1.5000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$dividend` | `int\|float\|string` | The dividend |
| `$divisor` | `int\|float\|string` | The divisor (must not be zero) |

**Returns** `string` — The remainder as a string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the divisor is zero

**See also**

- `\Phuture\Coherence\Numbers::divide()`

### `multiply()`

```php
public static function multiply(int|float|string $left, int|float|string $right): string
```

Multiplies two numbers using BCMath for precision and returns the result as a string.

Both values are converted to strings and multiplied using BCMath to avoid
floating-point precision loss.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::multiply(0.1, 0.2); // '0.0200000000'
Numbers::multiply(3, 4); // '12.0000000000'
Numbers::multiply(2.5, 4.0); // '10.0000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$left` | `int\|float\|string` | The first factor |
| `$right` | `int\|float\|string` | The second factor |

**Returns** `string` — The product as a BCMath string at BCMath precision

**See also**

- `\Phuture\Coherence\Numbers::divide()`

### `of()`

```php
public static function of(int|float|string $number): Type\Numbers
```

Creates a fluent Numbers instance for chaining number operations.

This method provides a convenient entry point for building a sequence of number
operations using method chaining. Instead of calling static methods one by one,
you can chain operations together in a single readable expression.

**Example:**
```php
use Phuture\Coherence\Numbers;

$result = Numbers::of(10)
    ->add(5)
    ->multiply(2)
    ->get();

// Returns: '30.0000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The starting number to wrap in the fluent interface |

**Returns** `\Phuture\Coherence\Type\Numbers` — Returns a fluent Numbers instance for chaining

**See also**

- `\Phuture\Coherence\Type\Numbers`

### `opposite()`

```php
public static function opposite(int|float|string $number): int|float|string
```

Returns the arithmetic opposite (negation) of a number.

Flips the sign of the number: positive values become negative and
negative values become positive. Zero remains zero.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::opposite(5); // -5
Numbers::opposite(-3.2); // 3.2
Numbers::opposite(0); // 0
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to negate |

**Returns** `int|float|string` — The negated value

**See also**

- `\Phuture\Coherence\Numbers::absolute()`

### `ordinal()`

```php
public static function ordinal(int $number): string
```

Converts an integer to its ordinal string representation.

Appends the correct English ordinal suffix to the given integer.
Handles the special cases for 11th, 12th, and 13th correctly.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::ordinal(1); // '1st'
Numbers::ordinal(2); // '2nd'
Numbers::ordinal(3); // '3rd'
Numbers::ordinal(4); // '4th'
Numbers::ordinal(11); // '11th'
Numbers::ordinal(21); // '21st'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int` | The integer to convert |

**Returns** `string` — The ordinal string with the appropriate suffix

**See also**

- `\Phuture\Coherence\Numbers::spell()`

### `parseFloat()`

```php
public static function parseFloat(mixed $value): float
```

Parses a string to a float using PHP's floatval function.

Converts the given value to a floating-point number. Throws when the value
is not a valid numeric representation (non-numeric strings, null, arrays, etc.).

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::parseFloat('3.14'); // 3.14
Numbers::parseFloat('-2.5'); // -2.5
Numbers::parseFloat('abc'); // throws InvalidArgumentException
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `mixed` | The value to parse |

**Returns** `float` — The parsed float value

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the value is not numeric

**See also**

- `\Phuture\Coherence\Numbers::parseInt()`
- `\Phuture\Coherence\Numbers::isNumber()`

### `parseInt()`

```php
public static function parseInt(mixed $value): int
```

Parses a string to an integer using PHP's intval function.

Converts the given value to an integer. Throws when the value is not
a valid numeric representation (non-numeric strings, null, arrays, etc.).

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::parseInt('42'); // 42
Numbers::parseInt('-7'); // -7
Numbers::parseInt('3.9'); // 3
Numbers::parseInt('abc'); // throws InvalidArgumentException
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `mixed` | The value to parse |

**Returns** `int` — The parsed integer value

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the value is not numeric

**See also**

- `\Phuture\Coherence\Numbers::parseFloat()`
- `\Phuture\Coherence\Numbers::isNumber()`

### `percentage()`

```php
public static function percentage(int|float|string $number, int|float|string $percentage): string
```

Calculates the given percentage of a number using BCMath for precision.

Returns the raw percentage amount without adding or subtracting it from the
original value. Useful when you need the percentage value itself rather than
an adjusted total.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::percentage(200, 20); // '40.0000000000' (20% of 200)
Numbers::percentage(50, 10); // '5.0000000000' (10% of 50)
Numbers::percentage(100, 5); // '5.0000000000' (5% of 100)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The base number |
| `$percentage` | `int\|float\|string` | The percentage to calculate (e.g. 20 means 20%) |

**Returns** `string` — The percentage amount as a BCMath string

**See also**

- `\Phuture\Coherence\Numbers::addPercentage()`
- `\Phuture\Coherence\Numbers::subtractPercentage()`

### `percentile()`

```php
public static function percentile(array $values, int|float $percentile): string
```

Computes a specific percentile of a list of numbers.

This method uses linear interpolation to compute the value at a given
percentile rank. The 50th percentile is equivalent to the median.
Values are sorted internally, and the result uses BCMath for precision.

The percentile is computed using the "exclusive" method: the 0th
percentile is the minimum value and the 100th percentile is the maximum.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::percentile([1, 2, 3, 4, 5], 50);
// Returns: '3.0000000000' (the median)

Numbers::percentile([1, 2, 3, 4, 5, 6], 25);
// Returns: '2.5000000000'

Numbers::percentile([1, 2, 3, 4, 5], 0);
// Returns: '1.0000000000' (the minimum)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$values` | `array` | The list of numbers to compute the percentile for |
| `$percentile` | `int\|float` | The percentile to compute, from 0 to 100 |

**Returns** `string` — The value at the given percentile as a BCMath string

**Throws**

- `InvalidArgumentException` — When the values array is empty or percentile is out of range

**See also**

- `\Phuture\Coherence\Numbers::median()`

### `range()`

```php
public static function range(array $values): string
```

Computes the range (difference between maximum and minimum) of a list of numbers.

This method finds the difference between the largest and smallest values
in the set, giving a simple measure of data spread.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::range([3, 7, 2, 9, 5]);
// Returns: '7.0000000000'

Numbers::range([1.5, 4.5]);
// Returns: '3.0000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$values` | `array` | The list of numbers to compute the range for |

**Returns** `string` — The range (max minus min) as a BCMath string

**Throws**

- `InvalidArgumentException` — When the values array is empty

**See also**

- `\Phuture\Coherence\Numbers::standardDeviation()`

### `rankineToCelsius()`

```php
public static function rankineToCelsius(string $value): string
```

Converts a Rankine temperature to Celsius using BCMath for precision.

Applies the formula: Celsius = (Rankine - 491.67) * 5 / 9

**Example:**
```php
use Phuture\Coherence\Numbers;

$celsius = Numbers::rankineToCelsius('671.67');

// Returns: '100.0000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `string` | The Rankine temperature as a numeric string |

**Returns** `string` — The Celsius temperature as a BCMath string

**See also**

- `\Phuture\Coherence\Numbers::celsiusToRankine()`

### `round()`

```php
public static function round(int|float|string $number, int $precision = 0, RoundingMode $mode = RoundingMode::HalfAwayFromZero): string
```

Rounds a number to the specified precision using the given rounding mode.

Delegates to the polyfilled/native `bcround()` for full BCMath precision.
The result has exactly `$precision` decimal places.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::round(3.456, 2); // '3.46'
Numbers::round(3.456, 0); // '3'
Numbers::round(3.5, 0, RoundingMode::HalfTowardsZero); // '3'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to round |
| `$precision` | `int` | The number of decimal places (default: 0) |
| `$mode` | `RoundingMode` | The rounding mode (default: \RoundingMode::HalfAwayFromZero) |

**Returns** `string` — The rounded value as a BCMath string with exactly `$precision` decimal places

**See also**

- `\Phuture\Coherence\Numbers::ceil()`
- `\Phuture\Coherence\Numbers::floor()`

### `sampleStandardDeviation()`

```php
public static function sampleStandardDeviation(array $values): string
```

Computes the sample standard deviation of a list of numbers.

Sample standard deviation uses sample variance (N-1 denominator) as its
base. Use this when your data is a sample from a larger population.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::sampleStandardDeviation([2, 4, 4, 4, 5, 5, 7, 9]);
// Returns: '2.1380899353'

Numbers::sampleStandardDeviation([1, 2, 3, 4, 5]);
// Returns: '1.5811388301'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$values` | `array` | The list of numbers to compute sample standard deviation for |

**Returns** `string` — The sample standard deviation as a BCMath string

**Throws**

- `InvalidArgumentException` — When the values array has fewer than 2 elements

**See also**

- `\Phuture\Coherence\Numbers::sampleVariance()`
- `\Phuture\Coherence\Numbers::standardDeviation()`

### `sampleVariance()`

```php
public static function sampleVariance(array $values): string
```

Computes the sample variance of a list of numbers.

Sample variance is similar to population variance but divides by N-1
instead of N (Bessel's correction). Use this when your data is a sample
from a larger population to get an unbiased estimate.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::sampleVariance([2, 4, 4, 4, 5, 5, 7, 9]);
// Returns: '4.5714285714'

Numbers::sampleVariance([1, 2, 3, 4, 5]);
// Returns: '2.5000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$values` | `array` | The list of numbers to compute sample variance for |

**Returns** `string` — The sample variance as a BCMath string

**Throws**

- `InvalidArgumentException` — When the values array has fewer than 2 elements

**See also**

- `\Phuture\Coherence\Numbers::variance()`
- `\Phuture\Coherence\Numbers::sampleStandardDeviation()`

### `spell()`

```php
public static function spell(int $number): string
```

Spells out a number in English words.

Converts an integer to its English word representation. Handles negative
numbers by prefixing "negative". Returns the numeric string for numbers
outside the supported range.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::spell(0); // 'zero'
Numbers::spell(7); // 'seven'
Numbers::spell(42); // 'forty-two'
Numbers::spell(-5); // 'negative five'
Numbers::spell(100); // 'one hundred'
Numbers::spell(1000); // 'one thousand'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int` | The number to spell out |

**Returns** `string` — The English word representation of the number

**See also**

- `\Phuture\Coherence\Numbers::ordinal()`

### `squareRoot()`

```php
public static function squareRoot(int|float|string $number, int $scale = self::DEFAULT_SCALE): string
```

Computes the square root of a number using BCMath for precision.

Returns the square root as a string with the specified number of decimal places.
Throws when the number is negative.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::squareRoot(9); // '3.0000000000'
Numbers::squareRoot(2, 4); // '1.4142'
Numbers::squareRoot(0); // '0.0000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to compute the square root of (must be non-negative) |
| `$scale` | `int` | The number of decimal places in the result (default: 10) |

**Returns** `string` — The square root as a string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the number is negative

**See also**

- `\Phuture\Coherence\Numbers::multiply()`
- `\Phuture\Coherence\Numbers::round()`

### `standardDeviation()`

```php
public static function standardDeviation(array $values): string
```

Computes the population standard deviation of a list of numbers.

Standard deviation is the square root of the variance. It measures how
spread out the numbers are from the mean in the same units as the data.
Use this when your data represents an entire population.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::standardDeviation([2, 4, 4, 4, 5, 5, 7, 9]);
// Returns: '2.0000000000'

Numbers::standardDeviation([1, 2, 3, 4, 5]);
// Returns: '1.4142135624'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$values` | `array` | The list of numbers to compute standard deviation for |

**Returns** `string` — The population standard deviation as a BCMath string

**Throws**

- `InvalidArgumentException` — When the values array is empty

**See also**

- `\Phuture\Coherence\Numbers::variance()`
- `\Phuture\Coherence\Numbers::sampleStandardDeviation()`

### `subtract()`

```php
public static function subtract(int|float|string $left, int|float|string $right): string
```

Subtracts the second number from the first using BCMath for precision.

Both values are converted to strings and subtracted using BCMath to avoid
floating-point precision loss.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::subtract(10, 3); // '7.0000000000'
Numbers::subtract(5.5, 2.5); // '3.0000000000'
Numbers::subtract(1, 1); // '0.0000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$left` | `int\|float\|string` | The minuend |
| `$right` | `int\|float\|string` | The subtrahend |

**Returns** `string` — The difference as a string

**See also**

- `\Phuture\Coherence\Numbers::add()`

### `subtractPercentage()`

```php
public static function subtractPercentage(int|float|string $number, int|float|string $percentage): string
```

Decreases a number by a given percentage and returns the result.

Computes the percentage of the number and subtracts it from the original value
using BCMath to avoid floating-point precision loss.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::subtractPercentage(100, 20); // '80.0000000000'  (100 - 20%)
Numbers::subtractPercentage(50, 10); // '45.0000000000'  (50 - 10%)
Numbers::subtractPercentage(200, 5); // '190.0000000000' (200 - 5%)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The base number |
| `$percentage` | `int\|float\|string` | The percentage to subtract (e.g. 20 means 20%) |

**Returns** `string` — The decreased value as a BCMath string

**See also**

- `\Phuture\Coherence\Numbers::addPercentage()`
- `\Phuture\Coherence\Numbers::percentage()`
- `\Phuture\Coherence\Numbers::subtract()`

### `toNumber()`

```php
public static function toNumber(int|float|string|bool|array $number): string
```

Normalizes any value into a BCMath-compatible numeric string.

Each input type is handled differently:
- **array**: returns the element count as a BCMath string (e.g. `[1,2,3]` → `'3.0000000000'`)
- **bool**: returns `'1.0000000000'` for `true`, `'0.0000000000'` for `false`
- **string**: passed directly into BCMath without a float round-trip, preserving all digits up to the scale
- **int / float**: converted to a BCMath string (e.g. `42` → `'42.0000000000'`)

The result is safe to pass directly into any other BCMath method without precision loss.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::toNumber(true); // '1.0000000000'
Numbers::toNumber('3.14'); // '3.1400000000'
Numbers::toNumber([1, 2, 3]); // '3.0000000000'
Numbers::toNumber(42); // '42.0000000000'
Numbers::toNumber('0.3333333333'); // '0.3333333333'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string\|bool\|array` | The value to normalize |

**Returns** `string` — The BCMath string representation

**See also**

- `\Phuture\Coherence\Numbers::parseInt()`
- `\Phuture\Coherence\Numbers::parseFloat()`
- `\Phuture\Coherence\Numbers::isNumber()`

### `trimTrailingZeros()`

```php
public static function trimTrailingZeros(int|float|string $number): string
```

Removes trailing zeros from a numeric string representation.

Converts the number to a string and strips any trailing zeros after
the decimal point. If all decimal digits are zeros, the decimal point
itself is also removed.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::trimTrailingZeros('3.14000'); // '3.14'
Numbers::trimTrailingZeros('5.00'); // '5'
Numbers::trimTrailingZeros('100.000'); // '100'
Numbers::trimTrailingZeros(7.500); // '7.5'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number or numeric string to trim |

**Returns** `string` — The trimmed number string

**See also**

- `\Phuture\Coherence\Numbers::format()`
- `\Phuture\Coherence\Numbers::abbreviate()`

### `variance()`

```php
public static function variance(array $values): string
```

Computes the population variance of a list of numbers.

Population variance measures how far each number in the set is from the
mean squared, averaged across all values. Use this when your data
represents an entire population, not a sample.

**Example:**
```php
use Phuture\Coherence\Numbers;

Numbers::variance([2, 4, 4, 4, 5, 5, 7, 9]);
// Returns: '4.0000000000'

Numbers::variance([1, 2, 3, 4, 5]);
// Returns: '2.0000000000'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$values` | `array` | The list of numbers to compute variance for |

**Returns** `string` — The population variance as a BCMath string

**Throws**

- `InvalidArgumentException` — When the values array is empty

**See also**

- `\Phuture\Coherence\Numbers::sampleVariance()`
- `\Phuture\Coherence\Numbers::standardDeviation()`

### `assertNotNan()`

```php
private static function assertNotNan(int|float|string $value, string $label): void
```

Asserts that the given value is not NAN.

NAN cannot be meaningfully compared with any value, including itself.
This method throws a clear exception when a NAN value is detected.

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `float` | The value to check |
| `$label` | `string` | The parameter label for the error message |

**Throws**

- `\Phuture\Coherence\Exception\LogicException` — When the value is NAN

### `assertPrecision()`

```php
private static function assertPrecision(int $precision): void
```

Asserts that the given precision is within the supported range.

| Parameter | Type | Description |
| --- | --- | --- |
| `$precision` | `int` | The number of decimal places to validate |

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the precision is negative or greater than MAX_PRECISION

### `convertNumberToWords()`

```php
private static function convertNumberToWords(int $number): string
```

Converts an integer between 0 and 999,999,999 into English words.

Breaks the number into groups of three digits and converts each group
separately, combining them with the appropriate scale words (thousand,
million).

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int` | The non-negative integer to convert |

**Returns** `string` — The English word representation

### `convertTemperature()`

```php
private static function convertTemperature(int|float|string $value, Unit $from, Unit $to): string
```

Converts a temperature value from one scale to another via Celsius as the
intermediate step.

Uses a two-step conversion: first to Celsius, then from Celsius to the target
scale. This avoids needing a conversion formula for every possible pair of
temperature units. All arithmetic uses BCMath for precision.

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `int\|float\|string` | The temperature value to convert |
| `$from` | `Unit` | The source temperature unit |
| `$to` | `Unit` | The target temperature unit |

**Returns** `string` — The converted temperature as a BCMath string

**See also**

- `\Phuture\Coherence\Numbers::convert()`

### `detectPrecision()`

```php
private static function detectPrecision(int|float|string $number): int
```

Detects the number of decimal places in a numeric value.

Examines the string representation of the number to determine how many
digits follow the decimal point.

| Parameter | Type | Description |
| --- | --- | --- |
| `$number` | `int\|float\|string` | The number to inspect |

**Returns** `int` — The number of decimal places found

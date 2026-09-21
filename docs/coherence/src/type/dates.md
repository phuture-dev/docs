# Dates

`Phuture\Coherence\Type\Dates`

```php
class Dates extends FluentClass implements Dateable
```

A fluent, chainable wrapper around the Dates utility class for date and time manipulation.

Each method delegates to the corresponding static method on `\Phuture\Coherence\Dates`,
stores the resulting `DateTimeImmutable` internally, and returns `$this` to enable
method chaining. Retrieve the final `DateTimeImmutable` by calling `get()` or
`toDateTimeImmutable()`, then use the static `Dates` class for formatting or inspection.

**Example:**
```php
use Phuture\Coherence\Dates;

$result = Dates::of('2026-04-21 14:30:00')
    ->addDays(10)
    ->startOfDay()
    ->get();
// DateTimeImmutable for '2026-05-01 00:00:00'

$formatted = Dates::toDateTime(
    Dates::of('2026-12-25', 'America/New_York')
        ->addHours(9)
        ->get()
);
// '2026-12-25 09:00:00'
```

## Methods

### `addDays()`

```php
public function addDays(int $days): self
```

Adds a number of days to the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$days` | `int` | The number of days to add |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::addDays()`

### `addHours()`

```php
public function addHours(int $hours): self
```

Adds a number of hours to the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$hours` | `int` | The number of hours to add |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::addHours()`

### `addMinutes()`

```php
public function addMinutes(int $minutes): self
```

Adds a number of minutes to the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$minutes` | `int` | The number of minutes to add |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::addMinutes()`

### `addMonths()`

```php
public function addMonths(int $months): self
```

Adds a number of months to the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$months` | `int` | The number of months to add |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::addMonths()`

### `addSeconds()`

```php
public function addSeconds(int $seconds): self
```

Adds a number of seconds to the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$seconds` | `int` | The number of seconds to add |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::addSeconds()`

### `addWeeks()`

```php
public function addWeeks(int $weeks): self
```

Adds a number of weeks to the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$weeks` | `int` | The number of weeks to add |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::addWeeks()`

### `addYears()`

```php
public function addYears(int $years): self
```

Adds a number of years to the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$years` | `int` | The number of years to add |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::addYears()`

### `endOfDay()`

```php
public function endOfDay(): self
```

Moves the wrapped date/time to 23:59:59 on the same calendar day.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::endOfDay()`

### `endOfMonth()`

```php
public function endOfMonth(): self
```

Moves the wrapped date/time to the last day of the same month at 23:59:59.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::endOfMonth()`

### `endOfWeek()`

```php
public function endOfWeek(): self
```

Moves the wrapped date/time to Sunday 23:59:59 of the same ISO week.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::endOfWeek()`

### `endOfYear()`

```php
public function endOfYear(): self
```

Moves the wrapped date/time to December 31st of the same year at 23:59:59.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::endOfYear()`

### `fromRelative()`

```php
public function fromRelative(string $expression): self
```

Parses a relative date expression and replaces the wrapped date with the result.

| Parameter | Type | Description |
| --- | --- | --- |
| `$expression` | `string` | A relative date expression (e.g. '+2 days', 'in 3 hours') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::fromRelative()`

### `getTimezone()`

```php
public function getTimezone(): string
```

Returns the timezone identifier of the wrapped date/time value.

**Returns** `string` — The timezone identifier string (e.g. 'Europe/Paris')

**See also**

- `\Phuture\Coherence\Dates::getTimezone()`

### `removeDays()`

```php
public function removeDays(int $days): self
```

Removes a number of days from the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$days` | `int` | The number of days to remove (must be >= 0) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::removeDays()`

### `removeHours()`

```php
public function removeHours(int $hours): self
```

Removes a number of hours from the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$hours` | `int` | The number of hours to remove (must be >= 0) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::removeHours()`

### `removeMinutes()`

```php
public function removeMinutes(int $minutes): self
```

Removes a number of minutes from the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$minutes` | `int` | The number of minutes to remove (must be >= 0) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::removeMinutes()`

### `removeMonths()`

```php
public function removeMonths(int $months): self
```

Removes a number of months from the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$months` | `int` | The number of months to remove (must be >= 0) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::removeMonths()`

### `removeSeconds()`

```php
public function removeSeconds(int $seconds): self
```

Removes a number of seconds from the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$seconds` | `int` | The number of seconds to remove (must be >= 0) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::removeSeconds()`

### `removeWeeks()`

```php
public function removeWeeks(int $weeks): self
```

Removes a number of weeks from the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$weeks` | `int` | The number of weeks to remove (must be >= 0) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::removeWeeks()`

### `removeYears()`

```php
public function removeYears(int $years): self
```

Removes a number of years from the wrapped date/time value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$years` | `int` | The number of years to remove (must be >= 0) |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::removeYears()`

### `startOfDay()`

```php
public function startOfDay(): self
```

Moves the wrapped date/time to midnight (00:00:00) on the same calendar day.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::startOfDay()`

### `startOfMonth()`

```php
public function startOfMonth(): self
```

Moves the wrapped date/time to the first day of the same month at 00:00:00.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::startOfMonth()`

### `startOfWeek()`

```php
public function startOfWeek(): self
```

Moves the wrapped date/time to Monday 00:00:00 of the same ISO week.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::startOfWeek()`

### `startOfYear()`

```php
public function startOfYear(): self
```

Moves the wrapped date/time to January 1st of the same year at 00:00:00.

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::startOfYear()`

### `toDate()`

```php
public function toDate(): string
```

Returns the date portion of the wrapped date/time as a Y-m-d string.

**Returns** `string` — The date portion formatted as 'Y-m-d'

**See also**

- `\Phuture\Coherence\Dates::toDate()`

### `toDateTime()`

```php
public function toDateTime(): string
```

Returns the wrapped date/time as a combined date and time string.

**Returns** `string` — The date and time formatted as 'Y-m-d H:i:s'

**See also**

- `\Phuture\Coherence\Dates::toDateTime()`

### `toDateTimeImmutable()`

```php
public function toDateTimeImmutable(): DateTimeImmutable
```

Returns the wrapped DateTimeImmutable value.

**Returns** `DateTimeImmutable` — The wrapped date and time value

### `toIso8601()`

```php
public function toIso8601(): string
```

Returns the wrapped date/time formatted as an ISO 8601 string.

**Returns** `string` — The date and time formatted according to ISO 8601

**See also**

- `\Phuture\Coherence\Dates::toIso8601()`

### `toRelative()`

```php
public function toRelative(DateTimeImmutable|string|null $comparedTo = null): string
```

Returns a human-readable string describing how far the wrapped date is from a reference point.

| Parameter | Type | Description |
| --- | --- | --- |
| `$comparedTo` | `DateTimeImmutable|string|null` | The reference date/time to compare against. Pass null to use the current moment (default: null) |

**Returns** `string` — A human-readable relative time string (e.g. '2 days ago', 'in 3 hours')

**See also**

- `\Phuture\Coherence\Dates::toRelative()`

### `toRfc1036()`

```php
public function toRfc1036(): string
```

Returns the wrapped date/time formatted as an RFC 1036 string.

**Returns** `string` — The date and time formatted according to RFC 1036

**See also**

- `\Phuture\Coherence\Dates::toRfc1036()`

### `toRfc1123()`

```php
public function toRfc1123(): string
```

Returns the wrapped date/time formatted as an RFC 1123 string.

**Returns** `string` — The date and time formatted according to RFC 1123

**See also**

- `\Phuture\Coherence\Dates::toRfc1123()`

### `toRfc2822()`

```php
public function toRfc2822(): string
```

Returns the wrapped date/time formatted as an RFC 2822 string.

**Returns** `string` — The date and time formatted according to RFC 2822

**See also**

- `\Phuture\Coherence\Dates::toRfc2822()`

### `toRfc7231()`

```php
public function toRfc7231(): string
```

Returns the wrapped date/time formatted as an RFC 7231 string (IMF-fixdate).

**Returns** `string` — The date and time formatted according to RFC 7231

**See also**

- `\Phuture\Coherence\Dates::toRfc7231()`

### `toRfc822()`

```php
public function toRfc822(): string
```

Returns the wrapped date/time formatted as an RFC 822 string.

**Returns** `string` — The date and time formatted according to RFC 822

**See also**

- `\Phuture\Coherence\Dates::toRfc822()`

### `toRfc850()`

```php
public function toRfc850(): string
```

Returns the wrapped date/time formatted as an RFC 850 string.

**Returns** `string` — The date and time formatted according to RFC 850

**See also**

- `\Phuture\Coherence\Dates::toRfc850()`

### `toTime()`

```php
public function toTime(): string
```

Returns the time portion of the wrapped date/time as an H:i:s string.

**Returns** `string` — The time portion formatted as 'H:i:s'

**See also**

- `\Phuture\Coherence\Dates::toTime()`

### `toTimestamp()`

```php
public function toTimestamp(): int
```

Returns the Unix timestamp representation of a date/time value.

**Returns** `int` — The number of seconds since the Unix epoch

**See also**

- `\Phuture\Coherence\Dates::toTimestamp()`

### `toTimezone()`

```php
public function toTimezone(string $timezone): self
```

Converts the wrapped date/time to a different timezone.

| Parameter | Type | Description |
| --- | --- | --- |
| `$timezone` | `string` | A valid PHP timezone identifier (e.g. 'America/New_York') |

**Returns** `self` — Returns the current instance for method chaining

**See also**

- `\Phuture\Coherence\Dates::toTimezone()`

### `toW3c()`

```php
public function toW3c(): string
```

Returns the wrapped date/time formatted as a W3C string.

**Returns** `string` — The date and time formatted according to the W3C standard

**See also**

- `\Phuture\Coherence\Dates::toW3c()`

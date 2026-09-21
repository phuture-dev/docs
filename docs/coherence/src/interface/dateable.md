# Dateable

`Phuture\Coherence\Interface\Dateable`

```php
interface Dateable
```

Interface for objects that represent a date and can be formatted in various ways.

This interface provides a standardized set of methods for converting date values
into different string formats, immutable datetime objects, and Unix timestamps.
Any class that wraps or represents a date should implement this interface so that
consumers can reliably obtain the date in the format they need.

## Methods

### `toDate()`

```php
public function toDate(): string
```

Returns the date portion formatted as a string.

This method extracts just the date part (year, month, day) from the
implementing object and returns it as a human-readable string.

**Returns** `string` — The date formatted as a string (e.g., 'Y-m-d')

### `toDateTime()`

```php
public function toDateTime(): string
```

Returns both the date and time formatted as a single string.

This method combines the full date and time into one readable string,
including year, month, day, hours, minutes, and seconds.

**Returns** `string` — The date and time formatted as a string (e.g., 'Y-m-d H:i:s')

### `toDateTimeImmutable()`

```php
public function toDateTimeImmutable(): DateTimeImmutable
```

Returns the date and time as an immutable DateTime object.

**Returns** `DateTimeImmutable` — An immutable copy of the date and time

### `toIso8601()`

```php
public function toIso8601(): string
```

Returns the date and time in ISO 8601 format.

ISO 8601 is an international standard for representing dates and times.
This format is widely used in APIs, data exchange, and databases because
it is unambiguous and machine-readable.

**Returns** `string` — The date and time in ISO 8601 format (e.g., 'Y-m-d\TH:i:sP')

### `toRfc1036()`

```php
public function toRfc1036(): string
```

Returns the date and time in RFC 1036 format.

RFC 1036 is the standard format used in Usenet news messages (NNTP).
It produces a string with a two-digit year, like "Tue, 21 Apr 26 14:30:00 +0000".

**Returns** `string` — The date and time in RFC 1036 format

### `toRfc1123()`

```php
public function toRfc1123(): string
```

Returns the date and time in RFC 1123 format.

RFC 1123 is the standard format for HTTP date headers. It produces a string
with a four-digit year, like "Tue, 21 Apr 2026 14:30:00 +0000".

**Returns** `string` — The date and time in RFC 1123 format

### `toRfc2822()`

```php
public function toRfc2822(): string
```

Returns the date and time in RFC 2822 format.

RFC 2822 is the date format used in email messages and HTTP headers.
It produces a human-readable string like "Mon, 15 Jun 2025 14:30:00 +0000".

**Returns** `string` — The date and time in RFC 2822 format (e.g., 'D, d M Y H:i:s O')

### `toRfc7231()`

```php
public function toRfc7231(): string
```

Returns the date and time in RFC 7231 format (IMF-fixdate).

RFC 7231 is the current standard for HTTP/1.1 date headers. It produces a string
like "Tue, 21 Apr 2026 14:30:00 GMT" with "GMT" as the fixed timezone indicator.
The date is automatically converted to GMT before formatting.

**Returns** `string` — The date and time in RFC 7231 format (IMF-fixdate)

### `toRfc822()`

```php
public function toRfc822(): string
```

Returns the date and time in RFC 822 format.

RFC 822 is the original standard for date and time in email messages.
It produces a string with a two-digit year, like "Tue, 21 Apr 26 14:30:00 +0000".
For most modern use cases, RFC 2822 (four-digit year) is preferred.

**Returns** `string` — The date and time in RFC 822 format

### `toRfc850()`

```php
public function toRfc850(): string
```

Returns the date and time in RFC 850 format.

RFC 850 is used in some older systems and protocols. It produces a string
with the full day name and a two-digit year, like
"Tuesday, 21-Apr-26 14:30:00 UTC".

**Returns** `string` — The date and time in RFC 850 format

### `toTime()`

```php
public function toTime(): string
```

Returns the time portion formatted as a string.

This method extracts just the time part (hours, minutes, seconds) from the
implementing object and returns it as a human-readable string.

**Returns** `string` — The time formatted as a string (e.g., 'H:i:s')

### `toTimestamp()`

```php
public function toTimestamp(): int
```

Returns the date and time as a Unix timestamp.

A Unix timestamp is the number of seconds that have elapsed since
January 1, 1970 (the Unix epoch). This is useful for storing dates
in databases, comparing dates numerically, or passing to functions
that expect a timestamp.

**Returns** `int` — The Unix timestamp representing the date and time

### `toW3c()`

```php
public function toW3c(): string
```

Returns the date and time in W3C format.

The W3C format is a simplified subset of ISO 8601 commonly used in
HTML documents, XML schemas, and web APIs. It produces a string like
"2026-04-21T14:30:00+00:00".

**Returns** `string` — The date and time in W3C format

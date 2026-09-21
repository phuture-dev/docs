# ArgumentExtractor

`Phuture\Coherence\Support\ArgumentExtractor`

```php
trait ArgumentExtractor
```

Provides argument parsing utilities for extracting callbacks and enums from function arguments.
This trait contains helper methods for parsing variable argument lists, particularly useful
for methods that accept flexible parameter orders with optional callbacks and enum values.

It enables clean extraction of callable functions and enum values from the end of argument
arrays while preserving the remaining arguments for further processing.

These utilities are commonly used in data manipulation classes and other libraries
that need to support flexible method signatures with optional parameters that can
appear at the end of the argument list.

## Methods

### `getBoolFromArguments()`

```php
private static function getBoolFromArguments(array &$args, bool $default = false): bool
```

Extracts a trailing boolean value from the end of an arguments array.

When the last element of `$args` is a bool, it is removed from the array
and returned. When no trailing bool is present the default value is returned
and the array is left unchanged.

| Parameter | Type | Description |
| --- | --- | --- |
| `$args` | `array` | The arguments array to process, passed by reference |
| `$default` | `bool` | Value to return when no trailing bool is found (default: false) |

**Returns** `bool` — The extracted boolean, or `$default` when none was present

### `getCallbacksFromArguments()`

```php
private static function getCallbacksFromArguments(array &$args, int $limit = 2): array
```

Extracts callback functions from the end of an arguments array.

This method iterates through the provided arguments array in reverse order,
extracting callable functions from the end until it encounters a non-callable.
The extracted callbacks are removed from the original arguments array.

Note: ALL trailing callbacks at the end of the array will be removed, regardless of the limit.
The limit only affects how many callbacks are returned in the result.

| Parameter | Type | Description |
| --- | --- | --- |
| `$args` | `array` | The arguments array to process, passed by reference |
| `$limit` | `int` | Maximum number of callbacks to extract and return. Default: 2 |

**Returns** `array` — Array of extracted callback functions, maintaining original order

### `getEnumsFromArguments()`

```php
private static function getEnumsFromArguments(array &$args, string $enum, int $limit = 1): array
```

Extracts enum values from the end of an arguments array.

This method searches through the provided arguments array from the end
and extracts enum values that match the specified enum type. The extracted
enums are removed from the original arguments array.

Note: ALL trailing enums at the end of the array will be removed, regardless of the limit.
The limit only affects how many enums are returned in the result.

| Parameter | Type | Description |
| --- | --- | --- |
| `$args` | `array` | The arguments array to search through (passed by reference) |
| `$enum` | `string` | The fully qualified enum class name |
| `$limit` | `int` | Maximum number of enum values to extract and return (default: 1) |

**Returns** `array` — Array of extracted enum values in original order

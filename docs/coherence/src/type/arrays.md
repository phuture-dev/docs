# Arrays

`Phuture\Coherence\Type\Arrays`

```php
class Arrays extends FluentClass implements Arrayable, Countable, IteratorAggregate
```

A fluent, array-like wrapper that provides object-oriented array manipulation.

Each method delegates to the corresponding static method on `\Phuture\Coherence\Arrays`, stores the
result internally, and returns `$this` to enable method chaining. Retrieve the final
value by calling `get()` or invoking the object directly.

**Example:**
```php
use Phuture\Coherence\Types\Arrays;

// Create from array
$fruits = new Arrays(['apple', 'banana', 'cherry']);

// Use like an array
echo $fruits[0]; // 'apple'
$fruits[] = 'orange'; // Append

foreach ($fruits as $fruit) {
    echo $fruit;
}

// Use fluent methods
$result = $fruits->reverse()
    ->notation()
    ->toArray();
```

## Methods

### `append()`

```php
public function append(array $items): self
```

Appends key-value pairs to the array if the keys do not already exist.

| Parameter | Type | Description |
| --- | --- | --- |
| `$items` | `array` | Associative array of key-value pairs to append if the keys are absent |

**Returns** `self` — An instance of the Arrays class with the updated array

**See also**

- `\Phuture\Coherence\Arrays::append()`
- `\Phuture\Coherence\Type\Arrays::prepend()`

### `associate()`

```php
public function associate(string|int $key, string|int|null $value = null): self
```

Transforms an array into an associative array according to a specified key.

| Parameter | Type | Description |
| --- | --- | --- |
| `$key` | `string\|int` | The field to use as the associative array key |
| `$value` | `string\|int\|null` | Optional field to use as the value. If null, uses the entire item |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::associate()`

### `changeKeyCase()`

```php
public function changeKeyCase(KeyCase $case = KeyCase::Lower): self
```

Changes the case of all keys in an array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$case` | `\Phuture\Coherence\Enum\KeyCase` | The case to convert keys to — Lower or Upper (default: KeyCase::Lower) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::changeKeyCase()`
- `\Phuture\Coherence\Enum\KeyCase`

### `collapse()`

```php
public function collapse(): self
```

Collapses an array of arrays into a single array.

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::collapse()`

### `column()`

```php
public function column(int|string|null $column, int|string|null $index = null): self
```

Extracts a single column from a multi-dimensional array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$column` | `int\|string\|null` | The column name or index to extract |
| `$index` | `int\|string\|null` | Optional column to use as keys in the result (default: null) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::column()`

### `combine()`

```php
public function combine(array $values): self
```

Creates an array by pairing keys with values from two separate arrays.

| Parameter | Type | Description |
| --- | --- | --- |
| `$values` | `array` | Array of values to use |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::combine()`

### `count()`

```php
public function count(): int
```

Counts the number of elements in the array.

**Returns** `int` — The number of elements in the array

### `crossJoin()`

```php
public function crossJoin(array ...$arrays): self
```

Compute the Cartesian product of the array with the given arrays.

| Parameter | Type | Description |
| --- | --- | --- |
| `...$arrays` | `array` | Arrays to cross join with |

**Returns** `self` — An instance of the Arrays class with the transformed array

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the current array or any given array is empty

**See also**

- `\Phuture\Coherence\Arrays::crossJoin()`

### `denote()`

```php
public function denote(): self
```

Expands a flattened array with dot notation keys back into a multi-dimensional array.

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::denote()`

### `difference()`

```php
public function difference(...$arrays): self
```

Computes the difference of arrays with additional index check.

| Parameter | Type | Description |
| --- | --- | --- |
| `...$arrays` | `array` | Additional arrays to compare against |
| `$callback` | `callable` | Optional comparison function that returns <0, 0, or >0 (optional) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::difference()`

### `differenceAssoc()`

```php
public function differenceAssoc(...$arrays): self
```

Computes the difference of arrays with additional index check.

| Parameter | Type | Description |
| --- | --- | --- |
| `...$arrays` | `array` | Arrays to compare against |
| `$comparator` | `ArrayComparator` | The comparator to use with the provided callback(s) (required with callbacks) |
| `$firstCallback` | `callable` | Optional comparison function that returns <0, 0, or >0 (optional) |
| `$secondCallback` | `callable` | Optional comparison function that returns <0, 0, or >0 (optional) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::differenceAssoc()`

### `differenceKeys()`

```php
public function differenceKeys(...$arrays): self
```

Computes the difference of arrays using keys for comparison.

| Parameter | Type | Description |
| --- | --- | --- |
| `...$arrays` | `array` | Additional arrays to compare against |
| `$callback` | `callable` | Optional comparison function for keys that returns <0, 0, or >0 (optional) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::differenceKeys()`

### `fillKeys()`

```php
public function fillKeys(mixed $value): self
```

Creates an associative array using the current array as keys, all set to the same value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `mixed` | The value to assign to every key |

**Returns** `self` — An instance of the Arrays class with the transformed array

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — If the current array is empty

**See also**

- `\Phuture\Coherence\Arrays::fillKeys()`

### `filter()`

```php
public function filter(?callable $callback = null): self
```

Filters elements of an array using a callback function.

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable\|null` | The callback function to use for filtering (default: null) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::filter()`

### `flatten()`

```php
public function flatten(): self
```

Flattens a multidimensional array into a single level.

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::flatten()`

### `flip()`

```php
public function flip(): self
```

Swaps keys and values in an array.

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::flip()`

### `getIterator()`

```php
public function getIterator(): Traversable
```

Returns an iterator for the array data.

**Returns** `Traversable` — An iterator that can be used to traverse the array elements

### `grep()`

```php
public function grep(string $pattern, bool $invert = false): self
```

Filters array elements using regular expression matching.

| Parameter | Type | Description |
| --- | --- | --- |
| `$pattern` | `string` | The regular expression pattern to match |
| `$invert` | `bool` | If true, returns elements that do not match the pattern (default: false) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::grep()`

### `groupBy()`

```php
public function groupBy(callable|string $groupBy): self
```

Groups array elements by a specified key or callback function.

| Parameter | Type | Description |
| --- | --- | --- |
| `$groupBy` | `callable\|string` | The key name to group by, or a callback function |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::groupBy()`

### `insertAfter()`

```php
public function insertAfter(string|int $key, array $items): self
```

Inserts key-value pairs into the array immediately after a specified key.

| Parameter | Type | Description |
| --- | --- | --- |
| `$key` | `string\|int` | The key after which the new items will be inserted |
| `$items` | `array` | Associative array of key-value pairs to insert |

**Returns** `self` — An instance of the Arrays class with the updated array

**See also**

- `\Phuture\Coherence\Arrays::insertAfter()`
- `\Phuture\Coherence\Type\Arrays::insertBefore()`

### `insertBefore()`

```php
public function insertBefore(string|int $key, array $items): self
```

Inserts key-value pairs into the array immediately before a specified key.

| Parameter | Type | Description |
| --- | --- | --- |
| `$key` | `string\|int` | The key before which the new items will be inserted |
| `$items` | `array` | Associative array of key-value pairs to insert |

**Returns** `self` — An instance of the Arrays class with the updated array

**See also**

- `\Phuture\Coherence\Arrays::insertBefore()`
- `\Phuture\Coherence\Type\Arrays::insertAfter()`

### `intersect()`

```php
public function intersect(...$arrays): self
```

Computes the intersection of arrays with additional index check.

| Parameter | Type | Description |
| --- | --- | --- |
| `...$arrays` | `array` | Additional arrays to intersect with |
| `$callback` | `callable` | Optional comparison function that returns <0, 0, or >0 (optional) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::intersect()`

### `intersectAssoc()`

```php
public function intersectAssoc(...$arrays): self
```

Computes the intersection of arrays using keys for comparison.

| Parameter | Type | Description |
| --- | --- | --- |
| `...$arrays` | `array` | Additional arrays to intersect with |
| `$comparator` | `ArrayComparator` | The comparator to use with the provided callback(s) (required with callbacks) |
| `$firstCallback` | `callable` | Optional comparison function that returns <0, 0, or >0 (optional) |
| `$secondCallback` | `callable` | Optional comparison function that returns <0, 0, or >0 (optional) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::intersectAssoc()`

### `intersectKeys()`

```php
public function intersectKeys(...$arrays): self
```

Computes the intersection of arrays using keys for comparison.

| Parameter | Type | Description |
| --- | --- | --- |
| `...$arrays` | `array` | Additional arrays to intersect with |
| `$callback` | `callable` | Optional comparison function that returns <0, 0, or >0 (optional) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::intersectKeys()`

### `iterate()`

```php
public function iterate(callable $callback, bool $recursive = false, mixed $args = null): self
```

Applies a callback function to every element of the array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to apply to each element. The callback has the signature `function (mixed &$value, mixed $key): void` |
| `$recursive` | `bool` | Whether to recursively process nested arrays (default: false) |
| `$args` | `mixed` | Optional extra data passed as a third argument to the callback (default: null) |

**Returns** `self` — An instance of the Arrays class with the updated array

**See also**

- `\Phuture\Coherence\Arrays::iterate()`

### `keys()`

```php
public function keys(): self
```

Returns all the keys from an array.

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::keys()`

### `map()`

```php
public function map(callable $callback): self
```

Applies a callback function to all elements of the array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The callback function to apply to each element |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::map()`

### `mapKeys()`

```php
public function mapKeys(callable $callback): self
```

Applies a callback function to the keys of an array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The callback function to apply to each key |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::mapKeys()`

### `mapWithKeys()`

```php
public function mapWithKeys(callable $callback): self
```

Applies a callback function that returns key/value pairs.

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The callback function that returns key/value pairs |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::mapWithKeys()`

### `merge()`

```php
public function merge(mixed ...$arrays): self
```

Merge the array with the given arrays.

Pass `true` as the last argument to enable recursive (deep) merging.

| Parameter | Type | Description |
| --- | --- | --- |
| `...$arrays` | `mixed` | Arrays to merge with, with an optional trailing bool for recursive mode (default: false) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the current array or any given array is empty

**See also**

- `\Phuture\Coherence\Arrays::merge()`

### `normalize()`

```php
public function normalize(): self
```

Normalizes a multi-dimensional array by converting all objects to arrays.

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::normalize()`

### `notation()`

```php
public function notation(string $prefix = ''): self
```

Flattens a multi-dimensional array into dot notation.

| Parameter | Type | Description |
| --- | --- | --- |
| `$prefix` | `string` | Optional prefix to prepend to each key (default: empty string) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::notation()`

### `offsetExists()`

```php
public function offsetExists(mixed $offset): bool
```

Checks if an offset exists in the array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$offset` | `mixed` | The offset to check |

**Returns** `bool` — True if the offset exists, false otherwise

### `offsetGet()`

```php
public function offsetGet(mixed $offset): mixed
```

Returns the value at the given offset.

| Parameter | Type | Description |
| --- | --- | --- |
| `$offset` | `mixed` | The offset to retrieve |

**Returns** `mixed` — The value at the given offset, or null if it does not exist

### `offsetSet()`

```php
public function offsetSet(mixed $offset, mixed $value): void
```

Sets a value at the given offset.

| Parameter | Type | Description |
| --- | --- | --- |
| `$offset` | `mixed` | The offset to assign to, or null to append |
| `$value` | `mixed` | The value to set |

**Returns** `void`

### `offsetUnset()`

```php
public function offsetUnset(mixed $offset): void
```

Removes the value at the given offset.

| Parameter | Type | Description |
| --- | --- | --- |
| `$offset` | `mixed` | The offset to unset |

**Returns** `void`

### `only()`

```php
public function only(array $keys): self
```

Filters the array to include only specified keys.

| Parameter | Type | Description |
| --- | --- | --- |
| `$keys` | `array` | Array of keys to include in the result |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::only()`

### `pad()`

```php
public function pad(int $length, mixed $value): self
```

Pads an array to the specified length with a value.

| Parameter | Type | Description |
| --- | --- | --- |
| `$length` | `int` | The size to pad the array to |
| `$value` | `mixed` | The value to pad the array with |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::pad()`

### `partition()`

```php
public function partition(callable $callback): self
```

Splits the array into two groups based on a callback function.

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | Function that returns true for the first group, false for the second. The callback has the signature `function (mixed $value, mixed $key): bool` |

**Returns** `self` — An instance of the Arrays class whose data is `[$passed, $failed]`

**See also**

- `\Phuture\Coherence\Arrays::partition()`
- `\Phuture\Coherence\Type\Arrays::filter()`

### `prepend()`

```php
public function prepend(array $items): self
```

Prepends key-value pairs to the beginning of the array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$items` | `array` | Associative array of key-value pairs to prepend if the keys are absent |

**Returns** `self` — An instance of the Arrays class with the updated array

**See also**

- `\Phuture\Coherence\Arrays::prepend()`
- `\Phuture\Coherence\Type\Arrays::append()`

### `pull()`

```php
public function pull(): self
```

Removes the last element from the array.

**Returns** `self` — An instance of the Arrays class with the last element removed

**Throws**

- `\Phuture\Coherence\Exception\OutOfBoundsException` — If the array is empty

**See also**

- `\Phuture\Coherence\Arrays::pull()`
- `\Phuture\Coherence\Type\Arrays::push()`
- `\Phuture\Coherence\Type\Arrays::shift()`

### `push()`

```php
public function push(mixed ...$values): self
```

Adds one or more elements to the end of the array.

| Parameter | Type | Description |
| --- | --- | --- |
| `...$values` | `mixed` | One or more values to add to the end of the array |

**Returns** `self` — An instance of the Arrays class with the new elements appended

**See also**

- `\Phuture\Coherence\Arrays::push()`
- `\Phuture\Coherence\Type\Arrays::pull()`

### `remove()`

```php
public function remove(string|int|array $key): self
```

Removes a key-value pair from the array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$key` | `string\|int\|array` | The key to remove (string/int for a top-level key, array for a nested path) |

**Returns** `self` — An instance of the Arrays class with the key removed

**See also**

- `\Phuture\Coherence\Arrays::remove()`

### `rename()`

```php
public function rename(string|int|array $oldKey, string|int $newKey): self
```

Rename keys in the array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$oldKey` | `string\|int\|array` | Old key name or array of key mappings |
| `$newKey` | `string\|int` | New key name (when $oldKey is not an array) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::rename()`

### `replace()`

```php
public function replace(bool $recursive = false, array ...$replacements): self
```

Replaces elements from passed arrays into the current array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$recursive` | `bool` | Whether to perform recursive replacement (default: false) |
| `...$replacements` | `array` | Arrays containing elements to replace |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::replace()`

### `reverse()`

```php
public function reverse(bool $preserveKeys = false): self
```

Returns an array with elements in reverse order.

| Parameter | Type | Description |
| --- | --- | --- |
| `$preserveKeys` | `bool` | Whether to preserve numeric keys (default: false) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::reverse()`

### `shift()`

```php
public function shift(): self
```

Removes the first element from the array.

**Returns** `self` — An instance of the Arrays class with the first element removed

**Throws**

- `\Phuture\Coherence\Exception\OutOfBoundsException` — If the array is empty

**See also**

- `\Phuture\Coherence\Arrays::shift()`
- `\Phuture\Coherence\Type\Arrays::pull()`

### `shuffle()`

```php
public function shuffle(): self
```

Randomly rearranges the elements of the array.

**Returns** `self` — An instance of the Arrays class with elements in random order

**See also**

- `\Phuture\Coherence\Arrays::shuffle()`

### `slice()`

```php
public function slice(int $offset, ?int $length = null, bool $preserveKeys = false): self
```

Extracts a slice of the array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$offset` | `int` | The starting offset of the slice |
| `$length` | `int\|null` | The maximum length of the slice (default: null for all remaining elements) |
| `$preserveKeys` | `bool` | Whether to preserve original keys (default: false) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::slice()`

### `sort()`

```php
public function sort(bool $reverse = false, ?callable $callback = null): self
```

Sort the array values using a callback function.

| Parameter | Type | Description |
| --- | --- | --- |
| `$reverse` | `bool` | Whether to sort in reverse order |
| `$callback` | `callable\|null` | Custom comparison function |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::sort()`

### `sortAssoc()`

```php
public function sortAssoc(bool $reverse = false, ?callable $callback = null): self
```

Sort an associative array by values while maintaining key association.

| Parameter | Type | Description |
| --- | --- | --- |
| `$reverse` | `bool` | Whether to sort in reverse order |
| `$callback` | `callable\|null` | Custom comparison function |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::sortAssoc()`

### `sortBy()`

```php
public function sortBy(string|array|callable $criteria, bool $reverse = false, int $flags = 0): self
```

Sort the array by a given key or multiple keys.

| Parameter | Type | Description |
| --- | --- | --- |
| `$criteria` | `string\|array\|callable` | The key(s) to sort by, or a callback |
| `$reverse` | `bool` | Whether to sort in descending order |
| `$flags` | `int` | Sort flags for natural sorting |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::sortBy()`

### `sortKeys()`

```php
public function sortKeys(bool $reverse = false, ?callable $callback = null): self
```

Sort the array by keys.

| Parameter | Type | Description |
| --- | --- | --- |
| `$reverse` | `bool` | Whether to sort in reverse order |
| `$callback` | `callable\|null` | Custom comparison function for keys |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::sortKeys()`

### `sortNatural()`

```php
public function sortNatural(bool $case_insensitive = false): self
```

Sort the array using natural ordering algorithm.

| Parameter | Type | Description |
| --- | --- | --- |
| `$case_insensitive` | `bool` | Whether to perform case-insensitive comparison |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::sortNatural()`

### `splice()`

```php
public function splice(int $offset, ?int $length = null, mixed $replacement = []): self
```

Removes a portion of the array and optionally replaces it with new elements.

| Parameter | Type | Description |
| --- | --- | --- |
| `$offset` | `int` | The position to start removing elements (negative counts from the end) |
| `$length` | `int\|null` | Number of elements to remove (default: null removes everything from offset onward) |
| `$replacement` | `mixed` | Elements to insert at the offset position (default: empty array) |

**Returns** `self` — An instance of the Arrays class with the modified array

**See also**

- `\Phuture\Coherence\Arrays::splice()`
- `\Phuture\Coherence\Type\Arrays::slice()`

### `split()`

```php
public function split(int $length, bool $preserveKeys = false): self
```

Splits an array into chunks.

| Parameter | Type | Description |
| --- | --- | --- |
| `$length` | `int` | The maximum size of each chunk |
| `$preserveKeys` | `bool` | Whether to preserve original keys in each chunk (default: false) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::split()`

### `toArray()`

```php
public function toArray(): array
```

Converts the object to a native PHP array.

**Returns** `array` — The internal data as a native PHP array

### `toJson()`

```php
public function toJson(): string
```

Converts the object to a JSON string.

**Returns** `string` — The internal data as a JSON-encoded string

### `toObject()`

```php
public function toObject(): object
```

Converts the object to a generic PHP object.

**Returns** `object` — The internal data as a plain PHP object

### `unique()`

```php
public function unique(SortComparison $comparison = SortComparison::String): self
```

Removes duplicate values from an array.

| Parameter | Type | Description |
| --- | --- | --- |
| `$comparison` | `\Phuture\Coherence\Enum\SortComparison` | How values are compared to detect duplicates — Regular, Numeric, String or LocaleString (default: SortComparison::String) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::unique()`
- `\Phuture\Coherence\Enum\SortComparison`

### `unless()`

```php
public function unless(bool|callable $condition, callable $callback): self
```

Applies a callback when a condition is false, preserving the fluent chain regardless.

| Parameter | Type | Description |
| --- | --- | --- |
| `$condition` | `bool\|callable` | A boolean value or a callback that receives the array data and returns a bool The callback has the signature `function (array $data): bool` |
| `$callback` | `callable` | The callback to execute when the condition is false The callback has the signature `function (self $array): void` |

**Returns** `self` — The current instance for continued chaining

**See also**

- `\Phuture\Coherence\Type\Arrays::when()`

### `values()`

```php
public function values(): self
```

Returns all values from an array.

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::values()`

### `when()`

```php
public function when(bool|callable $condition, callable $callback): self
```

Applies a callback when a condition is true, preserving the fluent chain regardless.

| Parameter | Type | Description |
| --- | --- | --- |
| `$condition` | `bool\|callable` | A boolean value or a callback that receives the array data and returns a bool The callback has the signature `function (array $data): bool` |
| `$callback` | `callable` | The callback to execute when the condition is true The callback has the signature `function (self $array): void` |

**Returns** `self` — The current instance for continued chaining

**See also**

- `\Phuture\Coherence\Type\Arrays::unless()`

### `where()`

```php
public function where(callable $callback): self
```

Filters an array using a callback function.

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | Function that tests each element, returns true to keep it |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::where()`

### `whereIn()`

```php
public function whereIn(string $key, array $values): self
```

Filters an array where a key's value is in a given list of values.

| Parameter | Type | Description |
| --- | --- | --- |
| `$key` | `string` | The key to check in each array item |
| `$values` | `array` | The list of values to match against |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::whereIn()`

### `wrap()`

```php
public function wrap(string $prefix = '', string $suffix = ''): self
```

Wraps each element of an array with a prefix and suffix.

| Parameter | Type | Description |
| --- | --- | --- |
| `$prefix` | `string` | The string to prepend to each element (default: empty string) |
| `$suffix` | `string` | The string to append to each element (default: empty string) |

**Returns** `self` — An instance of the Arrays class with the transformed array

**See also**

- `\Phuture\Coherence\Arrays::wrap()`

### `zip()`

```php
public function zip(array ...$arrays): self
```

Combines the array with one or more other arrays by pairing elements at the same index.

| Parameter | Type | Description |
| --- | --- | --- |
| `...$arrays` | `array` | One or more arrays to zip with the current array |

**Returns** `self` — An instance of the Arrays class with the zipped pairs

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — If no additional arrays are provided

**See also**

- `\Phuture\Coherence\Arrays::zip()`

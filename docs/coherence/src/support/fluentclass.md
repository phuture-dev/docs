# FluentClass

`Phuture\Coherence\Support\FluentClass`

```php
abstract class FluentClass
```

Base class for creating fluent interfaces that enable method chaining for data transformations.

This class provides the foundation for implementing the fluent interface pattern, allowing for more readable,
expressive code by eliminating intermediate variables and creating a natural language-like syntax for
sequential operations on data.

The intended way to use FluentClass is by extending it and implementing your own transformation
methods. Each method should transform the internal `$data` property and return `$this` to enable chaining.

**Example:**
```php
namespace Phuture\Coherence;

use Phuture\Coherence\Class\FluentClass;

class FluentString extends FluentClass
{
    public function upper(): self
    {
        $this->data = strtoupper($this->data);
        return $this;
    }

    public function reverse(): self
    {
        $this->data = strrev($this->data);
        return $this;
    }

    public function trim(): self
    {
        $this->data = trim($this->data);
        return $this;
    }
}

$result = FluentString::from('  hello  ')
    ->trim()
    ->upper()
    ->reverse()
    ->get();
// Returns "OLLEH"
```

## Methods

### `__construct()`

```php
public function __construct(mixed $data = null)
```

Initializes the fluent class with optional data.

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `mixed` | The initial data to wrap |

### `__call()`

```php
public function __call(string $name, array $arguments): void
```

Handle calls to undefined instance methods.

| Parameter | Type | Description |
| --- | --- | --- |
| `$name` | `string` | The name of the method being called |
| `$arguments` | `array` | Enumerated array containing the parameters passed to the method |

**Throws**

- `MemberAccessException` — If the called method does not exist on the class

### `__callStatic()`

```php
public static function __callStatic(string $name, array $arguments): void
```

Handle calls to undefined static methods.

| Parameter | Type | Description |
| --- | --- | --- |
| `$name` | `string` | The name of the method being called |
| `$arguments` | `array` | Enumerated array containing the parameters passed to the method |

**Throws**

- `MemberAccessException` — If the called static method does not exist on the class

### `__invoke()`

```php
public function __invoke(): mixed
```

Invokes the object and returns the transformed data.

This magic method allows an object to be called as a function,
returning the transformed data. This provides a convenient shortcut
to access the data without explicitly calling get().

**Example:**
```php
$fluent = (new FluentString('  hello world  '))
    ->trim()
    ->upper();

echo $fluent(); // Outputs: 'HELLO WORLD'

// Equivalent to:
echo $fluent->get();
```

**Returns** `mixed` — The transformed data

### `from()`

```php
public static function from(mixed $data): static
```

Creates a new fluent instance from the given data.

This static factory method provides a convenient way to create a new
instance of the fluent class with initial data. It's the preferred
way to instantiate fluent classes.

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `mixed` | The initial data to wrap in the fluent instance |

**Returns** `static` — A new fluent instance containing the provided data

### `get()`

```php
public function get(): mixed
```

Returns the final transformed data from the fluent chain.

**Returns** `mixed` — The wrapped data after all transformations

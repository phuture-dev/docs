# SingletonClass

`Phuture\Coherence\Support\SingletonClass`

```php
abstract class SingletonClass
```

Singleton base class that ensures only one instance of a class exists throughout the application lifecycle.

This class implements the Singleton design pattern, which restricts the instantiation of a class to a single
instance and provides global access to that instance. The pattern works by:
- Making the constructor private to prevent direct instantiation
- Preventing cloning via a private __clone() method
- Preventing unserialization via __wakeup() to avoid creating duplicate instances
- Providing a static getInstance() method that returns the single instance

**Important:** Use this pattern only when strictly necessary. The Singleton pattern can make code harder to test
and maintain due to hidden dependencies and global state. In most cases, a Dependency Injection container is
the recommended approach as it provides better testability, flexibility, and follows SOLID principles.

**Example:**
```php
namespace Phuture\Coherence;

use Phuture\Coherence\Class\SingletonClass;

class MyService extends SingletonClass
{
    public function doSomething() { ... }
}

$service = MyService::getInstance();
```

## Methods

### `__construct()`

```php
protected function __construct()
```

Class is singleton and cannot be instantiated directly.

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

### `__clone()`

```php
private function __clone()
```

Class is singleton and cannot be cloned.

### `__serialize()`

```php
public function __serialize()
```

Class is singleton and cannot be serialized.

### `__unserialize()`

```php
public function __unserialize(array $data)
```

Class is singleton and cannot be unserialized.

### `getInstance()`

```php
public static function getInstance(): self
```

This static method controls the access to the singleton class.
On the first run, it creates a singleton instance and places it into a private static field.
On subsequent runs, it returns the existing instance previously stored in the static field.

**Returns** `self` — A single instance of the current class

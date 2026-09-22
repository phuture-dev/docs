# StaticClass

`Phuture\Coherence\Support\StaticClass`

```php
abstract class StaticClass
```

Static base class that prevents instantiation and enforces static-only usage.

This class is designed to be extended by classes that should only contain static methods and never be instantiated.

**Important:** Use static classes sparingly and only when strictly necessary (e.g., utility/helper functions).
Static methods can make code harder to test and maintain due to:
- Inability to mock in unit tests
- Hidden dependencies and tight coupling
- Lack of polymorphism and interface implementation

Reserve static classes for simple utility functions that have no state or dependencies.

**Example:**
```php
namespace Phuture\Coherence;

use Phuture\Coherence\Class\StaticClass;

class StringHelper extends StaticClass
{
    public static function slugify(string $text): string { ... }
}

$slug = StringHelper::slugify('Hello World');
```

## Methods

### `__construct()`

```php
private function __construct()
```

Class is static and cannot be instantiated.

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

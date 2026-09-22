# Reflector

`Phuture\Coherence\Reflector`

```php
class Reflector extends StaticClass
```

Reflection utility class for inspecting classes, methods, properties, and functions.

This class provides a convenient wrapper around PHP's built-in reflection API,
making it easier to inspect classes, methods, properties, functions, and enums
without dealing directly with reflection objects and exceptions.

Key features:

- **Class Inspection**: Check class existence, get class names, namespaces, and parent classes
- **Method Inspection**: Check method existence, visibility, and list all methods
- **Property Inspection**: Check property existence, visibility, and list all properties
- **Function Inspection**: Get function arity and parameter names
- **Alias Management**: Create class and function aliases at runtime
- **Trait Inspection**: List traits used by a class
- **Direct Reflection**: Get raw reflection objects for classes, enums,
  functions, methods, properties, and parameters

## Methods

### `alias()`

```php
public static function alias(object|string $class, string $alias): bool
```

Creates an alias for an existing class.

This method registers a new name for an existing class, allowing the class
to be referenced by either its original name or the alias. The alias becomes
available globally and can be used for instantiation, type hints, and instanceof checks.

**Example:**
```php
use Phuture\Coherence\Reflector;

Reflector::alias(\DateTime::class, 'DT');

$date = new DT();
// The DateTime class is now also accessible as DT
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to create an alias for, either as a class name string or an instance |
| `$alias` | `string` | The new alias name for the class |

**Returns** `bool` — Returns true if the alias was created successfully

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the alias already exists or the class does not exist

### `aliasFunction()`

```php
public static function aliasFunction(string $function, string $alias): bool
```

Creates an alias for an existing function.

This method creates a new function that forwards all arguments to the original
function. The alias behaves identically to the original function for all inputs.

**Example:**
```php
use Phuture\Coherence\Reflector;

Reflector::aliasFunction('strlen', 'str_len');

$length = str_len('hello');
// Returns: 5 (same as strlen)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$function` | `string` | The name of the existing function to alias |
| `$alias` | `string` | The new alias name for the function |

**Returns** `bool` — Returns true if the alias was created successfully

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the alias already exists or the function does not exist

**See also**

- `\Phuture\Coherence\Reflector::alias()`

### `arity()`

```php
public static function arity(callable $method): int
```

Returns the number of parameters a callable accepts.

This method inspects a callable (closure, function, method, or invokable object)
and returns the total number of declared parameters, including optional ones.

**Example:**
```php
use Phuture\Coherence\Reflector;

$arity = Reflector::arity(fn ($a, $b, $c = null) => $a + $b);
// Returns: 3

$arity = Reflector::arity('strlen');
// Returns: 1
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$method` | `callable` | The callable to inspect (closure, function name, method array, or invokable object) |

**Returns** `int` — The number of declared parameters

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the callable cannot be reflected

**See also**

- `\Phuture\Coherence\Reflector::parameters()` — For getting the names of a callable's parameters

### `basename()`

```php
public static function basename(object|string $class): string
```

Returns the short name of a class without its namespace.

This method extracts the class name without the namespace prefix,
equivalent to calling (new ReflectionClass($class))->getShortName()
but with built-in validation.

**Example:**
```php
use Phuture\Coherence\Reflector;

$name = Reflector::basename(\DateTimeImmutable::class);
// Returns: 'DateTimeImmutable'

$name = Reflector::basename(new \DateTime());
// Returns: 'DateTime'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to get the short name for, either as a class name string or an instance |

**Returns** `string` — The short class name without namespace

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::name()` — For getting the fully qualified class name
- `\Phuture\Coherence\Reflector::namespace()` — For getting only the namespace portion

### `hasMethod()`

```php
public static function hasMethod(object|string $class, string $method): bool
```

Checks if a class has a specific method.

This method determines whether the given class defines or inherits
a method with the specified name, regardless of its visibility.

**Example:**
```php
use Phuture\Coherence\Reflector;

$hasMethod = Reflector::hasMethod(\DateTime::class, 'format');
// Returns: true

$hasMethod = Reflector::hasMethod(\DateTime::class, 'nonExistent');
// Returns: false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to check, either as a class name string or an instance |
| `$method` | `string` | The method name to look for |

**Returns** `bool` — Returns true if the method exists on the class, false otherwise

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::methods()` — For listing all methods of a class
- `\Phuture\Coherence\Reflector::methodVisibility()` — For getting the visibility of a method

### `hasProperty()`

```php
public static function hasProperty(object|string $class, string $property): bool
```

Checks if a class has a specific property.

This method determines whether the given class defines or inherits
a property with the specified name, regardless of its visibility.

**Example:**
```php
use Phuture\Coherence\Reflector;

class User {
    public string $name = '';
}

$hasProp = Reflector::hasProperty(User::class, 'name');
// Returns: true

$hasProp = Reflector::hasProperty(User::class, 'email');
// Returns: false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to check, either as a class name string or an instance |
| `$property` | `string` | The property name to look for |

**Returns** `bool` — Returns true if the property exists on the class, false otherwise

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::properties()` — For listing all properties of a class
- `\Phuture\Coherence\Reflector::propertyVisibility()` — For getting the visibility of a property

### `isClass()`

```php
public static function isClass(string $class): bool
```

Checks if a string refers to an existing class.

This method verifies that the given string is a valid, existing class name.
It returns false for interfaces, traits, and non-existent classes.

**Example:**
```php
use Phuture\Coherence\Reflector;

Reflector::isClass(\DateTime::class);
// Returns: true

Reflector::isClass('NonExistentClass');
// Returns: false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `string` | The class name to check |

**Returns** `bool` — Returns true if the string refers to an existing class, false otherwise

### `isMethodPrivate()`

```php
public static function isMethodPrivate(object|string $class, string $method): bool
```

Checks if a method is private.

This method inspects the given method on a class and returns true
if it is declared as private.

**Example:**
```php
use Phuture\Coherence\Reflector;

class Service {
    private function internalProcess(): void {}
}

Reflector::isMethodPrivate(Service::class, 'internalProcess');
// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to inspect, either as a class name string or an instance |
| `$method` | `string` | The method name to check |

**Returns** `bool` — Returns true if the method is private, false otherwise

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class or method cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::methodVisibility()` — For getting the raw visibility string
- `\Phuture\Coherence\Reflector::isMethodPublic()` — For checking if a method is public
- `\Phuture\Coherence\Reflector::isMethodProtected()` — For checking if a method is protected

### `isMethodProtected()`

```php
public static function isMethodProtected(object|string $class, string $method): bool
```

Checks if a method is protected.

This method inspects the given method on a class and returns true
if it is declared as protected.

**Example:**
```php
use Phuture\Coherence\Reflector;

class Repository {
    protected function findRaw(): array { return []; }
}

Reflector::isMethodProtected(Repository::class, 'findRaw');
// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to inspect, either as a class name string or an instance |
| `$method` | `string` | The method name to check |

**Returns** `bool` — Returns true if the method is protected, false otherwise

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class or method cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::methodVisibility()` — For getting the raw visibility string
- `\Phuture\Coherence\Reflector::isMethodPublic()` — For checking if a method is public
- `\Phuture\Coherence\Reflector::isMethodPrivate()` — For checking if a method is private

### `isMethodPublic()`

```php
public static function isMethodPublic(object|string $class, string $method): bool
```

Checks if a method is public.

This method inspects the given method on a class and returns true
if it is declared as public.

**Example:**
```php
use Phuture\Coherence\Reflector;

Reflector::isMethodPublic(\DateTime::class, 'format');
// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to inspect, either as a class name string or an instance |
| `$method` | `string` | The method name to check |

**Returns** `bool` — Returns true if the method is public, false otherwise

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class or method cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::methodVisibility()` — For getting the raw visibility string
- `\Phuture\Coherence\Reflector::isMethodPrivate()` — For checking if a method is private
- `\Phuture\Coherence\Reflector::isMethodProtected()` — For checking if a method is protected

### `isPropertyPrivate()`

```php
public static function isPropertyPrivate(object|string $class, string $property): bool
```

Checks if a property is private.

This method inspects the given property on a class and returns true
if it is declared as private.

**Example:**
```php
use Phuture\Coherence\Reflector;

class User {
    private string $password = '';
}

Reflector::isPropertyPrivate(User::class, 'password');
// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to inspect, either as a class name string or an instance |
| `$property` | `string` | The property name to check |

**Returns** `bool` — Returns true if the property is private, false otherwise

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class or property cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::propertyVisibility()` — For getting the raw visibility string
- `\Phuture\Coherence\Reflector::isPropertyPublic()` — For checking if a property is public
- `\Phuture\Coherence\Reflector::isPropertyProtected()` — For checking if a property is protected

### `isPropertyProtected()`

```php
public static function isPropertyProtected(object|string $class, string $property): bool
```

Checks if a property is protected.

This method inspects the given property on a class and returns true
if it is declared as protected.

**Example:**
```php
use Phuture\Coherence\Reflector;

class Model {
    protected array $attributes = [];
}

Reflector::isPropertyProtected(Model::class, 'attributes');
// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to inspect, either as a class name string or an instance |
| `$property` | `string` | The property name to check |

**Returns** `bool` — Returns true if the property is protected, false otherwise

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class or property cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::propertyVisibility()` — For getting the raw visibility string
- `\Phuture\Coherence\Reflector::isPropertyPublic()` — For checking if a property is public
- `\Phuture\Coherence\Reflector::isPropertyPrivate()` — For checking if a property is private

### `isPropertyPublic()`

```php
public static function isPropertyPublic(object|string $class, string $property): bool
```

Checks if a property is public.

This method inspects the given property on a class and returns true
if it is declared as public.

**Example:**
```php
use Phuture\Coherence\Reflector;

class User {
    public string $name = '';
}

Reflector::isPropertyPublic(User::class, 'name');
// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to inspect, either as a class name string or an instance |
| `$property` | `string` | The property name to check |

**Returns** `bool` — Returns true if the property is public, false otherwise

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class or property cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::propertyVisibility()` — For getting the raw visibility string
- `\Phuture\Coherence\Reflector::isPropertyPrivate()` — For checking if a property is private
- `\Phuture\Coherence\Reflector::isPropertyProtected()` — For checking if a property is protected

### `methods()`

```php
public static function methods(object|string $class): array
```

Returns all method names defined on a class.

This method returns an array of all public method names available on the
given class, including inherited methods.

**Example:**
```php
use Phuture\Coherence\Reflector;

$methods = Reflector::methods(\DateTime::class);
// Returns: ['__construct', '__wakeup', '__set_state', ... , 'format', 'modify', ...]
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to inspect, either as a class name string or an instance |

**Returns** `array` — An array of method names available on the class

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::hasMethod()` — For checking if a specific method exists
- `\Phuture\Coherence\Reflector::methodVisibility()` — For getting the visibility of a method

### `methodVisibility()`

```php
public static function methodVisibility(object|string $class, string $method): string
```

Returns the visibility level of a method as a string.

This method inspects the given method on a class and returns its
visibility level as one of: 'public', 'protected', or 'private'.

**Example:**
```php
use Phuture\Coherence\Reflector;

class Service {
    public function handle(): void {}
    protected function validate(): void {}
    private function execute(): void {}
}

Reflector::methodVisibility(Service::class, 'handle');
// Returns: 'public'

Reflector::methodVisibility(Service::class, 'validate');
// Returns: 'protected'

Reflector::methodVisibility(Service::class, 'execute');
// Returns: 'private'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to inspect, either as a class name string or an instance |
| `$method` | `string` | The method name to check |

**Returns** `string` — The visibility as 'public', 'protected', or 'private'

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class or method cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::isMethodPublic()` — For checking if a method is public
- `\Phuture\Coherence\Reflector::isMethodProtected()` — For checking if a method is protected
- `\Phuture\Coherence\Reflector::isMethodPrivate()` — For checking if a method is private

### `name()`

```php
public static function name(object|string $class): string
```

Returns the fully qualified class name of an object.

This method returns the complete class name including its namespace,
extracted from an object instance.

**Example:**
```php
use Phuture\Coherence\Reflector;

$name = Reflector::name(new \DateTimeImmutable());
// Returns: 'DateTimeImmutable'

$name = Reflector::name(new \Phuture\Coherence\Reflector());
// Throws InvalidArgumentException (Reflector extends StaticClass and cannot be instantiated)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to get the name for, either as a class name string or an instance |

**Returns** `string` — The fully qualified class name of the object

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::basename()` — For getting only the short class name
- `\Phuture\Coherence\Reflector::namespace()` — For getting only the namespace portion

### `namespace()`

```php
public static function namespace(object|string $class): string
```

Returns the namespace of a class.

This method extracts only the namespace portion of a fully qualified
class name, without the class name itself.

**Example:**
```php
use Phuture\Coherence\Reflector;

$ns = Reflector::namespace(\Phuture\Coherence\Hash::class);
// Returns: 'Phuture\Coherence'

$ns = Reflector::namespace(\DateTime::class);
// Returns: '' (empty string, since DateTime is in the global namespace)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to get the namespace for, either as a class name string or an instance |

**Returns** `string` — The namespace of the class, or an empty string for global namespace classes

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::basename()` — For getting only the short class name
- `\Phuture\Coherence\Reflector::name()` — For getting the fully qualified class name

### `parameters()`

```php
public static function parameters(callable $method): array
```

Returns the parameter names of a callable.

This method inspects a callable and returns an array containing the names
of all declared parameters, in their declaration order.

**Example:**
```php
use Phuture\Coherence\Reflector;

$params = Reflector::parameters(fn ($name, $age, $active = true) => null);
// Returns: ['name', 'age', 'active']

$params = Reflector::parameters('strlen');
// Returns: ['string']
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$method` | `callable` | The callable to inspect (closure, function name, method array, or invokable object) |

**Returns** `array` — An array of parameter names in declaration order

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the callable cannot be reflected

**See also**

- `\Phuture\Coherence\Reflector::arity()` — For getting the number of parameters

### `parent()`

```php
public static function parent(object|string $class): string
```

Returns the fully qualified class name of the parent class.

This method inspects the given class and returns the name of its
direct parent class.

**Example:**
```php
use Phuture\Coherence\Reflector;

$parent = Reflector::parent(\DateTimeImmutable::class);
// Returns: 'DateTime'

$parent = Reflector::parent(\stdClass::class);
// Throws InvalidArgumentException (stdClass has no parent)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to get the parent for, either as a class name string or an instance |

**Returns** `string` — The fully qualified class name of the parent class

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous or has no parent

**See also**

- `\Phuture\Coherence\Reflector::name()` — For getting the class name itself

### `properties()`

```php
public static function properties(object|string $class): array
```

Returns all public property names with their default values for a class.

This method returns an associative array where keys are property names
and values are their default values. Only public properties are included.

**Example:**
```php
use Phuture\Coherence\Reflector;

class User {
    public string $name = 'John';
    public int $age = 30;
    private string $secret = '';
}

$props = Reflector::properties(User::class);
// Returns: ['name' => 'John', 'age' => 30]
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to inspect, either as a class name string or an instance |

**Returns** `array` — An associative array of property names and their default values

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::hasProperty()` — For checking if a specific property exists
- `\Phuture\Coherence\Reflector::propertyVisibility()` — For getting the visibility of a property

### `propertyVisibility()`

```php
public static function propertyVisibility(object|string $class, string $property): string
```

Returns the visibility level of a property as a string.

This method inspects the given property on a class and returns its
visibility level as one of: 'public', 'protected', or 'private'.

**Example:**
```php
use Phuture\Coherence\Reflector;

class Config {
    public string $name = '';
    protected array $settings = [];
    private string $apiKey = '';
}

Reflector::propertyVisibility(Config::class, 'name');
// Returns: 'public'

Reflector::propertyVisibility(Config::class, 'settings');
// Returns: 'protected'

Reflector::propertyVisibility(Config::class, 'apiKey');
// Returns: 'private'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to inspect, either as a class name string or an instance |
| `$property` | `string` | The property name to check |

**Returns** `string` — The visibility as 'public', 'protected', or 'private'

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class or property cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

**See also**

- `\Phuture\Coherence\Reflector::isPropertyPublic()` — For checking if a property is public
- `\Phuture\Coherence\Reflector::isPropertyProtected()` — For checking if a property is protected
- `\Phuture\Coherence\Reflector::isPropertyPrivate()` — For checking if a property is private

### `reflectClass()`

```php
public static function reflectClass(object|string $class): ReflectionClass
```

Creates a ReflectionClass instance for the given class.

This method returns a native PHP ReflectionClass object, providing
full access to the reflection API for detailed class inspection.

**Example:**
```php
use Phuture\Coherence\Reflector;

$reflection = Reflector::reflectClass(\DateTime::class);
$methods = $reflection->getMethods();
$properties = $reflection->getProperties();
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to reflect, either as a class name string or an instance |

**Returns** `ReflectionClass` — A reflection object for the given class

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class cannot be reflected

**See also**

- `\Phuture\Coherence\Reflector::reflectMethod()` — For reflecting a specific method
- `\Phuture\Coherence\Reflector::reflectProperty()` — For reflecting a specific property

### `reflectEnum()`

```php
public static function reflectEnum(object|string $enum): ReflectionEnum
```

Creates a ReflectionEnum instance for the given enum.

This method returns a native PHP ReflectionEnum object for inspecting
enum types, including their cases and backing values.

**Example:**
```php
use Phuture\Coherence\Reflector;

enum Color: string {
    case Red = 'red';
    case Blue = 'blue';
}

$reflection = Reflector::reflectEnum(Color::class);
$cases = $reflection->getCases();
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$enum` | `object\|string` | The enum to reflect, either as a class name string or an enum instance |

**Returns** `ReflectionEnum` — A reflection object for the given enum

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the enum cannot be reflected

**See also**

- `\Phuture\Coherence\Reflector::reflectClass()` — For reflecting a regular class

### `reflectFunction()`

```php
public static function reflectFunction(callable $function): ReflectionFunction
```

Creates a ReflectionFunction instance for the given callable.

This method returns a native PHP ReflectionFunction object for inspecting
function definitions, including parameters, return types, and body.

**Example:**
```php
use Phuture\Coherence\Reflector;

$reflection = Reflector::reflectFunction('strlen');
$params = $reflection->getParameters();
$returnType = $reflection->getReturnType();
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$function` | `callable` | The function to reflect |

**Returns** `ReflectionFunction` — A reflection object for the given function

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the function cannot be reflected

**See also**

- `\Phuture\Coherence\Reflector::reflectMethod()` — For reflecting a class method
- `\Phuture\Coherence\Reflector::arity()` — For getting the parameter count

### `reflectMethod()`

```php
public static function reflectMethod(object|string $class, string $method): ReflectionMethod
```

Creates a ReflectionMethod instance for a specific method on a class.

This method returns a native PHP ReflectionMethod object for inspecting
method definitions, including visibility, parameters, return types, and body.

**Example:**
```php
use Phuture\Coherence\Reflector;

$reflection = Reflector::reflectMethod(\DateTime::class, 'format');
$isPublic = $reflection->isPublic();
$params = $reflection->getParameters();
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class containing the method, either as a class name string or an instance |
| `$method` | `string` | The method name to reflect |

**Returns** `ReflectionMethod` — A reflection object for the given method

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the method cannot be reflected

**See also**

- `\Phuture\Coherence\Reflector::reflectClass()` — For reflecting the entire class
- `\Phuture\Coherence\Reflector::methodVisibility()` — For getting method visibility

### `reflectParameter()`

```php
public static function reflectParameter(object|string $class, string $method, string $parameter): ReflectionParameter
```

Creates a ReflectionParameter instance for a specific method parameter.

This method returns a native PHP ReflectionParameter object for inspecting
parameter definitions, including type, default value, and whether it is optional.

**Example:**
```php
use Phuture\Coherence\Reflector;

$reflection = Reflector::reflectParameter(\DateTime::class, 'format', 'format');
$type = $reflection->getType();
$hasDefault = $reflection->isDefaultValueAvailable();
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class containing the method, either as a class name string or an instance |
| `$method` | `string` | The method name containing the parameter |
| `$parameter` | `string` | The parameter name to reflect |

**Returns** `ReflectionParameter` — A reflection object for the given parameter

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the parameter cannot be reflected

**See also**

- `\Phuture\Coherence\Reflector::reflectMethod()` — For reflecting the entire method
- `\Phuture\Coherence\Reflector::parameters()` — For getting all parameter names

### `reflectProperty()`

```php
public static function reflectProperty(object|string $class, string $property): ReflectionProperty
```

Creates a ReflectionProperty instance for a specific property on a class.

This method returns a native PHP ReflectionProperty object for inspecting
property definitions, including visibility, type, and default value.

**Example:**
```php
use Phuture\Coherence\Reflector;

class User {
    public string $name = '';
}

$reflection = Reflector::reflectProperty(User::class, 'name');
$type = $reflection->getType();
$isPublic = $reflection->isPublic();
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class containing the property, either as a class name string or an instance |
| `$property` | `string` | The property name to reflect |

**Returns** `ReflectionProperty` — A reflection object for the given property

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the property cannot be reflected

**See also**

- `\Phuture\Coherence\Reflector::reflectClass()` — For reflecting the entire class
- `\Phuture\Coherence\Reflector::propertyVisibility()` — For getting property visibility

### `traits()`

```php
public static function traits(object|string $class): array
```

Returns all traits used by a class.

This method returns an associative array of traits used by the given class,
where keys are the trait names and values are the trait names. Parent class
traits are not included.

**Example:**
```php
use Phuture\Coherence\Reflector;

trait HasTimestamps {
    public function touch(): void {}
}

class Model {
    use HasTimestamps;
}

$traits = Reflector::traits(Model::class);
// Returns: ['HasTimestamps' => 'HasTimestamps']
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$class` | `object\|string` | The class to inspect, either as a class name string or an instance |

**Returns** `array` — An associative array of trait names used by the class

**Throws**

- `\Phuture\Coherence\Exception\ReflectionException` — When the class cannot be reflected
- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the given class is anonymous

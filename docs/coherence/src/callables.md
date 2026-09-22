# Callables

`Phuture\Coherence\Callables`

```php
class Callables extends StaticClass
```

Comprehensive callable manipulation utility class for higher-order function composition.

This utility class provides a complete toolkit for working with functions and callables,
including function composition, currying, memoization, partial application, and execution
control mechanisms such as throttling, debouncing, retrying, and rate limiting.

Key features:

- **Composition**: Combine functions into pipelines using compose() and pipe()
- **Currying & Partial Application**: Transform multi-argument functions with curry() and partial()
- **Memoization**: Cache expensive function results with memoize() and a configurable TTL
- **Execution Control**: Throttle, debounce, defer, and rate-limit function calls
- **Error Handling**: Wrap functions with catch() and retry() for resilient execution
- **Lifecycle Hooks**: Attach before() and after() hooks to any callable
- **Introspection**: Inspect callables with arity(), isClosure(), isStatic(), and more
- **Transformation**: Flip argument order, limit argument count with unary() and binary()

## Constants

### `CACHE_TTL`

```php
const CACHE_TTL = 60 * 1000
```

Default time-to-live for cached function results in milliseconds.

This constant defines the default cache duration for the memoize() method
when no custom TTL is specified. The value is in milliseconds to provide
precise timing control.

Value: 60,000 milliseconds = 60 seconds = 1 minute

### `EXECUTION_DELAY`

```php
const EXECUTION_DELAY = 500
```

Default delay in milliseconds between function executions.

This constant is used as the default delay for both throttle() and
retry() methods when no custom delay is specified.

### `MAX_ATTEMPTS`

```php
const MAX_ATTEMPTS = 10
```

Default maximum number of retry attempts for the retry() method.

This constant defines how many times the retry mechanism will attempt
to execute a function before giving up and throwing the last exception.

## Methods

### `after()`

```php
public static function after(callable $callback, callable $after): Closure
```

Creates a function that executes a hook after the main function.

This method returns a closure that first executes the main function, then
executes an "after" function with the result and the original arguments.
The result of the after function is ignored - the main function's result
is always returned.

**Example:**
```php
use Phuture\Coherence\Callables;

$logEnd = fn($result, $operation) => echo "Finished $operation. Result: " . json_encode($result) . "\n";
$processData = fn($data) => array_map(fn($item) => $item * 2, $data);

$loggedProcess = Callables::after($processData, $logEnd);
$result = $loggedProcess([1, 2, 3]);
// Outputs: Finished processing. Result: [2,4,6]
// Returns [2, 4, 6]

// Cleanup after operations
$cleanup = fn($result, $tempFile) => unlink($tempFile);
$processWithTemp = fn($tempFile) => processFile($tempFile);
$cleanProcess = Callables::after($processWithTemp, $cleanup);

// Notifications
$notifyUser = fn($result, $userId) => sendNotification($userId, 'Task completed!');
$task = fn($userId) => performLongTask($userId);
$taskWithNotification = Callables::after($task, $notifyUser);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The main function to execute |
| `$after` | `callable` | The function to execute after the main function (receives result, then args) |

**Returns** `Closure` — A function that executes the main function, then the after hook

**See also**

- `\Phuture\Coherence\Callables::before()`
- `\Phuture\Coherence\Callables::wrap()`

### `apply()`

```php
public static function apply(callable $callback, array $args): mixed
```

Calls a function with arguments from an array.

This method executes a callable using an array of arguments. The array elements
are spread out as individual arguments to the function. This is useful when
you have arguments collected in an array that need to be passed to a function.

**Example:**
```php
use Phuture\Coherence\Callables;

$format = fn($name, $age, $city) => "$name is $age from $city";
$args = ['Alice', 30, 'New York'];
$result = Callables::apply($format, $args);
// Returns "Alice is 30 from New York"

// Database query example
$insert = fn($table, $data, $timestamp) => db_insert($table, $data, $timestamp);
$queryArgs = ['users', ['name' => 'John'], date('Y-m-d')];
Callables::apply($insert, $queryArgs);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to call |
| `$args` | `array` | The array of arguments to spread into the function |

**Returns** `mixed` — The return value of the called function

**See also**

- `\Phuture\Coherence\Callables::call()`
- `\Phuture\Coherence\Callables::spread()`

### `before()`

```php
public static function before(callable $callback, callable $before): Closure
```

Creates a function that executes a hook before the main function.

This method returns a closure that first executes a "before" function with
the provided arguments, then executes the main function with the same arguments.
The before function's return value is ignored - only the main function's
result is returned.

**Example:**
```php
use Phuture\Coherence\Callables;

$logStart = fn($operation, $data) => echo "Starting $operation with " . json_encode($data) . "\n";
$processData = fn($operation, $data) => array_map(fn($item) => $item * 2, $data);

$loggedProcess = Callables::before($processData, $logStart);
$result = $loggedProcess('doubling', [1, 2, 3]);
// Outputs: Starting doubling with [1,2,3]
// Returns [2, 4, 6]

// Validation before processing
$validate = fn($data) => {
    if (empty($data)) throw new \InvalidArgumentException('Data cannot be empty');
};
$safeProcess = Callables::before($processData, $validate);

// Setting up context
$setupDatabase = fn($query) => db()->beginTransaction();
$runQuery = fn($query) => db()->query($query);
$transactionalQuery = Callables::before($runQuery, $setupDatabase);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The main function to execute |
| `$before` | `callable` | The function to execute before the main function |

**Returns** `Closure` — A function that executes the before hook, then the main function

**See also**

- `\Phuture\Coherence\Callables::after()`
- `\Phuture\Coherence\Callables::wrap()`

### `binary()`

```php
public static function binary(callable $callback): Closure
```

Creates a function that only uses the first two arguments.

This method returns a closure that ignores all arguments except
the first two and passes them to the original function. Useful for
creating binary functions from multi-argument functions or ensuring
only two arguments are processed.

**Example:**
```php
use Phuture\Coherence\Callables;

$concat = fn($a, $b, $c, $d) => $a . $b . $c . $d;
$firstTwo = Callables::binary($concat);

$result1 = $firstTwo('Hello', ' ', 'World', '!'); // Returns "Hello " (ignores extra args)
$result2 = $firstTwo('A', 'B'); // Returns "AB"

// Mathematical operations
$power = fn($base, $exp, $mod) => pow($base, $exp);
$simplePower = Callables::binary($power);
$result3 = $simplePower(2, 3, 1000); // Returns 8 (ignores modulus)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to call with only the first two arguments |

**Returns** `Closure` — A function that uses only the first two arguments

**See also**

- `\Phuture\Coherence\Callables::unary()`

### `bind()`

```php
public static function bind(Closure $closure, ?object $class): Closure
```

Binds a closure to a specific object context.

This method changes the `$this` context of a closure to point to a specific
object. This allows the closure to access the object's properties and methods
as if it were a method of that class.

**Example:**
```php
use Phuture\Coherence\Callables;

class User {
    public $name = 'John';
    public $age = 30;
}

$closure = fn() => return "Name: {$this->name}, Age: {$this->age}";
$user = new User();

$bound = Callables::bind($closure, $user);
$result = $bound(); // Returns "Name: John, Age: 30"

// Without binding would cause an error
$result2 = $closure(); // Error: $this is not available
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$closure` | `Closure` | The closure to bind to an object |
| `$class` | `object\|null` | The object to bind to, or null to unbind |

**Returns** `Closure` — A new closure bound to the specified object

### `call()`

```php
public static function call(callable $callback, mixed ...$args): mixed
```

Calls a function with the provided arguments.

This method executes a callable with a variable number of arguments.
It's a simple wrapper that makes function calls more consistent and
allows for better function composition patterns.

**Example:**
```php
use Phuture\Coherence\Callables;

$add = fn($a, $b) => $a + $b;
$result = Callables::call($add, 5, 3); // Returns 8

// Useful with other Callables methods
$operations = [
    fn($n) => $n * 2,
    fn($n) => $n + 1,
    fn($n) => $n ** 2
];
$results = array_map(fn($op) => Callables::call($op, 5), $operations);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to call |
| `...$args` | `mixed` | The arguments to pass to the function |

**Returns** `mixed` — The return value of the called function

**See also**

- `\Phuture\Coherence\Callables::apply()`

### `catch()`

```php
public static function catch(callable $callback, callable $handler): Closure
```

Creates a function that catches exceptions and handles them gracefully.

This method returns a closure that executes the original function and,
if it throws an exception, calls a handler function instead. The handler
receives both the exception and the original arguments.

**Example:**
```php
use Phuture\Coherence\Callables;

$divide = fn($a, $b) => $a / $b;
$safeDivide = Callables::catch($divide, function ($e, $a, $b) {
    if ($e instanceof \DivisionByZeroError) {
        return "Cannot divide by zero";
    }
    return "Error: " . $e->getMessage();
});

$result1 = $safeDivide(10, 2); // Returns 5
$result2 = $safeDivide(10, 0); // Returns "Cannot divide by zero"

// File operations with error handling
$readFile = fn($path) => file_get_contents($path);
$safeRead = Callables::catch($readFile, function ($e, $path) {
    return "Could not read file: $path";
});
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to execute with error handling |
| `$handler` | `callable` | The function to call on exception (receives exception, then args) |

**Returns** `Closure` — A function that catches exceptions and handles them

**See also**

- `\Phuture\Coherence\Callables::safe()`

### `compose()`

```php
public static function compose(callable ...$callback): Closure
```

Creates a function that applies multiple functions in right-to-left order.

This method creates a closure that applies functions from last to first.
The output of each function becomes the input to the next function.
This is useful for creating data transformation pipelines.

**Example:**
```php
use Phuture\Coherence\Callables;

$add1 = fn($x) => $x + 1;
$double = fn($x) => $x * 2;

$composed = Callables::compose($add1, $double);
$result = $composed(5);
// Returns 11 (5 * 2 + 1)

// Multiple functions
$pipeline = Callables::compose(
    fn($x) => $x + 1,
    fn($x) => $x * 2,
    fn($x) => $x - 3
);
$result = $pipeline(10);
// Returns 19 ((10 - 3) * 2 + 1)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `...$callback` | `callable` | The functions to compose, applied right-to-left |

**Returns** `Closure` — A new closure that applies all functions in composition

**See also**

- `\Phuture\Coherence\Callables::pipe()`

### `constant()`

```php
public static function constant(mixed $value): Closure
```

Creates a function that always returns the same value.

This method returns a closure that ignores any arguments and always returns
the same constant value. Useful for default values, testing, or when you
need a function that provides a fixed response.

**Example:**
```php
use Phuture\Coherence\Callables;

$always42 = Callables::constant(42);
$result1 = $always42(); // Returns 42
$result2 = $always42(1, 2, 3); // Still returns 42

// Default values
$getDefaultId = Callables::constant('default-123');
$userId = $userId ?? $getDefaultId();

// Testing with mock data
$mockApi = Callables::constant(['status' => 'success', 'data' => [1, 2, 3]]);
$response = $mockApi($request);

// Configuration constants
$getTimeout = Callables::constant(30);
$timeout = $getTimeout();
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `mixed` | The value to always return |

**Returns** `Closure` — A function that always returns the specified value

**See also**

- `\Phuture\Coherence\Callables::identity()`

### `curry()`

```php
public static function curry(callable $callback, ?int $arity = null): Closure
```

Creates a curried version of a function.

This method transforms a function into a series of functions that each accept
a single argument. When all required arguments have been provided, the original
function is executed. This enables partial application and function composition.

**Example:**
```php
use Phuture\Coherence\Callables;

$add = fn($a, $b, $c) => $a + $b + $c;
$curried = Callables::curry($add);

// Provide arguments one at a time
$add1 = $curried(1);
$add1and2 = $add1(2);
$result = $add1and2(3); // Returns 6 (1 + 2 + 3)

// Or provide multiple arguments at once
$result2 = $curried(10, 20)(5); // Returns 35 (10 + 20 + 5)

// Custom arity (number of arguments expected)
$customCurry = Callables::curry($add, 2); // Expects only 2 arguments
$result3 = $customCurry(5)(10); // Returns 15 (ignores the third parameter)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to curry |
| `$arity` | `int\|null` | The number of arguments expected (null to auto-detect) |

**Returns** `Closure` — A curried version of the function

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When unable to determine function arity automatically

**See also**

- `Reflector::arity()`

### `defer()`

```php
public static function defer(callable $callback, int $milliseconds = self::EXECUTION_DELAY): Closure
```

Creates a function that delays execution before calling the callback.

This method returns a closure that waits for the specified number of
milliseconds before executing the original function. Useful for debouncing,
creating delays in animations, or implementing retry logic with backoff.

**Example:**
```php
use Phuture\Coherence\Callables;

$sendEmail = fn($to, $message) => mail($to, 'Subject', $message);

// Delay email sending by 2 seconds
$delayedEmail = Callables::defer($sendEmail, 2000);
$result = $delayedEmail('user@example.com', 'Hello!'); // Sends after 2 seconds

// Debouncing user input
$search = fn($query) => performApiSearch($query);
$debouncedSearch = Callables::defer($search, 500);

// If user types quickly, only the last search executes
$debouncedSearch('a');
$debouncedSearch('ap');
$debouncedSearch('app'); // Only this one executes after 500ms

// Simulated slow operation for testing
$slowOperation = Callables::defer(fn() => 'Done', 1000);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to execute after delay |
| `$milliseconds` | `int` | The delay in milliseconds before execution (default: EXECUTION_DELAY) |

**Returns** `Closure` — A function that delays execution before calling the callback

**See also**

- `\Phuture\Coherence\Callables::EXECUTION_DELAY`

### `flip()`

```php
public static function flip(callable $callback): Closure
```

Creates a function that reverses the order of arguments.

This method returns a closure that calls the original function with
its arguments in reverse order. Useful for adapting functions that
expect arguments in a different order than what you have available.

**Example:**
```php
use Phuture\Coherence\Callables;

$divide = fn($a, $b) => $a / $b;
$reciprocalDivide = Callables::flip($divide);

$result1 = $divide(10, 2); // Returns 5 (10 ÷ 2)
$result2 = $reciprocalDivide(10, 2); // Returns 0.2 (2 ÷ 10)

// String formatting with reversed arguments
$format = fn($template, $value) => sprintf($template, $value);
$reverseFormat = Callables::flip($format);
$result3 = $reverseFormat('Hello %s', 'World'); // "World Hello"
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to call with reversed arguments |

**Returns** `Closure` — A function that reverses argument order

### `identity()`

```php
public static function identity(): Closure
```

Creates a function that returns its input unchanged.

This method returns a closure that simply returns whatever value it receives.
It's the identity function in mathematics - f(x) = x. Useful as a default
transformation or when you need a function that does nothing.

**Example:**
```php
use Phuture\Coherence\Callables;

$identity = Callables::identity();
$result1 = $identity(5); // Returns 5
$result2 = $identity('hello'); // Returns 'hello'
$result3 = $identity([1, 2, 3]); // Returns [1, 2, 3]

// As default transformation
$transform = $transformFunction ?? Callables::identity();
$data = array_map($transform, $items);

// In pipelines where you might want to skip a step
$process = $shouldProcess ? $actualProcessor : Callables::identity();
```

**Returns** `Closure` — A function that returns its input unchanged

**See also**

- `\Phuture\Coherence\Callables::constant()`

### `if()`

```php
public static function if(callable $condition, callable $then, ?callable $else): Closure
```

Creates a function that chooses between two callbacks based on a condition function.

This method returns a closure that evaluates a condition function with the provided
arguments. If the condition returns true, it executes the "then" callback. If false,
it executes the "else" callback. All three functions receive the same arguments.

**Example:**
```php
use Phuture\Coherence\Callables;

$isEven = fn($n) => $n % 2 === 0;
$sayEven = fn($n) => "$n is even";
$sayOdd = fn($n) => "$n is odd";

$checkNumber = Callables::ifElse($isEven, $sayEven, $sayOdd);
$result1 = $checkNumber(4); // Returns "4 is even"
$result2 = $checkNumber(5); // Returns "5 is odd"
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$condition` | `callable` | The function that determines which callback to execute |
| `$then` | `callable` | The function to execute when the condition is true |
| `$else` | `callable\|null` | The function to execute when the condition is false (optional) |

**Returns** `Closure` — A function that chooses between two callbacks based on a condition

**See also**

- `\Phuture\Coherence\Callables::when()`
- `\Phuture\Coherence\Callables::unless()`

### `isCallable()`

```php
public static function isCallable(mixed $value): bool
```

Checks if a value can be called as a function.

This method determines if the given value is callable, meaning it can be
invoked as a function. This includes functions, methods, closures, and
objects with an __invoke method.

**Example:**
```php
use Phuture\Coherence\Callables;

Callables::isCallable('strlen'); // true
Callables::isCallable([new DateTime(), 'format']); // true
Callables::isCallable(fn($x) => $x); // true
Callables::isCallable('not_a_function'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `mixed` | The value to check if it's callable |

**Returns** `bool` — True if the value is callable, false otherwise

**See also**

- `\Phuture\Coherence\Callables::isClosure()`
- `\Phuture\Coherence\Callables::isFunction()`

### `isClosure()`

```php
public static function isClosure(mixed $value): bool
```

Checks if a value is a closure.

This method determines if the given value is an instance of a Closure,
which is an anonymous function that can be stored in a variable and
passed as an argument.

**Example:**
```php
use Phuture\Coherence\Callables;

Callables::isClosure(fn($x) => $x * 2); // true
Callables::isClosure(function() { return 'hi'; }); // true
Callables::isClosure('strlen'); // false
Callables::isClosure([DateTime::class, 'format']); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `mixed` | The value to check if it's a closure |

**Returns** `bool` — True if the value is a closure, false otherwise

**See also**

- `\Phuture\Coherence\Callables::isCallable()`

### `isFunction()`

```php
public static function isFunction(mixed $value): bool
```

Checks if a value represents an existing PHP function.

This method determines if the given value is a string that matches the
name of an existing PHP function. This includes built-in functions and
user-defined functions, but not methods or class methods.

**Example:**
```php
use Phuture\Coherence\Callables;

Callables::isFunction('strlen'); // true (built-in)
Callables::isFunction('my_custom_func'); // true (if defined)
Callables::isFunction('DateTime::format'); // false (method)
Callables::isFunction(['Class', 'method']); // false (array)
Callables::isFunction(fn($x) => $x); // false (closure)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `mixed` | The value to check if it's a function name |

**Returns** `bool` — True if the value is a valid function name, false otherwise

**See also**

- `\Phuture\Coherence\Callables::isMethod()`
- `\Phuture\Coherence\Callables::isCallable()`

### `isInvokable()`

```php
public static function isInvokable(mixed $value): bool
```

Checks if an object can be invoked as a function.

This method determines if the given value is an object that has an
__invoke method, which allows the object to be called like a function.
This is useful for objects that need to behave like callables.

**Example:**
```php
use Phuture\Coherence\Callables;

class Invoker {
    public function __invoke($x) {
        return $x * 2;
    }
}

$invoker = new Invoker();
Callables::isInvokable($invoker); // true (has __invoke)
Callables::isInvokable(new DateTime()); // false (no __invoke)
Callables::isInvokable('strlen'); // false (string)
Callables::isInvokable(fn($x) => $x); // false (closure)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `mixed` | The value to check if it's an invokable object |

**Returns** `bool` — True if the value is an object with __invoke method, false otherwise

**See also**

- `\Phuture\Coherence\Callables::isCallable()`

### `isMethod()`

```php
public static function isMethod(mixed $value): bool
```

Checks if a value represents a method callable.

This method determines if the given value is an array that represents a
method call. A method callable must have exactly two elements: the first
is the object or class name, and the second is the method name as a string.

**Example:**
```php
use Phuture\Coherence\Callables;

$object = new DateTime();
Callables::isMethod([$object, 'format']); // true (instance method)
Callables::isMethod([DateTime::class, 'format']); // true (static method)
Callables::isMethod(['stdClass', 'format']); // false (method doesn't exist)
Callables::isMethod(['function_name']); // false (wrong format)
Callables::isMethod('strlen'); // false (not an array)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$value` | `mixed` | The value to check if it's a method callable |

**Returns** `bool` — True if the value is a valid method callable, false otherwise

**See also**

- `\Phuture\Coherence\Callables::isFunction()`
- `\Phuture\Coherence\Callables::isCallable()`

### `isStatic()`

```php
public static function isStatic(callable $callback): bool
```

Checks if a callable is static.

This method determines if the given callable represents a static method call.
A static callable is one that doesn't require an object instance, such as
[Class::class, 'method'] or 'Class::method'.

**Example:**
```php
use Phuture\Coherence\Callables;

Callables::isStatic([DateTime::class, 'format']); // true (static method)
Callables::isStatic('DateTime::format'); // true (static string)
Callables::isStatic([new DateTime(), 'format']); // false (instance method)
Callables::isStatic('strlen'); // true (function)
Callables::isStatic(fn($x) => $x); // false (closure)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The callable to check if it's static |

**Returns** `bool` — True if the callable is static, false otherwise

### `memoize()`

```php
public static function memoize(callable $callback, ?int $ttl = self::CACHE_TTL): Closure
```

Creates a memoized version of a function with optional time-to-live.

This method returns a closure that caches the results of function calls
for a specified time period. The cache key is based on the serialized
arguments, so identical argument sets will return cached results.

**Example:**
```php
use Phuture\Coherence\Callables;

$slowOperation = fn($x) => {
    sleep(1); // Simulate slow operation
    return $x * 2;
};

// Use default TTL from CACHE_TTL constant
$memoized = Callables::memoize($slowOperation);
$result1 = $memoized(5); // Takes 1 second, returns 10
$result2 = $memoized(5); // Instant, returns 10 (from cache)

// Custom TTL of 10 seconds
$customCache = Callables::memoize($slowOperation, 10);
$result3 = $customCache(5); // Returns 10, expires after 10 seconds

// Different arguments create separate cache entries
$result4 = $memoized(10); // Takes 1 second, returns 20

// Null TTL means cache never expires during runtime
$permanentCache = Callables::memoize($slowOperation, null);
$result5 = $permanentCache(5); // Cached until script ends
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to memoize |
| `$ttl` | `int\|null` | Time-to-live in seconds, defaults to CACHE_TTL, null for runtime permanent cache |

**Returns** `Closure` — A memoized version of the function with TTL support

**See also**

- `\Phuture\Coherence\Callables::CACHE_TTL`

### `negate()`

```php
public static function negate(callable $callback): Closure
```

Creates a function that returns the logical negation of the original result.

This method returns a closure that executes the original function and
returns the opposite boolean value. Useful for inverting conditions,
validation logic, or boolean predicates.

**Example:**
```php
use Phuture\Coherence\Callables;

$isEven = fn($n) => $n % 2 === 0;
$isOdd = Callables::negate($isEven);

$result1 = $isOdd(4); // Returns false (4 is even)
$result2 = $isOdd(5); // Returns true (5 is odd)

// Validation filtering
$isValid = fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL);
$isInvalid = Callables::negate($isValid);
$invalidEmails = array_filter($emails, $isInvalid);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to negate the result of |

**Returns** `Closure` — A function that returns the opposite boolean value

### `once()`

```php
public static function once(callable $callback): Closure
```

Creates a function that only executes once per unique callback.

This method returns a closure that executes the original function only
the first time it's called. Subsequent calls will return null without
executing the function again. The tracking is based on the callback
itself, not the arguments, so each unique function can only run once.

**Example:**
```php
use Phuture\Coherence\Callables;

$setupDatabase = fn() => echo "Setting up database\n";
$onceSetup = Callables::once($setupDatabase);

$result1 = $onceSetup(); // Outputs "Setting up database", returns null
$result2 = $onceSetup(); // Returns null (no execution)
$result3 = $onceSetup(); // Still returns null (no execution)

// Different callback, can execute once
$anotherSetup = fn() => echo "Another setup\n";
$onceAnother = Callables::once($anotherSetup);
$result4 = $onceAnother(); // Outputs "Another setup", returns null
$result5 = $onceAnother(); // Returns null (no execution)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function that should only run once |

**Returns** `Closure` — A function that executes only once per callback

### `partial()`

```php
public static function partial(callable $callback, mixed ...$args): Closure
```

Creates a new function with some arguments pre-filled from the left.

This method returns a closure that has some of the original function's
arguments already set. When called, it prepends the pre-filled arguments
to any new arguments provided.

**Example:**
```php
use Phuture\Coherence\Callables;

$subtract = fn($a, $b, $c) => $a - $b - $c;
$partialSub = Callables::partial($subtract, 10);
$result = $partialSub(2, 3);
// Returns 5 (10 - 2 - 3)

// Multiple pre-filled arguments
$add = fn($a, $b, $c, $d) => $a + $b + $c + $d;
$partialAdd = Callables::partial($add, 1, 2);
$result = $partialAdd(3, 4);
// Returns 10 (1 + 2 + 3 + 4)

// String formatting
$format = fn($prefix, $name, $suffix) => "$prefix$name$suffix";
$greet = Callables::partial($format, "Hello, ");
$result = $greet("World", "!");
// Returns "Hello, World!"
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to partially apply |
| `...$args` | `mixed` | The arguments to pre-fill from the left |

**Returns** `Closure` — A new function with left arguments pre-filled

**See also**

- `\Phuture\Coherence\Callables::partialRight()`
- `\Phuture\Coherence\Callables::curry()`

### `partialRight()`

```php
public static function partialRight(callable $callback, mixed ...$args): Closure
```

Creates a new function with some arguments pre-filled from the right.

This method returns a closure that has some of the original function's
arguments already set. When called, it appends the pre-filled arguments
to any new arguments provided.

**Example:**
```php
use Phuture\Coherence\Callables;

$subtract = fn($a, $b, $c) => $a - $b - $c;
$partialSub = Callables::partialRight($subtract, 3);
$result = $partialSub(10, 2);
// Returns 5 (10 - 2 - 3)

// Multiple pre-filled arguments
$add = fn($a, $b, $c, $d) => $a + $b + $c + $d;
$partialAdd = Callables::partialRight($add, 3, 4);
$result = $partialAdd(1, 2);
// Returns 10 (1 + 2 + 3 + 4)

// Division with fixed divisor
$divide = fn($numerator, $denominator) => $numerator / $denominator;
$halve = Callables::partialRight($divide, 2);
$result = $halve(20);
// Returns 10

$quarter = Callables::partialRight($divide, 4);
$result = $quarter(20);
// Returns 5
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to partially apply |
| `...$args` | `mixed` | The arguments to pre-fill from the right |

**Returns** `Closure` — A new function with right arguments pre-filled

**See also**

- `\Phuture\Coherence\Callables::partial()`
- `\Phuture\Coherence\Callables::curry()`

### `passthrough()`

```php
public static function passthrough(callable $callback, mixed $value): mixed
```

Executes a function with a value but returns the value unchanged.

This method is like the simple version of tap() - it immediately executes
a function with a value and returns that same value. It's useful when you
want to do something with a value right now but keep using the original value.

The main difference from tap() is:
- tap(): gives you a new function to use later
- passthrough(): does the action right now

**Example:**
```php
use Phuture\Coherence\Callables;

$save = fn($data) => file_put_contents('log.txt', $data);
$data = "Important information";

// Save the data but keep using it
$result = Callables::passthrough($data, $save);
// $result is still "Important information" (data was saved)

// Debugging without breaking the flow
$debug = fn($value) => echo "Current value: $value\n";
$name = Callables::passthrough("John", $debug);
// Outputs: Current value: John
// $name is still "John"

// Same thing with tap() would be:
$name = Callables::tap($debug)("John");
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to execute with the value |
| `$value` | `mixed` | The value to pass to the function |

**Returns** `mixed` — The original value unchanged

**See also**

- `\Phuture\Coherence\Callables::tap()`

### `pipe()`

```php
public static function pipe(callable ...$callback): Closure
```

Creates a function that applies multiple functions in left-to-right order.

This method creates a closure that applies functions from first to last.
The output of each function becomes the input to the next function.
This is useful for creating processing pipelines where order matters.

**Example:**
```php
use Phuture\Coherence\Callables;

$add1 = fn($x) => $x + 1;
$double = fn($x) => $x * 2;

$piped = Callables::pipe($add1, $double);
$result = $piped(5);
// Returns 12 ((5 + 1) * 2)

// Multiple functions
$pipeline = Callables::pipe(
    fn($x) => $x + 1,
    fn($x) => $x * 2,
    fn($x) => $x - 3
);
$result = $pipeline(10);
// Returns 19 (((10 + 1) * 2) - 3)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `...$callback` | `callable` | The functions to pipe, applied left-to-right |

**Returns** `Closure` — A new closure that applies all functions in sequence

**See also**

- `\Phuture\Coherence\Callables::compose()`

### `rateLimit()`

```php
public static function rateLimit(callable $callback, int $maxAttempts = self::MAX_ATTEMPTS, int $milliseconds = self::EXECUTION_DELAY): Closure
```

Creates a function that limits the number of calls within a time period.

This method returns a closure that tracks how many times it's been called
within a specified time period. If the limit is exceeded, it throws an
exception. Different from throttle() which just skips execution when
rate-limited.

**Example:**
```php
use Phuture\Coherence\Callables;

$apiCall = fn($endpoint) => json_decode(file_get_contents($endpoint));

// Maximum 10 calls per minute
$limitedApi = Callables::rateLimit($apiCall, 10, 60);

$result1 = $limitedApi('https://api.example.com/data'); // Executes
// After 10 calls within 60 seconds:
$result11 = $limitedApi('https://api.example.com/data'); // Throws RuntimeException

// Rate limiting per user
$userApiCalls = [];
$rateLimitPerUser = function($userId, $endpoint) use ($apiCall, &$userApiCalls) {
    $key = "user_$userId";
    $limiter = $userApiCalls[$key] ?? Callables::rateLimit($apiCall, 5, 60);
    $userApiCalls[$key] = $limiter;
    return $limiter($endpoint);
};
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to rate-limit |
| `$maxAttempts` | `int` | Maximum number of allowed calls within the time period, defaults to MAX_ATTEMPTS |
| `$milliseconds` | `int` | Delay between calls in milliseconds, defaults to EXECUTION_DELAY |

**Returns** `Closure` — A rate-limited version of the function

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the rate limit is exceeded

**See also**

- `\Phuture\Coherence\Callables::MAX_ATTEMPTS`
- `\Phuture\Coherence\Callables::EXECUTION_DELAY`

### `retry()`

```php
public static function retry(callable $callback, int $maxAttempts = self::MAX_ATTEMPTS, int $milliseconds = self::EXECUTION_DELAY): Closure
```

Creates a function that retries execution on failure.

This method returns a closure that attempts to execute the original function
multiple times if it throws an exception. Between attempts, it waits for the
specified delay. If all attempts fail, it throws the last exception.

**Example:**
```php
use Phuture\Coherence\Callables;

$unreliableApi = fn($id) => {
    static $failCount = 0;
    $failCount++;
    if ($failCount <= 2) {
        throw new \Exception("API failed");
    }
    return "Data for $id";
};

$reliableApi = Callables::retry($unreliableApi, 3, 1000);
$result = $reliableApi(123); // Succeeds after 2 retries, returns "Data for 123"

// Use default values (10 max attempts, 500ms delay)
$defaultRetry = Callables::retry($unreliableApi);

// Fast retry with no delay
$fastRetry = Callables::retry($unreliableApi, 5, 0);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to retry on failure |
| `$maxAttempts` | `int` | Maximum number of attempts, defaults to MAX_ATTEMPTS |
| `$milliseconds` | `int` | Delay between attempts in milliseconds, defaults to EXECUTION_DELAY |

**Returns** `Closure` — A retry-enabled version of the function

**See also**

- `\Phuture\Coherence\Callables::MAX_ATTEMPTS`
- `\Phuture\Coherence\Callables::EXECUTION_DELAY`

### `safe()`

```php
public static function safe(callable $callback): Closure
```

Creates a function that never throws exceptions.

This method returns a closure that always returns a two-element array:
[exception, result]. If the original function succeeds, the exception is
null and the result contains the return value. If it fails, the exception
object is returned and the result is null.

**Example:**
```php
use Phuture\Coherence\Callables;

$divide = fn($a, $b) => $a / $b;
$safeDivide = Callables::safe($divide);

$result1 = $safeDivide(10, 2);
// Returns [null, 5]

$result2 = $safeDivide(10, 0);
// Returns [DivisionByZeroError, null]

// Processing results safely
$parseJson = fn($str) => json_decode($str, true);
$safeParse = Callables::safe($parseJson);

[$error, $data] = $safeParse('{"valid": "json"}');
if ($error === null) {
    echo "Parsed: " . json_encode($data);
} else {
    echo "JSON error: " . $error->getMessage();
}
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to make exception-safe |

**Returns** `Closure` — A function that returns [exception, result] instead of throwing

**See also**

- `\Phuture\Coherence\Callables::catch()`

### `spread()`

```php
public static function spread(callable $callback): Closure
```

Creates a function that accepts an array and spreads it as arguments.

This method returns a closure that takes an array of arguments and
spreads them into individual arguments for the original function.
Useful for working with functions that expect individual arguments
when you have them collected in an array.

**Example:**
```php
use Phuture\Coherence\Callables;

$add = fn($a, $b, $c) => $a + $b + $c;
$addFromArray = Callables::spread($add);

$numbers = [1, 2, 3];
$result1 = $addFromArray($numbers); // Returns 6 (1 + 2 + 3)

// Database query parameters
$query = fn($table, $where, $orderBy) => "SELECT * FROM $table WHERE $where ORDER BY $orderBy";
$buildQuery = Callables::spread($query);
$params = ['users', 'active = 1', 'created_at DESC'];
$result2 = $buildQuery($params);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to call with spread arguments |

**Returns** `Closure` — A function that accepts an array and spreads it

**See also**

- `\Phuture\Coherence\Callables::apply()`

### `tap()`

```php
public static function tap(callable $callback): Closure
```

Creates a function that lets you "tap" into a value without changing it.

This method returns a closure that executes a function with a value but
always returns the original value unchanged. Think of it like peeking
at the value - you can look at it or do something with it, but the
value stays the same and continues on its way.

**Example:**
```php
use Phuture\Coherence\Callables;

$logger = fn($message) => echo "Log: $message\n";
$tapLogger = Callables::tap($logger);

$result = $tapLogger("Hello World");
// Outputs: Log: Hello World
// $result contains "Hello World"

// Debugging in chains
$process = fn($data) => $data * 2;
$debug = fn($value) => echo "Processing: $value\n";

$pipeline = Callables::pipe(
    $process,
    Callables::tap($debug),
    $process
);
$result = $pipeline(5); // Logs "Processing: 10", returns 20
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to execute with the value |

**Returns** `Closure` — A function that executes the function but returns the original value

**See also**

- `\Phuture\Coherence\Callables::passthrough()`

### `throttle()`

```php
public static function throttle(callable $callback, int $milliseconds = self::EXECUTION_DELAY): Closure
```

Creates a function that limits execution frequency to a minimum interval.

This method returns a closure that executes the original function only if
enough time has passed since the last execution with the same arguments.

The milliseconds parameter specifies the required minimum interval and
must always be provided.

It's useful for rate-limiting operations like API calls, animations, or
preventing excessive resource usage.

**Example:**
```php
use Phuture\Coherence\Callables;

$saveToDatabase = fn($data) => {
    echo "Saving: " . json_encode($data) . "\n";
    return true;
};

$throttled = Callables::throttle($saveToDatabase, 1000); // 1 second required

$result1 = $throttled(['id' => 1]); // Executes, returns true
$result2 = $throttled(['id' => 2]); // Returns null (only 500ms passed)
usleep(600000); // Wait 600ms (total 1100ms)
$result3 = $throttled(['id' => 3]); // Executes again, returns true

// Different argument sets have separate timers
$result4 = $throttled(['key' => 'A']); // Executes, separate 1s timer for 'A'
$result5 = $throttled(['key' => 'A']); // Returns null (too soon for 'A')
$result6 = $throttled(['key' => 'B']); // Executes, separate 1s timer for 'B'

// Common throttle intervals:
$slowApi = Callables::throttle($apiCall, 5000); // 5 seconds
$uiUpdate = Callables::throttle($refreshUI, 100); // 100ms for smooth UI
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to rate-limit |
| `$milliseconds` | `int` | Minimum time between executions in milliseconds, defaults to Callables::EXECUTION_DELAY |

**Returns** `Closure` — A throttled version of the function

**See also**

- `\Phuture\Coherence\Callables::EXECUTION_DELAY`

### `time()`

```php
public static function time(callable $callback, mixed ...$args): array
```

Measures the execution time of a function.

This method executes a callback and measures how long it takes to run.
It returns an array containing both the function's result and the execution
time in milliseconds. Useful for performance testing and optimization.

**Example:**
```php
use Phuture\Coherence\Callables;

$slowFunction = fn($n) => {
    usleep(100000); // Sleep for 100ms
    return $n * 2;
};

$result = Callables::time($slowFunction);
// Returns ['result' => 10, 'time' => ~100.5]
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to time |
| `...$args` | `mixed` | The arguments to pass to the function |

**Returns** `array` — An array with 'result' (function output) and 'time' (milliseconds)

### `toCallable()`

```php
public static function toCallable(Closure $callback): callable|array|string
```

Converts a closure to its underlying callable representation.

This method extracts the actual callable from a closure. If the closure
wraps a method call, it returns an array with the object and method name.
For simple closures, it returns the closure itself.

**Example:**
```php
use Phuture\Coherence\Callables;

// Simple closure
$closure = fn($x) => $x * 2;
$result = Callables::toCallable($closure);
// Returns the closure itself

// Method closure
$object = new DateTime();
$methodClosure = fn(...$args) => $object->format(...$args);
$result = Callables::toCallable($methodClosure);
// Returns [$object, 'format']
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `Closure` | The closure to convert to callable |

**Returns** `callable|array|string` — The underlying callable, either as callable or array

**See also**

- `\Phuture\Coherence\Callables::toClosure()`

### `toClosure()`

```php
public static function toClosure(callable $callback): Closure
```

Converts any callable to a closure.

This method creates a closure from any callable value, including functions,
methods, and invokable objects. This is useful when you need a consistent
closure type for function parameters or variable assignment.

**Example:**
```php
use Phuture\Coherence\Callables;

// Function to closure
$closure = Callables::toClosure('strlen');
$result = $closure('hello'); // Returns 5

// Method to closure
$object = new DateTime();
$closure = Callables::toClosure([$object, 'format']);
$result = $closure('Y-m-d'); // Returns current date

// Static method to closure
$closure = Callables::toClosure([DateTime::class, 'createFromFormat']);
$date = $closure('Y-m-d', '2023-01-01');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The callable to convert to a closure |

**Returns** `Closure` — The closure version of the callable

**See also**

- `\Phuture\Coherence\Callables::toCallable()`

### `toString()`

```php
public static function toString(callable $callback): string
```

Converts a callable to a readable string representation.

This method creates a string that shows what the callable is, which can be
useful for debugging or logging. The format depends on the callable type:

- Functions: shows the function name
- Methods: shows "Class::method"
- Closures: shows "Closure"
- Invokable objects: shows "Class::__invoke"

**Example:**
```php
use Phuture\Coherence\Callables;

$str = Callables::toString('strlen'); // 'strlen'
$str = Callables::toString([DateTime::class, 'format']); // 'DateTime::format'
$str = Callables::toString(fn($x) => $x * 2); // 'Closure'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The callable to convert to string |

**Returns** `string` — The readable string representation of the callable

### `unary()`

```php
public static function unary(callable $callback): Closure
```

Creates a function that only uses the first argument.

This method returns a closure that ignores all arguments except
the first one and passes it to the original function. Useful for
creating unary functions from multi-argument functions or ensuring
only one argument is processed.

**Example:**
```php
use Phuture\Coherence\Callables;

$add = fn($a, $b) => $a + $b;
$firstArg = Callables::unary($add);

$result1 = $firstArg(5, 10, 15); // Returns 5 (ignores extra args)
$result2 = $firstArg(100); // Returns 100

// Array processing
$getFirst = fn($array) => $array[0];
$extractFirst = Callables::unary($getFirst);
$result3 = $extractFirst([1, 2, 3], [4, 5], [6, 7]); // Returns 1
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to call with only the first argument |

**Returns** `Closure` — A function that uses only the first argument

**See also**

- `\Phuture\Coherence\Callables::binary()`

### `unless()`

```php
public static function unless(callable $callback, bool $condition): Closure
```

Creates a conditional function that executes only when a condition is false.

This method is the opposite of when() - it returns a new function that will
execute your callback only when the condition is false. If the condition is
true, it returns the first argument or null without executing the callback.

**Example:**
```php
use Phuture\Coherence\Callables;

$logWhenNotProduction = Callables::unless(fn($msg) => error_log($msg), $isProduction);
$logWhenNotProduction('Debug message'); // Only logs if not in production
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to execute when condition is false |
| `$condition` | `bool` | The condition to check before executing the callback |

**Returns** `Closure` — Returns a new function that conditionally executes the callback

**See also**

- `\Phuture\Coherence\Callables::when()`

### `when()`

```php
public static function when(callable $callback, bool $condition): Closure
```

Creates a conditional function that executes a callback only when a condition is true.

This method returns a new function that will check a condition before executing
your callback. If the condition is true, it runs the callback with the provided
arguments. If false, it returns the first argument or null.

**Example:**
```php
use Phuture\Coherence\Callables;

$logWhenDebug = Callables::when(fn($msg) => error_log($msg), $debugMode);
$logWhenDebug('Debug message'); // Only logs if $debugMode is true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The function to execute when condition is true |
| `$condition` | `bool` | The condition to check before executing the callback |

**Returns** `Closure` — Returns a new function that conditionally executes the callback

**See also**

- `\Phuture\Coherence\Callables::unless()`

### `wrap()`

```php
public static function wrap(callable $callback, ?callable $before = null, ?callable $after = null): Closure
```

Creates a function with optional before and after hooks.

This method combines before() and after() into one convenient wrapper.
It executes an optional "before" function, then the main function, then an
optional "after" function. All three functions receive the same arguments,
and the after function also receives the main function's result.

**Example:**
```php
use Phuture\Coherence\Callables;

$before = fn($data) => echo "Processing " . count($data) . " items\n";
$process = fn($data) => array_map(fn($x) => $x * 2, $data);
$after = fn($result, $original) => echo "Processed " . count($result) . " results\n";

$wrapped = Callables::wrap($process, $before, $after);
$result = $wrapped([1, 2, 3, 4]);
// Outputs: Processing 4 items
//         Processed 4 results
// Returns [2, 4, 6, 8]

// Only before hook
$withBefore = Callables::wrap($process, $before);

// Only after hook
$withAfter = Callables::wrap($process, null, $after);

// Database transaction wrapper
$beginTransaction = fn() => db()->beginTransaction();
$commit = fn($result) => db()->commit();
$rollback = fn($error) => db()->rollback();

$transactional = Callables::wrap($query, $beginTransaction, $commit);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$callback` | `callable` | The main function to wrap |
| `$before` | `callable\|null` | Optional function to execute before the main function |
| `$after` | `callable\|null` | Optional function to execute after the main function (receives result, then args) |

**Returns** `Closure` — A function that wraps the main function with optional hooks

**See also**

- `\Phuture\Coherence\Callables::before()`
- `\Phuture\Coherence\Callables::after()`

### `delay()`

```php
private static function delay(int $milliseconds): void
```

Suspends execution for the given number of milliseconds.

Whole seconds are slept separately from the remaining microseconds so that the microsecond
count handed to `usleep()` always stays below UINT_MAX. Passing the delay straight to
`usleep()` overflows for delays beyond roughly 71 minutes, which PHP 8.6 reports as a
ValueError. Delays of zero or less return immediately.

| Parameter | Type | Description |
| --- | --- | --- |
| `$milliseconds` | `int` | The number of milliseconds to sleep |

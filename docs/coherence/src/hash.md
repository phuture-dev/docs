# Hash

`Phuture\Coherence\Hash`

```php
class Hash extends StaticClass
```

Comprehensive cryptographic hash utility class.

This utility class offers a complete toolkit for cryptographic hash operations,
supporting multiple algorithms and use cases including data integrity verification,
password security, HMAC generation, file checksums, and key derivation.

Key features:

- **Basic Hashing**: Support for MD2, MD4, MD5, SHA1, SHA256, SHA384, SHA512, and Adler-32/CRC32 algorithms
- **Blake2 Hashing**: Support for Blake2b (512-bit) and Blake2s (256-bit) modern cryptographic hash algorithms
- **Password Security**: Secure password hashing with automatic salt generation and verification
- **Argon2ID Password Hashing**: Memory-hard password hashing with resistance to GPU and side-channel attacks
- **Argon2I Password Hashing**: Side-channel resistant password hashing using the Argon2I variant
- **HMAC Operations**: Message authentication codes for data integrity and authenticity
- **File Integrity**: Efficient file hashing for integrity verification and checksums
- **Streaming Support**: Memory-efficient streaming for large data processing
- **Random Generation**: Cryptographically secure random strings, UUIDs, tokens, and salts
- **Salt Management**: Automatic salt generation and salt-based hash operations
- **Key Derivation**: PBKDF2 implementation for secure key stretching
- **Serialization Support**: Hashing of PHP arrays and objects
- **Binary Conversion**: Binary string to hexadecimal conversion utilities

Security considerations:

- This class includes legacy algorithms (MD2, MD4, MD5, SHA1) for compatibility only
- For password hashing, use the dedicated password methods with automatic salt
- For new applications, prefer SHA256, SHA384, or SHA512 for better security
- Always use HMAC methods when authentication is required
- PBKDF2 provides key stretching for password-derived encryption keys
- For modern password hashing, prefer Argon2ID or Argon2I via the PasswordAlgorithm enum

## Constants

### `DEFAULT_PBKDF2_ITERATIONS`

```php
const DEFAULT_PBKDF2_ITERATIONS = 100000
```

Default number of iterations for PBKDF2 key derivation.

This value provides a reasonable balance between security and performance.
Higher values increase security but slow down the derivation process.

**See also**

- `\Phuture\Coherence\Hash::pbkdf2()`

### `MAX_DERIVED_KEY_LENGTH`

```php
const MAX_DERIVED_KEY_LENGTH = 100000
```

Maximum allowed length in bytes for a derived key.

Prevents excessively large key derivation requests that could consume
excessive memory or computation time.

**See also**

- `\Phuture\Coherence\Hash::pbkdf2()`

## Methods

### `adler32()`

```php
public static function adler32(string $data, bool $binary = false): string
```

Generates an Adler-32 hash of the given data.

This method creates an Adler-32 checksum, which is a fast algorithm for detecting
data corruption and verifying file integrity. It's commonly used for quick integrity
checks and is faster than CRC32 but less reliable for error detection.

**Example:**
```php
use Phuture\Coherence\Hash;

$checksum = Hash::adler32('Hello, World!');

// Returns: '1f9e046a'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to generate a checksum for |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the Adler-32 checksum as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::crc32()` — For generating a CRC32 checksum

### `algorithms()`

```php
public static function algorithms(): array
```

Returns a list of all supported hash algorithms.

This method retrieves an array of all hash algorithms supported by the current
PHP installation. This is useful for checking algorithm availability before
using them or for providing users with algorithm selection options.

**Example:**
```php
use Phuture\Coherence\Hash;

$algorithms = Hash::algorithms();

// Returns: ['md2', 'md4', 'md5', 'sha1', 'sha256', 'sha384', 'sha512', ...]
```

**Returns** `array` — Returns an array of supported hash algorithm names

**See also**

- `\Phuture\Coherence\Hash::supports()` — For checking if a specific algorithm is supported
- `\Phuture\Coherence\Hash::hmacAlgorithms()` — For listing HMAC-supported algorithms

### `array()`

```php
public static function array(array $data, bool $binary = false, string $algo = 'sha256'): string
```

Generates a hash of a PHP array by serializing it first.

This method serializes a PHP array into a string representation and then
hashes the serialized data. This is useful for detecting changes in
array structures, validating configuration arrays, or creating signatures
for complex data.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = ['name' => 'John', 'age' => 30, 'active' => true];
$hash = Hash::array($data);

// Returns: hash of serialized array
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `array` | The array to serialize and hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |
| `$algo` | `string` | The hash algorithm to use (default: 'sha256') |

**Returns** `string` — Returns the hash of the serialized array as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::object()` — For hashing serialized objects
- `\Phuture\Coherence\Hash::make()` — For hashing string data with a configurable algorithm

### `blake2b()`

```php
public static function blake2b(string $data, bool $binary = false): string
```

Generates a Blake2b hash of the given data.

This method creates a Blake2b (512-bit variant) hash, which is a modern cryptographic
hash function designed to be faster than MD5 and SHA families while providing security
at least equal to SHA-3. Blake2b is optimized for 64-bit platforms and produces a
512-bit output represented as a 128 hexadecimal character string.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = Hash::blake2b('Hello, World!');

// Returns: 128-character hex string (Blake2b-512 hash)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the Blake2b hash as a 128-character hex string or 64 bytes of raw binary data

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When Blake2b is not supported by this PHP installation

**See also**

- `\Phuture\Coherence\Hash::fileBlake2b()` — For hashing file contents with Blake2b
- `\Phuture\Coherence\Hash::hmacBlake2b()` — For generating HMAC with Blake2b
- `\Phuture\Coherence\Hash::blake2s()` — For the 256-bit Blake2s variant

### `blake2s()`

```php
public static function blake2s(string $data, bool $binary = false): string
```

Generates a Blake2s hash of the given data.

This method creates a Blake2s (256-bit variant) hash, which is a modern cryptographic
hash function designed as a faster and more secure alternative to MD5 and SHA-1. Blake2s
is optimized for 8- to 32-bit platforms and produces a 256-bit output represented as a
64 hexadecimal character string, matching the output size of SHA256.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = Hash::blake2s('Hello, World!');

// Returns: 64-character hex string (Blake2s-256 hash)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the Blake2s hash as a 64-character hex string or 32 bytes of raw binary data

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When Blake2s is not supported by this PHP installation

**See also**

- `\Phuture\Coherence\Hash::fileBlake2s()` — For hashing file contents with Blake2s
- `\Phuture\Coherence\Hash::hmacBlake2s()` — For generating HMAC with Blake2s
- `\Phuture\Coherence\Hash::blake2b()` — For the 512-bit Blake2b variant

### `check()`

```php
public static function check(string $data, string $hash, string $algo = 'sha256'): bool
```

Verifies data against a hash by comparing the computed hash with the provided hash.

This method is a convenient way to verify that data hasn't been tampered with.
It computes the hash of the provided data and compares it securely against
the expected hash.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'important message';
$expectedHash = Hash::sha256($data);
$isValid = Hash::check($data, $expectedHash);

// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The original data to verify |
| `$hash` | `string` | The expected hash to compare against |
| `$algo` | `string` | The hash algorithm used (default: 'sha256') |

**Returns** `bool` — Returns true if the data matches the hash, false otherwise

**See also**

- `\Phuture\Coherence\Hash::checkWithSalt()` — For verifying data against a salted hash
- `\Phuture\Coherence\Hash::hmacCheck()` — For verifying HMAC hashes

### `checkWithSalt()`

```php
public static function checkWithSalt(string $data, string $hash, string $salt, string $algo = 'sha256'): bool
```

Verifies data against a salted hash.

This method checks if data matches a previously created salted hash by
recombining the data with the same salt and comparing the resulting hashes.
The salt is internally hashed using SHA-256 regardless of the chosen algorithm
to ensure a consistent and secure salt preprocessing step.

**Example:**
```php
use Phuture\Coherence\Hash;

$result = Hash::makeWithSalt('password123');
$isValid = Hash::checkWithSalt('password123', $result['hash'], $result['salt']);

// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The original data to verify |
| `$hash` | `string` | The salted hash to verify against |
| `$salt` | `string` | The salt that was used to create the original hash |
| `$algo` | `string` | The hash algorithm that was used (default: 'sha256') |

**Returns** `bool` — Returns true if the data and salt match the hash, false otherwise

**See also**

- `\Phuture\Coherence\Hash::makeWithSalt()` — For generating a salted hash
- `\Phuture\Coherence\Hash::check()` — For verifying data against an unsalted hash

### `crc32()`

```php
public static function crc32(string $data, bool $binary = false): string
```

Generates a CRC32 hash of the given data.

This method creates a CRC32 checksum, which is widely used for error detection
in network communications and file transfers. It's more reliable than Adler-32
for detecting errors but still not suitable for cryptographic security.

**Example:**
```php
use Phuture\Coherence\Hash;

$checksum = Hash::crc32('Hello, World!');

// Returns: 'dffed8e6'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to generate a checksum for |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the CRC32 checksum as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::crc32b()` — For generating a CRC32b checksum
- `\Phuture\Coherence\Hash::crc32c()` — For generating a CRC32c checksum
- `\Phuture\Coherence\Hash::adler32()` — For generating an Adler-32 checksum

### `crc32b()`

```php
public static function crc32b(string $data, bool $binary = false): string
```

Generates a CRC32b hash of the given data.

This method creates a CRC32b checksum, which is commonly used for error
checking and data integrity verification. It's fast but not suitable for
cryptographic security purposes.

**Example:**
```php
use Phuture\Coherence\Hash;

$checksum = Hash::crc32b('some data for checksum');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to generate a checksum for |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the CRC32b checksum as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::crc32()` — For generating a CRC32 checksum
- `\Phuture\Coherence\Hash::crc32c()` — For generating a CRC32c checksum

### `crc32c()`

```php
public static function crc32c(string $data, bool $binary = false): string
```

Generates a CRC32c hash of the given data.

This method creates a CRC32c checksum using the Castagnoli polynomial,
which is optimized for certain use cases and commonly used in storage
and networking protocols.

**Example:**
```php
use Phuture\Coherence\Hash;

$checksum = Hash::crc32c('network packet data');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to generate a checksum for |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the CRC32c checksum as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::crc32()` — For generating a CRC32 checksum
- `\Phuture\Coherence\Hash::crc32b()` — For generating a CRC32b checksum

### `equals()`

```php
public static function equals(string $hash, string $secondHash): bool
```

Securely compares two hash strings to prevent timing attacks.

This method compares two strings in a way that prevents timing attacks.
It's important for security-sensitive comparisons like password verification
or API signature validation.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash1 = 'abc123';
$hash2 = 'abc123';
$isMatch = Hash::equals($hash1, $hash2);

// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$hash` | `string` | The first hash string to compare |
| `$secondHash` | `string` | The second hash string to compare |

**Returns** `bool` — Returns true if the strings are identical, false otherwise

**See also**

- `\Phuture\Coherence\Hash::hmacTimingSafe()` — For timing-safe comparison using HMAC

### `file()`

```php
public static function file(string $file, bool $binary = false, string $algo = 'sha256'): string
```

Generates a hash of a file's contents using memory-efficient streaming.

This method uses PHP's optimized hash_file() function which processes files
in small chunks, making it suitable for hashing very large files (GB+ sizes)
without memory issues. The streaming approach ensures constant memory usage
regardless of file size.

**Example:**
```php
use Phuture\Coherence\Hash;

$fileHash = Hash::file('/path/to/large-video.mp4');

// Returns: SHA256 hash of the file contents (64-character hex string)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `string` | The path to the file to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |
| `$algo` | `string` | The hash algorithm to use (default: 'sha256') |

**Returns** `string` — Returns the file hash as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the specified algorithm is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or is not readable

**See also**

- `\Phuture\Coherence\Hash::make()` — For hashing string data with a configurable algorithm
- `\Phuture\Coherence\Hash::hmacFile()` — For generating HMAC of file contents

### `fileBlake2b()`

```php
public static function fileBlake2b(string $file, bool $binary = false): string
```

Generates a Blake2b hash of a file's contents.

This method reads a file and generates a Blake2b-512 hash of its contents using
memory-efficient streaming. It is suitable for hashing very large files (GB+ sizes)
without memory issues. The streaming approach ensures constant memory usage
regardless of file size.

**Example:**
```php
use Phuture\Coherence\Hash;

$fileHash = Hash::fileBlake2b('/path/to/file.dat');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `string` | The path to the file to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the Blake2b file hash as a 128-character hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When Blake2b is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or is not readable

**See also**

- `\Phuture\Coherence\Hash::blake2b()` — For hashing string data with Blake2b
- `\Phuture\Coherence\Hash::hmacBlake2b()` — For generating HMAC with Blake2b

### `fileBlake2s()`

```php
public static function fileBlake2s(string $file, bool $binary = false): string
```

Generates a Blake2s hash of a file's contents.

This method reads a file and generates a Blake2s-256 hash of its contents using
memory-efficient streaming. It is suitable for hashing very large files (GB+ sizes)
without memory issues. The streaming approach ensures constant memory usage
regardless of file size.

**Example:**
```php
use Phuture\Coherence\Hash;

$fileHash = Hash::fileBlake2s('/path/to/file.dat');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `string` | The path to the file to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the Blake2s file hash as a 64-character hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When Blake2s is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or is not readable

**See also**

- `\Phuture\Coherence\Hash::blake2s()` — For hashing string data with Blake2s
- `\Phuture\Coherence\Hash::hmacBlake2s()` — For generating HMAC with Blake2s

### `fileMd2()`

```php
public static function fileMd2(string $file, bool $binary = false): string
```

Generates an MD2 hash of a file's contents.

This method reads a file and generates an MD2 hash of its contents.
Note: MD2 is considered cryptographically weak and should only be used
for compatibility with legacy systems.

**Example:**
```php
use Phuture\Coherence\Hash;

$fileHash = Hash::fileMd2('/path/to/legacy-file.dat');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `string` | The path to the file to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the MD2 file hash as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be read

**See also**

- `\Phuture\Coherence\Hash::md2()` — For hashing string data with MD2
- `\Phuture\Coherence\Hash::hmacMd2()` — For generating HMAC with MD2

### `fileMd4()`

```php
public static function fileMd4(string $file, bool $binary = false): string
```

Generates an MD4 hash of a file's contents.

This method reads a file and generates an MD4 hash of its contents.
Note: MD4 is considered cryptographically weak and should only be used
for compatibility with legacy systems.

**Example:**
```php
use Phuture\Coherence\Hash;

$fileHash = Hash::fileMd4('/path/to/legacy-file.dat');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `string` | The path to the file to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the MD4 file hash as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be read

**See also**

- `\Phuture\Coherence\Hash::md4()` — For hashing string data with MD4
- `\Phuture\Coherence\Hash::hmacMd4()` — For generating HMAC with MD4

### `fileMd5()`

```php
public static function fileMd5(string $file, bool $binary = false): string
```

Generates an MD5 hash of a file's contents.

This method reads a file and generates an MD5 hash of its contents.
MD5 is commonly used for file integrity checks and duplicate detection,
but should not be used for security-critical applications.

**Example:**
```php
use Phuture\Coherence\Hash;

$fileHash = Hash::fileMd5('/path/to/document.pdf');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `string` | The path to the file to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the MD5 file hash as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be read

**See also**

- `\Phuture\Coherence\Hash::md5()` — For hashing string data with MD5
- `\Phuture\Coherence\Hash::hmacMd5()` — For generating HMAC with MD5

### `fileSha1()`

```php
public static function fileSha1(string $file, bool $binary = false): string
```

Generates a SHA1 hash of a file's contents.

This method reads a file and generates a SHA1 hash of its contents.
SHA1 provides better security than MD5 but is still considered weak
for new security applications. Consider using SHA256 or stronger.

**Example:**
```php
use Phuture\Coherence\Hash;

$fileHash = Hash::fileSha1('/path/to/archive.zip');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `string` | The path to the file to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA1 file hash as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be read

**See also**

- `\Phuture\Coherence\Hash::sha1()` — For hashing string data with SHA1
- `\Phuture\Coherence\Hash::hmacSha1()` — For generating HMAC with SHA1

### `fileSha256()`

```php
public static function fileSha256(string $file, bool $binary = false): string
```

Generates a SHA256 hash of a file's contents.

This method reads a file and generates a SHA256 hash of its contents.
SHA256 is currently recommended for most security applications and provides
a good balance of security and performance for file integrity verification.

**Example:**
```php
use Phuture\Coherence\Hash;

$fileHash = Hash::fileSha256('/path/to/important-file.exe');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `string` | The path to the file to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA256 file hash as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be read

**See also**

- `\Phuture\Coherence\Hash::sha256()` — For hashing string data with SHA256
- `\Phuture\Coherence\Hash::hmacSha256()` — For generating HMAC with SHA256

### `fileSha384()`

```php
public static function fileSha384(string $file, bool $binary = false): string
```

Generates a SHA384 hash of a file's contents.

This method reads a file and generates a SHA384 hash of its contents.
SHA384 provides stronger security than SHA256 and is suitable for
high-security applications requiring 384-bit hash output.

**Example:**
```php
use Phuture\Coherence\Hash;

$fileHash = Hash::fileSha384('/path/to/sensitive-data.dat');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `string` | The path to the file to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA384 file hash as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be read

**See also**

- `\Phuture\Coherence\Hash::sha384()` — For hashing string data with SHA384
- `\Phuture\Coherence\Hash::hmacSha384()` — For generating HMAC with SHA384

### `fileSha512()`

```php
public static function fileSha512(string $file, bool $binary = false): string
```

Generates a SHA512 hash of a file's contents.

This method reads a file and generates a SHA512 hash of its contents.
SHA512 provides the strongest security among the SHA2 family and is suitable
for maximum security applications requiring 512-bit hash output.

**Example:**
```php
use Phuture\Coherence\Hash;

$fileHash = Hash::fileSha512('/path/to/critical-file.bin');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `string` | The path to the file to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA512 file hash as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be read

**See also**

- `\Phuture\Coherence\Hash::sha512()` — For hashing string data with SHA512
- `\Phuture\Coherence\Hash::hmacSha512()` — For generating HMAC with SHA512

### `final()`

```php
public static function final(HashContext $context): string
```

Finalizes the incremental hash calculation and returns the result.

This method completes the incremental hashing process and returns the final
hash string. After calling this method, the hash context cannot be used
for further updates.

**Example:**
```php
use Phuture\Coherence\Hash;

$context = Hash::init('sha256');
Hash::update($context, 'Large file data...');
$finalHash = Hash::final($context);

// Returns: SHA256 hash of all data
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$context` | `HashContext` | The hash context to finalize |

**Returns** `string` — Returns the final hash as a hexadecimal string

**See also**

- `\Phuture\Coherence\Hash::init()` — For creating a hash context
- `\Phuture\Coherence\Hash::update()` — For adding data to the context

### `fromBinary()`

```php
public static function fromBinary(string $data): string
```

Converts binary data to its hexadecimal representation.

This method converts binary data (raw bytes) to a readable hexadecimal string.
This is useful for displaying binary hashes, debugging, or storing binary data
in text-based formats like JSON or databases.

**Example:**
```php
use Phuture\Coherence\Hash;

$binary = "\x48\x65\x6C\x6C\x6F"; // Binary "Hello"
$hex = Hash::fromBinary($binary);

// Returns: '48656c6c6f'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The binary data to convert to hexadecimal |

**Returns** `string` — Returns the hexadecimal representation of the binary data

**See also**

- `\Phuture\Coherence\Hash::make()` — For generating hashes with binary output option

### `hash()`

```php
public static function hash(string $data, ?string $salt = null, bool $binary = false): string
```

Generates a combined hash by joining the SHA-256 and SHA-512 hashes of the data.

This method produces a single, longer fingerprint by hashing the data twice — once
with SHA-256 and once with SHA-512 — and joining the two results together. When a
salt is provided, both halves are salted before hashing, which makes identical inputs
produce different fingerprints.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = Hash::hash('Hello, World!');

// Returns: the SHA-256 hash followed by the SHA-512 hash, joined into one string
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash |
| `$salt` | `string\|null` | Optional salt applied to both hashes (default: null for no salt) |
| `$binary` | `bool` | Whether to return raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA-256 and SHA-512 hashes joined into a single string

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported

**See also**

- `\Phuture\Coherence\Hash::make()` — For generating a single-algorithm hash
- `\Phuture\Coherence\Hash::makeWithSalt()` — For generating a salted hash

### `hmac()`

```php
public static function hmac(string $data, string $key, bool $binary = false, string $algo = 'sha256'): string
```

Generates a keyed hash message authentication code (HMAC).

This method creates a secure hash using both your data and a secret key.
HMAC is used to verify both the data integrity and authenticity of a message.
It's commonly used for API signatures and secure data transmission.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'important message';
$secretKey = 'my-secret-key';
$signature = Hash::hmac($data, $secretKey);

// Returns: secure hash that can only be verified with the same key
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to authenticate |
| `$key` | `string` | The secret key used for authentication |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |
| `$algo` | `string` | The hash algorithm to use (default: 'sha256') |

**Returns** `string` — Returns the HMAC as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the specified algorithm is not supported

**See also**

- `\Phuture\Coherence\Hash::hmacCheck()` — For verifying an HMAC
- `\Phuture\Coherence\Hash::hmacFile()` — For generating HMAC of file contents

### `hmacAlgorithms()`

```php
public static function hmacAlgorithms(): array
```

Returns a list of all supported HMAC hash algorithms.

This method retrieves an array of all hash algorithms that can be used with
HMAC operations. This is useful for checking HMAC algorithm availability
or for providing users with secure algorithm selection options for authentication.

**Example:**
```php
use Phuture\Coherence\Hash;

$hmacAlgos = Hash::hmacAlgorithms();

// Returns: ['md5', 'sha1', 'sha256', 'sha384', 'sha512', ...]
```

**Returns** `array` — Returns an array of supported HMAC hash algorithm names

**See also**

- `\Phuture\Coherence\Hash::hmacSupports()` — For checking if a specific algorithm is supported for HMAC
- `\Phuture\Coherence\Hash::algorithms()` — For listing all supported hash algorithms

### `hmacBlake2b()`

```php
public static function hmacBlake2b(string $data, string $key, bool $binary = false): string
```

Generates an HMAC using the Blake2b algorithm.

This method creates an HMAC using the Blake2b-512 hash algorithm with your data and
secret key. Blake2b provides strong authentication with a 512-bit output and is
significantly faster than SHA-512 based HMAC on 64-bit platforms.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'message';
$key = 'secret';
$hmac = Hash::hmacBlake2b($data, $key);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to authenticate |
| `$key` | `string` | The secret key for authentication |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the Blake2b HMAC as a 128-character hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When Blake2b is not supported for HMAC

**See also**

- `\Phuture\Coherence\Hash::blake2b()` — For hashing string data with Blake2b
- `\Phuture\Coherence\Hash::fileBlake2b()` — For hashing file contents with Blake2b

### `hmacBlake2s()`

```php
public static function hmacBlake2s(string $data, string $key, bool $binary = false): string
```

Generates an HMAC using the Blake2s algorithm.

This method creates an HMAC using the Blake2s-256 hash algorithm with your data and
secret key. Blake2s provides strong authentication with a 256-bit output and is
optimized for 8- to 32-bit platforms while remaining suitable for all environments.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'message';
$key = 'secret';
$hmac = Hash::hmacBlake2s($data, $key);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to authenticate |
| `$key` | `string` | The secret key for authentication |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the Blake2s HMAC as a 64-character hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When Blake2s is not supported for HMAC

**See also**

- `\Phuture\Coherence\Hash::blake2s()` — For hashing string data with Blake2s
- `\Phuture\Coherence\Hash::fileBlake2s()` — For hashing file contents with Blake2s

### `hmacCheck()`

```php
public static function hmacCheck(string $data, string $key, string $hash, string $algo = 'sha256'): bool
```

Verifies data against an HMAC hash using a secret key.

This method verifies that data was signed with a specific secret key.
It's commonly used to verify API requests, webhooks, or secure data transmission.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'important message';
$secretKey = 'my-secret-key';
$signature = Hash::hmac($data, $secretKey);
$isValid = Hash::hmacCheck($data, $secretKey, $signature);

// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The original data to verify |
| `$key` | `string` | The secret key used to create the original HMAC |
| `$hash` | `string` | The expected HMAC hash to compare against |
| `$algo` | `string` | The hash algorithm used (default: 'sha256') |

**Returns** `bool` — Returns true if the data and key match the HMAC, false otherwise

**See also**

- `\Phuture\Coherence\Hash::hmac()` — For generating an HMAC
- `\Phuture\Coherence\Hash::hmacCheckWithSalt()` — For verifying salted HMACs

### `hmacCheckWithSalt()`

```php
public static function hmacCheckWithSalt(string $data, string $key, string $hash, string $salt, string $algo = 'sha256'): bool
```

Verifies data against a salted HMAC.

This method checks if data matches a previously created salted HMAC by
recombining the data with the same salt and comparing the resulting HMACs.
The salt is internally hashed using SHA-256 regardless of the chosen algorithm
to ensure a consistent and secure salt preprocessing step.

**Example:**
```php
use Phuture\Coherence\Hash;

$result = Hash::hmacWithSalt('message', 'secret-key');
$isValid = Hash::hmacCheckWithSalt('message', 'secret-key', $result['hmac'], $result['salt']);

// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The original data to verify |
| `$key` | `string` | The secret key that was used to create the original HMAC |
| `$hash` | `string` | The salted HMAC to verify against |
| `$salt` | `string` | The salt that was used to create the original HMAC |
| `$algo` | `string` | The HMAC algorithm that was used (default: 'sha256') |

**Returns** `bool` — Returns true if the data, key, and salt match the HMAC, false otherwise

**See also**

- `\Phuture\Coherence\Hash::hmacWithSalt()` — For generating a salted HMAC
- `\Phuture\Coherence\Hash::hmacCheck()` — For verifying unsalted HMACs

### `hmacFile()`

```php
public static function hmacFile(string $file, string $key, bool $binary = false, string $algo = 'sha256'): string
```

Generates an HMAC of a file's contents using memory-efficient streaming.

This method uses PHP's optimized hash_hmac_file() function which processes files
in small chunks, making it suitable for generating HMACs of very large files (GB+ sizes)
without memory issues. The streaming approach ensures constant memory usage
regardless of file size while providing both integrity and authenticity verification.

**Example:**
```php
use Phuture\Coherence\Hash;

$fileHmac = Hash::hmacFile('/path/to/large-video.mp4', 'secret-key');

// Returns: HMAC-SHA256 of the file contents (64-character hex string)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `string` | The path to the file to HMAC |
| `$key` | `string` | The secret key used for authentication |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |
| `$algo` | `string` | The HMAC algorithm to use (default: 'sha256') |

**Returns** `string` — Returns the HMAC as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the specified algorithm is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or is not readable

**See also**

- `\Phuture\Coherence\Hash::file()` — For hashing file contents without authentication
- `\Phuture\Coherence\Hash::hmac()` — For generating HMAC of string data

### `hmacFinal()`

```php
public static function hmacFinal(HashContext $context, bool $binary = false): string
```

Finalizes an incremental HMAC calculation and returns the result.

This method completes the incremental HMAC process started with hmacInit() and
returns the final authentication code. After calling this method, the context
cannot be used for further updates.

**Example:**
```php
use Phuture\Coherence\Hash;

$context = Hash::hmacInit('secret-key');
Hash::hmacUpdate($context, 'First chunk');
Hash::hmacUpdate($context, 'Second chunk');
$hmac = Hash::hmacFinal($context);

// Returns: HMAC-SHA256 of all data combined
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$context` | `HashContext` | The HMAC hash context to finalize |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the final HMAC as a hexadecimal string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::hmacInit()` — For creating an HMAC hash context
- `\Phuture\Coherence\Hash::hmacUpdate()` — For adding data to the context

### `hmacInit()`

```php
public static function hmacInit(string $key, string $algo = 'sha256'): HashContext
```

Initializes an incremental HMAC hashing context for streaming authentication.

This method creates a new HMAC hash context that allows you to authenticate
large amounts of data in chunks without loading everything into memory. This
is ideal for processing large files, streams, or data that arrives over time,
while still benefiting from authentication with a secret key.

**Example:**
```php
use Phuture\Coherence\Hash;

$context = Hash::hmacInit('secret-key');
Hash::hmacUpdate($context, 'First chunk of data');
Hash::hmacUpdate($context, 'Second chunk of data');
$hmac = Hash::hmacFinal($context);

// Returns: HMAC of combined data, identical to Hash::hmac('First chunkSecond chunk', 'secret-key')
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$key` | `string` | The secret key for HMAC authentication |
| `$algo` | `string` | The hash algorithm to use (default: 'sha256') |

**Returns** `HashContext` — Returns an HMAC hash context for incremental hashing

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported for HMAC

**See also**

- `\Phuture\Coherence\Hash::hmacUpdate()` — For adding data to the context
- `\Phuture\Coherence\Hash::hmacFinal()` — For completing the HMAC calculation
- `\Phuture\Coherence\Hash::init()` — For non-authenticated incremental hashing

### `hmacMd2()`

```php
public static function hmacMd2(string $data, string $key, bool $binary = false): string
```

Generates an HMAC using the MD2 algorithm.

This method creates an HMAC using the MD2 hash algorithm with your data and secret key.
Note: MD2 is considered cryptographically weak and should only be used for compatibility
with legacy systems.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'message';
$key = 'secret';
$hmac = Hash::hmacMd2($data, $key);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to authenticate |
| `$key` | `string` | The secret key for authentication |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the MD2 HMAC as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported for HMAC

**See also**

- `\Phuture\Coherence\Hash::md2()` — For hashing string data with MD2
- `\Phuture\Coherence\Hash::fileMd2()` — For hashing file contents with MD2

### `hmacMd4()`

```php
public static function hmacMd4(string $data, string $key, bool $binary = false): string
```

Generates an HMAC using the MD4 algorithm.

This method creates an HMAC using the MD4 hash algorithm with your data and secret key.
Note: MD4 is considered cryptographically weak and should only be used for compatibility
with legacy systems.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'message';
$key = 'secret';
$hmac = Hash::hmacMd4($data, $key);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to authenticate |
| `$key` | `string` | The secret key for authentication |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the MD4 HMAC as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported for HMAC

**See also**

- `\Phuture\Coherence\Hash::md4()` — For hashing string data with MD4
- `\Phuture\Coherence\Hash::fileMd4()` — For hashing file contents with MD4

### `hmacMd5()`

```php
public static function hmacMd5(string $data, string $key, bool $binary = false): string
```

Generates an HMAC using the MD5 algorithm.

This method creates an HMAC using the MD5 hash algorithm with your data and secret key.
Note: MD5 is considered cryptographically weak and should only be used for compatibility
with legacy systems or non-security-critical applications.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'message';
$key = 'secret';
$hmac = Hash::hmacMd5($data, $key);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to authenticate |
| `$key` | `string` | The secret key for authentication |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the MD5 HMAC as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported for HMAC

**See also**

- `\Phuture\Coherence\Hash::md5()` — For hashing string data with MD5
- `\Phuture\Coherence\Hash::fileMd5()` — For hashing file contents with MD5

### `hmacSha1()`

```php
public static function hmacSha1(string $data, string $key, bool $binary = false): string
```

Generates an HMAC using the SHA1 algorithm.

This method creates an HMAC using the SHA1 hash algorithm with your data and secret key.
SHA1 provides better security than MD5 but is still considered weak for new applications.
Consider using SHA256 or stronger algorithms for new implementations.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'message';
$key = 'secret';
$hmac = Hash::hmacSha1($data, $key);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to authenticate |
| `$key` | `string` | The secret key for authentication |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA1 HMAC as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported for HMAC

**See also**

- `\Phuture\Coherence\Hash::sha1()` — For hashing string data with SHA1
- `\Phuture\Coherence\Hash::fileSha1()` — For hashing file contents with SHA1

### `hmacSha256()`

```php
public static function hmacSha256(string $data, string $key, bool $binary = false): string
```

Generates an HMAC using the SHA256 algorithm.

This method creates an HMAC using the SHA256 hash algorithm with your data and secret key.
SHA256 is currently recommended for most security applications and provides
a good balance of security and performance.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'message';
$key = 'secret';
$hmac = Hash::hmacSha256($data, $key);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to authenticate |
| `$key` | `string` | The secret key for authentication |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA256 HMAC as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported for HMAC

**See also**

- `\Phuture\Coherence\Hash::sha256()` — For hashing string data with SHA256
- `\Phuture\Coherence\Hash::fileSha256()` — For hashing file contents with SHA256

### `hmacSha384()`

```php
public static function hmacSha384(string $data, string $key, bool $binary = false): string
```

Generates an HMAC using the SHA384 algorithm.

This method creates an HMAC using the SHA384 hash algorithm with your data and secret key.
SHA384 provides stronger security than SHA256 and is suitable for high-security
applications requiring 384-bit hash output.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'message';
$key = 'secret';
$hmac = Hash::hmacSha384($data, $key);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to authenticate |
| `$key` | `string` | The secret key for authentication |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA384 HMAC as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported for HMAC

**See also**

- `\Phuture\Coherence\Hash::sha384()` — For hashing string data with SHA384
- `\Phuture\Coherence\Hash::fileSha384()` — For hashing file contents with SHA384

### `hmacSha512()`

```php
public static function hmacSha512(string $data, string $key, bool $binary = false): string
```

Generates an HMAC using the SHA512 algorithm.

This method creates an HMAC using the SHA512 hash algorithm with your data and secret key.
SHA512 provides the strongest security among the SHA2 family and is suitable for
maximum security applications requiring 512-bit hash output.

**Example:**
```php
use Phuture\Coherence\Hash;

$data = 'message';
$key = 'secret';
$hmac = Hash::hmacSha512($data, $key);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to authenticate |
| `$key` | `string` | The secret key for authentication |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA512 HMAC as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported for HMAC

**See also**

- `\Phuture\Coherence\Hash::sha512()` — For hashing string data with SHA512
- `\Phuture\Coherence\Hash::fileSha512()` — For hashing file contents with SHA512

### `hmacSupports()`

```php
public static function hmacSupports(string $algo): bool
```

Checks if an HMAC hash algorithm is supported by the current PHP installation.

This method provides a convenient way to verify that a specific hash algorithm
can be used for HMAC operations before attempting to create an HMAC. This is
useful for feature detection and graceful fallbacks in authentication systems.

**Example:**
```php
use Phuture\Coherence\Hash;

$isSupported = Hash::hmacSupports('sha256');

// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$algo` | `string` | The hash algorithm to check for HMAC support (e.g., 'sha256', 'md5', 'sha1') |

**Returns** `bool` — Returns true if the algorithm is supported for HMAC operations, false otherwise

**See also**

- `\Phuture\Coherence\Hash::hmacAlgorithms()` — For listing all supported HMAC algorithms

### `hmacTimingSafe()`

```php
public static function hmacTimingSafe(string $data1, string $data2, string $key): bool
```

Compare two HMAC values using timing-safe comparison.

This method provides timing-safe comparison of two pieces of data by generating
HMACs for both and comparing them using hash_equals(). This prevents timing attacks
that could reveal information about the data being compared.

**Example:**
```php
use Phuture\Coherence\Hash;

$key = 'secret-key';
$data1 = 'user_input_1';
$data2 = 'user_input_2';

// Securely compare if both inputs produce the same HMAC
$isValid = Hash::hmacTimingSafe($data1, $data2, $key);

// Returns: true if both inputs are identical, false otherwise
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data1` | `string` | The first data to HMAC |
| `$data2` | `string` | The second data to HMAC |
| `$key` | `string` | The secret key for HMAC generation |

**Returns** `bool` — True if both HMACs are equal, false otherwise

**See also**

- `\Phuture\Coherence\Hash::equals()` — For direct timing-safe hash comparison

### `hmacUpdate()`

```php
public static function hmacUpdate(HashContext $context, string $data): void
```

Adds data to an incremental HMAC hashing context.

This method appends data to an existing HMAC context created by hmacInit(), allowing
you to process large data in chunks. Multiple calls to hmacUpdate() accumulate all
data for the final HMAC calculation performed by hmacFinal().

**Example:**
```php
use Phuture\Coherence\Hash;

$context = Hash::hmacInit('secret-key');
Hash::hmacUpdate($context, 'First chunk');
Hash::hmacUpdate($context, 'Second chunk');
$hmac = Hash::hmacFinal($context);

// Returns: HMAC of "First chunkSecond chunk"
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$context` | `HashContext` | The HMAC context to update, created by hmacInit() |
| `$data` | `string` | The data to add to the HMAC calculation |

**Returns** `void`

**See also**

- `\Phuture\Coherence\Hash::hmacInit()` — For creating an HMAC context
- `\Phuture\Coherence\Hash::hmacFinal()` — For completing the HMAC calculation

### `hmacWithSalt()`

```php
public static function hmacWithSalt(string $data, string $key, ?string $salt = null, bool $binary = false, string $algo = 'sha256'): array
```

Generates a salted HMAC using the specified algorithm.

This method creates an HMAC by combining a salted hash of the salt with the data
before authenticating with a secret key. This provides additional security by
making each HMAC unique even for identical inputs with the same key, while also
preventing length extension attacks.

The method uses a secure construction: HMAC(hash('sha256', $salt, true) . $data, key)
instead of the insecure HMAC($data . $salt, key) concatenation.

**Example:**
```php
use Phuture\Coherence\Hash;

$result = Hash::hmacWithSalt('message', 'secret-key');

// Returns: ['hmac' => '...', 'salt' => '...']
// The HMAC is: HMAC(hash('sha256', $salt, true) . 'message', 'secret-key')
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to authenticate |
| `$key` | `string` | The secret key for authentication |
| `$salt` | `string\|null` | Optional custom salt (default: null to generate random salt) |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |
| `$algo` | `string` | The HMAC algorithm to use (default: 'sha256') |

**Returns** `array` — Returns an array with 'hmac' and 'salt' keys

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the specified algorithm is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When random bytes generation fails

**See also**

- `\Phuture\Coherence\Hash::hmacCheckWithSalt()` — For verifying a salted HMAC
- `\Phuture\Coherence\Hash::makeWithSalt()` — For generating a salted hash

### `init()`

```php
public static function init(string $algo = 'sha256'): HashContext
```

Initializes an incremental hashing context for streaming data.

This method creates a new hash context that allows you to hash large amounts
of data in chunks without loading everything into memory. This is ideal for
processing large files, streams, or data that arrives over time.

**Example:**
```php
use Phuture\Coherence\Hash;

$context = Hash::init('sha256');
Hash::update($context, 'First chunk of data');
Hash::update($context, 'Second chunk of data');
$hash = Hash::final($context);

// Returns: hash of combined data
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$algo` | `string` | The hash algorithm to use (default: 'sha256') |

**Returns** `HashContext` — Returns a hash context for incremental hashing

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the specified algorithm is not supported

**See also**

- `\Phuture\Coherence\Hash::update()` — For adding data to the context
- `\Phuture\Coherence\Hash::final()` — For completing the hash calculation

### `make()`

```php
public static function make(string $data, bool $binary = false, string $algo = 'sha256'): string
```

Generates a hash using the specified algorithm.

This method provides a flexible way to create hashes using any supported algorithm.
It's a convenient wrapper around PHP's hash() function with built-in validation
for supported algorithms. Choose from algorithms like 'md5', 'sha1', 'sha256', etc.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = Hash::make('Hello, World!', false, 'md5');

// Returns: '65a8e27d8879283831b664bd8b7f0ad4'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash |
| `$binary` | `bool` | Whether to return raw binary data (default: false for hex string) |
| `$algo` | `string` | The hash algorithm to use (e.g., 'sha256', 'md5', 'sha1', default: 'sha256') |

**Returns** `string` — Returns the hash as a hex string or raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the specified algorithm is not supported

**See also**

- `\Phuture\Coherence\Hash::check()` — For verifying data against a hash
- `\Phuture\Coherence\Hash::file()` — For hashing file contents
- `\Phuture\Coherence\Hash::hmac()` — For generating authenticated hashes

### `makeWithSalt()`

```php
public static function makeWithSalt(string $data, ?string $salt = null, bool $binary = false, string $algo = 'sha256'): array
```

Generates a salted hash using the specified algorithm.

This method creates a hash by combining a salted hash of the salt with the data
before final hashing. This prevents length extension attacks and makes identical
inputs produce different hashes. Returns both the hash and salt for storage.

The method uses a secure construction: hash(hash('sha256', $salt, true) . $data)
instead of the insecure $data . $salt concatenation.

**Example:**
```php
use Phuture\Coherence\Hash;

$result = Hash::makeWithSalt('password123');

// Returns: ['hash' => '...', 'salt' => '...']
// The hash is: hash(hash('sha256', $salt, true) . 'password123')
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash with salt |
| `$salt` | `string\|null` | Optional custom salt (default: null to generate random salt) |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |
| `$algo` | `string` | The hash algorithm to use (default: 'sha256') |

**Returns** `array` — Returns an array with 'hash' and 'salt' keys

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the algorithm is not supported
- `\Phuture\Coherence\Exception\RuntimeException` — When random bytes generation fails

**See also**

- `\Phuture\Coherence\Hash::checkWithSalt()` — For verifying a salted hash
- `\Phuture\Coherence\Hash::hmacWithSalt()` — For generating a salted HMAC

### `md2()`

```php
public static function md2(string $data, bool $binary = false): string
```

Generates an MD2 hash of the given data.

This method creates an MD2 hash, which is part of the MD family of hash functions.
Note: MD2 is considered cryptographically weak and should only be used for
compatibility with legacy systems or non-security applications.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = Hash::md2('Hello, World!');

// Returns: '1c8f1e6a94aaa7145210bf90bb52871a'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the MD2 hash as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::fileMd2()` — For hashing file contents with MD2
- `\Phuture\Coherence\Hash::hmacMd2()` — For generating HMAC with MD2

### `md4()`

```php
public static function md4(string $data, bool $binary = false): string
```

Generates an MD4 hash of the given data.

This method creates an MD4 hash, which was designed for high-speed 32-bit
processing but is now considered cryptographically broken and insecure.
Should only be used for compatibility with legacy systems.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = Hash::md4('Hello, World!');

// Returns: '94e3cb0fa9aa7a5ee3db74b79e915989'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the MD4 hash as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::fileMd4()` — For hashing file contents with MD4
- `\Phuture\Coherence\Hash::hmacMd4()` — For generating HMAC with MD4

### `md5()`

```php
public static function md5(string $data, bool $binary = false): string
```

Generates an MD5 hash of the given data.

This method creates an MD5 hash, which produces a 128-bit hash value.
MD5 is widely used for file integrity checks and checksums but should not be
used for password storage or security-critical applications due to vulnerabilities.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = Hash::md5('Hello, World!');

// Returns: '65a8e27d8879283831b664bd8b7f0ad4'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the MD5 hash as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::fileMd5()` — For hashing file contents with MD5
- `\Phuture\Coherence\Hash::hmacMd5()` — For generating HMAC with MD5

### `object()`

```php
public static function object(object $object, bool $binary = false, string $algo = 'sha256'): string
```

Generates a hash of a PHP object by serializing it first.

This method serializes a PHP object into a string representation and then
hashes the serialized data. This is useful for detecting changes in object
state, validating data transfer objects, or creating signatures for
complex object structures.

**Example:**
```php
use Phuture\Coherence\Hash;

$user = new User('John', 'john@example.com');
$hash = Hash::object($user);

// Returns: hash of serialized object
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$object` | `object` | The object to serialize and hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |
| `$algo` | `string` | The hash algorithm to use (default: 'sha256') |

**Returns** `string` — Returns the hash of the serialized object as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::array()` — For hashing serialized arrays
- `\Phuture\Coherence\Hash::make()` — For hashing string data with a configurable algorithm

### `password()`

```php
public static function password(string $password, PasswordAlgorithm $algo = PasswordAlgorithm::Default, array $options = []): string
```

Creates a secure password hash.

This method generates a strong hash for storing passwords securely. It uses PHP's
built-in password hashing functions which automatically handle salt generation and
use cryptographically secure algorithms. Use the \Phuture\Coherence\Enum\PasswordAlgorithm
enum to choose between PHP's recommended default, BCrypt, Argon2I, or Argon2ID.

**Example:**
```php
use Phuture\Coherence\Hash;
use Phuture\Coherence\Enum\PasswordAlgorithm;

// PHP's recommended default
$hash = Hash::password('user123');

// BCrypt
$hash = Hash::password('user123', PasswordAlgorithm::Bcrypt);

// Argon2ID
$hash = Hash::password('user123', PasswordAlgorithm::Argon2id);

// Argon2I
$hash = Hash::password('user123', PasswordAlgorithm::Argon2i);

// Returns: hashed password string
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$password` | `string` | The plain text password to hash |
| `$algo` | `PasswordAlgorithm` | The algorithm to use (default: PasswordAlgorithm::Default) |
| `$options` | `array` | Algorithm options: 'cost' for BCrypt; 'memory_cost', 'time_cost', 'threads' for Argon2I and Argon2ID |

**Returns** `string` — Returns the hashed password string

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When an Argon2 algorithm is requested but not supported

**See also**

- `\Phuture\Coherence\Hash::passwordCheck()` — For verifying a password against its hash
- `\Phuture\Coherence\Hash::passwordNeedsRehash()` — For checking if a hash needs updating

### `passwordCheck()`

```php
public static function passwordCheck(string $password, string $hash): bool
```

Verifies a password against its hash.

This method checks if a plain text password matches a previously generated hash.
It's the secure way to verify user login credentials without ever storing or
exposing the actual password.

**Example:**
```php
use Phuture\Coherence\Hash;

$password = 'user123';
$hash = Hash::password($password);
$isValid = Hash::passwordCheck($password, $hash);

// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$password` | `string` | The plain text password to verify |
| `$hash` | `string` | The hash to verify against |

**Returns** `bool` — Returns true if the password matches the hash, false otherwise

**See also**

- `\Phuture\Coherence\Hash::password()` — For creating a password hash
- `\Phuture\Coherence\Hash::passwordNeedsRehash()` — For checking if a hash needs updating

### `passwordInfo()`

```php
public static function passwordInfo(string $hash): array
```

Retrieves information about a password hash.

This method analyzes a password hash and returns details about the algorithm used,
cost factor, and other relevant information. It's useful for understanding how a
password was hashed.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = '$2y$10$AbCdEfGhIjKlMnOpQrStU.vWxYz1234567890abcdefg';
$info = Hash::passwordInfo($hash);

// Returns: ['algo' => 1, 'algoName' => 'bcrypt', 'options' => ['cost' => 10]]
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$hash` | `string` | The password hash to analyze |

**Returns** `array` — Returns an array with algorithm details and options

**See also**

- `\Phuture\Coherence\Hash::password()` — For creating a password hash
- `\Phuture\Coherence\Hash::passwordNeedsRehash()` — For checking if a hash needs updating

### `passwordNeedsRehash()`

```php
public static function passwordNeedsRehash(string $hash, PasswordAlgorithm $algo = PasswordAlgorithm::Default, array $options = []): bool
```

Checks if a password hash needs to be rehashed with a stronger algorithm or updated options.

This method determines if a password hash was created using an outdated algorithm or
options. It's useful for upgrading password hashes when you change your hashing parameters
or when PHP updates its default algorithm. Use the \Phuture\Coherence\Enum\PasswordAlgorithm
enum to specify which algorithm the hash should be checked against.

**Example:**
```php
use Phuture\Coherence\Hash;
use Phuture\Coherence\Enum\PasswordAlgorithm;

// BCrypt cost upgrade
$hash = Hash::password('user123', PasswordAlgorithm::Bcrypt, ['cost' => 10]);
$needsRehash = Hash::passwordNeedsRehash($hash, PasswordAlgorithm::Bcrypt, ['cost' => 12]);

// Argon2ID memory upgrade
$hash = Hash::password('user123', PasswordAlgorithm::Argon2id, ['memory_cost' => 65536]);
$needsRehash = Hash::passwordNeedsRehash($hash, PasswordAlgorithm::Argon2id, ['memory_cost' => 131072]);

// Argon2I time cost upgrade
$hash = Hash::password('user123', PasswordAlgorithm::Argon2i, ['time_cost' => 4]);
$needsRehash = Hash::passwordNeedsRehash($hash, PasswordAlgorithm::Argon2i, ['time_cost' => 8]);

// Returns: true if the hash was created with lower cost parameters
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$hash` | `string` | The password hash to check |
| `$algo` | `PasswordAlgorithm` | The algorithm to check against (default: PasswordAlgorithm::Default) |
| `$options` | `array` | Options to compare against (default: []) |

**Returns** `bool` — Returns true if the hash needs to be rehashed, false otherwise

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When an Argon2 algorithm is requested but not supported

**See also**

- `\Phuture\Coherence\Hash::password()` — For creating a password hash
- `\Phuture\Coherence\Hash::passwordCheck()` — For verifying a password

### `pbkdf2()`

```php
public static function pbkdf2(string $password, ?string $salt = null, int $iterations = self::DEFAULT_PBKDF2_ITERATIONS, int $length = 32, string $algo = 'sha256'): string
```

Derives a cryptographic key from a password using PBKDF2.

This method implements the Password-Based Key Derivation Function 2 (PBKDF2),
which securely derives cryptographic keys from passwords. PBKDF2 applies a hash
function repeatedly (iterations) to make the derivation computationally expensive
and resistant to brute force attacks.

**Example:**
```php
use Phuture\Coherence\Hash;

$key = Hash::pbkdf2('user-password', 'salt-value', 10000, 32);

// Returns: 32-byte derived key
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$password` | `string` | The password to derive the key from |
| `$salt` | `string\|null` | Optional salt value (default: null to generate random salt) |
| `$iterations` | `int` | Number of hash iterations (default: 100000) |
| `$length` | `int` | Desired length of derived key in bytes (default: 32) |
| `$algo` | `string` | The hash algorithm to use (default: 'sha256') |

**Returns** `string` — Returns the derived key as raw binary data

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When iterations, length, or algorithm are invalid

**See also**

- `\Phuture\Coherence\Hash::pbkdf2Algorithms()` — For listing supported PBKDF2 algorithms
- `\Phuture\Coherence\Hash::pbkdf2Supports()` — For checking if an algorithm is supported for PBKDF2

### `pbkdf2Algorithms()`

```php
public static function pbkdf2Algorithms(): array
```

Returns a list of PBKDF2-supported hash algorithms.

This method returns an array of hash algorithms that are both supported by the
current PHP installation for HMAC operations and are suitable for use with
PBKDF2 key derivation. Only the SHA family of algorithms are included.

**Example:**
```php
use Phuture\Coherence\Hash;

$algorithms = Hash::pbkdf2Algorithms();

// Returns: ['sha1', 'sha256', 'sha384', 'sha512'] (depending on system support)
```

**Returns** `array` — Returns an array of PBKDF2-compatible hash algorithm names

**See also**

- `\Phuture\Coherence\Hash::pbkdf2Supports()` — For checking if a specific algorithm is supported for PBKDF2
- `\Phuture\Coherence\Hash::algorithms()` — For listing all supported hash algorithms

### `pbkdf2Supports()`

```php
public static function pbkdf2Supports(string $algo): bool
```

Checks if a hash algorithm is supported for PBKDF2 operations.

This method provides a convenient way to verify that a specific hash algorithm
can be used with PBKDF2 key derivation before attempting to use it. This is
useful for feature detection and graceful fallbacks in key derivation systems.

**Example:**
```php
use Phuture\Coherence\Hash;

$isSupported = Hash::pbkdf2Supports('sha256');

// Returns: true if SHA256 is available for PBKDF2
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$algo` | `string` | The hash algorithm to check for PBKDF2 support (e.g., 'sha256', 'sha512') |

**Returns** `bool` — Returns true if the algorithm is supported for PBKDF2 operations, false otherwise

**See also**

- `\Phuture\Coherence\Hash::pbkdf2Algorithms()` — For listing all PBKDF2-compatible algorithms

### `random()`

```php
public static function random(int $length = 128, bool $binary = false): string
```

Generates cryptographically secure random data.

Creates the given number of random bytes, returned as either a hex string
(twice the byte length) or raw binary data (exactly the byte length). The
length parameter always represents the number of random bytes generated,
regardless of output format. Useful for encryption keys, salts, nonces,
and other security-sensitive data.

**Example:**
```php
use Phuture\Coherence\Hash;

$randomHex = Hash::random(32); // 64-character hex string (32 bytes of entropy)
$randomBinary = Hash::random(16, true); // 16 bytes of raw binary data
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$length` | `int` | The number of random bytes to generate (default: 128) |
| `$binary` | `bool` | Whether to return raw binary data instead of a hex string (default: false) |

**Returns** `string` — A hex string of twice the given length, or raw binary bytes of the given length

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When unable to generate random bytes

**See also**

- `\Phuture\Coherence\Hash::salt()` — For generating a random salt
- `\Phuture\Coherence\Hash::token()` — For generating a random token

### `salt()`

```php
public static function salt(int $length = 16): string
```

Generates a cryptographically secure random salt for hashing purposes.

This method creates a random salt string suitable for password hashing,
key derivation, or other cryptographic purposes. The salt is returned
as a hexadecimal string for easy storage and use.

**Example:**
```php
use Phuture\Coherence\Hash;

$salt = Hash::salt(16);

// Returns: 'a1b2c3d4e5f678901234567890123456' (32 hex characters for 16 bytes)
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$length` | `int` | The desired length of the salt in bytes (default: 16) |

**Returns** `string` — Returns a hexadecimal string representing the random salt

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When unable to generate random bytes

**See also**

- `\Phuture\Coherence\Hash::random()` — For generating random data
- `\Phuture\Coherence\Hash::makeWithSalt()` — For generating a salted hash

### `sha1()`

```php
public static function sha1(string $data, bool $binary = false): string
```

Generates a SHA1 hash of the given data.

This method creates a SHA1 hash, which produces a 160-bit hash value.
SHA1 provides better security than MD5 but is still considered weak for new
security applications. Consider using SHA256 or stronger algorithms.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = Hash::sha1('Hello, World!');

// Returns: '0a0a9f2a6772942557ab5355d76af442f8f65e01'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA1 hash as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::fileSha1()` — For hashing file contents with SHA1
- `\Phuture\Coherence\Hash::hmacSha1()` — For generating HMAC with SHA1

### `sha256()`

```php
public static function sha256(string $data, bool $binary = false): string
```

Generates a SHA256 hash of the given data.

This method creates a SHA256 hash, which produces a 256-bit hash value.
SHA256 is currently recommended for most security applications and provides
an excellent balance of security and performance for digital signatures,
certificate fingerprints, and password storage (with proper salting).

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = Hash::sha256('Hello, World!');

// Returns: 'dffd6021bb2bd5b0af676290809ec3a53191dd81c7f70a4b28688a362182986f'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA256 hash as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::fileSha256()` — For hashing file contents with SHA256
- `\Phuture\Coherence\Hash::hmacSha256()` — For generating HMAC with SHA256

### `sha384()`

```php
public static function sha384(string $data, bool $binary = false): string
```

Generates a SHA384 hash of the given data.

This method creates a SHA384 hash, which produces a 384-bit hash value.
SHA384 provides stronger security than SHA256 and is suitable for high-security
applications requiring more robust protection against collision attacks.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = Hash::sha384('Hello, World!');

// Returns: '5485cc9b3365b4305dfb4e8337e0a598a574f8242bf17289e0dd6c20a3cd44a089de16ab4ab308f63e44b1170eb5f515'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA384 hash as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::fileSha384()` — For hashing file contents with SHA384
- `\Phuture\Coherence\Hash::hmacSha384()` — For generating HMAC with SHA384

### `sha512()`

```php
public static function sha512(string $data, bool $binary = false): string
```

Generates a SHA512 hash of the given data.

This method creates a SHA512 hash, which produces a 512-bit hash value.
SHA512 provides the strongest security among the SHA2 family and is suitable
for maximum security applications requiring the highest level of protection
against collision attacks and for future-proofing cryptographic systems.

**Example:**
```php
use Phuture\Coherence\Hash;

$hash = Hash::sha512('Hello, World!');

// Returns:
'374d794a95cdcfd8b35993185fef9ba368f160d8daf432d08ba9f1ed1e5abe6cc69291e0fa2fe0006a52570ef18c19def4e617c33ce52ef0a6e5fbe318cb0387'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$data` | `string` | The data to hash |
| `$binary` | `bool` | Whether to output raw binary data (default: false for hex string) |

**Returns** `string` — Returns the SHA512 hash as a hex string or raw binary data

**See also**

- `\Phuture\Coherence\Hash::fileSha512()` — For hashing file contents with SHA512
- `\Phuture\Coherence\Hash::hmacSha512()` — For generating HMAC with SHA512

### `supports()`

```php
public static function supports(string $algo): bool
```

Checks if a hash algorithm is supported by the current PHP installation.

This method provides a convenient way to verify that a specific hash algorithm
is available before attempting to use it. This is useful for feature detection
and graceful fallbacks in applications.

**Example:**
```php
use Phuture\Coherence\Hash;

$isSupported = Hash::supports('sha256');

// Returns: true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$algo` | `string` | The hash algorithm to check (e.g., 'sha256', 'md5', 'sha1') |

**Returns** `bool` — Returns true if the algorithm is supported, false otherwise

**See also**

- `\Phuture\Coherence\Hash::algorithms()` — For listing all supported hash algorithms

### `token()`

```php
public static function token(): string
```

Generates a cryptographically secure random token.

This method creates a secure random token suitable for API keys, authentication
tokens, session identifiers, and other security-sensitive purposes. The token
is 64 characters long (32 bytes converted to hexadecimal).

**Example:**
```php
use Phuture\Coherence\Hash;

$token = Hash::token();

// Returns: 'a1b2c3d4e5f6789012345678901234567890abcdef1234567890abcdef123456'
```

**Returns** `string` — Returns a 64-character hexadecimal token

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When unable to generate random bytes

**See also**

- `\Phuture\Coherence\Hash::random()` — For generating random data
- `\Phuture\Coherence\Hash::unique()` — For generating a unique identifier

### `unique()`

```php
public static function unique(): string
```

Generates a unique identifier combining timestamp and random data.

This method creates a unique identifier by combining a high-resolution timestamp
with cryptographically secure random bytes, then hashing the result with SHA256.
This provides both uniqueness and unpredictability.

**Example:**
```php
use Phuture\Coherence\Hash;

$uniqueId = Hash::unique();

// Returns: 64-character SHA256 hash
```

**Returns** `string` — Returns a unique SHA256 hash (64 hexadecimal characters)

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When unable to generate random bytes

**See also**

- `\Phuture\Coherence\Hash::token()` — For generating a random token
- `\Phuture\Coherence\Hash::uuid()` — For generating a UUID v4

### `update()`

```php
public static function update(HashContext $context, string $data): void
```

Adds data to an incremental hashing context.

This method appends data to an existing hash context, allowing you to process
data in chunks. Multiple calls to update() will accumulate all the data for
the final hash calculation.

**Example:**
```php
use Phuture\Coherence\Hash;

$context = Hash::init('md5');
Hash::update($context, 'Hello, ');
Hash::update($context, 'World!');
$hash = Hash::final($context);

// Returns: MD5 of "Hello, World!"
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$context` | `HashContext` | The hash context to update |
| `$data` | `string` | The data to add to the hash calculation |

**Returns** `void`

**See also**

- `\Phuture\Coherence\Hash::init()` — For creating a hash context
- `\Phuture\Coherence\Hash::final()` — For completing the hash calculation

### `uuid()`

```php
public static function uuid(): string
```

Generates a UUID (Universally Unique Identifier) version 4.

This method creates a random UUID v4, which is a 36-character string in the format
xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx. UUIDs are commonly used for unique identifiers
in databases, distributed systems, and as keys for data records.

**Example:**
```php
use Phuture\Coherence\Hash;

$uuid = Hash::uuid();

// Returns: '550e8400-e29b-41d4-a716-446655440000'
```

**Returns** `string` — Returns a UUID v4 string

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When unable to generate random bytes

**See also**

- `\Phuture\Coherence\Hash::unique()` — For generating a unique identifier
- `\Phuture\Coherence\Hash::token()` — For generating a random token

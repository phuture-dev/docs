# Files

`Phuture\Coherence\Type\Files`

```php
class Files extends FluentClass implements Fileable
```

A fluent wrapper around the Files utility class for chainable file manipulation.

Each method delegates to the corresponding static method on `\Phuture\Coherence\Files`, stores the
result internally, and returns `$this` to enable method chaining. The wrapped
value is always the current file path as a string.

**Example:**
```php
use Phuture\Coherence\Files;

$content = Files::of('/path/to/draft.txt')
    ->copy('/path/to/backup.txt')
    ->rename('final.txt')
    ->write('Updated content')
    ->read();
// 'Updated content'
```

## Methods

### `append()`

```php
public function append(string $content): self
```

Appends content to the end of the wrapped file.

| Parameter | Type | Description |
| --- | --- | --- |
| `$content` | `string` | The content to append to the file |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be written

**See also**

- `\Phuture\Coherence\Files::append()`

### `chgrp()`

```php
public function chgrp(string|int $group): self
```

Changes the group ownership of the wrapped file.

| Parameter | Type | Description |
| --- | --- | --- |
| `$group` | `string\|int` | The new group name or numeric group ID |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the group cannot be changed

**See also**

- `\Phuture\Coherence\Files::chgrp()`

### `chmod()`

```php
public function chmod(int $mode): self
```

Changes the permission mode of the wrapped file.

| Parameter | Type | Description |
| --- | --- | --- |
| `$mode` | `int` | The permission mode (octal notation, e.g. 0644) |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When permissions cannot be changed

**See also**

- `\Phuture\Coherence\Files::chmod()`

### `chown()`

```php
public function chown(string|int $user): self
```

Changes the owner of the wrapped file.

| Parameter | Type | Description |
| --- | --- | --- |
| `$user` | `string\|int` | The new owner name or numeric user ID |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the owner cannot be changed

**See also**

- `\Phuture\Coherence\Files::chown()`

### `compress()`

```php
public function compress(string $destination, CompressionFormat $format = CompressionFormat::Zip): self
```

Compresses the wrapped file or directory into an archive and switches the internal path to it.

After compression, the internal path is updated to point to the newly created
archive file, so subsequent operations act on the compressed archive.

| Parameter | Type | Description |
| --- | --- | --- |
| `$destination` | `string` | The path where the archive will be saved |
| `$format` | `\Phuture\Coherence\Enum\CompressionFormat` | The archive format to use (default: Zip) |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — If the source does not exist
- `\Phuture\Coherence\Exception\RuntimeException` — If the archive cannot be created

**See also**

- `\Phuture\Coherence\Files::compress()`

### `copy()`

```php
public function copy(string $destination, bool $overwrite = true): self
```

Copies the wrapped file to a new location and updates the internal path.

After copying, the internal path remains unchanged (it still points to
the source). Use `copyTo()` when you want the path to switch to the
destination after copying.

| Parameter | Type | Description |
| --- | --- | --- |
| `$destination` | `string` | The destination file or directory path to copy to |
| `$overwrite` | `bool` | Whether to overwrite existing files at the destination (default: true) |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the source does not exist or the destination cannot be written

**See also**

- `\Phuture\Coherence\Files::copy()`

### `copyTo()`

```php
public function copyTo(string $destination, bool $overwrite = true): self
```

Copies the wrapped file to a new location and switches the internal path to the destination.

This is the same as `copy()` but after copying, the internal path is updated
to point to the destination, so subsequent operations act on the copy.

| Parameter | Type | Description |
| --- | --- | --- |
| `$destination` | `string` | The destination file or directory path to copy to |
| `$overwrite` | `bool` | Whether to overwrite existing files at the destination (default: true) |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the source does not exist or the destination cannot be written

**See also**

- `\Phuture\Coherence\Files::copy()`

### `decompress()`

```php
public function decompress(string $destination, CompressionFormat $format = CompressionFormat::Zip): self
```

Extracts or decompresses the wrapped archive to a destination path.

| Parameter | Type | Description |
| --- | --- | --- |
| `$destination` | `string` | The directory where the archive contents will be placed |
| `$format` | `\Phuture\Coherence\Enum\CompressionFormat` | The archive format to use (default: Zip) |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — If the archive does not exist
- `\Phuture\Coherence\Exception\RuntimeException` — If extraction fails

**See also**

- `\Phuture\Coherence\Files::decompress()`

### `delete()`

```php
public function delete(): void
```

Deletes the file or directory at the current path.

After deletion, the internal path is set to an empty string. This method
returns void because no further chaining is possible after the path is removed.

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path cannot be deleted

**See also**

- `\Phuture\Coherence\Files::delete()`

### `extension()`

```php
public function extension(): string
```

Returns the file extension without the leading dot.

**Returns** `string` — The file extension without the leading dot, or an empty string when there is none

**See also**

- `\Phuture\Coherence\Files::extension()`

### `lastModified()`

```php
public function lastModified(): int
```

Returns the last modification time of the file as a Unix timestamp.

**Returns** `int` — The last modification time as a Unix timestamp

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or the time cannot be read

**See also**

- `\Phuture\Coherence\Files::lastModified()`

### `makeWritable()`

```php
public function makeWritable(int $directoryMode = 0777, int $fileMode = 0666): self
```

Sets file permissions to make the current path writable.

| Parameter | Type | Description |
| --- | --- | --- |
| `$directoryMode` | `int` | The permission mode for directories (default: 0777) |
| `$fileMode` | `int` | The permission mode for files (default: 0666) |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path does not exist or permissions cannot be changed

**See also**

- `\Phuture\Coherence\Files::makeWritable()`

### `mimeType()`

```php
public function mimeType(): string
```

Returns the MIME type of the file detected from its content.

**Returns** `string` — The MIME type of the file (e.g., 'text/plain', 'image/png')

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or the MIME type cannot be detected

**See also**

- `\Phuture\Coherence\Files::mimeType()`

### `move()`

```php
public function move(string $destination, bool $overwrite = true): self
```

Moves the wrapped file to a new location and updates the internal path to the destination.

| Parameter | Type | Description |
| --- | --- | --- |
| `$destination` | `string` | The new file or directory path |
| `$overwrite` | `bool` | Whether to overwrite existing files at the destination (default: true) |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the source does not exist, the destination cannot be written, or the move fails

**See also**

- `\Phuture\Coherence\Files::move()`

### `name()`

```php
public function name(bool $includeExtension = true): string
```

Returns the name of the file.

| Parameter | Type | Description |
| --- | --- | --- |
| `$includeExtension` | `bool` | Whether to include the file extension (default: true) |

**Returns** `string` — The file name with or without extension

**See also**

- `\Phuture\Coherence\Files::name()`

### `path()`

```php
public function path(): string
```

Returns the full absolute path to the file.

**Returns** `string` — The full absolute path to the file

### `prepend()`

```php
public function prepend(string $content): self
```

Prepends content to the beginning of the wrapped file.

| Parameter | Type | Description |
| --- | --- | --- |
| `$content` | `string` | The content to prepend to the file |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be written

**See also**

- `\Phuture\Coherence\Files::prepend()`

### `read()`

```php
public function read(): string
```

Reads and returns the entire contents of the file as a string.

**Returns** `string` — The complete contents of the file

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or cannot be read

**See also**

- `\Phuture\Coherence\Files::read()`

### `rename()`

```php
public function rename(string $newName, bool $overwrite = true): self
```

Renames the file within its current directory and updates the internal path.

| Parameter | Type | Description |
| --- | --- | --- |
| `$newName` | `string` | The new name (without directory path) |
| `$overwrite` | `bool` | Whether to overwrite an existing file with the new name (default: true) |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path does not exist, the new name is empty, or the rename fails

**See also**

- `\Phuture\Coherence\Files::rename()`

### `replaceInFile()`

```php
public function replaceInFile(string|array $search, string|array $replace): self
```

Replaces all occurrences of a search string within the wrapped file.

| Parameter | Type | Description |
| --- | --- | --- |
| `$search` | `string\|array` | The value or values to search for |
| `$replace` | `string\|array` | The replacement value or values |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be read or written

**See also**

- `\Phuture\Coherence\Files::replaceInFile()`

### `size()`

```php
public function size(): int
```

Returns the size of the file in bytes.

**Returns** `int` — The file size in bytes

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or the size cannot be read

**See also**

- `\Phuture\Coherence\Files::size()`

### `write()`

```php
public function write(string $content, int $mode = 0666, bool $lock = false): self
```

Writes content to the wrapped file, creating it if it does not exist.

| Parameter | Type | Description |
| --- | --- | --- |
| `$content` | `string` | The content to write to the file |
| `$mode` | `int` | The permission mode for the file (default: 0666) |
| `$lock` | `bool` | Whether to acquire an exclusive lock before writing (default: false) |

**Returns** `self` — Returns the current instance for method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be written

**See also**

- `\Phuture\Coherence\Files::write()`

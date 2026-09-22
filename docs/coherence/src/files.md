# Files

`Phuture\Coherence\Files`

```php
class Files extends StaticClass
```

Comprehensive file system utility class with file manipulation, searching, and information capabilities.

This utility class provides a complete toolkit for working with files and directories,
combining file system manipulation, file searching, file information retrieval, MIME type
detection, and file upload handling into a single cohesive interface.

Key features:

- **File Manipulation**: Copy, create, delete, move, read, and write files and directories
- **Path Utilities**: Normalize, join, and convert path separators across platforms
- **File Searching**: Find files and directories using glob-style patterns with optional recursion
- **File Information**: Retrieve size, extension, modification time, and other metadata
- **MIME Type Detection**: Identify file types using the system's MIME database
- **File Uploads**: Handle single and multiple file uploads from HTTP requests
- **Directory Listing**: List and filter directory contents
- **Fluent Interface**: Call `Files::of()` to obtain a chainable `\Phuture\Coherence\Type\Files` wrapper

## Methods

### `append()`

```php
public static function append(string $path, string $content): void
```

Appends content to the end of a file, creating it if it does not exist.

When the file does not exist, it is created. Parent directories are
created automatically when they do not exist.

**Example:**
```php
use Phuture\Coherence\Files;

Files::write('/path/to/log.txt', 'First line');
Files::append('/path/to/log.txt', 'Second line');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to append to |
| `$content` | `string` | The content to append to the file |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be written

**See also**

- `\Phuture\Coherence\Files::write()`
- `\Phuture\Coherence\Files::prepend()`

### `chgrp()`

```php
public static function chgrp(string $path, string|int $group): void
```

Changes the group ownership of a file or directory.

**Example:**
```php
use Phuture\Coherence\Files;

Files::chgrp('/path/to/file.txt', 'www-data');
Files::chgrp('/path/to/directory', 1000);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file or directory path |
| `$group` | `string\|int` | The new group name or numeric group ID |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path does not exist or the group cannot be changed

**See also**

- `\Phuture\Coherence\Files::chmod()`
- `\Phuture\Coherence\Files::chown()`

### `chmod()`

```php
public static function chmod(string $path, int $mode): void
```

Changes the permission mode of a file or directory.

**Example:**
```php
use Phuture\Coherence\Files;

Files::chmod('/path/to/file.txt', 0644);
Files::chmod('/path/to/directory', 0755);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file or directory path |
| `$mode` | `int` | The permission mode (octal notation, e.g. 0755) |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path does not exist or permissions cannot be changed

**See also**

- `\Phuture\Coherence\Files::chown()`
- `\Phuture\Coherence\Files::chgrp()`
- `\Phuture\Coherence\Files::makeWritable()`

### `chown()`

```php
public static function chown(string $path, string|int $user): void
```

Changes the owner of a file or directory.

**Example:**
```php
use Phuture\Coherence\Files;

Files::chown('/path/to/file.txt', 'www-data');
Files::chown('/path/to/directory', 1000);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file or directory path |
| `$user` | `string\|int` | The new owner name or numeric user ID |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path does not exist or the owner cannot be changed

**See also**

- `\Phuture\Coherence\Files::chmod()`
- `\Phuture\Coherence\Files::chgrp()`

### `compress()`

```php
public static function compress(string $source, string $destination, CompressionFormat $format = CompressionFormat::Zip): void
```

Compresses a file or directory into an archive.

All three formats — `Zip`, `Tar`, and `Gzip` — support both files and
directories as input. `Zip` creates a `.zip` archive, `Tar` creates an
uncompressed `.tar` archive, and `Gzip` creates a GZIP-compressed TAR
archive (`.tar.gz`). When no format is given, `Zip` is used by default.

The parent directory of `$destination` is created automatically when it
does not already exist.

**Example:**
```php
use Phuture\Coherence\Files;
use Phuture\Coherence\Enum\CompressionFormat;

// ZIP (default) — compress a directory
Files::compress('/var/app/uploads', '/var/backups/uploads.zip');

// TAR — archive without compression
Files::compress('/var/app/uploads', '/var/backups/uploads.tar', CompressionFormat::Tar);

// GZIP — compress as .tar.gz, supports directories too
Files::compress('/var/app/uploads', '/var/backups/uploads.tar.gz', CompressionFormat::Gzip);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$source` | `string` | The file or directory path to compress |
| `$destination` | `string` | The path where the archive file will be saved |
| `$format` | `\Phuture\Coherence\Enum\CompressionFormat` | The archive format to use (default: Zip) |

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — If the source path does not exist
- `\Phuture\Coherence\Exception\RuntimeException` — If the archive cannot be created or written

**See also**

- `\Phuture\Coherence\Files::decompress()`

### `copy()`

```php
public static function copy(string $source, string $destination, bool $overwrite = true): void
```

Copies a file or an entire directory to a new location.

When copying a directory, all files and subdirectories within it are copied
recursively. By default, existing files at the destination are overwritten.
When `$overwrite` is false and the destination already exists, a
`\Phuture\Coherence\Exception\RuntimeException` is thrown.

**Example:**
```php
use Phuture\Coherence\Files;

Files::copy('/path/to/source.txt', '/path/to/destination.txt');
Files::copy('/path/to/source_dir', '/path/to/destination_dir');
Files::copy('/path/to/file.txt', '/path/to/existing.txt', overwrite: false);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$source` | `string` | The source file or directory path to copy from |
| `$destination` | `string` | The destination file or directory path to copy to |
| `$overwrite` | `bool` | Whether to overwrite existing files at the destination (default: true) |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the source does not exist or the destination cannot be written

**See also**

- `\Phuture\Coherence\Files::move()`

### `create()`

```php
public static function create(string $path, int $mode = 0777): void
```

Creates a file or directory at the given path.

When the path ends with a directory separator (`/` or `\`), a directory is
created including all missing parent directories. Otherwise, an empty file is
created using `touch()`, with parent directories created automatically when
they do not exist. If the path already exists, this method does nothing.

**Example:**
```php
use Phuture\Coherence\Files;

Files::create('/path/to/new/file.txt');      // creates empty file
Files::create('/path/to/new/directory/');    // creates directory
Files::create('/path/to/nested/dirs/', 0755); // creates directory with mode
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file or directory path to create |
| `$mode` | `int` | The permission mode applied to directories and (masked to 0666) to files (default: 0777) |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path cannot be created

**See also**

- `\Phuture\Coherence\Files::delete()`

### `createTemporaryDirectory()`

```php
public static function createTemporaryDirectory(string $prefix = 'tmp_', int $mode = 0700, string $parentDirectory = ''): string
```

Creates a temporary directory with a unique name.

This method creates a new empty directory inside the system's temporary directory
(or a custom directory you specify) with a unique name that avoids collisions.
The directory name is generated using a prefix you provide combined with random
characters, so multiple calls will always produce different directories.

**Example:**
```php
use Phuture\Coherence\Files;

$tempDir = Files::createTemporaryDirectory();
// Returns something like '/tmp/tmp_664b5a3c1f8d2'

$customDir = Files::createTemporaryDirectory(prefix: 'myapp_');
// Returns something like '/tmp/myapp_664b5a3c1f8d2'

$specificParent = Files::createTemporaryDirectory(parentDirectory: '/var/tmp');
// Creates the directory inside '/var/tmp' instead
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$prefix` | `string` | A short string added to the start of the directory name to make it easy to identify (default: 'tmp_') |
| `$mode` | `int` | The permission mode for the directory (default: 0700) |
| `$parentDirectory` | `string` | The directory where the temporary directory will be created (default: system temporary directory) |

**Returns** `string` — The full path to the newly created temporary directory

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the directory cannot be created

**See also**

- `\Phuture\Coherence\Files::createTemporaryFile()`
- `\Phuture\Coherence\Files::delete()`

### `createTemporaryFile()`

```php
public static function createTemporaryFile(string $prefix = 'tmp_', string $extension = '', int $mode = 0600, string $parentDirectory = ''): string
```

Creates a temporary file with a unique name.

This method creates a new empty file inside the system's temporary directory
(or a custom directory you specify) with a unique name that avoids collisions.
The file name is generated using a prefix you provide combined with random
characters, so multiple calls will always produce different files.

**Example:**
```php
use Phuture\Coherence\Files;

$tempFile = Files::createTemporaryFile();
// Returns something like '/tmp/tmp_664b5a3c1f8d2'

$customFile = Files::createTemporaryFile(prefix: 'myapp_', extension: '.csv');
// Returns something like '/tmp/myapp_664b5a3c1f8d2.csv'

$specificDir = Files::createTemporaryFile(parentDirectory: '/var/tmp');
// Creates the file inside '/var/tmp' instead
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$prefix` | `string` | A short string added to the start of the file name to make it easy to identify (default: 'tmp_') |
| `$extension` | `string` | The file extension to append, including the dot (default: '' — no extension) |
| `$mode` | `int` | The permission mode for the file (default: 0600) |
| `$parentDirectory` | `string` | The directory where the temporary file will be created (default: system temporary directory) |

**Returns** `string` — The full path to the newly created temporary file

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be created

**See also**

- `\Phuture\Coherence\Files::createTemporaryDirectory()`
- `\Phuture\Coherence\Files::delete()`

### `decompress()`

```php
public static function decompress(string $archive, string $destination, CompressionFormat $format = CompressionFormat::Zip): void
```

Extracts an archive into a destination directory.

All three formats — `Zip`, `Tar`, and `Gzip` — extract their contents into
the directory given by `$destination`. `Gzip` archives are treated as
`.tar.gz` files and behave identically to `Tar`. When no format is given,
`Zip` is used by default.

The destination directory is created automatically when it does not already
exist. All extracted files are placed directly inside `$destination`.

**Example:**
```php
use Phuture\Coherence\Files;
use Phuture\Coherence\Enum\CompressionFormat;

// ZIP (default) — extract into a directory
Files::decompress('/var/backups/uploads.zip', '/var/app/uploads');

// TAR — extract an uncompressed archive
Files::decompress('/var/backups/uploads.tar', '/var/app/uploads', CompressionFormat::Tar);

// GZIP — extract a .tar.gz archive
Files::decompress('/var/backups/uploads.tar.gz', '/var/app/uploads', CompressionFormat::Gzip);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$archive` | `string` | The path to the archive file to extract |
| `$destination` | `string` | The directory where the archive contents will be placed |
| `$format` | `\Phuture\Coherence\Enum\CompressionFormat` | The archive format to use (default: Zip) |

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — If the archive file does not exist
- `\Phuture\Coherence\Exception\RuntimeException` — If the archive cannot be extracted

**See also**

- `\Phuture\Coherence\Files::compress()`

### `delete()`

```php
public static function delete(string $path): void
```

Deletes a file or an entire directory at the given path.

When the path points to a directory, all of its contents (files and subdirectories)
are deleted recursively before the directory itself is removed. If the path does not
exist, this method does nothing.

**Example:**
```php
use Phuture\Coherence\Files;

Files::delete('/path/to/file.txt');
Files::delete('/path/to/directory');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file or directory path to delete |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path cannot be deleted

**See also**

- `\Phuture\Coherence\Files::copy()`
- `\Phuture\Coherence\Files::move()`

### `directory()`

```php
public static function directory(string $path, int $levels = 1): string
```

Returns the parent directory path of a file or directory.

Optionally, you can specify the number of levels to go up. For example,
a `$levels` of 2 goes up two parent directories.

**Example:**
```php
use Phuture\Coherence\Files;

Files::directory('/path/to/file.txt'); // '/path/to'
Files::directory('/path/to/file.txt', 2); // '/path'
Files::directory('/path/to/directory/'); // '/path/to'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file or directory path |
| `$levels` | `int` | The number of parent directories to go up (default: 1) |

**Returns** `string` — The parent directory path

**See also**

- `\Phuture\Coherence\Files::name()`

### `exists()`

```php
public static function exists(string $path): bool
```

Determines whether a file or directory exists at the given path.

Returns true for both files and directories. Use `isFile()` or
`isDirectory()` for type-specific checks.

**Example:**
```php
use Phuture\Coherence\Files;

Files::exists('/path/to/file.txt'); // true or false
Files::exists('/path/to/directory'); // true or false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The path to check for existence |

**Returns** `bool` — True when a file or directory exists at the path

**See also**

- `\Phuture\Coherence\Files::isFile()`
- `\Phuture\Coherence\Files::isDirectory()`

### `extension()`

```php
public static function extension(string $path): string
```

Extracts the file extension from a path.

Returns the extension without the leading dot. When the file has no
extension, an empty string is returned.

**Example:**
```php
use Phuture\Coherence\Files;

Files::extension('/path/to/file.txt'); // 'txt'
Files::extension('/path/to/archive.tar.gz'); // 'gz'
Files::extension('/path/to/README'); // ''
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to extract the extension from |

**Returns** `string` — The file extension without the leading dot, or an empty string when there is none

**See also**

- `\Phuture\Coherence\Files::name()`
- `\Phuture\Coherence\Files::mimeType()`

### `find()`

```php
public static function find(string $directory = '.', string|array $masks = '*', bool $recursive = false): array
```

Finds files and directories matching the given glob-style patterns.

Returns all files and directories within the specified directory that match
any of the provided masks. When `$recursive` is true, subdirectories are
searched as well. Masks use glob patterns: `*` matches any characters, `?`
matches a single character, and `[...]` matches a character class.

**Example:**
```php
use Phuture\Coherence\Files;

$all = Files::find('/path/to/dir');
$phpAndMd = Files::find('/path/to/src', ['*.php', '*.md'], recursive: true);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$directory` | `string` | The directory to search in (default: '.') |
| `$masks` | `string\|array` | One or more glob patterns to match against (default: '*') |
| `$recursive` | `bool` | Whether to search subdirectories (default: false) |

**Returns** `array` — Array of file and directory paths matching the patterns

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the directory does not exist

**See also**

- `\Phuture\Coherence\Files::findFiles()`
- `\Phuture\Coherence\Files::findDirectories()`

### `findDirectories()`

```php
public static function findDirectories(string $directory = '.', string|array $masks = '*', bool $recursive = false): array
```

Finds only directories matching the given glob-style patterns.

Works like `find()` but excludes files from the results. Only
directories that match any of the provided masks are returned.

**Example:**
```php
use Phuture\Coherence\Files;

$dirs = Files::findDirectories('/path/to/project');
$srcDirs = Files::findDirectories('/path/to', 'src*', recursive: true);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$directory` | `string` | The directory to search in (default: '.') |
| `$masks` | `string\|array` | One or more glob patterns to match against (default: '*') |
| `$recursive` | `bool` | Whether to search subdirectories (default: false) |

**Returns** `array` — Array of directory paths matching the patterns

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the directory does not exist

**See also**

- `\Phuture\Coherence\Files::find()`
- `\Phuture\Coherence\Files::findFiles()`

### `findFiles()`

```php
public static function findFiles(string $directory = '.', string|array $masks = '*', bool $recursive = false): array
```

Finds only files matching the given glob-style patterns.

Works like `find()` but excludes directories from the results. Only
regular files that match any of the provided masks are returned.

**Example:**
```php
use Phuture\Coherence\Files;

$phpFiles = Files::findFiles('/path/to/src', '*.php');
$allCode = Files::findFiles('/path/to/project', ['*.php', '*.js'], recursive: true);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$directory` | `string` | The directory to search in (default: '.') |
| `$masks` | `string\|array` | One or more glob patterns to match against (default: '*') |
| `$recursive` | `bool` | Whether to search subdirectories (default: false) |

**Returns** `array` — Array of file paths matching the patterns

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the directory does not exist

**See also**

- `\Phuture\Coherence\Files::find()`
- `\Phuture\Coherence\Files::findDirectories()`

### `getLink()`

```php
public static function getLink(string $path): string
```

Returns the target of a symbolic link.

Returns the path that the symbolic link points to. The returned path
may be relative or absolute depending on how the link was created.

**Example:**
```php
use Phuture\Coherence\Files;

$target = Files::getLink('/path/to/symlink');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The symbolic link path |

**Returns** `string` — The target path that the link points to

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path is not a symbolic link or cannot be read

**See also**

- `\Phuture\Coherence\Files::isLink()`
- `\Phuture\Coherence\Files::link()`

### `isAbsolute()`

```php
public static function isAbsolute(string $path): bool
```

Determines whether a path is absolute.

An absolute path starts with a forward slash on Unix systems or a drive
letter followed by a colon on Windows (for example, `C:/`).

**Example:**
```php
use Phuture\Coherence\Files;

Files::isAbsolute('/usr/local/bin'); // true
Files::isAbsolute('relative/path'); // false
Files::isAbsolute('C:/Windows'); // true
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The path to check |

**Returns** `bool` — True when the path is absolute, false when it is relative

**See also**

- `\Phuture\Coherence\Files::normalizePath()`
- `\Phuture\Coherence\Files::joinPaths()`

### `isDirectory()`

```php
public static function isDirectory(string $path): bool
```

Determines whether the given path is a directory.

Returns false for regular files, symlinks pointing to files, and
non-existent paths.

**Example:**
```php
use Phuture\Coherence\Files;

Files::isDirectory('/path/to/directory'); // true
Files::isDirectory('/path/to/file.txt'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The path to check |

**Returns** `bool` — True when the path is a directory

**See also**

- `\Phuture\Coherence\Files::isFile()`
- `\Phuture\Coherence\Files::exists()`

### `isEmpty()`

```php
public static function isEmpty(string $path): bool
```

Determines whether a directory is empty (contains no files or subdirectories).

Returns true when the directory exists and contains no entries. Returns
false when the directory contains at least one file or subdirectory.
Throws when the path is not a valid directory.

**Example:**
```php
use Phuture\Coherence\Files;

Files::isEmpty('/path/to/empty/dir'); // true
Files::isEmpty('/path/to/full/dir'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The directory path to check |

**Returns** `bool` — True when the directory is empty, false when it contains entries

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the path is not a directory

**See also**

- `\Phuture\Coherence\Files::isDirectory()`
- `\Phuture\Coherence\Files::listing()`

### `isFile()`

```php
public static function isFile(string $path): bool
```

Determines whether the given path is a regular file (not a directory).

Returns false for directories, symlinks pointing to directories, and
non-existent paths.

**Example:**
```php
use Phuture\Coherence\Files;

Files::isFile('/path/to/file.txt'); // true
Files::isFile('/path/to/directory'); // false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The path to check |

**Returns** `bool` — True when the path is a regular file

**See also**

- `\Phuture\Coherence\Files::isDirectory()`
- `\Phuture\Coherence\Files::exists()`

### `isLink()`

```php
public static function isLink(string $path): bool
```

Determines whether the given path is a symbolic link.

**Example:**
```php
use Phuture\Coherence\Files;

Files::isLink('/path/to/symlink'); // true or false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The path to check |

**Returns** `bool` — True when the path is a symbolic link

**See also**

- `\Phuture\Coherence\Files::link()`
- `\Phuture\Coherence\Files::getLink()`
- `\Phuture\Coherence\Files::unlink()`

### `isLocked()`

```php
public static function isLocked(string $path): bool
```

Determines whether a file has an exclusive lock.

Attempts to acquire a non-blocking shared lock on the file. When the
lock cannot be acquired because another process holds an exclusive
lock, returns true.

**Example:**
```php
use Phuture\Coherence\Files;

if (Files::isLocked('/path/to/file.txt')) {
    echo 'File is locked by another process';
}
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to check |

**Returns** `bool` — True when the file appears to be exclusively locked

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or cannot be opened

**See also**

- `\Phuture\Coherence\Files::write()`

### `isReadable()`

```php
public static function isReadable(string $path): bool
```

Determines whether a file or directory is readable.

**Example:**
```php
use Phuture\Coherence\Files;

Files::isReadable('/path/to/file.txt'); // true or false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The path to check |

**Returns** `bool` — True when the path exists and is readable

**See also**

- `\Phuture\Coherence\Files::isWritable()`

### `isWritable()`

```php
public static function isWritable(string $path): bool
```

Determines whether a file or directory is writable.

**Example:**
```php
use Phuture\Coherence\Files;

Files::isWritable('/path/to/file.txt'); // true or false
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The path to check |

**Returns** `bool` — True when the path exists and is writable

**See also**

- `\Phuture\Coherence\Files::isReadable()`

### `joinPaths()`

```php
public static function joinPaths(string ...$segments): string
```

Joins multiple path segments into a single normalized path.

Segments are joined with forward slashes and the resulting path is
normalized to resolve `.` and `..` references. Trailing slashes on
individual segments are handled correctly.

**Example:**
```php
use Phuture\Coherence\Files;

Files::joinPaths('a', 'b', 'file.txt'); // 'a/b/file.txt'
Files::joinPaths('/a/', '/b/'); // '/a/b/'
Files::joinPaths('/a/', '/../b'); // '/b'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `...$segments` | `string` | The path segments to join together |

**Returns** `string` — The joined and normalized path

**See also**

- `\Phuture\Coherence\Files::normalizePath()`
- `\Phuture\Coherence\Files::isAbsolute()`

### `lastModified()`

```php
public static function lastModified(string $path): int
```

Returns the last modification time of a file as a Unix timestamp.

The timestamp represents the number of seconds since the Unix epoch
(January 1, 1970, 00:00:00 UTC) when the file was last modified.

**Example:**
```php
use Phuture\Coherence\Files;

$timestamp = Files::lastModified('/path/to/file.txt');
echo date('Y-m-d H:i:s', $timestamp);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to check |

**Returns** `int` — The last modification time as a Unix timestamp

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or the time cannot be read

**See also**

- `\Phuture\Coherence\Files::size()`

### `link()`

```php
public static function link(string $target, string $link): void
```

Creates a symbolic link from the target to the link path.

**Example:**
```php
use Phuture\Coherence\Files;

Files::link('/path/to/target', '/path/to/symlink');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$target` | `string` | The path that the link will point to |
| `$link` | `string` | The path where the symbolic link will be created |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the link cannot be created

**See also**

- `\Phuture\Coherence\Files::isLink()`
- `\Phuture\Coherence\Files::getLink()`
- `\Phuture\Coherence\Files::unlink()`

### `listing()`

```php
public static function listing(string $path, string|callable|null $filter = null): array
```

Lists the contents of a directory, optionally filtered by a pattern or callback.

Returns an array of file and directory paths within the specified directory.
When `$filter` is a string, only entries matching the glob pattern are included.
When `$filter` is a callable, it receives each entry's full path as the first
argument and the entry name as the second argument, and must return true to include it.

**Example:**
```php
use Phuture\Coherence\Files;

$all = Files::listing('/path/to/dir');
$phpFiles = Files::listing('/path/to/dir', '*.php');
$largeFiles = Files::listing('/path/to/dir', fn($path, $name) => filesize($path) > 1024);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The directory path to list |
| `$filter` | `string\|callable\|null` | A glob pattern string, a callback function, or null for no filtering (default: null). The callback has the signature `function (string $fullPath, string $entryName): bool` |

**Returns** `array` — Array of file and directory paths within the directory

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When the path is not a directory
- `\Phuture\Coherence\Exception\RuntimeException` — When the directory cannot be opened

**See also**

- `\Phuture\Coherence\Files::findFiles()`
- `\Phuture\Coherence\Files::isEmpty()`

### `makeWritable()`

```php
public static function makeWritable(string $path, int $directoryMode = 0777, int $fileMode = 0666): void
```

Sets file and directory permissions to make a path writable.

When the path points to a directory, permissions are applied recursively to
all files and subdirectories within it. Directories receive `$directoryMode`
and files receive `$fileMode`.

**Example:**
```php
use Phuture\Coherence\Files;

Files::makeWritable('/path/to/file.txt');
Files::makeWritable('/path/to/directory', 0755, 0644);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file or directory path to make writable |
| `$directoryMode` | `int` | The permission mode for directories (default: 0777) |
| `$fileMode` | `int` | The permission mode for files (default: 0666) |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path does not exist or permissions cannot be changed

**See also**

- `\Phuture\Coherence\Files::chmod()`

### `mimeType()`

```php
public static function mimeType(string $path): string
```

Returns the MIME type of a file detected from the file's content.

Uses the system's MIME database to determine the file type by examining
the file's actual content rather than relying on the file extension. This
provides a more accurate result than extension-based detection.

**Example:**
```php
use Phuture\Coherence\Files;

Files::mimeType('/path/to/image.png'); // 'image/png'
Files::mimeType('/path/to/document.pdf'); // 'application/pdf'
Files::mimeType('/path/to/script.php'); // 'text/x-php'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to detect the MIME type for |

**Returns** `string` — The MIME type of the file (for example, 'text/plain', 'image/png')

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or the MIME type cannot be detected

**See also**

- `\Phuture\Coherence\Files::extension()`

### `move()`

```php
public static function move(string $source, string $destination, bool $overwrite = true): void
```

Moves a file or directory to a new location.

This is equivalent to renaming the path. By default, existing files at the
destination are overwritten. When `$overwrite` is false and the destination
already exists, a `\Phuture\Coherence\Exception\RuntimeException` is thrown.

**Example:**
```php
use Phuture\Coherence\Files;

Files::move('/path/to/old.txt', '/path/to/new.txt');
Files::move('/path/to/old_dir', '/path/to/new_dir');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$source` | `string` | The current file or directory path |
| `$destination` | `string` | The new file or directory path |
| `$overwrite` | `bool` | Whether to overwrite existing files at the destination (default: true) |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the source does not exist, the destination cannot be written, or the move fails

**See also**

- `\Phuture\Coherence\Files::copy()`
- `\Phuture\Coherence\Files::rename()`

### `name()`

```php
public static function name(string $path, bool $includeExtension = true): string
```

Returns the name of a file or directory from a path.

By default, returns the full basename including the extension. When
`$includeExtension` is false, the extension is stripped from the result.

**Example:**
```php
use Phuture\Coherence\Files;

Files::name('/path/to/file.txt'); // 'file.txt'
Files::name('/path/to/file.txt', includeExtension: false); // 'file'
Files::name('/path/to/directory/'); // 'directory'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to extract the name from |
| `$includeExtension` | `bool` | Whether to include the file extension in the result (default: true) |

**Returns** `string` — The name of the file or directory without the parent path

**See also**

- `\Phuture\Coherence\Files::directory()`
- `\Phuture\Coherence\Files::extension()`

### `normalizePath()`

```php
public static function normalizePath(string $path): string
```

Normalizes a path by resolving `.` and `..` references and converting slashes.

Removes `.` segments, resolves `..` by removing the preceding directory,
and converts all directory separators to the system's standard separator.
A trailing slash is preserved only when the original path ends with a separator.

**Example:**
```php
use Phuture\Coherence\Files;

Files::normalizePath('/file/.'); // '/file'
Files::normalizePath('\\file\\..'); // '/'
Files::normalizePath('/file/../..'); // '/..'
Files::normalizePath('file/../../bar'); // '../bar'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The path to normalize |

**Returns** `string` — The normalized path using the system's directory separator

**See also**

- `\Phuture\Coherence\Files::joinPaths()`
- `\Phuture\Coherence\Files::unixSlashes()`

### `of()`

```php
public static function of(string $path): Type\Files
```

Creates a fluent wrapper around the given file path for method chaining.

Returns a `\Phuture\Coherence\Type\Files` instance that wraps the provided
file path and exposes chainable file manipulation methods alongside the
`\Phuture\Coherence\Interface\Fileable` inspection methods.

The path must point to an existing file. Directories are not accepted.
The path is resolved to its full absolute real path before being passed
to the wrapper.

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

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to wrap for fluent operations |

**Returns** `\Phuture\Coherence\Type\Files` — A fluent wrapper instance that enables method chaining

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path does not exist or is a directory

**See also**

- `\Phuture\Coherence\Type\Files` — For the fluent wrapper implementation

### `platformSlashes()`

```php
public static function platformSlashes(string $path): string
```

Converts all directory separators in a path to the current platform's standard.

On Windows, backslashes are used. On all other platforms, forward slashes
are used.

**Example:**
```php
use Phuture\Coherence\Files;

// On Linux/macOS:
Files::platformSlashes('path\\to\\file.txt'); // 'path/to/file.txt'
// On Windows:
Files::platformSlashes('path/to/file.txt'); // 'path\to\file.txt'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The path to convert |

**Returns** `string` — The path with platform-specific slashes

**See also**

- `\Phuture\Coherence\Files::unixSlashes()`

### `prepend()`

```php
public static function prepend(string $path, string $content): void
```

Prepends content to the beginning of an existing file.

When the file does not exist, it is created with the given content.
Parent directories are created automatically when they do not exist.

**Example:**
```php
use Phuture\Coherence\Files;

Files::write('/path/to/file.txt', 'Original content');
Files::prepend('/path/to/file.txt', 'Header: ');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to prepend to |
| `$content` | `string` | The content to prepend to the file |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be written

**See also**

- `\Phuture\Coherence\Files::write()`
- `\Phuture\Coherence\Files::append()`

### `read()`

```php
public static function read(string $path): string
```

Reads and returns the entire contents of a file.

Loads the complete file contents into a string. For large files, consider
using `readLines()` which processes the file line by line without loading
it all into memory at once.

**Example:**
```php
use Phuture\Coherence\Files;

$content = Files::read('/path/to/file.txt');
echo $content;
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to read |

**Returns** `string` — The complete contents of the file

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or cannot be read

**See also**

- `\Phuture\Coherence\Files::readLines()`
- `\Phuture\Coherence\Files::write()`

### `readLines()`

```php
public static function readLines(string $path, bool $stripNewLines = true): Generator
```

Reads a file line by line, yielding each line as a string.

Returns a generator that produces one line at a time, making it
memory-efficient for large files. By default, trailing newline characters
(`\r` and `\n`) are stripped from each line.

**Example:**
```php
use Phuture\Coherence\Files;

foreach (Files::readLines('/path/to/file.txt') as $lineNumber => $line) {
    echo "Line {$lineNumber}: {$line}\n";
}
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to read |
| `$stripNewLines` | `bool` | Whether to remove trailing `\r` and `\n` from each line (default: true) |

**Returns** `Generator<int,` — string> A generator yielding line numbers (zero-based) and line content

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or cannot be opened

**See also**

- `\Phuture\Coherence\Files::read()`

### `rename()`

```php
public static function rename(string $path, string $newName, bool $overwrite = true): void
```

Renames a file or directory to a new name within the same directory.

Unlike `move()`, which accepts a full destination path, this method takes
only the new name and keeps the file in its current parent directory. When
`$overwrite` is false and a file with the new name already exists, a
`\Phuture\Coherence\Exception\RuntimeException` is thrown.

**Example:**
```php
use Phuture\Coherence\Files;

Files::rename('/path/to/old.txt', 'new.txt');
Files::rename('/path/to/old_dir', 'new_dir', overwrite: false);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The current file or directory path |
| `$newName` | `string` | The new name (without directory path) |
| `$overwrite` | `bool` | Whether to overwrite an existing file with the new name (default: true) |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path does not exist, the new name is empty, or the rename fails

**See also**

- `\Phuture\Coherence\Files::move()`
- `\Phuture\Coherence\Files::name()`

### `replaceInFile()`

```php
public static function replaceInFile(string $path, string|array $search, string|array $replace): void
```

Replaces all occurrences of a search string with a replacement string within a file.

Reads the file, performs the replacement, and writes the result back.
When `$search` is an array, each occurrence of any search value is
replaced with the corresponding value in `$replace`.

**Example:**
```php
use Phuture\Coherence\Files;

Files::replaceInFile('/path/to/config.php', 'old-value', 'new-value');
Files::replaceInFile('/path/to/template.html', ['{{name}}', '{{email}}'], ['John', 'john@example.com']);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to modify |
| `$search` | `string\|array` | The value or values to search for |
| `$replace` | `string\|array` | The replacement value or values |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file does not exist or cannot be written

**See also**

- `\Phuture\Coherence\Files::write()`
- `\Phuture\Coherence\Files::read()`

### `size()`

```php
public static function size(string $path): int
```

Returns the size of a file or directory in bytes.

When the path points to a file, returns its exact size. When the path
points to a directory, returns the total combined size of all files
within it recursively. Throws when the path does not exist.

**Example:**
```php
use Phuture\Coherence\Files;

$fileBytes = Files::size('/path/to/file.txt');
$dirBytes = Files::size('/path/to/directory');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file or directory path to check |

**Returns** `int` — The size in bytes

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path does not exist or the size cannot be read

**See also**

- `\Phuture\Coherence\Files::lastModified()`
- `\Phuture\Coherence\Files::mimeType()`

### `unixSlashes()`

```php
public static function unixSlashes(string $path): string
```

Converts all directory separators in a path to forward slashes (Unix style).

Useful for normalizing paths for display or for use in contexts that
require forward slashes regardless of the operating system.

**Example:**
```php
use Phuture\Coherence\Files;

Files::unixSlashes('path\\to\\file.txt'); // 'path/to/file.txt'
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The path to convert |

**Returns** `string` — The path with forward slashes

**See also**

- `\Phuture\Coherence\Files::platformSlashes()`
- `\Phuture\Coherence\Files::normalizePath()`

### `unlink()`

```php
public static function unlink(string $path): void
```

Removes a symbolic link.

Validates that the path is a symbolic link before removing it. Throws
when the path is not a symbolic link or cannot be removed.

**Example:**
```php
use Phuture\Coherence\Files;

Files::unlink('/path/to/symlink');
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The symbolic link path to remove |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the path is not a symbolic link or cannot be removed

**See also**

- `\Phuture\Coherence\Files::isLink()`
- `\Phuture\Coherence\Files::link()`

### `upload()`

```php
public static function upload(string $key, string $destination, ?array $files = null, array $options = []): array|false
```

Handles file uploads from an HTTP request, saving files to a destination directory.

Supports both single file uploads and multiple file uploads (when the request
field uses array notation like `files[]`). When the upload field contains
multiple files (detected automatically), all files are saved and an array of
file information arrays is returned.

When the first parameter is `"files"` and the request contains `files[]`,
the method detects the array structure and processes all uploaded files.

Optional validation rules can be passed via the `$options` array. When a file
fails validation in non-strict mode (default), it is silently skipped. When
`'strict'` is `true`, an `InvalidArgumentException` is thrown for the first
invalid file. MIME type validation uses the actual file content, not the
client-provided `type` field.

**Example:**
```php
use Phuture\Coherence\Files;

// Single file upload: <input type="file" name="avatar">
$result = Files::upload('avatar', '/path/to/uploads');
// Returns: ['name' => 'photo.jpg', 'path' => '...', 'size' => 12345, ...]

// Multiple file upload: <input type="file" name="files[]" multiple>
$results = Files::upload('files', '/path/to/uploads');
// Returns: [['name' => 'a.jpg', ...], ['name' => 'b.jpg', ...]]

// With validation
$result = Files::upload('avatar', '/uploads', null, [
    'extensions' => ['jpg', 'png', 'gif'],
    'mimeTypes'  => ['image/jpeg', 'image/png', 'image/gif'],
    'maxSize'    => 2 * 1024 * 1024, // 2 MB
]);

// Strict mode: throws on invalid file
Files::upload('document', '/uploads', null, [
    'extensions' => ['pdf', 'docx'],
    'strict'     => true,
]);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$key` | `string` | The form field name from the upload request |
| `$destination` | `string` | The directory path where uploaded files should be saved |
| `$files` | `array\|null` | The files array to use (default: null, which uses $_FILES) |
| `$options` | `array` | Optional validation rules with keys: - `extensions`: `string[]` of allowed extensions without dots (e.g. `['jpg', 'png']`) - `mimeTypes`: `string[]` of allowed MIME types, checked against actual file content - `maxSize`: `int` maximum file size in bytes - `strict`: `bool` when true, throws on validation failure instead of skipping (default: false) |

**Returns** `array|false` — A single file info array, an array of file info arrays for multiple uploads, or false on failure

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When a file fails validation in strict mode

**See also**

- `\Phuture\Coherence\Files::move()`
- `\Phuture\Coherence\Files::create()`

### `write()`

```php
public static function write(string $path, string $content, int $mode = 0666, bool $lock = false): void
```

Writes content to a file, creating the file if it does not exist.

If the file already exists, its contents are replaced entirely. Parent
directories are created automatically when they do not exist.

When `$lock` is true, an exclusive lock is acquired before writing to
prevent concurrent writes from corrupting the file.

**Example:**
```php
use Phuture\Coherence\Files;

Files::write('/path/to/file.txt', 'Hello, World!');
Files::write('/path/to/new/file.txt', 'New content', 0644);
Files::write('/path/to/file.txt', 'Locked write', lock: true);
```

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The file path to write to |
| `$content` | `string` | The content to write to the file |
| `$mode` | `int` | The permission mode for the file (default: 0666) |
| `$lock` | `bool` | Whether to acquire an exclusive lock before writing (default: false) |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — When the file cannot be written

**See also**

- `\Phuture\Coherence\Files::read()`
- `\Phuture\Coherence\Files::append()`
- `\Phuture\Coherence\Files::prepend()`

### `compressGzip()`

```php
private static function compressGzip(string $source, string $destination): void
```

Compresses a file or directory into a GZIP-compressed TAR archive (.tar.gz).

First builds a temporary TAR archive using `PharData`, then compresses it
with `gzencode()` and writes the result to the destination path. The temporary
TAR file is always removed, even if an error occurs.

| Parameter | Type | Description |
| --- | --- | --- |
| `$source` | `string` | The file or directory to compress |
| `$destination` | `string` | The path where the .tar.gz archive will be saved |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — If the archive cannot be created or written

### `compressTar()`

```php
private static function compressTar(string $source, string $destination): void
```

Compresses a file or directory into a TAR archive using PHP's built-in PharData.

When the source is a directory, all its contents are added using
`PharData::buildFromDirectory()`. When the source is a single file,
it is added under its base name.

| Parameter | Type | Description |
| --- | --- | --- |
| `$source` | `string` | The file or directory path to archive |
| `$destination` | `string` | The path where the TAR archive will be saved |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — If the archive cannot be created

### `compressZip()`

```php
private static function compressZip(string $source, string $destination): void
```

Compresses a file or directory into a ZIP archive using nelexa/zip.

When the source is a directory, all its contents are added recursively.
When the source is a single file, it is added under its base name.

| Parameter | Type | Description |
| --- | --- | --- |
| `$source` | `string` | The file or directory path to compress |
| `$destination` | `string` | The path where the ZIP archive will be saved |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — If the archive cannot be created or saved

### `copyDirectory()`

```php
private static function copyDirectory(string $source, string $destination, bool $overwrite): void
```

Copies a directory recursively to a new location.

| Parameter | Type | Description |
| --- | --- | --- |
| `$source` | `string` | The source directory to copy |
| `$destination` | `string` | The destination directory path |
| `$overwrite` | `bool` | Whether to overwrite existing files |

### `createDirectory()`

```php
private static function createDirectory(string $path, int $mode = 0777): void
```

Creates a directory at the given path, including any parent directories that do not exist.

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The directory path to create |
| `$mode` | `int` | The permission mode for the directory (default: 0777) |

### `decompressGzip()`

```php
private static function decompressGzip(string $archive, string $destination): void
```

Extracts a GZIP-compressed TAR archive (.tar.gz) to a destination directory.

Decompresses the archive with `gzdecode()` into a temporary TAR file, then
uses `PharData` to extract its contents to the destination directory. The
temporary TAR file is always removed, even if an error occurs.

| Parameter | Type | Description |
| --- | --- | --- |
| `$archive` | `string` | The path to the .tar.gz archive to extract |
| `$destination` | `string` | The directory where the archive contents will be extracted |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — If the archive cannot be read, decompressed, or extracted

### `decompressTar()`

```php
private static function decompressTar(string $archive, string $destination): void
```

Extracts a TAR archive to a destination directory using PHP's built-in PharData.

Creates the destination directory if it does not already exist,
then extracts all entries from the archive into it.

| Parameter | Type | Description |
| --- | --- | --- |
| `$archive` | `string` | The path to the TAR archive to extract |
| `$destination` | `string` | The directory where the archive contents will be extracted |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — If the archive cannot be opened or extracted

### `decompressZip()`

```php
private static function decompressZip(string $archive, string $destination): void
```

Extracts a ZIP archive to a destination directory using nelexa/zip.

Creates the destination directory if it does not already exist,
then extracts all entries from the archive into it.

| Parameter | Type | Description |
| --- | --- | --- |
| `$archive` | `string` | The path to the ZIP archive to extract |
| `$destination` | `string` | The directory where the archive contents will be extracted |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — If the archive cannot be opened or extracted

### `deleteDirectory()`

```php
private static function deleteDirectory(string $path): void
```

Deletes a directory and all of its contents recursively.

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The directory to delete |

### `directorySize()`

```php
private static function directorySize(string $path): int
```

Calculates the total size of all files in a directory recursively.

| Parameter | Type | Description |
| --- | --- | --- |
| `$path` | `string` | The directory path to calculate size for |

**Returns** `int` — The total size in bytes

### `findByType()`

```php
private static function findByType(string|array $masks, string $directory, bool $recursive, ?string $type): array
```

Finds files and/or directories by type, matching the given masks.

| Parameter | Type | Description |
| --- | --- | --- |
| `$masks` | `string\|array` | The glob patterns to match |
| `$directory` | `string` | The directory to search in |
| `$recursive` | `bool` | Whether to search subdirectories |
| `$type` | `string\|null` | The type filter: 'file', 'dir', or null for both |

**Returns** `array` — Array of matching paths

### `handleLastError()`

```php
private static function handleLastError(string $prefix): never
```

Throws a RuntimeException that includes the last PHP-level error message.

Use this after calling `error_clear_last()` and then a PHP filesystem
function that may fail. The actual PHP error (e.g. "Permission denied")
is appended to `$prefix` so the developer can see the real reason.

| Parameter | Type | Description |
| --- | --- | --- |
| `$prefix` | `string` | The base exception message |

**Throws**

- `\Phuture\Coherence\Exception\RuntimeException` — Always

### `handleMultipleUpload()`

```php
private static function handleMultipleUpload(array $files, string $destination, array $options): array
```

Handles a multiple file upload by processing each file individually.

| Parameter | Type | Description |
| --- | --- | --- |
| `$files` | `array` | The upload information array with array values from $_FILES |
| `$destination` | `string` | The directory to save files in |
| `$options` | `array` | Validation options (see {@see \Phuture\Coherence\Files::upload()}) |

**Returns** `array` — Array of file information arrays for each successfully uploaded file

### `handleSingleUpload()`

```php
private static function handleSingleUpload(array $file, string $destination, array $options): array|false
```

Handles a single file upload by moving it to the destination directory.

Runs validation checks (extension, MIME type, file size) when the
corresponding options are provided. In non-strict mode invalid files
return false; in strict mode an InvalidArgumentException is thrown.

| Parameter | Type | Description |
| --- | --- | --- |
| `$file` | `array` | The upload information array from $_FILES |
| `$destination` | `string` | The directory to save the file in |
| `$options` | `array` | Validation options (see {@see \Phuture\Coherence\Files::upload()}) |

**Returns** `array|false` — File information array or false on failure

**Throws**

- `\Phuture\Coherence\Exception\InvalidArgumentException` — When validation fails in strict mode

### `matchesAnyMask()`

```php
private static function matchesAnyMask(string $filename, array $masks): bool
```

Checks whether a filename matches any of the given glob patterns.

| Parameter | Type | Description |
| --- | --- | --- |
| `$filename` | `string` | The filename to test |
| `$masks` | `array` | The glob patterns to match against |

**Returns** `bool` — True when the filename matches at least one pattern

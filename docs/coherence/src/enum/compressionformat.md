# CompressionFormat

`Phuture\Coherence\Enum\CompressionFormat`

```php
enum CompressionFormat
```

Enumeration for file compression and archive formats.

This enum defines the supported archive and compression formats for
\Phuture\Coherence\Files::compress() and \Phuture\Coherence\Files::decompress().
Each case maps to a specific format and its underlying implementation.

## Cases

### `Gzip`

```php
case Gzip
```

GZIP-compressed TAR archive format (.tar.gz).

Supports compressing both files and directories by first building a TAR
archive and then applying GZIP compression. Uses PHP's built-in PharData
class for TAR creation and the zlib extension for GZIP compression.

### `Tar`

```php
case Tar
```

TAR archive format (.tar).

Supports archiving files and directories without compression.
Uses PHP's built-in PharData class.

### `Zip`

```php
case Zip
```

ZIP archive format (.zip).

Supports compressing files and directories. Uses the nelexa/zip
pure-PHP library, so no system zip command is required.

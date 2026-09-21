# UuidVersion

`Phuture\Coherence\Enum\UuidVersion`

```php
enum UuidVersion
```

Enumeration for UUID generation version selection.

This enum defines the UUID versions available when generating unique identifiers.
Use it with `\Phuture\Coherence\Strings::uuid()` to select which version to generate.

## Cases

### `V4`

```php
case V4
```

Generate a version 4 UUID (random).

UUID v4 uses cryptographically secure random data for all bits except the
version and variant fields. This is the most widely used UUID version and
is suitable for generating unique identifiers without requiring coordination.

### `V7`

```php
case V7
```

Generate a version 7 UUID (time-ordered).

UUID v7 combines a Unix timestamp in milliseconds with random data, producing
identifiers that are both unique and sortable by creation time. The first 48 bits
encode the timestamp, making these UUIDs naturally ordered chronologically.

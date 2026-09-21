# Phuture Framework

**Phuture** is a modern PHP framework built around a single conviction: every
line of code must be explicit, deterministic, and fully traceable in its
behavior.

It is made of small, focused packages that can be used on their own or
together. Each one is a standalone Composer package with its own repository,
its own tests, and its own documentation — you take what you need and nothing
else comes along for the ride.

## What it does

Phuture gives you the foundations an application is written on — the helpers,
the types, and the compatibility layer — without the magic that usually comes
with them:

- **Explicitness over convention.** A method does exactly what its signature
  and documentation describe, nothing more.
- **Determinism as a requirement.** The same input produces the same output on
  every invocation, runtime, and environment.
- **No magic.** No method interception, no implicit injection, no
  convention-based resolution. Every execution path can be read linearly in the
  source.
- **Traceability.** Every call navigates straight to its implementation, and
  every value can be followed from origin to transformation by static analysis.

The [Developer Workflow Guide](/workflow) sets out these principles in full,
along with the coding standards and architectural patterns every package
follows.

## Current status

> **Phuture is in Alpha and under active development.**

The framework is usable and tested, but it is not finished. Until the first
stable release:

- Public APIs may change between releases, including in breaking ways.
- Packages may be added, renamed, or merged as the framework takes shape.
- Documentation tracks the `main` branch of each repository, so it describes
  what exists today rather than what a tagged release holds.

Pinning an exact version in your `composer.json` is strongly recommended if you
are building on Phuture right now.

## Packages

### Coherence

A coherent collection of utility helpers for PHP — consistent, fluent, and
chainable.

Coherence covers the work every application repeats: [arrays](/coherence/src/arrays),
[callables](/coherence/src/callables), [dates](/coherence/src/dates),
[files](/coherence/src/files), [hashing and passwords](/coherence/src/hash),
[HTML](/coherence/src/html), [numbers](/coherence/src/numbers),
[runtime reflection](/coherence/src/reflector), and
[strings](/coherence/src/strings). Alongside the static helpers it ships
[typed wrappers](/coherence/src/type/arrays) for fluent chaining, together with
the [enums](/coherence/src/enum/arraycomparator),
[interfaces](/coherence/src/interface/arrayable),
[exceptions](/coherence/src/exception/badmethodcallexception), and
[support classes](/coherence/src/support/argumentextractor) they are built on.

```bash
composer require phuture/coherence
```

**[Read the Coherence documentation →](/coherence)**

### Continuum

A forward-compatibility layer bringing tomorrow's PHP features to today's
runtimes.

Continuum extends [Symfony Polyfill](https://github.com/symfony/polyfill) with
additional functions, constants, classes, enums, and attributes, so code
written against a newer PHP keeps working on an older one. It polyfills most of
the common additions from PHP 8.1 through 8.6 and runs on PHP 8.0 or later,
falling back gracefully where a feature cannot be reproduced faithfully.

```bash
composer require phuture/continuum
```

**[Read the Continuum documentation →](/continuum)**

## Contributing

Phuture is open source and contributions are welcome. Start with the
[Contribution Guide](/contributing) for how to propose and submit a change, and
read the [Developer Workflow Guide](/workflow) before writing code — it is the
standard every pull request is reviewed against.

Everyone taking part is expected to follow the
[Code of Conduct](/code_of_conduct).

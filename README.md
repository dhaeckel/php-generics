# haeckel/php-generics

haeckel/generics is a php Library for dealing with generic data structures.
It aims for simplicity and being fully typesafe at runtime, so unit tests will make errors visible.
Uses PHPStan's generic syntax for type documentation and static analysis.

## Features
- [Types](src/Type) classes that denote php types for usage, where the native type system
    cannot be used, e.g. in terms of generics for parameters, because they violate the LSP rules.
    All type classes can be used to type check at runtime and you can get a string representation
    that matches the native php notation.
- [Filters](src/Filter) the filter interfaces enforce callable signatures, by specifying the
    __invoke() method signature. These are used to filter generic data structures
    (e.g. find a subset of a collection) or to remove specific elements.
- [Struct](src/Struct/) contains standard data structures that support generic usage.
    There is always an interface and an abstract base class for these structures.

## PHPStan
This project uses the max level of [PHPStan](https://phpstan.org/) and
[phpstan/phpstan-strict-rules](https://github.com/phpstan/phpstan-strict-rules)
to ensure the code follows a good standard.
Here are the places, where @phpstan-ignore is used to step around a check
(cf. unsafe mode in rust or go, where you can opt out of compiler guarantees).

- [equal.notAllowed (necessary for object cmp)](src/Struct/BaseCollection.php) in method remove()

## Installation

```sh
composer require haeckel/generics
```

## Usage

See the tests Folder for runnable implementations.

## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.

[Source Code](https://github.com/dhaeckel/php-generics)

## Support

Let us know if you have issues.

[Issue Tracker](https://github.com/dhaeckel/php-generics/issues)

## License

[LGPL-3.0-or-later](COPYING.LESSER)

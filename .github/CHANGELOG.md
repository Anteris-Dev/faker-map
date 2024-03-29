# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v2.0.0] - 2021-04-16

## Added
- Various performance benefits that make this version over 8x faster than the previous version.

## Changed
- Most of the API to be more simple and straightforward.

## Removed
- Support for any PHP versions below 8.

## [v1.1.0] - 2020-12-28
### Added
- `type()` method to `FakerResolver::class`. This allows you to specify the type that the generated value should equal.

## [v1.0.1] - 2020-12-28
### Added
- Type hinting for `FakerResolver::class`.

### Fixed
- "Address" in dictionary was returning "safeAddress" which does not exist on Faker.

## [v1.0.0] - 2020-12-12

### Added
- `FakerResolver` class which will now make resolutions to Faker.
- Data types to the dictionary.
- Social security updates to dictionary.
- Pass through to faker so the `FakerMap` class can be used just like faker as well.

### Changed
- API used for generating values to be more consistent and allow fallback to default values.

## [v0.1.0] - 2020-12-04
### Added
- Initial release

[v1.0.1]: https://github.com/Anteris-Dev/faker-map/compare/v1.0.1...v1.1.0
[v1.0.1]: https://github.com/Anteris-Dev/faker-map/compare/v1.0.0...v1.0.1
[v1.0.0]: https://github.com/Anteris-Dev/faker-map/compare/v0.1.0...v1.0.0
[v0.1.0]: https://github.com/Anteris-Dev/faker-map/releases/tag/v0.1.0


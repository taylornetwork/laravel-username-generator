# Changelog

## [2.8] - UNRELEASED
### Added
- `increment_max_attempts` config option to avoid an infinite loop should increment make unique method get used.
- `isUnique` method to `BaseDriver`

### Changed
- FindSimilarUsernames now provides a public `getUsernameColumnName` method. 
- The `makeUnique` algorithm
  - Rather than simply counting the total number of similar usernames and adding 1 to it, we'll now handle it in stages - this would cause problems see #63.
  - Now we perform a check to see if the original algorithm works, then return if so.
  - If not, we use the list of similar usernames and get the one with the highest value, and add 1 to it, if unique, return it.
  - If not, we start at 0 and increment and check every value until we either get a unique one, or exhaust the `increment_max_attempts`

### Deprecated
- FindSimilarUsernames `getColumn` method in favour of `getUsernameColumnName` (this likely affects no one unless you're specifically overriding that trait.)

## [2.7] - 2022-07-07
### Changed
- FindSimilarUsernames no longer needs a `usernameColumn` property in the parent model if the column is different than `'username'` and the config file is not directly changed.

### Added
- Missing types for some properties.
- Driver and HandlesConfig contracts.
- getDriver() method in Generator class.

## [2.6.2] - 2022-02-21
### Security
- Possible SQL injection vulnerability, see [#54](https://github.com/taylornetwork/laravel-username-generator/pull/54)

### Fixed
- Bug where `findSimilarUsernames` would return an incorrect number of similarities when using the REGEXP function with a separator.

### Changed
- `prefer_regexp` config option by default is now `false`


## [2.6.1] - 2022-01-02
### Changed
- Added support for PHP 8.1
- Set minimum PHP version to 7.4


## [2.6] - 2021-08-17
### Added
- Added first and last hook for custom drivers
- Generator now supports multibyte characters (Cyrillic, etc.)
- Added options for converting to ascii and validating the input string
- Text will automatically be converted to ASCII by default

### Changed
- Moved the EmailDriver hook to first
- Convert case now happens second rather than first


## [2.5.1] - 2021-08-05
### Fixed
- Fixes issue where custom dictionary nouns and adjectives were not being used


## [2.5] - 2020-12-02
## Added
- Added maximum length check.
- Added ability for pre-filled usernames to go through generate process to allow for consistent username styles.
- Added checking for similar usernames using REGEXP or LIKE (LIKE is a fallback if REGEXP fails).
- Added a check if a username is unique as is before checking for similar ones.

## Changed
- Updated `composer.json` to support PHP 7.2 and above
- Updated readme for better Laravel 8+ quickstart


## [2.4] - 2020-11-11
## Changed
- Changed default User model from `App\User` to `App\Models\User` to mirror new Laravel versions (8.0+).
- Moved the adjective and noun word lists from the config file to a separate file, making the published config smaller and allowing you to create your own word lists if you wish.


## [2.3.2] - 2020-11-10
### Fixed
- Fixed bug where a model using the GeneratesUsernames without the specified column would throw an error rather than return a random username. See[#25](https://github.com/taylornetwork/laravel-username-generator/issues/25) 


## [2.3.1] - 2019-10-31
### Fixed
- Bug fix that was preventing username generation on unique usernames, using the FindSimilarUsernames trait when the username had found nothing similar. See[#23](https://github.com/taylornetwork/laravel-username-generator/issues/23)


## [2.3] - 2019-10-31
### Added
- Providing an empty string to the generator now randomly generates a random username from a list of a nouns and adjectives.


## [2.2.2] - 2019-06-01
### Fixed
- Fixed bug where setting a custom column in the model it wouldn't be respected. See [#16](https://github.com/taylornetwork/laravel-username-generator/issues/16)
- Fixed support for overriding the `getName` method from `GeneratesUsernames`


## [2.2.1] - 2019-05-23
### Changed
- Added support for more PHP versions.


## [2.2] - 2019-05-23
### Added
- Added support for minimum length check.


## [2.1] - 2019-05-20
### Changed
- Switch to driver based conversion rather than name.

### Added
- Support for email conversion through drivers.


## [2.0] - 2019-05-08
## Removed
- Support for `makeUsername` method

## Changed 
- `Generator` will now only accept an array of config as the optional constructing arguments

## Added
- `UsernameGenerator` facade


## [1.1.4] - 2019-02-17
### Fixed
- Issue where separators were added when trimming extra characters.


## [1.1.3] - 2019-02-11
### Fixed
- Fixes assignment error bug.


## [1.1.2] - 2018-10-23
### Fixed
- Bug fix for getting attributes in Laravel 5.7

## [1.1] - 2018-05-29
### Deprecated
- `makeUsername` method. Will be removed in v2
- `Generator` constructor accepting a name

### Changed
- `Generator` constructor accepts a name and config, name is deprecated.

### Added
- `generate` method to replace `makeUsername`
- `GeneratesUsernames` trait
- `generateFor` method


## [1.0.2] - 2018-04-30
### Added
- Support for Laravel 5.6


## [1.0] - 2017-09-21
### Added 
- Readme

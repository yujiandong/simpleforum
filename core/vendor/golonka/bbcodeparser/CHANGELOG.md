# Changelog

All Notable changes to `juiceland\BBCodeParser` will be documented in this file.

## v3.0.0 - 2019-07-30

Identical functionality compared to v2.3 but skipped support for PHP versions lower than v7.2.

- Bumped minimum PHP version to v7.2.
- Bumped phpunit version to ~8.

## v2.3.0 - 2019-07-30

This package was deleted from github some time ago because i couldn't find time to maintain it. Brought it back after some emails from different people that still used it. That motivated me to at least try and maintain it again and I will try to update some things over time. Sorry for the inconvenience, won't happen again!

### Added
- Added BBCode for sup, sub and small tags.

## v2.2.0 - 2015-09-07

### Added
- You can now strip all BBCode tags by using the ``stripBBCodeTags`` function.

## v2.1.0 - 2015-06-20

### Added
- Made ``parseCaseSensitive`` and ``parseCaseInsensitive`` functions to make parsing more readable.

## v2.0.0 - 2015-06-02

### Added
- Using PSR-4 instead of PSR-0
- Moved the ``arrayOnly`` and ``arrayExcept`` functions into a trait
- Minimum supported PHP version bumped to 5.4

### Fixed
- Renamed some tag names, mostly making them all lowercase
 - `` underLine -> underline ``
 - `` lineThrough -> linethrough ``
 - `` fontSize -> size ``
 - `` fontColor -> color ``
 - `` namedQuote -> namedquote ``
 - `` namedQuote -> namedquote ``
 - `` namedLink -> namedlink ``
 - `` orderedListNumerical -> orderedlistnumerical ``
 - `` orderedListAlpha -> orderedlistalpha ``
 - `` unorderedList -> unorderedlist ``
 - `` listItem -> listitem ``

### Removed
- The ``iterate`` property is removed. Unneeded after improvements in parsing method.
- Removed deprecated tags ``[ul]`` and ``[ol]``

## v1.4 - 2015-05-05

### Added
- Optional parameter enables or disables case insensitivity. Disabled by default.

## v1.3.0 - 2014-06-30

### Fixed
- The only/except functionally have been broken since like 1.1, but now it´s working. Better late then never!

## v1.2.7 - 2014-05-19

### Added
- A new iterate key is added to tags that typically could contain more tags of the same kind, like quotes.

### Fixed
- Problem where tags of the same kind would just parse the top level.

## v1.2.6 - 2014-05-17

### Fixed
- Fixed a problem where if a tag had a line break in them they wouldn't parse.

## v1.2.5 - 2014-05-15

### Fixed
- Improved most regex matches by removing unnecessary greediness.

## v1.2.0 - 2014-03-25

### Fixed
- Better syntax for lists.

## v1.1.0 - 2014-01-27

### Added
- Support for custom bbcode tags.

## v1.0.0 - 2013-11-07

Released the package.

# Changelog

All notable changes to Lock - Laravel will be documented in this file. This file follows the *[Keep a CHANGELOG](http://keepachangelog.com/)* standards.

## 0.3.0 - 2015-06-04

### Added

- Laravel 5.1 support ([#27](https://github.com/BeatSwitch/lock-laravel/pull/27))

## 0.2.1 - 2015-03-09

### Changed

- More stricter version constraints for Laravel 5 ([#20](https://github.com/BeatSwitch/lock-laravel/pull/20))

## 0.2.0 - 2015-03-08

### Added

- Laravel 5 support ([#15](https://github.com/BeatSwitch/lock-laravel/pull/17))

## 0.1.3 - 2015-01-15

### Fixed

- Fixed a bug with permission caching performance ([#8](https://github.com/BeatSwitch/lock-laravel/pull/8)) 

## 0.1.2 - 2015-01-13

### Changed

- Permissions are now cached at runtime. Permissions are only reset when permissions are stored or removed

### Fixed

- Fixed a bug where the guest caller had an incorrect id set

## 0.1.1 - 2014-12-17

### Fixed

- Implemented forgotten aliases

## 0.1.0 - 2014-12-17

### Added

- Added Scrutinizer config

### Fixed

- Use new `createFromData` method to support data objects
- Fix a bug where the default driver couldn't be changed

## 0.1.0-alpha.2 - 2014-12-09

### Added

- Added badges to the readme

### Fixed

- Fixed a critical bug in the config file

## 0.1.0-alpha.1 - 2014-12-08

Initial release.

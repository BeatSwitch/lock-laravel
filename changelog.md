# Changelog

All notable changes to Lock - Laravel will be documented in this file. This file follows the *[Keep a CHANGELOG](http://keepachangelog.com/)* standards.

## 0.5.0 - 2016-12-18

## Added

- Added a new `UserLock` class to handle the default Lock instance
- Added a new `InitLockAwareTrait` middleware to bootstrap the `LockAware` trait

## Changed

- PHP 5.6 and Laravel 5.3 are now the new minimum requirements
- Completely reworked the `LockServiceProvider` class
- `permissions` option in the acl only handles role configuration now
- Moved the `config` and `migrations` directories out of the src directory

## 0.4.2 - 2015-09-05

### Fixed

- Fixed a another class dependency issue ([#36](https://github.com/BeatSwitch/lock-laravel/pull/36))

### Changed

- Switched from Scrutinizer to Code Climate

## 0.4.1 - 2015-08-14

### Fixed

- Fixed a class dependency issue ([#34](https://github.com/BeatSwitch/lock-laravel/pull/34))

## 0.4.0 - 2015-08-09

### Changed

- Require PHP 5.5.9+
- Dropped support for Laravel 5.0
- Removed `lock` and `lock.manager` aliases from IoC container bindings

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

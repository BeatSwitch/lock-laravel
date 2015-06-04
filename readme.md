# Lock - Laravel 5 Driver

[![Build Status](https://img.shields.io/travis/BeatSwitch/lock-laravel/master.svg?style=flat-square)](https://travis-ci.org/BeatSwitch/lock-laravel)
[![Quality Score](https://img.shields.io/scrutinizer/g/BeatSwitch/lock-laravel.svg?style=flat-square)](https://scrutinizer-ci.com/g/BeatSwitch/lock-laravel)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/BeatSwitch/lock-laravel.svg?style=flat-square)](https://scrutinizer-ci.com/g/BeatSwitch/lock-laravel)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](license.md)
[![Packagist Version](https://img.shields.io/packagist/v/beatswitch/lock-laravel.svg?style=flat-square)](https://packagist.org/packages/beatswitch/lock-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/beatswitch/lock-laravel.svg?style=flat-square)](https://packagist.org/packages/beatswitch/lock-laravel)

Lock is a flexible, driver based Acl package for PHP 5.4+.

This package is a Laravel 5 driver for [Lock](https://github.com/BeatSwitch/lock). Check the documentation of Lock for more info.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
    - [Setting roles and aliases](#setting-roles-and-aliases)
    - [Setting permissions with the array driver](#setting-permissions-with-the-array-driver)
    - [Using the database driver](#using-the-database-driver)
    - [Using the facades](#using-the-facades)
    - [Using dependency injection](#using-dependency-injection)
- [Maintainer](#maintainer)
- [Contributing](#contributing)
- [Changelog](#changelog)
- [License](#license)

## Installation

Install this package through Composer.

```bash
$ composer require beatswitch/lock-laravel
```

Register the service provider in your `app.php` config file.

```php
'BeatSwitch\Lock\Integrations\Laravel\LockServiceProvider',
```

Register the facades in your `app.php` config file.

```php
'Lock' => 'BeatSwitch\Lock\Integrations\Laravel\Facades\Lock',
'LockManager' => 'BeatSwitch\Lock\Integrations\Laravel\Facades\LockManager',
```

Publish the configuration file. After publishing you can edit the configuration options at `config/lock.php`.

```bash
$ php artisan vendor:publish --provider="BeatSwitch\Lock\Integrations\Laravel\LockServiceProvider" --tag="config"
```

If you're using the database driver you should run the package's migrations. This will create the database table where all permissions will be stored.

```bash
$ php artisan vendor:publish --provider="BeatSwitch\Lock\Integrations\Laravel\LockServiceProvider" --tag="migrations"
$ php artisan migrate
```

Please read the main [Lock documentation](https://github.com/BeatSwitch/lock) for setting up the caller contract on your `User` model and for more in-depth documentation on how Lock works.

Also make sure to set the `BeatSwitch\Lock\LockAware` trait on your `User` model. That way your authenticated user will receive a Lock instance of itself so you can call permissions directly from your user object. If no user is authenticated, a `SimpleCaller` object will be bootstrapped which has the `guest` role. That way you can still use the `Lock` facade.

## Usage

### Setting roles and aliases

You can register roles and aliases beforehand through the `permissions` callback in the config file. Here you can say which actions should be grouped under an alias or set which roles should inherit permissions from each other.

```php
<?php

use BeatSwitch\Lock\Callers\Caller;
use BeatSwitch\Lock\Manager;

return [

    ...

    'permissions' => function (Manager $manager, Caller $caller) {
        // Set your configuration here.
        $manager->alias('manage', ['create', 'read', 'update', 'delete']);
        $manager->setRole('user', 'guest');
        $manager->setRole(['editor', 'admin'], 'user');
    },
];
```

### Setting permissions with the array driver

If you're using the array driver you can set all your permissions beforehand in the same `permissions` callback from above.

```php
<?php

use BeatSwitch\Lock\Callers\Caller;
use BeatSwitch\Lock\Callers\SimpleCaller;
use BeatSwitch\Lock\Drivers\ArrayDriver;
use BeatSwitch\Lock\Manager;

return [

    ...

    'permissions' => function (Manager $manager, Caller $caller) {
        // Only set permissions beforehand when using the array driver.
        if ($manager->getDriver() instanceof ArrayDriver) {
            // Set some role permissions.
            $manager->role('guest')->allow('read', 'posts');
            $manager->role('user')->allow('create', 'posts');
            $manager->role('editor')->allow('publish', 'posts');

            // Set permissions for a specific user.
            $manager->caller(new SimpleCaller('users', 1))->allow('publish', 'posts');
        }
    },
];
```

You'll probably never want to set permissions for your current authenticated user caller because they'd apply to every user who logs in but it's there if you need it.

> **Warning:** Make sure that you never set permissions through the `permissions` callback when using the database driver. This would result in permissions getting stored into your database each time your app is run.

### Using the database driver

Enable the database driver by switching the driver type in the config file. The database driver will use your default database connection to store permissions to your database. You can choose which table to store the permissions into by changing the setting in the config file.

Now that you have your database driver set up, you're ready to create a UI for your permissions and use the lock manager instance in your application to change permissions for callers or roles.

### Using the facades

This package ships with two facades: the `Lock` facade which holds the `BeatSwitch\Lock\Lock` instance for your current authenticated user (or the guest user if no user is authenticated) and the `LockManager` class which can be used to bootstrap new lock instances for callers or roles.

Checking permissions for the current user is easy.

```php
Lock::can('create', 'posts');
Lock::cannot('publish', $post);

// Or use the auth instance. This is possible because your User model has the LockAware trait.
Auth::user()->can('create', 'posts');
```

Use the manager to set permissions.

```php
LockManager::caller($user)->allow('create', 'posts');
LockManager::caller($user)->allow('all');
LockManager::role('editor')->allow('create', 'posts');
```

### Using dependency injection

You can use Laravel's IoC container to insert an instance of the current user's lock instance or the lock manager instance into your classes or controllers.

```php
<?php

use BeatSwitch\Lock\Manager;

class UserManagementController extends BaseController
{
    protected $lockManager;

    public function __construct(Manager $lockManager)
    {
        $this->lockManager = $lockManager;
    }

    public function togglePermission()
    {
        $userId = Input::get('user');
        $action = Input::get('action');
        $resource = Input::get('resource');

        $user = User::find($userId);

        $this->lockManager->caller($user)->toggle($action, $resource);

        return Redirect::route('user_management');
    }
}
```

## Maintainer

This package is currently maintained by [Dries Vints](https://github.com/driesvints).  
If you have any questions please don't hesitate to [ask them in an issue](https://github.com/BeatSwitch/lock-laravel/issues/new).

## Contributing

Please see [the contributing file](contributing.md) for details.

## Changelog

You can see a list of changes for each release in [the changelog file](changelog.md).

## License

The MIT License. Please see [the license file](license.md) for more information.

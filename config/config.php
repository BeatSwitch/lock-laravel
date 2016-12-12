<?php

use BeatSwitch\Lock\Drivers\ArrayDriver;
use BeatSwitch\Lock\Lock;
use BeatSwitch\Lock\Manager;

return [

    /*
    |--------------------------------------------------------------------------
    | Driver
    |--------------------------------------------------------------------------
    |
    | Choose your preferred driver. When choosing the static array driver,
    | you can set permissions for callers and roles in the configuration
    | callback below. The persistent database driver will store permissions to
    | a database table using the default database connection.
    |
    | Available drivers: array, database
    |
    */

    'driver' => 'array',

    /*
    |--------------------------------------------------------------------------
    | User Caller Type
    |--------------------------------------------------------------------------
    |
    | This is the caller type for your user caller. We need to set this here
    | because if no user is authed, a SimpleCaller object will be created with
    | the "guest" role.
    |
    */

    'user_caller_type' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Array Driver Configuration
    |--------------------------------------------------------------------------
    |
    | If you've selected the array driver than you can add permission
    | configuration for your roles and authed user below. The first argument in
    | the callback is the lock manager instance, the second one is your authed
    | user. If no user is authed, we'll bootstrap a SimpleCaller object which
    | has the "guest" role.
    |
    | Note that these permissions are only configured for the array driver!
    |
    */

    'permissions' => function (Manager $manager, Lock $caller) {
        // Set your configuration here.
        // $manager->alias('manage', ['create', 'read', 'update', 'delete']);
        // $manager->setRole('user', 'guest');
        // $manager->setRole(['editor', 'admin'], 'user');

        // We only want to set permissions beforehand if we're using the array driver.
        // If we would do this with the database driver, these permissions would be registered
        // each time our application was run.
        if ($manager->getDriver() instanceof ArrayDriver) {
            // Set some role permissions.
            // $manager->role('guest')->allow('read', 'posts');
            // $manager->role('user')->allow('create', 'posts');
            // $manager->role('editor')->allow('publish', 'posts');

            // Set some permissions for your current authed or guest user.
            // You'll probably don't want to do this often because this will
            // apply on every user using your application.
            // $manager->caller($caller)->deny('create', 'posts');
        }
    },

    /*
    |--------------------------------------------------------------------------
    | Database Driver Table
    |--------------------------------------------------------------------------
    |
    | If you've chosen the persistent database driver, you can choose here to
    | which table the permissions should be stored to.
    |
    */

    'table' => 'lock_permissions',

];

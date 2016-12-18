<?php

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

    'driver' => BeatSwitch\Lock\Drivers\ArrayDriver::class,

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
    | configuration for your roles below. The first argument in
    | the callback is the lock manager instance.
    |
    */

    'permissions' => function (Manager $manager) {
        // Set your configuration here.
        // $manager->alias('manage', ['create', 'read', 'update', 'delete']);
        // $manager->setRole('user', 'guest');
        // $manager->setRole(['editor', 'admin'], 'user');

        // Set some role permissions.
        // $manager->role('guest')->allow('read', 'posts');
        // $manager->role('user')->allow('create', 'posts');
        // $manager->role('editor')->allow('publish', 'posts');
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

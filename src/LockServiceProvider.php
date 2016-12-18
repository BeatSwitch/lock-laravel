<?php

namespace BeatSwitch\Lock\Integrations\Laravel;

use BeatSwitch\Lock\Drivers\ArrayDriver;
use BeatSwitch\Lock\Lock;
use BeatSwitch\Lock\Manager;
use Illuminate\Support\ServiceProvider;

class LockServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Package configuration
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('lock.php')
        ], 'config');

        // Package migrations
        $this->publishes([
            __DIR__ . '/../migrations/' => base_path('/database/migrations')
        ], 'migrations');
    }

    /**
     * Register the service provider
     *
     * @return void
     */
    public function register()
    {
        $this->registerManager();
        $this->registerAuthenticatedUserLock();
    }

    /**
     * This method will register the lock manager instance
     */
    protected function registerManager()
    {
        $this->app->singleton(Manager::class, function () {
            $manager = new Manager($this->driver());

            // If we're using the array driver, we'll try to bootstrap the permissions from the config file.
            if ($manager->getDriver() instanceof ArrayDriver) {
                // Get the permissions callback from the config file.
                $callback = config('lock.permissions');

                // Add the permissions which were set in the config file.
                if ($callback !== null) {
                    call_user_func($callback, $manager);
                }
            }

            return $manager;
        });
    }

    /**
     * Returns the configured driver
     *
     * @return \BeatSwitch\Lock\Drivers\Driver
     */
    protected function driver()
    {
        // If the user choose the persistent database driver, bootstrap
        // the database driver with the default database connection.
        if (config('lock.driver') === 'database') {
            return new DatabaseDriver($this->app['db']->connection(), config('lock.table'));
        }

        // Otherwise use the static array driver.
        return new ArrayDriver();
    }

    /**
     * This will register the lock instance for the authenticated user
     */
    protected function registerAuthenticatedUserLock()
    {
        $this->app->singleton(Lock::class, function($app) {
            return new UserLock($app[Manager::class], $app['auth.driver'], config('lock.user_caller_type'));
        });
    }
}

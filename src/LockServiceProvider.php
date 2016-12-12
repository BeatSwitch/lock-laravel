<?php

namespace BeatSwitch\Lock\Integrations\Laravel;

use BeatSwitch\Lock\Callers\SimpleCaller;
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
    private function registerManager()
    {
        $this->app->singleton(Manager::class, function () {
            return new Manager($this->getDriver());
        });
    }

    /**
     * Returns the configured driver
     *
     * @return \BeatSwitch\Lock\Drivers\Driver
     */
    private function getDriver()
    {
        // Get the configuration options for Lock.
        $driver = config('lock.driver');

        // If the user choose the persistent database driver, bootstrap
        // the database driver with the default database connection.
        if ($driver === 'database') {
            return new DatabaseDriver($this->app['db']->connection(), config('lock.table'));
        }

        // Otherwise use the static array driver.
        return new ArrayDriver();
    }

    /**
     * This will register the lock instance for the authenticated user
     */
    private function registerAuthenticatedUserLock()
    {
        $this->app->singleton(Lock::class, function ($app) {
            // If the user is logged in, we'll make the user lock aware and register its lock instance.
            if ($app['auth']->check()) {
                // Get the lock instance for the authenticated user.
                $lock = $app[Manager::class]->caller($app['auth']->user());

                // Enable the LockAware trait on the user.
                $app['auth']->user()->setLock($lock);

                return $lock;
            }

            // Get the caller type for the user caller.
            $userCallerType = config('lock.user_caller_type');

            // Bootstrap a SimpleCaller object which has the "guest" role.
            return $app[Manager::class]->caller(new SimpleCaller($userCallerType, 0, ['guest']));
        });
    }
}

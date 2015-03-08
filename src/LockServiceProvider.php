<?php
namespace BeatSwitch\Lock\Integrations\Laravel;

use BeatSwitch\Lock\Callers\SimpleCaller;
use BeatSwitch\Lock\Drivers\ArrayDriver;
use BeatSwitch\Lock\Manager;
use Illuminate\Support\ServiceProvider;

class LockServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the service provider
     */
    public function boot()
    {
        // Package configuration
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('lock-laravel.php')
        ], 'config');

        // Here we should execute the permissions callback from the config file so all
        // the roles and aliases get registered and if we're using the array driver,
        // all of our permissions get set beforehand.

        // Get the permissions callback from the config file.
        $callback = $this->app['config']->get('lock-laravel::permissions');

        // Add the permissions which were set in the config file.
        call_user_func($callback, $this->app['lock.manager'], $this->app['lock']);
    }

    /**
     * Register the service provider
     *
     * @return void
     */
    public function register()
    {
        $this->bootstrapManager();
        $this->bootstrapAuthedUserLock();
    }

    /**
     * This method will bootstrap the lock manager instance
     */
    protected function bootstrapManager()
    {
        $driver = $this->getDriver();

        $this->app->bindShared('lock.manager', function () use ($driver) {
            return new Manager($driver);
        });

        $this->app->alias('lock.manager', 'BeatSwitch\Lock\Manager');
    }

    /**
     * Returns the configured driver
     *
     * @return \BeatSwitch\Lock\Drivers\Driver
     */
    protected function getDriver()
    {
        // Get the configuration options for Lock.
        $driver = $this->app['config']->get('lock-laravel::driver');

        // If the user choose the persistent database driver, bootstrap
        // the database driver with the default database connection.
        if ($driver === 'database') {
            $table = $this->app['config']->get('lock-laravel::table');

            return new DatabaseDriver($this->app['db']->connection(), $table);
        }

        // Otherwise bootstrap the static array driver.
        return new ArrayDriver();
    }

    /**
     * This will bootstrap the lock instance for the authed user
     */
    protected function bootstrapAuthedUserLock()
    {
        $this->app->bindShared('lock', function ($app) {
            // If the user is logged in, we'll make the user lock aware and register its lock instance.
            if ($app['auth']->check()) {
                // Get the lock instance for the authed user.
                $lock = $app['lock.manager']->caller($app['auth']->user());

                // Enable the LockAware trait on the user.
                $app['auth']->user()->setLock($lock);

                return $lock;
            }

            // Get the caller type for the user caller.
            $userCallerType = $app['config']->get('lock-laravel::user_caller_type');

            // Bootstrap a SimpleCaller object which has the "guest" role.
            return $app['lock.manager']->caller(new SimpleCaller($userCallerType, 0, ['guest']));
        });

        $this->app->alias('lock', 'BeatSwitch\Lock\Lock');
    }

    /**
     * Get the services provided by the provider
     *
     * @return string[]
     */
    public function provides()
    {
        return ['lock', 'lock.manager'];
    }
}

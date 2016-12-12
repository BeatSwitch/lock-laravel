<?php
namespace BeatSwitch\Lock\Integrations\Laravel\Middleware;

use BeatSwitch\Lock\Lock;
use BeatSwitch\Lock\Manager;
use Closure;

class BootstrapLockPermissions
{
    /**
     * @var \BeatSwitch\Lock\Manager
     */
    private $lockManager;

    /**
     * @var \BeatSwitch\Lock\Lock
     */
    private $lock;

    public function __construct(Manager $lockManager, Lock $lock)
    {
        $this->lockManager = $lockManager;
        $this->lock = $lock;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Load all Lock permissions for the current user
        $this->bootstrapPermissions();

        return $next($request);
    }

    /**
     * Here we should execute the permissions callback from the config file so all
     * the roles and aliases get registered and if we're using the array driver,
     * all of our permissions get set beforehand.
     */
    private function bootstrapPermissions()
    {
        // Get the permissions callback from the config file.
        $callback = config('lock.permissions');

        // Add the permissions which were set in the config file.
        if ($callback !== null) {
            call_user_func($callback, $this->lockManager, $this->lock);
        }
    }
}

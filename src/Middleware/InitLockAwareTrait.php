<?php

namespace BeatSwitch\Lock\Integrations\Laravel\Middleware;

use Auth;
use BeatSwitch\Lock\Manager;
use Closure;

class InitLockAwareTrait
{
    /**
     * @var \BeatSwitch\Lock\Manager
     */
    private $manager;

    public function __construct(Manager $lockManager)
    {
        $this->manager = $lockManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // Get the lock instance for the authenticated user.
            $lock = $this->manager->caller(Auth::user());

            // Enable the LockAware trait on the user.
            Auth::user()->setLock($lock);
        }


        return $next($request);
    }
}

<?php

namespace BeatSwitch\Lock\Integrations\Laravel;

use BeatSwitch\Lock\Callers\SimpleCaller;
use BeatSwitch\Lock\Manager;
use Illuminate\Contracts\Auth\Guard;

class UserLock
{
    /**
     * @var \BeatSwitch\Lock\Manager
     */
    private $manager;

    /**
     * @var \Illuminate\Contracts\Auth\Guard
     */
    private $auth;

    /**
     * @var string
     */
    private $userCallerType;

    /**
     * @param string $userCallerType
     */
    public function __construct(Manager $manager, Guard $auth, $userCallerType)
    {
        $this->manager = $manager;
        $this->auth = $auth;
        $this->userCallerType = $userCallerType;
    }

    /**
     * @return \BeatSwitch\Lock\Callers\CallerLock
     */
    public function getAccountLock()
    {
        // If the user is logged in, we'll the lock instance for the authenticated user.
        if ($this->auth->check()) {
            return $this->manager->caller($this->auth->user());
        }

        // Bootstrap a SimpleCaller object which has the "guest" role.
        return $this->manager->caller(new SimpleCaller($this->userCallerType, 0, ['guest']));
    }

    /**
     * Dynamically call the account lock instance.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->getAccountLock(), $method], $parameters);
    }
}

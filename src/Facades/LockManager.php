<?php

namespace BeatSwitch\Lock\Integrations\Laravel\Facades;

use BeatSwitch\Lock\Manager;
use Illuminate\Support\Facades\Facade;

class LockManager extends Facade
{
    /**
     * Get the registered name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}

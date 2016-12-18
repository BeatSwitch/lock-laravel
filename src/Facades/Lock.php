<?php

namespace BeatSwitch\Lock\Integrations\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Lock extends Facade
{
    /**
     * Get the registered name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \BeatSwitch\Lock\Lock::class;
    }
}

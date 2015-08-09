<?php
namespace BeatSwitch\Lock\Integrations\Laravel\Facades;

use BeatSwitch\Lock\Lock;
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
        return Lock::class;
    }
}

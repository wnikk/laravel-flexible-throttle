<?php

namespace wnikk\FlexibleThrottle\Facades;

use Illuminate\Support\Facades\Facade;

class FlexibleIpResolverFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'flexibleipresolver';
    }
}
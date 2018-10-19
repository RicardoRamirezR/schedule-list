<?php

namespace i8086\ScheduleList;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade
{
    protected static function getFacadeAccessor()
    {
        return 'ScheduleList';
    }
}

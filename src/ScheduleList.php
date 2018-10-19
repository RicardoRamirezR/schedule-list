<?php

namespace i8086\ScheduleList;

use Illuminate\Support\Facades\Artisan;

class ScheduleList
{
    public function index()
    {
        $exitCode = Artisan::call('schedule:list', ['--raw' => '--raw']);

        return Artisan::output();
    }
}

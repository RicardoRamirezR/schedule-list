<?php

namespace i8086\ScheduleList;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use i8086\ScheduleList\Console\Commands\ScheduleList;
use Illuminate\Console\Scheduling\Schedule;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            ScheduleList::class,
        ]);
        if ($this->app->runningInConsole()) {
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ScheduleList::class, function () {
            $schedule = app()->make(Schedule::class);
            return new ScheduleList($schedule);
        });

        $this->app->alias(ScheduleList::class, 'ScheduleList');
    }
}

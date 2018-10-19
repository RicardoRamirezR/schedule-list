<?php
namespace i8086\ScheduleList\Test;

use i8086\ScheduleList\Facade;
use Illuminate\Contracts\Console\Kernel;
use i8086\ScheduleList\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return i8086\ScheduleList\ServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'ScheduleList' => Facade::class,
        ];
    }

    protected function seeInConsoleOutput($expectedText)
    {
        $consoleOutput = $this->app[Kernel::class]->output();

        $this->assertContains($expectedText, $consoleOutput, "Did not see `{$expectedText}` in console output: `$consoleOutput`");
    }
}

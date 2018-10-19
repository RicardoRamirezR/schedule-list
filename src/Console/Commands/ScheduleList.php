<?php

namespace i8086\ScheduleList\Console\Commands;

use Cron\CronExpression;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:list {--raw : Shows raw output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List scheduled tasks';

    /**
     * The schedule instance.
     *
     * @var \Illuminate\Console\Scheduling\Schedule
     */
    protected $schedule;

    /**
     * The inital timestamp to shows command tu be run.
     *
     * @var \Illuminate\Support\Carbon;
     */
    protected $fromNow;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;

        $this->fromNow = Carbon::now();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $headers = ['Command', 'Expression', 'Next'];

        $separator = [['---- ' . $this->fromNow->toDateTimeString().' ----', '', '']];

        if ($raw = $this->option('raw')) {
            return $this->raw();
        }

        $this->table($headers, $this->events($separator));
    }

    public function raw()
    {
        $separator = [[
            'command' => $this->fromNow->toDateTimeString(),
            'expression' => '',
            'execution' => 'today',
        ]];

        $this->comment(json_encode($this->events($separator)));
        return ;
    }

    public function events($separator = [])
    {
        $events = collect($this->schedule->events());

        $previousEvents = $events->map([$this, 'eventPreviousRow'])
                                 ->sortBy('execution');

        $nextEvents = $events->map([$this, 'eventNextRow'])
                                 ->sortBy('execution');

        return $previousEvents->merge($separator)->merge($nextEvents)->toArray();
    }

    public function eventPreviousRow($event)
    {
        return $this->eventRow($event, $next = false);
    }

    public function eventNextRow($event)
    {
        return $this->eventRow($event, $next = true);
    }

    protected function eventRow($event, $next)
    {
        $execution = $next
            ? $this->nextRunDate($event)
            : $this->previousRunDate($event);

        return [
            'command' => $this->getLaravelCommand($event->command),
            'expression' => $event->getExpression(),
            'execution' => $execution->toDateTimeString()
        ];
    }

    protected function getLaravelCommand($command)
    {
        $pos = strpos($command, 'artisan');

        return $pos === false ? $command : substr($command, $pos + 9);
    }

    /**
     * get the previous calculated run.
     *
     * @return formated date
     */
    protected function previousRunDate($event)
    {
        return $this->runDate($event, 'getPreviousRunDate');
    }

    /**
     * get the previous calculated run.
     *
     * @return formated date
     */
    protected function nextRunDate($event)
    {
        return $this->runDate($event, 'getNextRunDate');
    }

    protected function runDate($event, $runDate)
    {
        $date = clone $this->fromNow;

        if ($event->timezone) {
            $date->setTimezone($event->timezone);
        }

        return Carbon::instance(CronExpression::factory(
            $event->expression
        )->{$runDate}($date->toDateTimeString()));
    }
}

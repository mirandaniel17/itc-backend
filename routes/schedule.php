<?php
use Illuminate\Console\Scheduling\Schedule;

return function (Schedule $schedule) {
    $schedule->command('notify:course-completion')->dailyAt('00:00');
};

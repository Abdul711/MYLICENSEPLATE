

<?php

use Illuminate\Console\Scheduling\Schedule;



// Schedule the command
app()->booted(function () {
    $schedule = app(Schedule::class);

    // First command
    $schedule->command('plates:generate-challans')
        ->everyMinute()
        ->appendOutputTo(storage_path('logs/plates_generate.log'));

    // Second command
    $schedule->command('users:unpaid-challan')
        ->everyFiveMinutes()
        ->appendOutputTo(storage_path('logs/users_unpaid.log'));
});


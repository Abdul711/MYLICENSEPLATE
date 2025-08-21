

<?php

use Illuminate\Console\Scheduling\Schedule;



// Schedule the command
app()->booted(function () {
    $schedule = app(Schedule::class);
   $schedule->command('plates:generate-challans')->everyMinute(); // Adjust frequency as needed
});

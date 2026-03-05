<?php

use Illuminate\Support\Facades\Schedule;

// Schedule::command('test:scheduler')->everyMinute();
Schedule::command('task:scheduler')->everyTwoSeconds();


// use Illuminate\Foundation\Inspiring;
// use Illuminate\Support\Facades\Artisan;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');


<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('horizon:snapshot')
    ->everyFiveMinutes()
    ->appendOutputTo(storage_path('logs/horizon_snapshot.log'));

//Schedule::call(function () {
//    logger('Scheduler ran at ' . now());
//})->everyMinute();

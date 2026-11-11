<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Check official fee source pages once daily. Run "php artisan schedule:run" every minute on the host.
Schedule::command('mrk:check-government-fees')->dailyAt('03:00')->withoutOverlapping();

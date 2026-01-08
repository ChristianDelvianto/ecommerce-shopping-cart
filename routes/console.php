<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('send-daily-sales-report')->dailyAt('01:00');
Schedule::command('clean-up-product-notify-records')->dailyAt('01:15');

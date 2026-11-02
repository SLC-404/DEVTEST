<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('exchange-rate:update')
    ->dailyAt('00:01')
    ->timezone('America/Mexico_City')
    ->withoutOverlapping();

Schedule::command('exchange-rate:update')
    ->dailyAt('06:00')
    ->timezone('America/Mexico_City')
    ->withoutOverlapping();

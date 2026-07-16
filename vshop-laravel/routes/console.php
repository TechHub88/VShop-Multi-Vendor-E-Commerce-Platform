<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('vshop:refund-sla-check')->daily();
Schedule::command('orders:auto-refund-expired')->hourly();
Schedule::command('hold:release')->twiceDaily(6, 18); // Runs at 6 AM and 6 PM

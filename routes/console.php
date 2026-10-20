<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Refresh verified MRR from connected providers every hour.
Schedule::command('mrr:sync')->hourly()->withoutOverlapping();

// "Spot the Next Big Thing" weekly report — Mondays 09:00 UTC.
Schedule::command('newsletter:send-weekly')->weeklyOn(1, '09:00')->withoutOverlapping();

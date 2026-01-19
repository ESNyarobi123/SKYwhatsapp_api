<?php

use App\Jobs\CheckExpiredSubscriptions;
use App\Jobs\SendRenewalReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule: Check expired subscriptions daily at midnight
Schedule::job(new CheckExpiredSubscriptions)->daily();

// Schedule: Send renewal reminders daily at 9:00 AM
Schedule::job(new SendRenewalReminders)->dailyAt('09:00');

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('jadwal:check-status')->everyMinute();
Schedule::command('jadwal:send-reminders')->everyMinute();
Schedule::command('jadwal:berangkat')->everyMinute();
Schedule::command('pemesanan:cancel-unpaid')->everyMinute();
Schedule::command('jadwal:boarding')->everyMinute();
Schedule::command('jadwal:pengingat-kedatangan')->everyMinute();
Schedule::command('jadwal:selesai')->everyMinute();

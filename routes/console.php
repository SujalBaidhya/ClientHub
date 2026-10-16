<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
use App\Console\Commands\MarkOverdueInvoices;
use App\Console\Commands\SendDueSoonInvoiceReminders;
use Illuminate\Support\Facades\Schedule;
Schedule::command(MarkOverdueInvoices::class)->hourly();
Schedule::command(SendDueSoonInvoiceReminders::class)->dailyAt('09:00');
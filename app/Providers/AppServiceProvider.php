<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use App\Models\ActivityLog;
use App\Mail\BrevoTransport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.layouts.app', function ($view) {
            if (auth()->check() && auth()->user()->role === 'admin') {
                $view->with('unreadActivityCount', ActivityLog::where('is_read', false)->count());
                $view->with('recentActivity', ActivityLog::with('user')->where('is_read', false)->latest()->take(8)->get());
            } else {
                $view->with('unreadActivityCount', 0);
                $view->with('recentActivity', collect());
            }
        });

      Mail::extend('brevo', function (array $config = []) {
    return new BrevoTransport(config('services.brevo.key'));
});
    }
}
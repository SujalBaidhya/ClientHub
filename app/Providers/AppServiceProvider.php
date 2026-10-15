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
            $user = auth()->user();
            if ($user && in_array($user->role, ['admin', 'client'])) {
                $unread = fn () => ActivityLog::visibleTo($user)->unreadFor($user);

                $view->with('unreadActivityCount', $unread()->count());
                $view->with('recentActivity', $unread()->with('user')->latest()->take(8)->get());
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
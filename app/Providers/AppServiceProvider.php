<?php

namespace App\Providers;

use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer(['layouts.admin', 'admin.*'], function ($view) {
            if (Auth::check() && Auth::user()->isAdmin()) {
                $incompleteCount = Attendance::where('status', 'incomplete')
                    ->orWhere(function ($q) {
                        $q->where('date', '<', now()->toDateString())
                            ->whereNull('check_out');
                    })
                    ->count();

                $view->with('incompleteAttendanceCount', $incompleteCount);
            }
        });
    }
}

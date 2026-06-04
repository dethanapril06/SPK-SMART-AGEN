<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

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
        View::composer('layouts.calon-agen', function ($view) {
            if (Auth::check() && Auth::user()->role === 'calon_agen') {
                $notifikasiNavbar = Notifikasi::where('user_id', Auth::id())
                    ->latest()
                    ->take(5)
                    ->get();

                $unreadCount = Notifikasi::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count();

                $view->with(compact('notifikasiNavbar', 'unreadCount'));
            }
        });
    }
}

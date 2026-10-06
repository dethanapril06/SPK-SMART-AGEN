<?php

namespace App\Providers;

use App\Models\Notifikasi;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
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
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email - SPK SMART Agen')
                ->greeting('Halo, ' . $notifiable->name . '!')
                ->line('Terima kasih telah mendaftarkan diri di SPK SMART Agen.')
                ->line('Untuk mengaktifkan akun dan melanjutkan proses seleksi calon agen, silakan klik tombol verifikasi di bawah ini:')
                ->action('Verifikasi Email Saya', $url)
                ->line('Tautan verifikasi ini berlaku selama 60 menit.')
                ->line('Jika Anda tidak merasa mendaftar di sistem SPK SMART Agen, silakan abaikan email ini.')
                ->salutation("Salam hangat,\nTim SPK SMART Agen");
        });

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

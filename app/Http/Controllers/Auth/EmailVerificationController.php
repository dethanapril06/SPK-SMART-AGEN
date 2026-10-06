<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    /**
     * Tampilkan pemberitahuan verifikasi email.
     */
    public function notice(Request $request): View|RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return $this->redirectVerifiedUser($request->user());
        }

        return view('auth.verify-email');
    }

    /**
     * Proses verifikasi email dari signed link.
     */
    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('calon-agen.dashboard')
            ->with('success', 'Email Anda berhasil diverifikasi. Selamat datang!');
    }

    /**
     * Kirim ulang link verifikasi email.
     */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()?->hasVerifiedEmail()) {
            return $this->redirectVerifiedUser($request->user());
        }

        try {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('success', 'Link verifikasi email baru telah dikirim ke ' . $request->user()->email . '. Silakan cek kotak masuk atau folder spam Anda.');
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email verifikasi: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email verifikasi: ' . $e->getMessage() . '. Pastikan konfigurasi SMTP sudah benar.');
        }
    }

    /**
     * Admin mengirim ulang verifikasi email ke user calon agen.
     */
    public function adminResend(User $user): RedirectResponse
    {
        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'Email user ini sudah terverifikasi.');
        }

        try {
            $user->sendEmailVerificationNotification();
            return back()->with('success', "Link verifikasi berhasil dikirim ke {$user->email}.");
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email verifikasi admin: ' . $e->getMessage());
            return back()->with('error', "Gagal mengirim email verifikasi: {$e->getMessage()}.");
        }
    }

    private function redirectVerifiedUser(User $user): RedirectResponse
    {
        return match ($user->role) {
            'admin'          => redirect()->route('admin.dashboard'),
            'petugas_survey' => redirect()->route('admin.penilaian.index'),
            'calon_agen'     => redirect()->route('calon-agen.dashboard'),
            default          => redirect('/'),
        };
    }
}

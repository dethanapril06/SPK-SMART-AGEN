<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CalonAgen;
use App\Models\PeriodePendaftaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    // -------------------------------------------------------------------------
    // LOGIN
    // -------------------------------------------------------------------------

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $request->session()->regenerate();

        return $this->redirectByRole(Auth::user()->role);
    }

    // -------------------------------------------------------------------------
    // REGISTER (hanya untuk calon_agen)
    // -------------------------------------------------------------------------

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'nik'          => ['required', 'string', 'size:16', 'unique:calon_agen,nik'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_hp'        => ['required', 'string', 'max:20'],
            'alamat'       => ['required', 'string'],
            'ktp'          => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'nib'          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'npwp'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'formulir_pendaftaran' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'nik.size'          => 'NIK harus terdiri dari 16 digit.',
            'nik.unique'        => 'NIK sudah terdaftar.',
            'ktp.required'      => 'Dokumen KTP wajib diunggah.',
            'formulir_pendaftaran.required' => 'Formulir pendaftaran wajib diunggah.',
        ]);

        // Cek apakah ada periode pendaftaran yang aktif
        $periode = PeriodePendaftaran::where('status', 'aktif')->first();

        if (!$periode) {
            return back()
                ->withInput()
                ->withErrors(['periode' => 'Tidak ada periode pendaftaran yang sedang aktif saat ini.']);
        }

        $dokumen = [
            'ktp_path' => $request->file('ktp')->store('dokumen-calon-agen/ktp', 'public'),
            'nib_path' => $request->hasFile('nib')
                ? $request->file('nib')->store('dokumen-calon-agen/nib', 'public')
                : null,
            'npwp_path' => $request->hasFile('npwp')
                ? $request->file('npwp')->store('dokumen-calon-agen/npwp', 'public')
                : null,
            'formulir_pendaftaran_path' => $request->file('formulir_pendaftaran')
                ->store('dokumen-calon-agen/formulir-pendaftaran', 'public'),
        ];

        // Buat akun user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'calon_agen',
        ]);

        // Buat data calon agen
        CalonAgen::create([
            'user_id'      => $user->id,
            'periode_id'   => $periode->id,
            'nik'          => $request->nik,
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp'        => $request->no_hp,
            'alamat'       => $request->alamat,
            ...$dokumen,
            'status'       => 'diproses',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('calon-agen.dashboard')
            ->with('success', 'Registrasi berhasil! Data Anda sedang diproses.');
    }

    // -------------------------------------------------------------------------
    // LOGOUT
    // -------------------------------------------------------------------------

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // -------------------------------------------------------------------------
    // HELPER
    // -------------------------------------------------------------------------

    private function redirectByRole(string $role): RedirectResponse
    {
        return match ($role) {
            'admin'      => redirect()->route('admin.dashboard'),
            'calon_agen' => redirect()->route('calon-agen.dashboard'),
            default      => redirect('/'),
        };
    }
}

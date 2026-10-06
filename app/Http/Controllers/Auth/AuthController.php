<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CalonAgen;
use App\Models\PeriodePendaftaran;
use App\Models\User;
use App\Services\DraftUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(protected DraftUploadService $draftUploadService) {}

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
        // Tangkap file upload ke temporary/draft agar tidak hilang jika validasi gagal
        $this->draftUploadService->captureUploads(
            $request,
            ['ktp', 'nib', 'npwp', 'formulir_pendaftaran'],
            'register_drafts'
        );

        // Cek apakah ada periode pendaftaran yang aktif
        $periode = PeriodePendaftaran::where('status', 'aktif')->first();

        if (!$periode) {
            return back()
                ->withInput()
                ->withErrors(['periode' => 'Gagal mendaftar: tidak ada periode pendaftaran yang sedang aktif saat ini atau periode sudah ditutup.']);
        }

        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email:rfc,filter', 'max:255', 'unique:users,email'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
            'nik'              => ['required', 'string', 'size:16', 'unique:calon_agen,nik'],
            'nama_lengkap'     => ['required', 'string', 'max:255'],
            'nama_usaha'       => ['nullable', 'string', 'max:255'],
            'no_hp'            => ['required', 'string', 'max:20'],
            'alamat_domisili'  => ['required', 'string'],
            'lat_domisili'     => ['nullable', 'numeric'],
            'lng_domisili'     => ['nullable', 'numeric'],
            'alamat_usaha'     => ['nullable', 'string'],
            'lat_usaha'        => ['nullable', 'numeric'],
            'lng_usaha'        => ['nullable', 'numeric'],
            'ktp'              => [
                Rule::requiredIf(fn() => !$this->draftUploadService->hasDraft('ktp', 'register_drafts')),
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048'
            ],
            'nib'              => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'npwp'             => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'formulir_pendaftaran' => [
                Rule::requiredIf(fn() => !$this->draftUploadService->hasDraft('formulir_pendaftaran', 'register_drafts')),
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048'
            ],
        ], [
            'nik.size'          => 'NIK harus terdiri dari 16 digit.',
            'nik.unique'        => 'NIK sudah terdaftar.',
            'ktp.required'      => 'Dokumen KTP wajib diunggah.',
            'formulir_pendaftaran.required' => 'Formulir pendaftaran wajib diunggah.',
            'password.confirmed'=> 'Konfirmasi password tidak cocok.',
            'email.unique'      => 'Alamat email ini sudah terdaftar.',
        ]);

        // Simpan dokumen secara permanen
        $ktpPath = $this->draftUploadService->storePermanen($request, 'ktp', 'dokumen-calon-agen/ktp', 'register_drafts');
        $formulirPath = $this->draftUploadService->storePermanen($request, 'formulir_pendaftaran', 'dokumen-calon-agen/formulir-pendaftaran', 'register_drafts');
        $nibPath = $this->draftUploadService->storePermanen($request, 'nib', 'dokumen-calon-agen/nib', 'register_drafts');
        $npwpPath = $this->draftUploadService->storePermanen($request, 'npwp', 'dokumen-calon-agen/npwp', 'register_drafts');

        // Pastikan nama usaha memiliki prefix BeJuBis@ jika diisi
        $namaUsaha = null;
        if ($request->filled('nama_usaha')) {
            $inputUsaha = trim($request->nama_usaha);
            $namaUsaha = Str::startsWith($inputUsaha, 'BeJuBis@') ? $inputUsaha : 'BeJuBis@' . $inputUsaha;
        }

        // Buat akun user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'calon_agen',
        ]);

        // Kirim email verifikasi
        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email verifikasi pendaftaran: ' . $e->getMessage());
        }

        // Buat data calon agen
        CalonAgen::create([
            'user_id'                 => $user->id,
            'periode_id'              => $periode->id,
            'nik'                     => $request->nik,
            'nama_lengkap'            => $request->nama_lengkap,
            'nama_usaha'              => $namaUsaha,
            'no_hp'                   => $request->no_hp,
            'alamat_domisili'         => $request->alamat_domisili,
            'lat_domisili'            => $request->lat_domisili ?: null,
            'lng_domisili'            => $request->lng_domisili ?: null,
            'alamat_usaha'            => $request->alamat_usaha,
            'lat_usaha'               => $request->lat_usaha ?: null,
            'lng_usaha'               => $request->lng_usaha ?: null,
            'ktp_path'                => $ktpPath,
            'formulir_pendaftaran_path' => $formulirPath,
            'nib_path'                => $nibPath,
            'npwp_path'               => $npwpPath,
            'status'                  => 'diproses',
            'sumber_pendaftaran'      => 'mandiri',
            'didaftarkan_oleh'        => null,
            'status_verifikasi'       => 'menunggu',
        ]);

        // Bersihkan draft session
        $this->draftUploadService->clearDrafts('register_drafts');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('calon-agen.dashboard')
            ->with('success', 'Registrasi berhasil! Link verifikasi telah dikirim ke email Anda. Silakan verifikasi untuk melanjutkan.');
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
            'admin'          => redirect()->route('admin.dashboard'),
            'petugas_survey' => redirect()->route('admin.penilaian.index'),
            'calon_agen'     => redirect()->route('calon-agen.dashboard'),
            default          => redirect('/'),
        };
    }
}

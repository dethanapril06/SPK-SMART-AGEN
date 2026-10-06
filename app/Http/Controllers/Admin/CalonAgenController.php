<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonAgen;
use App\Models\Notifikasi;
use App\Models\PeriodePendaftaran;
use App\Models\User;
use App\Services\DraftUploadService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalonAgenController extends Controller
{
    public function __construct(protected DraftUploadService $draftUploadService) {}

    public function index(Request $request): View
    {
        $periodes = PeriodePendaftaran::orderBy('created_at', 'desc')->get();

        $calonAgens = CalonAgen::with(['user', 'periode', 'didaftarkanOleh'])
            ->when($request->periode_id, fn($q) => $q->where('periode_id', $request->periode_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->status_verifikasi, fn($q) => $q->where('status_verifikasi', $request->status_verifikasi))
            ->latest()
            ->get();

        return view('admin.calon-agen.index', compact('calonAgens', 'periodes'));
    }

    public function create(): View
    {
        $periodes = PeriodePendaftaran::orderBy('created_at', 'desc')->get();

        return view('admin.calon-agen.create', compact('periodes'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Tangkap file upload ke draft session agar tidak hilang saat validasi gagal
        $this->draftUploadService->captureUploads(
            $request,
            ['ktp', 'nib', 'npwp', 'formulir_pendaftaran'],
            'admin_calon_agen_drafts'
        );

        $periode = PeriodePendaftaran::find($request->periode_id);
        if ($periode && $periode->isDitutup()) {
            return back()
                ->withInput()
                ->withErrors(['periode_id' => 'Gagal mendaftarkan: periode pendaftaran sudah ditutup.']);
        }

        $validated = $request->validate([
            'periode_id'      => ['required', 'exists:periode_pendaftaran,id'],
            'email'           => ['required', 'string', 'email:rfc,filter', 'max:255', 'unique:users,email'],
            'nik'             => ['required', 'string', 'size:16', 'unique:calon_agen,nik'],
            'nama_lengkap'    => ['required', 'string', 'max:255'],
            'nama_usaha'      => ['nullable', 'string', 'max:255'],
            'no_hp'           => ['required', 'string', 'max:20'],
            'alamat_domisili' => ['required', 'string'],
            'lat_domisili'    => ['nullable', 'numeric'],
            'lng_domisili'    => ['nullable', 'numeric'],
            'alamat_usaha'    => ['nullable', 'string'],
            'lat_usaha'       => ['nullable', 'numeric'],
            'lng_usaha'       => ['nullable', 'numeric'],
            'ktp'             => [
                Rule::requiredIf(fn() => !$this->draftUploadService->hasDraft('ktp', 'admin_calon_agen_drafts')),
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048'
            ],
            'nib'             => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'npwp'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'formulir_pendaftaran' => [
                Rule::requiredIf(fn() => !$this->draftUploadService->hasDraft('formulir_pendaftaran', 'admin_calon_agen_drafts')),
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048'
            ],
        ], [
            'nik.size'                      => 'NIK harus terdiri dari 16 digit.',
            'nik.unique'                    => 'NIK sudah terdaftar.',
            'ktp.required'                  => 'Dokumen KTP wajib diunggah.',
            'formulir_pendaftaran.required' => 'Formulir pendaftaran wajib diunggah.',
            'email.unique'                  => 'Alamat email ini sudah terdaftar.',
        ]);

        $namaUsaha = null;
        if (!empty($validated['nama_usaha'])) {
            $inputUsaha = trim($validated['nama_usaha']);
            $namaUsaha = Str::startsWith($inputUsaha, 'BeJuBis@') ? $inputUsaha : 'BeJuBis@' . $inputUsaha;
        }

        $user = User::create([
            'name'     => $validated['nama_lengkap'],
            'email'    => $validated['email'],
            'password' => Hash::make('password'),
            'role'     => 'calon_agen',
        ]);

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim email verifikasi oleh admin: ' . $e->getMessage());
        }

        $ktpPath = $this->draftUploadService->storePermanen($request, 'ktp', 'dokumen-calon-agen/ktp', 'admin_calon_agen_drafts');
        $formulirPath = $this->draftUploadService->storePermanen($request, 'formulir_pendaftaran', 'dokumen-calon-agen/formulir-pendaftaran', 'admin_calon_agen_drafts');
        $nibPath = $this->draftUploadService->storePermanen($request, 'nib', 'dokumen-calon-agen/nib', 'admin_calon_agen_drafts');
        $npwpPath = $this->draftUploadService->storePermanen($request, 'npwp', 'dokumen-calon-agen/npwp', 'admin_calon_agen_drafts');

        CalonAgen::create([
            'user_id'                   => $user->id,
            'periode_id'                => $validated['periode_id'],
            'nik'                       => $validated['nik'],
            'nama_lengkap'              => $validated['nama_lengkap'],
            'nama_usaha'                => $namaUsaha,
            'no_hp'                     => $validated['no_hp'],
            'alamat_domisili'           => $validated['alamat_domisili'],
            'lat_domisili'              => ($validated['lat_domisili'] ?? null) ?: null,
            'lng_domisili'              => ($validated['lng_domisili'] ?? null) ?: null,
            'alamat_usaha'              => $validated['alamat_usaha'] ?? null,
            'lat_usaha'                 => ($validated['lat_usaha'] ?? null) ?: null,
            'lng_usaha'                 => ($validated['lng_usaha'] ?? null) ?: null,
            'ktp_path'                  => $ktpPath,
            'formulir_pendaftaran_path'   => $formulirPath,
            'nib_path'                  => $nibPath,
            'npwp_path'                 => $npwpPath,
            'status'                    => 'diproses',
            'sumber_pendaftaran'        => 'admin',
            'didaftarkan_oleh'          => auth()->id(),
            'status_verifikasi'         => 'menunggu',
        ]);

        $this->draftUploadService->clearDrafts('admin_calon_agen_drafts');

        return redirect()
            ->route('admin.calon-agen.index')
            ->with('success', "Calon agen berhasil ditambahkan. Email login: {$user->email}, password default: password. Link verifikasi telah dikirimkan ke email.");
    }

    public function show(CalonAgen $calonAgen): View
    {
        $calonAgen->load(['user', 'periode', 'penilaian', 'hasilSmart', 'didaftarkanOleh', 'diverifikasiOleh']);

        return view('admin.calon-agen.show', compact('calonAgen'));
    }

    public function edit(CalonAgen $calonAgen): View|RedirectResponse
    {
        if ($calonAgen->periode->isDitutup()) {
            return redirect()
                ->route('admin.calon-agen.show', $calonAgen)
                ->with('error', 'Data calon agen tidak dapat diedit karena periode pendaftaran sudah ditutup.');
        }

        $periodes = PeriodePendaftaran::orderBy('created_at', 'desc')->get();
        $calonAgen->load(['user', 'periode']);

        return view('admin.calon-agen.edit', compact('calonAgen', 'periodes'));
    }

    public function update(Request $request, CalonAgen $calonAgen): RedirectResponse
    {
        if ($calonAgen->periode->isDitutup()) {
            return redirect()
                ->route('admin.calon-agen.show', $calonAgen)
                ->with('error', 'Data calon agen tidak dapat diubah karena periode pendaftaran sudah ditutup.');
        }

        $targetPeriode = PeriodePendaftaran::find($request->periode_id);
        if ($targetPeriode && $targetPeriode->isDitutup() && $targetPeriode->id !== $calonAgen->periode_id) {
            return back()
                ->withInput()
                ->withErrors(['periode_id' => 'Gagal memindahkan: periode tujuan sudah ditutup.']);
        }

        $validated = $request->validate([
            'periode_id'      => ['required', 'exists:periode_pendaftaran,id'],
            'email'           => ['required', 'string', 'email:rfc,filter', 'max:255', Rule::unique('users', 'email')->ignore($calonAgen->user_id)],
            'nik'             => [
                'required',
                'string',
                'size:16',
                Rule::unique('calon_agen', 'nik')->ignore($calonAgen->id),
            ],
            'nama_lengkap'    => ['required', 'string', 'max:255'],
            'nama_usaha'      => ['nullable', 'string', 'max:255'],
            'no_hp'           => ['required', 'string', 'max:20'],
            'alamat_domisili' => ['required', 'string'],
            'lat_domisili'    => ['nullable', 'numeric'],
            'lng_domisili'    => ['nullable', 'numeric'],
            'alamat_usaha'    => ['nullable', 'string'],
            'lat_usaha'       => ['nullable', 'numeric'],
            'lng_usaha'       => ['nullable', 'numeric'],
            'ktp'             => [
                Rule::requiredIf(!$calonAgen->ktp_path),
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
            'nib'             => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'npwp'            => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'formulir_pendaftaran' => [
                Rule::requiredIf(!$calonAgen->formulir_pendaftaran_path),
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
        ], [
            'nik.size'                      => 'NIK harus terdiri dari 16 digit.',
            'ktp.required'                  => 'Dokumen KTP wajib diunggah.',
            'formulir_pendaftaran.required' => 'Formulir pendaftaran wajib diunggah.',
            'email.unique'                  => 'Alamat email ini sudah terdaftar.',
        ]);

        $namaUsaha = null;
        if (!empty($validated['nama_usaha'])) {
            $inputUsaha = trim($validated['nama_usaha']);
            $namaUsaha = Str::startsWith($inputUsaha, 'BeJuBis@') ? $inputUsaha : 'BeJuBis@' . $inputUsaha;
        }

        $calonAgen->update([
            'periode_id'      => $validated['periode_id'],
            'nik'             => $validated['nik'],
            'nama_lengkap'    => $validated['nama_lengkap'],
            'nama_usaha'      => $namaUsaha,
            'no_hp'           => $validated['no_hp'],
            'alamat_domisili' => $validated['alamat_domisili'],
            'lat_domisili'    => ($validated['lat_domisili'] ?? null) ?: null,
            'lng_domisili'    => ($validated['lng_domisili'] ?? null) ?: null,
            'alamat_usaha'    => $validated['alamat_usaha'] ?? null,
            'lat_usaha'       => ($validated['lat_usaha'] ?? null) ?: null,
            'lng_usaha'       => ($validated['lng_usaha'] ?? null) ?: null,
            ...$this->updateDokumen($request, $calonAgen),
        ]);

        if ($calonAgen->user) {
            $emailChanged = $calonAgen->user->email !== $validated['email'];
            $calonAgen->user->update([
                'name'  => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'email_verified_at' => $emailChanged ? null : $calonAgen->user->email_verified_at,
            ]);

            if ($emailChanged) {
                try {
                    $calonAgen->user->sendEmailVerificationNotification();
                } catch (\Throwable $e) {
                    Log::error('Gagal mengirim ulang email verifikasi: ' . $e->getMessage());
                }
            }
        }

        return redirect()
            ->route('admin.calon-agen.show', $calonAgen)
            ->with('success', 'Data calon agen berhasil diperbarui.');
    }

    public function ubahStatus(Request $request, CalonAgen $calonAgen): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::in(['diproses', 'disurvey', 'direkomendasi', 'belumdirekomendasi'])],
        ]);

        $calonAgen->update(['status' => $request->status]);

        return redirect()
            ->back()
            ->with('success', "Status calon agen berhasil diubah menjadi '{$request->status}'.");
    }

    public function verifikasiDokumen(Request $request, CalonAgen $calonAgen): RedirectResponse
    {
        $request->validate([
            'status_verifikasi' => ['required', Rule::in(['valid', 'tidak_valid'])],
            'catatan_verifikasi' => [
                Rule::requiredIf(fn() => $request->status_verifikasi === 'tidak_valid'),
                'nullable',
                'string',
                'max:1000'
            ],
        ], [
            'catatan_verifikasi.required' => 'Catatan verifikasi wajib diisi apabila dokumen dinyatakan tidak valid.',
        ]);

        $calonAgen->update([
            'status_verifikasi'  => $request->status_verifikasi,
            'catatan_verifikasi' => $request->catatan_verifikasi,
            'diverifikasi_oleh'  => auth()->id(),
            'diverifikasi_at'    => now(),
        ]);

        $statusTeks = $request->status_verifikasi === 'valid' ? 'VALID' : 'TIDAK VALID';
        $pesan = $request->status_verifikasi === 'valid'
            ? 'Dokumen administratif Anda telah diverifikasi oleh admin dan dinyatakan VALID. Pendaftaran Anda siap dilanjutkan ke tahap penilaian/survey lapangan.'
            : 'Dokumen administratif Anda dinyatakan TIDAK VALID oleh admin. Catatan: ' . $request->catatan_verifikasi . '. Silakan perbaiki/unggah ulang dokumen di menu dashboard akun Anda.';

        Notifikasi::create([
            'user_id'       => $calonAgen->user_id,
            'calon_agen_id' => $calonAgen->id,
            'judul'         => "Verifikasi Dokumen: {$statusTeks}",
            'pesan'         => $pesan,
            'is_read'       => false,
        ]);

        return redirect()
            ->back()
            ->with('success', "Dokumen administratif berhasil diverifikasi sebagai '{$request->status_verifikasi}'.");
    }

    public function destroy(CalonAgen $calonAgen): RedirectResponse
    {
        if ($calonAgen->periode->isDitutup()) {
            return redirect()
                ->route('admin.calon-agen.index')
                ->with('error', 'Calon agen pada periode yang sudah ditutup tidak dapat dihapus.');
        }

        if ($calonAgen->isDirekomendasi()) {
            return redirect()
                ->route('admin.calon-agen.index')
                ->with('error', 'Calon agen yang sudah direkomendasi tidak dapat dihapus.');
        }

        try {
            DB::transaction(function () use ($calonAgen) {
                $calonAgen->hasilSmart()->delete();
                $calonAgen->penilaian()->delete();
                $calonAgen->delete();
            });
        } catch (QueryException) {
            return redirect()
                ->route('admin.calon-agen.index')
                ->with('error', 'Calon agen tidak dapat dihapus karena masih memiliki relasi dengan data lain.');
        }

        $this->deleteDokumen($calonAgen);

        return redirect()
            ->route('admin.calon-agen.index')
            ->with('success', 'Data calon agen berhasil dihapus.');
    }

    private function storeDokumen(Request $request): array
    {
        return [
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
    }

    private function generateEmailFromName(string $name): string
    {
        $base = Str::slug($name, '.');

        if ($base === '') {
            $base = 'calon.agen';
        }

        $email = "{$base}@calon-agen.local";
        $counter = 2;

        while (User::where('email', $email)->exists()) {
            $email = "{$base}{$counter}@calon-agen.local";
            $counter++;
        }

        return $email;
    }

    private function updateDokumen(Request $request, CalonAgen $calonAgen): array
    {
        $fields = [
            'ktp' => ['column' => 'ktp_path', 'directory' => 'dokumen-calon-agen/ktp'],
            'nib' => ['column' => 'nib_path', 'directory' => 'dokumen-calon-agen/nib'],
            'npwp' => ['column' => 'npwp_path', 'directory' => 'dokumen-calon-agen/npwp'],
            'formulir_pendaftaran' => [
                'column' => 'formulir_pendaftaran_path',
                'directory' => 'dokumen-calon-agen/formulir-pendaftaran',
            ],
        ];

        $data = [];

        foreach ($fields as $input => $meta) {
            if (!$request->hasFile($input)) {
                continue;
            }

            $column = $meta['column'];

            if ($calonAgen->{$column}) {
                Storage::disk('public')->delete($calonAgen->{$column});
            }

            $data[$column] = $request->file($input)->store($meta['directory'], 'public');
        }

        return $data;
    }

    private function deleteDokumen(CalonAgen $calonAgen): void
    {
        foreach (['ktp_path', 'nib_path', 'npwp_path', 'formulir_pendaftaran_path'] as $field) {
            if ($calonAgen->{$field}) {
                Storage::disk('public')->delete($calonAgen->{$field});
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonAgen;
use App\Models\PeriodePendaftaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalonAgenController extends Controller
{
    public function index(Request $request): View
    {
        $periodes = PeriodePendaftaran::orderBy('created_at', 'desc')->get();

        $calonAgens = CalonAgen::with(['user', 'periode'])
            ->when($request->periode_id, fn($q) => $q->where('periode_id', $request->periode_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
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
        $validated = $request->validate([
            'periode_id'   => ['required', 'exists:periode_pendaftaran,id'],
            'nik'          => ['required', 'string', 'size:16', 'unique:calon_agen,nik'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_hp'        => ['required', 'string', 'max:20'],
            'alamat'       => ['required', 'string'],
            'ktp'          => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'nib'          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'npwp'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'formulir_pendaftaran' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ], [
            'nik.size' => 'NIK harus terdiri dari 16 digit.',
            'ktp.required' => 'Dokumen KTP wajib diunggah.',
            'formulir_pendaftaran.required' => 'Formulir pendaftaran wajib diunggah.',
        ]);

        $email = $this->generateEmailFromName($validated['nama_lengkap']);

        $user = User::create([
            'name' => $validated['nama_lengkap'],
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => 'calon_agen',
        ]);

        CalonAgen::create([
            'user_id' => $user->id,
            'periode_id' => $validated['periode_id'],
            'nik' => $validated['nik'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'no_hp' => $validated['no_hp'],
            'alamat' => $validated['alamat'],
            ...$this->storeDokumen($request),
            'status' => 'diproses',
        ]);

        return redirect()
            ->route('admin.calon-agen.index')
            ->with('success', "Calon agen berhasil ditambahkan. Email login: {$email}, password: password.");
    }

    public function show(CalonAgen $calonAgen): View
    {
        $calonAgen->load(['user', 'periode', 'penilaian', 'hasilSmart']);

        return view('admin.calon-agen.show', compact('calonAgen'));
    }

    public function edit(CalonAgen $calonAgen): View
    {
        $periodes = PeriodePendaftaran::orderBy('created_at', 'desc')->get();
        $calonAgen->load(['user', 'periode']);

        return view('admin.calon-agen.edit', compact('calonAgen', 'periodes'));
    }

    public function update(Request $request, CalonAgen $calonAgen): RedirectResponse
    {
        $validated = $request->validate([
            'periode_id'   => ['required', 'exists:periode_pendaftaran,id'],
            'nik'          => [
                'required',
                'string',
                'size:16',
                Rule::unique('calon_agen', 'nik')->ignore($calonAgen->id),
            ],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'no_hp'        => ['required', 'string', 'max:20'],
            'alamat'       => ['required', 'string'],
            'ktp'          => [
                Rule::requiredIf(!$calonAgen->ktp_path),
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
            'nib'          => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'npwp'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'formulir_pendaftaran' => [
                Rule::requiredIf(!$calonAgen->formulir_pendaftaran_path),
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:2048',
            ],
        ], [
            'nik.size' => 'NIK harus terdiri dari 16 digit.',
            'ktp.required' => 'Dokumen KTP wajib diunggah.',
            'formulir_pendaftaran.required' => 'Formulir pendaftaran wajib diunggah.',
        ]);

        $calonAgen->update([
            'periode_id' => $validated['periode_id'],
            'nik' => $validated['nik'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'no_hp' => $validated['no_hp'],
            'alamat' => $validated['alamat'],
            ...$this->updateDokumen($request, $calonAgen),
        ]);

        if ($calonAgen->user) {
            $calonAgen->user->update([
                'name' => $validated['nama_lengkap'],
            ]);
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

    public function destroy(CalonAgen $calonAgen): RedirectResponse
    {
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

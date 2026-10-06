<?php

namespace App\Http\Controllers\CalonAgen;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $user->load('calonAgen.periode');

        $calonAgen = $user->calonAgen;

        $notifikasiTerbaru = Notifikasi::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $hasilSmart = null;
        $penilaian  = collect();

        if ($calonAgen) {
            $hasilSmart = $calonAgen->hasilSmart;

            $penilaian = $calonAgen->penilaian()
                ->with(['kriteria', 'subKriteria'])
                ->get();
        }

        return view('calon-agen.dashboard', compact(
            'calonAgen',
            'notifikasiTerbaru',
            'hasilSmart',
            'penilaian'
        ));
    }

    public function updateDokumen(Request $request): RedirectResponse
    {
        $calonAgen = Auth::user()->calonAgen;

        if (!$calonAgen) {
            return back()->with('error', 'Data pendaftaran Anda tidak ditemukan.');
        }

        // Cek apakah dokumen sudah dinyatakan VALID
        if ($calonAgen->isVerifikasiValid()) {
            return back()->with('error', 'Dokumen administratif Anda sudah diverifikasi VALID oleh admin dan tidak dapat diubah lagi.');
        }

        // Cek apakah periode sudah ditutup
        if ($calonAgen->periode && $calonAgen->periode->isDitutup()) {
            return back()->with('error', 'Periode pendaftaran sudah ditutup, Anda tidak dapat mengubah data dokumen lagi.');
        }

        $request->validate([
            'ktp'                  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'formulir_pendaftaran' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'nib'                  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'npwp'                 => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        if (!$request->hasFile('ktp') && !$request->hasFile('formulir_pendaftaran') && !$request->hasFile('nib') && !$request->hasFile('npwp')) {
            return back()->withErrors([
                'dokumen' => 'Pilih setidaknya satu file dokumen yang ingin diunggah/diperbaiki.',
            ]);
        }

        $data = [];

        if ($request->hasFile('ktp')) {
            if ($calonAgen->ktp_path) {
                Storage::disk('public')->delete($calonAgen->ktp_path);
            }
            $data['ktp_path'] = $request->file('ktp')->store('dokumen-calon-agen/ktp', 'public');
        }

        if ($request->hasFile('formulir_pendaftaran')) {
            if ($calonAgen->formulir_pendaftaran_path) {
                Storage::disk('public')->delete($calonAgen->formulir_pendaftaran_path);
            }
            $data['formulir_pendaftaran_path'] = $request->file('formulir_pendaftaran')->store('dokumen-calon-agen/formulir-pendaftaran', 'public');
        }

        if ($request->hasFile('nib')) {
            if ($calonAgen->nib_path) {
                Storage::disk('public')->delete($calonAgen->nib_path);
            }
            $data['nib_path'] = $request->file('nib')->store('dokumen-calon-agen/nib', 'public');
        }

        if ($request->hasFile('npwp')) {
            if ($calonAgen->npwp_path) {
                Storage::disk('public')->delete($calonAgen->npwp_path);
            }
            $data['npwp_path'] = $request->file('npwp')->store('dokumen-calon-agen/npwp', 'public');
        }

        // Jika sebelumnya berstatus tidak_valid, kembalikan ke menunggu agar diverifikasi ulang oleh admin
        if ($calonAgen->isVerifikasiTidakValid()) {
            $data['status_verifikasi'] = 'menunggu';
            $data['catatan_verifikasi'] = null;
        }

        $calonAgen->update($data);

        return back()->with('success', 'Dokumen berhasil diperbaiki/diperbarui. Menunggu verifikasi dari admin.');
    }
}

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
        $request->validate([
            'nib'  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'npwp' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        if (!$request->hasFile('nib') && !$request->hasFile('npwp')) {
            return back()->withErrors([
                'dokumen' => 'Pilih file NIB atau NPWP yang ingin diunggah.',
            ]);
        }

        $calonAgen = Auth::user()->calonAgen;

        if (!$calonAgen) {
            return back()->with('error', 'Data pendaftaran Anda tidak ditemukan.');
        }

        $data = [];

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

        $calonAgen->update($data);

        return back()->with('success', 'Dokumen berhasil diperbarui.');
    }
}

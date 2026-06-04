<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilSmart;
use App\Models\PeriodePendaftaran;
use App\Services\SmartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SmartController extends Controller
{
    public function __construct(protected SmartService $smartService) {}

    /**
     * Tampil daftar periode + status perhitungan.
     */
    public function index(): View
    {
        $periodes = PeriodePendaftaran::withCount([
            'calonAgen',
            'hasilSmart',
        ])->latest()->get();

        return view('admin.smart.index', compact('periodes'));
    }

    /**
     * Tampil form konfirmasi hitung + hasil jika sudah pernah dihitung.
     */
    public function show(PeriodePendaftaran $periode): View
    {
        $sudahDihitung = HasilSmart::where('periode_id', $periode->id)->exists();
        $detail        = $sudahDihitung
            ? $this->smartService->getDetailPerhitungan($periode)
            : [];

        return view('admin.smart.show', compact('periode', 'sudahDihitung', 'detail'));
    }

    /**
     * Tampil langkah perhitungan SMART.
     */
    public function langkah(PeriodePendaftaran $periode): View
    {
        abort_unless(HasilSmart::where('periode_id', $periode->id)->exists(), 404);

        $langkah = $this->smartService->getLangkah($periode);

        return view('admin.smart.langkah', compact('periode', 'langkah'));
    }

    /**
     * Jalankan perhitungan SMART.
     */
    public function hitung(Request $request, PeriodePendaftaran $periode): RedirectResponse
    {
        $request->validate([
            'top_n' => ['required', 'integer', 'min:1'],
        ], [
            'top_n.required' => 'Jumlah calon agen yang direkomendasi wajib diisi.',
            'top_n.integer'  => 'Jumlah harus berupa angka.',
            'top_n.min'      => 'Jumlah minimal 1.',
        ]);

        $hasil = $this->smartService->hitung($periode, (int) $request->top_n);

        if ($hasil->isEmpty()) {
            return back()->with('error', 'Tidak ada calon agen yang sudah dinilai lengkap di periode ini.');
        }

        return redirect()
            ->route('admin.smart.show', $periode)
            ->with('success', "Perhitungan SMART selesai. {$hasil->count()} calon agen telah diperingkat.");
    }
}
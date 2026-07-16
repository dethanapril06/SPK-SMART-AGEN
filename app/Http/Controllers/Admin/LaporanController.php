<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonAgen;
use App\Models\HasilSmart;
use App\Models\Penilaian;
use App\Models\PeriodePendaftaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        $periodes = PeriodePendaftaran::orderBy('created_at', 'desc')->get();

        return view('admin.laporan.index', compact('periodes'));
    }

    // Laporan 1: Rekap Periode
    public function rekapPeriode()
    {
        $periodes = PeriodePendaftaran::withCount([
            'calonAgen',
            'calonAgen as total_direkomendasi' => fn($q) => $q->where('status', 'direkomendasi'),
            'calonAgen as total_belumdirekomendasi'  => fn($q) => $q->where('status', 'belumdirekomendasi'),
            'calonAgen as total_diproses' => fn($q) => $q->where('status', 'diproses'),
            'calonAgen as total_disurvey' => fn($q) => $q->where('status', 'disurvey'),
        ])->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('admin.laporan.pdf.rekap-periode', compact('periodes'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-rekap-periode.pdf');
    }

    // Laporan 2: Calon Agen
    public function calonAgen(Request $request)
    {
        $request->validate([
            'periode_id' => ['nullable', 'exists:periode_pendaftaran,id'],
            'status'     => ['nullable', 'in:diproses,disurvey,direkomendasi,belumdirekomendasi'],
        ]);

        $periode = null;
        if ($request->periode_id) {
            $periode = PeriodePendaftaran::find($request->periode_id);
        }

        $calonAgens = CalonAgen::with(['user', 'periode'])
            ->when($request->periode_id, fn($q) => $q->where('periode_id', $request->periode_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderBy('nama_usaha')
            ->get();

        $pdf = Pdf::loadView('admin.laporan.pdf.calon-agen', compact('calonAgens', 'periode', 'request'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-calon-agen.pdf');
    }

    // Laporan 3: Penilaian Per Kriteria
    public function penilaian(Request $request)
    {
        $request->validate([
            'periode_id' => ['required', 'exists:periode_pendaftaran,id'],
        ]);

        $periode = PeriodePendaftaran::findOrFail($request->periode_id);

        $penilaians = Penilaian::with(['calonAgen', 'kriteria', 'subKriteria'])
            ->where('periode_id', $request->periode_id)
            ->orderBy('calon_agen_id')
            ->orderBy('kriteria_id')
            ->get()
            ->groupBy('calon_agen_id');

        $pdf = Pdf::loadView('admin.laporan.pdf.penilaian', compact('penilaians', 'periode'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-penilaian.pdf');
    }

    // Laporan 4: Hasil Seleksi SMART
    public function hasilSeleksi(Request $request)
    {
        $request->validate([
            'periode_id' => ['required', 'exists:periode_pendaftaran,id'],
        ]);

        $periode = PeriodePendaftaran::findOrFail($request->periode_id);

        $hasilSmarts = HasilSmart::with(['calonAgen', 'periode'])
            ->where('periode_id', $request->periode_id)
            ->orderBy('peringkat')
            ->get();

        $pdf = Pdf::loadView('admin.laporan.pdf.hasil-seleksi', compact('hasilSmarts', 'periode'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-hasil-seleksi.pdf');
    }
}

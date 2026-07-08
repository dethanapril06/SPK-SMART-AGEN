<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePenilaianRequest;
use App\Models\CalonAgen;
use App\Models\Kriteria;
use App\Models\Penilaian;
use App\Models\PeriodePendaftaran;
use App\Models\SubKriteria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PenilaianController extends Controller
{
    /**
     * Langkah 1: Tampil daftar periode untuk dipilih.
     */
    public function index(): View
    {
        $periodes = PeriodePendaftaran::withCount('calonAgen')
            ->latest()
            ->get();

        return view('admin.penilaian.index', compact('periodes'));
    }

    /**
     * Langkah 2: Tampil daftar calon agen dalam periode yang dipilih.
     */
    public function daftarCalonAgen(PeriodePendaftaran $periode): View
    {
        $kriteriaCount = Kriteria::count();

        $calonAgens = CalonAgen::where('periode_id', $periode->id)
            ->withCount([
                'penilaian as sudah_dinilai_count' => function ($q) use ($periode) {
                    $q->where('periode_id', $periode->id);
                }
            ])
            ->get()
            ->map(function ($calonAgen) use ($kriteriaCount) {
                $calonAgen->sudah_lengkap = $calonAgen->sudah_dinilai_count >= $kriteriaCount;
                return $calonAgen;
            });

        return view('admin.penilaian.calon-agen', compact('periode', 'calonAgens', 'kriteriaCount'));
    }

    /**
     * Langkah 3: Form penilaian untuk satu calon agen.
     */
    public function form(PeriodePendaftaran $periode, CalonAgen $calonAgen): View
    {
        // Log sementara untuk membantu diagnosis 404 di production
        Log::info('PenilaianController::form binding', [
            'url' => request()->fullUrl(),
            'periode_id' => $periode->id ?? null,
            'calon_agen_id' => $calonAgen->id ?? null,
            'calon_agen_periode_id' => $calonAgen->periode_id ?? null,
            'user_id' => auth()->id(),
        ]);

        abort_if($calonAgen->periode_id !== $periode->id, 404);

        $kriterias = Kriteria::with('subKriteria')->get();

        // Ambil penilaian yang sudah ada: [kriteria_id => sub_kriteria_id]
        $existingPenilaian = Penilaian::where('periode_id', $periode->id)
            ->where('calon_agen_id', $calonAgen->id)
            ->pluck('sub_kriteria_id', 'kriteria_id');

        // Ambil catatan yang sudah ada: [kriteria_id => catatan]
        $existingCatatan = Penilaian::where('periode_id', $periode->id)
            ->where('calon_agen_id', $calonAgen->id)
            ->pluck('catatan', 'kriteria_id');

        $existingFormScreening = $calonAgen->form_screening_path;

        return view('admin.penilaian.form', compact(
            'periode',
            'calonAgen',
            'kriterias',
            'existingPenilaian',
            'existingCatatan',
            'existingFormScreening'
        ));
    }

    /**
     * Simpan atau update penilaian.
     */
    public function store(StorePenilaianRequest $request, PeriodePendaftaran $periode, CalonAgen $calonAgen): RedirectResponse
    {
        abort_if($calonAgen->periode_id !== $periode->id, 404);

        foreach ($request->validated()['penilaian'] as $kriteriaId => $subKriteriaId) {
            $nilaiInput = SubKriteria::find($subKriteriaId)->nilai;
            $catatan    = $request->input("catatan.{$kriteriaId}");

            Penilaian::updateOrCreate(
                [
                    'periode_id'    => $periode->id,
                    'calon_agen_id' => $calonAgen->id,
                    'kriteria_id'   => $kriteriaId,
                ],
                [
                    'sub_kriteria_id' => $subKriteriaId,
                    'admin_id'        => auth()->id(),
                    'nilai_input'     => $nilaiInput,
                    'catatan'         => $catatan ?: null,
                ]
            );
        }

        // Upload Form Screening (opsional, hanya admin yang bisa lihat)
        if ($request->hasFile('form_screening')) {
            if ($calonAgen->form_screening_path) {
                Storage::disk('public')->delete($calonAgen->form_screening_path);
            }
            $screeningPath = $request->file('form_screening')
                ->store('form-screening', 'public');
            $calonAgen->update(['form_screening_path' => $screeningPath]);
        }

        $calonAgen->update(['status' => 'disurvey']);

        return redirect()
            ->route('admin.penilaian.calon-agen', $periode)
            ->with('success', "Penilaian untuk {$calonAgen->nama_lengkap} berhasil disimpan.");
    }
}
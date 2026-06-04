<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Http\Request;

class SubKriteriaController extends Controller
{
    public function all(Request $request)
    {
        $kriterias = Kriteria::orderBy('kode_kriteria')->get();

        $subKriterias = SubKriteria::with('kriteria')
            ->when($request->kriteria_id, fn($q) => $q->where('kriteria_id', $request->kriteria_id))
            ->latest()
            ->get();

        return view('admin.sub-kriteria.all', compact('subKriterias', 'kriterias'));
    }
    public function index(Kriteria $kriteria)
    {
        $subKriterias = $kriteria->subKriteria()->latest()->get();

        return view('admin.sub-kriteria.index', compact('kriteria', 'subKriterias'));
    }

    public function create(Kriteria $kriteria)
    {
        return view('admin.sub-kriteria.create', compact('kriteria'));
    }

    public function store(Request $request, Kriteria $kriteria)
    {
        $validated = $request->validate([
            'nama_sub'    => ['required', 'string', 'max:255'],
            'nilai'       => ['required', 'numeric', 'min:0'],
            'keterangan'  => ['nullable', 'string', 'max:255'],
        ], [
            'nama_sub.required' => 'Nama sub kriteria wajib diisi.',
            'nilai.required'    => 'Nilai wajib diisi.',
            'nilai.numeric'     => 'Nilai harus berupa angka.',
            'nilai.min'         => 'Nilai tidak boleh negatif.',
        ]);

        $kriteria->subKriteria()->create($validated);

        return redirect()
            ->route('admin.kriteria.sub-kriteria.index', $kriteria)
            ->with('success', 'Sub kriteria berhasil ditambahkan.');
    }

    public function edit(SubKriteria $subKriteria)
    {
        $kriteria = $subKriteria->kriteria;

        return view('admin.sub-kriteria.edit', compact('kriteria', 'subKriteria'));
    }

    public function update(Request $request, SubKriteria $subKriteria)
    {
        $validated = $request->validate([
            'nama_sub'   => ['required', 'string', 'max:255'],
            'nilai'      => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'nama_sub.required' => 'Nama sub kriteria wajib diisi.',
            'nilai.required'    => 'Nilai wajib diisi.',
            'nilai.numeric'     => 'Nilai harus berupa angka.',
            'nilai.min'         => 'Nilai tidak boleh negatif.',
        ]);

        $subKriteria->update($validated);

        $kriteria = $subKriteria->kriteria;

        return redirect()
            ->route('admin.kriteria.sub-kriteria.index', $kriteria)
            ->with('success', 'Sub kriteria berhasil diperbarui.');
    }

    public function destroy(SubKriteria $subKriteria)
    {
        if ($subKriteria->penilaian()->exists()) {
            return back()->with('error', 'Sub kriteria tidak dapat dihapus karena sudah digunakan dalam penilaian.');
        }

        $kriteria = $subKriteria->kriteria;

        $subKriteria->delete();

        // Jika request dari halaman all, redirect ke all
        $referer = request()->headers->get('referer', '');
        if (str_contains($referer, 'sub-kriteria') && !str_contains($referer, 'kriteria')) {
            return redirect()
                ->route('admin.sub-kriteria.all')
                ->with('success', 'Sub kriteria berhasil dihapus.');
        }

        return redirect()
            ->route('admin.kriteria.sub-kriteria.index', $kriteria)
            ->with('success', 'Sub kriteria berhasil dihapus.');
    }
}
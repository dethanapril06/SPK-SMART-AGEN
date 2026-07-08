<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kriteria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KriteriaController extends Controller
{
    public function index()
    {
        $kriterias = Kriteria::with('pembuat')
            ->latest()
            ->get();

        return view('admin.kriteria.index', compact('kriterias'));
    }

    public function create()
    {
        return view('admin.kriteria.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kriteria' => ['required', 'string', 'max:255'],
            'kode_kriteria' => ['required', 'string', 'max:10', 'unique:kriteria,kode_kriteria'],
            'bobot'         => ['required', 'numeric', 'min:1', 'max:10'],
            'tipe'          => ['required', Rule::in(['benefit', 'cost'])],
        ], [
            'nama_kriteria.required' => 'Nama kriteria wajib diisi.',
            'kode_kriteria.required' => 'Kode kriteria wajib diisi.',
            'kode_kriteria.unique'   => 'Kode kriteria sudah digunakan.',
            'bobot.required'         => 'Bobot wajib diisi.',
            'bobot.numeric'          => 'Bobot harus berupa angka.',
            'bobot.min'              => 'Bobot minimal bernilai 1.',
            'bobot.max'              => 'Bobot maksimal bernilai 10.',
            'tipe.required'          => 'Tipe wajib dipilih.',
            'tipe.in'                => 'Tipe tidak valid.',
        ]);

        Kriteria::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function show(Kriteria $kriteria)
    {
        $kriteria->load(['pembuat', 'subKriteria']);

        return view('admin.kriteria.show', compact('kriteria'));
    }

    public function edit(Kriteria $kriteria)
    {
        return view('admin.kriteria.edit', compact('kriteria'));
    }

    public function update(Request $request, Kriteria $kriteria)
    {
        $validated = $request->validate([
            'nama_kriteria' => ['required', 'string', 'max:255'],
            'kode_kriteria' => ['required', 'string', 'max:10', Rule::unique('kriteria', 'kode_kriteria')->ignore($kriteria->id)],
            'bobot'         => ['required', 'numeric', 'min:1', 'max:10'],
            'tipe'          => ['required', Rule::in(['benefit', 'cost'])],
        ], [
            'nama_kriteria.required' => 'Nama kriteria wajib diisi.',
            'kode_kriteria.required' => 'Kode kriteria wajib diisi.',
            'kode_kriteria.unique'   => 'Kode kriteria sudah digunakan.',
            'bobot.required'         => 'Bobot wajib diisi.',
            'bobot.numeric'          => 'Bobot harus berupa angka.',
            'bobot.min'              => 'Bobot minimal bernilai 1.',
            'bobot.max'              => 'Bobot maksimal bernilai 10.',
            'tipe.required'          => 'Tipe wajib dipilih.',
            'tipe.in'                => 'Tipe tidak valid.',
        ]);

        $kriteria->update($validated);

        return redirect()
            ->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Kriteria $kriteria)
    {
        if ($kriteria->subKriteria()->exists()) {
            return redirect()
                ->route('admin.kriteria.index')
                ->with('error', 'Kriteria tidak dapat dihapus karena masih memiliki sub kriteria.');
        }

        $kriteria->delete();

        return redirect()
            ->route('admin.kriteria.index')
            ->with('success', 'Kriteria berhasil dihapus.');
    }
}

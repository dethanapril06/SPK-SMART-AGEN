<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodePendaftaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PeriodePendaftaranController extends Controller
{
    /**
     * Tampilkan daftar semua periode pendaftaran.
     */
    public function index()
    {
        $periodes = PeriodePendaftaran::with('pembuat')
            ->latest()
            ->get();

        return view('admin.periode-pendaftaran.index', compact('periodes'));
    }

    /**
     * Tampilkan form untuk membuat periode baru.
     */
    public function create()
    {
        return view('admin.periode-pendaftaran.create');
    }

    /**
     * Simpan periode pendaftaran baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_periode'  => ['required', 'string', 'max:255'],
            'tanggal_buka'  => ['required', 'date'],
            'tanggal_tutup' => ['required', 'date', 'after:tanggal_buka'],
            'status'        => ['required', Rule::in(['draft', 'aktif', 'ditutup'])],
        ], [
            'nama_periode.required'  => 'Nama periode wajib diisi.',
            'tanggal_buka.required'  => 'Tanggal buka wajib diisi.',
            'tanggal_tutup.required' => 'Tanggal tutup wajib diisi.',
            'tanggal_tutup.after'    => 'Tanggal tutup harus setelah tanggal buka.',
            'status.required'        => 'Status wajib dipilih.',
            'status.in'              => 'Status tidak valid.',
        ]);

        // Pastikan hanya ada satu periode yang aktif
        if ($validated['status'] === 'aktif') {
            $this->nonaktifkanPeriodeLain();
        }

        PeriodePendaftaran::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('admin.periode-pendaftaran.index')
            ->with('success', 'Periode pendaftaran berhasil dibuat.');
    }

    /**
     * Tampilkan detail satu periode pendaftaran.
     */
    public function show(PeriodePendaftaran $periodePendaftaran)
    {
        $periodePendaftaran->load([
            'pembuat',
            'calonAgen',
            'penilaian',
            'hasilSmart',
        ]);

        return view('admin.periode-pendaftaran.show', compact('periodePendaftaran'));
    }

    /**
     * Tampilkan form edit periode pendaftaran.
     */
    public function edit(PeriodePendaftaran $periodePendaftaran)
    {
        return view('admin.periode-pendaftaran.edit', compact('periodePendaftaran'));
    }

    /**
     * Update periode pendaftaran di database.
     */
    public function update(Request $request, PeriodePendaftaran $periodePendaftaran)
    {
        $validated = $request->validate([
            'nama_periode'  => ['required', 'string', 'max:255'],
            'tanggal_buka'  => ['required', 'date'],
            'tanggal_tutup' => ['required', 'date', 'after:tanggal_buka'],
            'status'        => ['required', Rule::in(['draft', 'aktif', 'ditutup'])],
        ], [
            'nama_periode.required'  => 'Nama periode wajib diisi.',
            'tanggal_buka.required'  => 'Tanggal buka wajib diisi.',
            'tanggal_tutup.required' => 'Tanggal tutup wajib diisi.',
            'tanggal_tutup.after'    => 'Tanggal tutup harus setelah tanggal buka.',
            'status.required'        => 'Status wajib dipilih.',
            'status.in'              => 'Status tidak valid.',
        ]);

        // Pastikan hanya ada satu periode yang aktif
        if ($validated['status'] === 'aktif') {
            $this->nonaktifkanPeriodeLain(exceptId: $periodePendaftaran->id);
        }

        $periodePendaftaran->update($validated);

        return redirect()
            ->route('admin.periode-pendaftaran.index')
            ->with('success', 'Periode pendaftaran berhasil diperbarui.');
    }

    /**
     * Hapus periode pendaftaran dari database.
     */
    public function destroy(PeriodePendaftaran $periodePendaftaran)
    {
        // Cegah hapus jika sedang aktif
        if ($periodePendaftaran->isAktif()) {
            return redirect()
                ->route('admin.periode-pendaftaran.index')
                ->with('error', 'Periode yang sedang aktif tidak dapat dihapus.');
        }

        // Cegah hapus jika sudah ada data terkait
        if ($periodePendaftaran->calonAgen()->exists()) {
            return redirect()
                ->route('admin.periode-pendaftaran.index')
                ->with('error', 'Periode tidak dapat dihapus karena sudah memiliki data calon agen.');
        }

        $periodePendaftaran->delete();

        return redirect()
            ->route('admin.periode-pendaftaran.index')
            ->with('success', 'Periode pendaftaran berhasil dihapus.');
    }

    /**
     * Ubah status periode secara cepat (toggle).
     */
    public function ubahStatus(Request $request, PeriodePendaftaran $periodePendaftaran)
    {
        $request->validate([
            'status' => ['required', Rule::in(['draft', 'aktif', 'ditutup'])],
        ]);

        $status = $request->input('status');

        if ($status === 'aktif') {
            $this->nonaktifkanPeriodeLain(exceptId: $periodePendaftaran->id);
        }

        $periodePendaftaran->update(['status' => $status]);

        return redirect()
            ->back()
            ->with('success', "Status periode berhasil diubah menjadi '{$status}'.");
    }

    /**
     * Nonaktifkan semua periode yang sedang aktif,
     * kecuali periode dengan ID tertentu.
     */
    private function nonaktifkanPeriodeLain(?int $exceptId = null): void
    {
        PeriodePendaftaran::where('status', 'aktif')
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->update(['status' => 'ditutup']);
    }
}
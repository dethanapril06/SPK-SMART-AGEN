<?php

namespace App\Services;

use App\Models\CalonAgen;
use App\Models\HasilSmart;
use App\Models\Kriteria;
use App\Models\Notifikasi;
use App\Models\Penilaian;
use App\Models\PeriodePendaftaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SmartService
{
    /**
     * Jalankan perhitungan SMART untuk satu periode.
     * Mengembalikan collection hasil yang sudah diurutkan.
     *
     * @param  PeriodePendaftaran  $periode
     * @param  int                 $topN  Jumlah calon agen terbaik yang direkomendasikan
     */
    public function hitung(PeriodePendaftaran $periode, int $topN): Collection
    {
        // -------------------------------------------------------------------
        // 1. Ambil semua kriteria beserta bobotnya
        //    Normalisasi bobot: bobot / total bobot (agar jumlahnya = 1)
        // -------------------------------------------------------------------
        $kriterias    = Kriteria::all();
        $totalBobot   = $kriterias->sum('bobot');

        if ($kriterias->isEmpty() || $totalBobot <= 0) {
            return collect();
        }

        $bobotNormal  = $kriterias->mapWithKeys(fn ($k) => [
            $k->id => $k->bobot / $totalBobot,
        ]);

        // -------------------------------------------------------------------
        // 2. Ambil semua calon agen yang sudah dinilai lengkap di periode ini
        //    (jumlah penilaian == jumlah kriteria)
        // -------------------------------------------------------------------
        $jumlahKriteria = $kriterias->count();

        $calonAgens = CalonAgen::where('periode_id', $periode->id)
            ->whereHas('penilaian', function ($q) use ($periode) {
                $q->where('periode_id', $periode->id);
            }, '>=', $jumlahKriteria)
            ->get();

        if ($calonAgens->isEmpty()) {
            return collect();
        }

        // -------------------------------------------------------------------
        // 3. Ambil semua penilaian untuk periode ini, kelompokkan per
        //    calon_agen_id dan kriteria_id
        //    Struktur: $nilaiMatrix[calon_agen_id][kriteria_id] = nilai_input
        // -------------------------------------------------------------------
        $semuaPenilaian = Penilaian::where('periode_id', $periode->id)
            ->whereIn('calon_agen_id', $calonAgens->pluck('id'))
            ->get();

        $nilaiMatrix = [];
        foreach ($semuaPenilaian as $p) {
            $nilaiMatrix[$p->calon_agen_id][$p->kriteria_id] = $p->nilai_input;
        }

        // -------------------------------------------------------------------
        // 4. Normalisasi nilai per kriteria (SMART)
        //    - Benefit : (nilai - nilai_min) / (nilai_maks - nilai_min)
        //    - Cost    : (nilai_maks - nilai) / (nilai_maks - nilai_min)
        // -------------------------------------------------------------------
        $nilaiMaks = [];
        $nilaiMin  = [];

        foreach ($kriterias as $k) {
            $nilaiKolom = collect($nilaiMatrix)
                ->pluck($k->id)
                ->filter(fn ($nilai) => $nilai !== null)
                ->values();

            $nilaiMaks[$k->id] = $nilaiKolom->max();
            $nilaiMin[$k->id]  = $nilaiKolom->min();
        }

        $nilaiNormal = []; // [calon_agen_id][kriteria_id] = nilai ternormalisasi

        foreach ($calonAgens as $ca) {
            foreach ($kriterias as $k) {
                $nilai = $nilaiMatrix[$ca->id][$k->id] ?? 0;
                $rentang = $nilaiMaks[$k->id] - $nilaiMin[$k->id];

                if ($k->tipe === 'benefit') {
                    $nilaiNormal[$ca->id][$k->id] = $rentang > 0
                        ? ($nilai - $nilaiMin[$k->id]) / $rentang
                        : 0;
                } else {
                    // cost: makin kecil makin baik
                    $nilaiNormal[$ca->id][$k->id] = $rentang > 0
                        ? ($nilaiMaks[$k->id] - $nilai) / $rentang
                        : 0;
                }
            }
        }

        // -------------------------------------------------------------------
        // 5. Hitung skor akhir SMART
        //    Skor = Σ (bobot_normal[k] * nilai_normal[ca][k])
        // -------------------------------------------------------------------
        $skorList = collect();

        foreach ($calonAgens as $ca) {
            $skor = 0;

            foreach ($kriterias as $k) {
                $skor += $bobotNormal[$k->id] * ($nilaiNormal[$ca->id][$k->id] ?? 0);
            }

            $skorList->push([
                'calon_agen'    => $ca,
                'skor_akhir'    => round($skor, 6),
                'nilai_normal'  => $nilaiNormal[$ca->id],
                'nilai_asli'    => $nilaiMatrix[$ca->id],
            ]);
        }

        // -------------------------------------------------------------------
        // 6. Urutkan dari skor tertinggi, tentukan peringkat & keputusan
        // -------------------------------------------------------------------
        $skorSorted = $skorList->sortByDesc('skor_akhir')->values();

        // -------------------------------------------------------------------
        // 7. Simpan hasil ke tabel hasil_smart (overwrite jika sudah ada)
        //    dan update status calon agen + kirim notifikasi
        // -------------------------------------------------------------------
        DB::transaction(function () use ($skorSorted, $topN, $periode) {
            foreach ($skorSorted as $peringkat => $item) {
                $nomorPeringkat = $peringkat + 1;
                $keputusan      = $nomorPeringkat <= $topN ? 'direkomendasi' : 'belumdirekomendasi';
                $calonAgen      = $item['calon_agen'];

                // Simpan / update hasil SMART
                HasilSmart::updateOrCreate(
                    [
                        'calon_agen_id' => $calonAgen->id,
                        'periode_id'    => $periode->id,
                    ],
                    [
                        'skor_akhir'  => $item['skor_akhir'],
                        'peringkat'   => $nomorPeringkat,
                        'keputusan'   => $keputusan,
                        'dihitung_at' => now(),
                    ]
                );

                // Update status calon agen
                $calonAgen->update(['status' => $keputusan]);

                // Kirim notifikasi ke user calon agen
                $judul = $keputusan === 'direkomendasi'
                    ? 'Selamat! Anda Direkomendasikan Sebagai Agen'
                    : 'Mohon Maaf, Pendaftaran Anda Tidak Direkomendasikan';

                $pesan = $keputusan === 'direkomendasi'
                    ? "Selamat {$calonAgen->nama_lengkap}! Anda dinyatakan DIREKOMENDASIKAN sebagai agen pada periode {$periode->nama_periode} dengan peringkat ke-{$nomorPeringkat}."
                    : "Mohon maaf {$calonAgen->nama_lengkap}, Anda dinyatakan tidak direkomendasikan pada periode {$periode->nama_periode}. Terima kasih telah mendaftar.";

                // Hapus notifikasi lama untuk periode ini (jika hitung ulang)
                Notifikasi::where('user_id', $calonAgen->user_id)
                    ->where('calon_agen_id', $calonAgen->id)
                    ->whereIn('tipe', ['direkomendasi', 'belumdirekomendasi'])
                    ->delete();

                Notifikasi::create([
                    'user_id'       => $calonAgen->user_id,
                    'calon_agen_id' => $calonAgen->id,
                    'judul'         => $judul,
                    'pesan'         => $pesan,
                    'tipe'          => $keputusan,
                    'is_read'       => false,
                ]);
            }
        });

        return $skorSorted;
    }

    /**
     * Ambil detail perhitungan untuk ditampilkan di view (tabel proses SMART).
     * Mengembalikan data mentah per calon agen per kriteria.
     */
    public function getDetailPerhitungan(PeriodePendaftaran $periode): array
    {
        $kriterias = Kriteria::all();

        $hasilSmart = HasilSmart::where('periode_id', $periode->id)
            ->with('calonAgen')
            ->orderBy('peringkat')
            ->get();

        if ($hasilSmart->isEmpty()) {
            return [];
        }

        $calonAgenIds = $hasilSmart->pluck('calon_agen_id');

        $penilaian = Penilaian::where('periode_id', $periode->id)
            ->whereIn('calon_agen_id', $calonAgenIds)
            ->get()
            ->groupBy('calon_agen_id');

        return [
            'kriterias'   => $kriterias,
            'hasil'       => $hasilSmart,
            'penilaian'   => $penilaian,
        ];
    }

    public function getLangkah(PeriodePendaftaran $periode): array
    {
        $kriterias  = Kriteria::all();
        $totalBobot = $kriterias->sum('bobot');

        $bobotNormal = $kriterias->mapWithKeys(fn($k) => [
            $k->id => $totalBobot > 0 ? $k->bobot / $totalBobot : 0,
        ]);

        // Ambil hasil SMART yang sudah tersimpan, urutkan by peringkat
        $hasilSmart = HasilSmart::where('periode_id', $periode->id)
            ->with('calonAgen')
            ->orderBy('peringkat')
            ->get();

        $calonAgenIds = $hasilSmart->pluck('calon_agen_id');

        // Ambil semua penilaian
        $semuaPenilaian = Penilaian::where('periode_id', $periode->id)
            ->whereIn('calon_agen_id', $calonAgenIds)
            ->get();

        // Susun matriks nilai asli [calon_agen_id][kriteria_id] = nilai_input
        $nilaiMatrix = [];
        foreach ($semuaPenilaian as $p) {
            $nilaiMatrix[$p->calon_agen_id][$p->kriteria_id] = $p->nilai_input;
        }

        // Hitung nilai maks & min per kriteria
        $nilaiMaks = [];
        $nilaiMin  = [];
        foreach ($kriterias as $k) {
            $kolom = collect($nilaiMatrix)
                ->pluck($k->id)
                ->filter(fn ($nilai) => $nilai !== null)
                ->values();
            $nilaiMaks[$k->id] = $kolom->max();
            $nilaiMin[$k->id]  = $kolom->min();
        }

        // Hitung nilai normal per calon agen per kriteria
        $calonAgens = [];
        foreach ($hasilSmart as $h) {
            $ca          = $h->calonAgen;
            $nilaiNormal = [];

            foreach ($kriterias as $k) {
                $nilai = $nilaiMatrix[$ca->id][$k->id] ?? 0;
                $rentang = $nilaiMaks[$k->id] - $nilaiMin[$k->id];

                $nilaiNormal[$k->id] = $k->tipe === 'benefit'
                    ? ($rentang > 0 ? round(($nilai - $nilaiMin[$k->id]) / $rentang, 6) : 0)
                    : ($rentang > 0 ? round(($nilaiMaks[$k->id] - $nilai) / $rentang, 6) : 0);
            }

            $calonAgens[] = [
                'nama'         => $ca->nama_lengkap,
                'nilai_asli'   => $nilaiMatrix[$ca->id] ?? [],
                'nilai_normal' => $nilaiNormal,
                'skor_akhir'   => $h->skor_akhir,
                'peringkat'    => $h->peringkat,
                'keputusan'    => $h->keputusan,
            ];
        }

        return [
            'kriterias'   => $kriterias,
            'calon_agens' => $calonAgens,
            'nilai_maks'  => $nilaiMaks,
            'nilai_min'   => $nilaiMin,
            'bobot_normal'=> $bobotNormal,
        ];
    }
}

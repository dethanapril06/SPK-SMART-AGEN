<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use App\Models\SubKriteria;
use Illuminate\Database\Seeder;

class SubKriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kriteria = Kriteria::pluck('id', 'kode_kriteria');
 
        $subKriterias = [
            // --- C1: Kelengkapan Administratif ---
            ['kriteria_id' => $kriteria['C1'], 'nama_sub' => 'Memiliki satu dokumen administratif',              'nilai' => 25],
            ['kriteria_id' => $kriteria['C1'], 'nama_sub' => 'Memiliki dua dokumen administratif',              'nilai' => 50],
            ['kriteria_id' => $kriteria['C1'], 'nama_sub' => 'Memiliki dokumen lengkap namun tidak seluruhnya valid', 'nilai' => 75],
            ['kriteria_id' => $kriteria['C1'], 'nama_sub' => 'Memiliki dokumen lengkap dan valid',               'nilai' => 100],
 
            // --- C2: Lokasi Usaha ---
            ['kriteria_id' => $kriteria['C2'], 'nama_sub' => 'Tidak strategis',   'nilai' => 20],
            ['kriteria_id' => $kriteria['C2'], 'nama_sub' => 'Kurang strategis',  'nilai' => 40],
            ['kriteria_id' => $kriteria['C2'], 'nama_sub' => 'Cukup strategis',   'nilai' => 60],
            ['kriteria_id' => $kriteria['C2'], 'nama_sub' => 'Strategis',         'nilai' => 80],
            ['kriteria_id' => $kriteria['C2'], 'nama_sub' => 'Sangat strategis',  'nilai' => 100],
 
            // --- C3: Riwayat Kredit ---
            ['kriteria_id' => $kriteria['C3'], 'nama_sub' => 'Riwayat kredit bermasalah berat',  'nilai' => 20],
            ['kriteria_id' => $kriteria['C3'], 'nama_sub' => 'Riwayat kredit bermasalah sedang', 'nilai' => 40],
            ['kriteria_id' => $kriteria['C3'], 'nama_sub' => 'Riwayat kredit bermasalah ringan', 'nilai' => 60],
            ['kriteria_id' => $kriteria['C3'], 'nama_sub' => 'Riwayat kredit cukup baik',        'nilai' => 80],
            ['kriteria_id' => $kriteria['C3'], 'nama_sub' => 'Riwayat kredit sangat baik',       'nilai' => 100],
 
            // --- C4: Komitmen Berusaha ---
            ['kriteria_id' => $kriteria['C4'], 'nama_sub' => 'Komitmen berusaha sangat rendah', 'nilai' => 20],
            ['kriteria_id' => $kriteria['C4'], 'nama_sub' => 'Komitmen berusaha rendah',        'nilai' => 40],
            ['kriteria_id' => $kriteria['C4'], 'nama_sub' => 'Komitmen berusaha cukup',         'nilai' => 60],
            ['kriteria_id' => $kriteria['C4'], 'nama_sub' => 'Komitmen berusaha baik',          'nilai' => 80],
            ['kriteria_id' => $kriteria['C4'], 'nama_sub' => 'Komitmen berusaha sangat baik',   'nilai' => 100],
 
            // --- C5: Modal Usaha ---
            ['kriteria_id' => $kriteria['C5'], 'nama_sub' => '< Rp 500.000',              'nilai' => 20],
            ['kriteria_id' => $kriteria['C5'], 'nama_sub' => 'Rp 500.000',                'nilai' => 40],
            ['kriteria_id' => $kriteria['C5'], 'nama_sub' => 'Rp 600.000 – Rp 1.000.000','nilai' => 60],
            ['kriteria_id' => $kriteria['C5'], 'nama_sub' => 'Rp 1.000.000 – Rp 2.000.000', 'nilai' => 80],
            ['kriteria_id' => $kriteria['C5'], 'nama_sub' => '> Rp 3.000.000',            'nilai' => 100],
 
            // --- C6: Ketersediaan Sarana Pendukung ---
            ['kriteria_id' => $kriteria['C6'], 'nama_sub' => 'Sarana pendukung sangat tidak memadai', 'nilai' => 20],
            ['kriteria_id' => $kriteria['C6'], 'nama_sub' => 'Sarana pendukung tidak memadai',        'nilai' => 40],
            ['kriteria_id' => $kriteria['C6'], 'nama_sub' => 'Sarana pendukung cukup memadai',        'nilai' => 60],
            ['kriteria_id' => $kriteria['C6'], 'nama_sub' => 'Sarana pendukung memadai',              'nilai' => 80],
            ['kriteria_id' => $kriteria['C6'], 'nama_sub' => 'Sarana pendukung sangat memadai',       'nilai' => 100],
        ];
 
        foreach ($subKriterias as $sub) {
            SubKriteria::firstOrCreate(
                [
                    'kriteria_id' => $sub['kriteria_id'],
                    'nama_sub'    => $sub['nama_sub'],
                ],
                ['nilai' => $sub['nilai']]
            );
        }
    }
}

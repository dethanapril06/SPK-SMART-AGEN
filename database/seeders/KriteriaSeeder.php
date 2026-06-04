<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use App\Models\User;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
 
        $kriterias = [
            [
                'kode_kriteria' => 'C1',
                'nama_kriteria' => 'Kelengkapan Administratif',
                'tipe'          => 'benefit',
                'bobot'         => 20,
                'created_by'    => $admin->id,
            ],
            [
                'kode_kriteria' => 'C2',
                'nama_kriteria' => 'Lokasi Usaha',
                'tipe'          => 'benefit',
                'bobot'         => 20,
                'created_by'    => $admin->id,
            ],
            [
                'kode_kriteria' => 'C3',
                'nama_kriteria' => 'Riwayat Kredit',
                'tipe'          => 'cost',
                'bobot'         => 17,
                'created_by'    => $admin->id,
            ],
            [
                'kode_kriteria' => 'C4',
                'nama_kriteria' => 'Komitmen Berusaha',
                'tipe'          => 'benefit',
                'bobot'         => 20,
                'created_by'    => $admin->id,
            ],
            [
                'kode_kriteria' => 'C5',
                'nama_kriteria' => 'Modal Usaha',
                'tipe'          => 'benefit',
                'bobot'         => 10,
                'created_by'    => $admin->id,
            ],
            [
                'kode_kriteria' => 'C6',
                'nama_kriteria' => 'Ketersediaan Sarana Pendukung',
                'tipe'          => 'benefit',
                'bobot'         => 13,
                'created_by'    => $admin->id,
            ],
        ];
 
        foreach ($kriterias as $kriteria) {
            Kriteria::firstOrCreate(
                ['kode_kriteria' => $kriteria['kode_kriteria']],
                $kriteria
            );
        }
    }
}

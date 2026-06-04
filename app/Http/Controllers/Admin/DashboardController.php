<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CalonAgen;
use App\Models\HasilSmart;
use App\Models\Kriteria;
use App\Models\PeriodePendaftaran;
use App\Models\SubKriteria;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik utama
        $totalCalonAgen     = CalonAgen::count();
        $totalKriteria      = Kriteria::count();
        $totalSubKriteria   = SubKriteria::count();
        $totalAdmin         = User::where('role', 'admin')->count();

        // Statistik calon agen per status
        $totalDiproses  = CalonAgen::where('status', 'diproses')->count();
        $totalDisurvey  = CalonAgen::where('status', 'disurvey')->count();
        $totalDirekomendasi  = CalonAgen::where('status', 'direkomendasi')->count();
        $totalBelumDirekomendasi   = CalonAgen::where('status', 'belumdirekomendasi')->count();

        // Periode aktif
        $periodeAktif = PeriodePendaftaran::where('status', 'aktif')->first();

        // Calon agen terbaru (5 data)
        $calonAgenTerbaru = CalonAgen::with(['user', 'periode'])
            ->latest()
            ->take(5)
            ->get();

        // Hasil SMART terbaru jika ada
        $hasilSmartTerbaru = HasilSmart::with(['calonAgen', 'periode'])
            ->latest('dihitung_at')
            ->take(5)
            ->get();

        // Total periode
        $totalPeriode   = PeriodePendaftaran::count();
        $totalHasilSmart = HasilSmart::count();

        return view('admin.dashboard', compact(
            'totalCalonAgen',
            'totalKriteria',
            'totalSubKriteria',
            'totalAdmin',
            'totalDiproses',
            'totalDisurvey',
            'totalDirekomendasi',
            'totalBelumDirekomendasi',
            'periodeAktif',
            'calonAgenTerbaru',
            'hasilSmartTerbaru',
            'totalPeriode',
            'totalHasilSmart',
        ));
    }
}
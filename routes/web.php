<?php

use App\Http\Controllers\Admin\CalonAgenController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\KriteriaController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PenilaianController;
use App\Http\Controllers\Admin\PeriodePendaftaranController;
use App\Http\Controllers\Admin\SmartController;
use App\Http\Controllers\Admin\SubKriteriaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\CalonAgen\DashboardController as CalonAgenDashboard;
use App\Http\Controllers\CalonAgen\NotifikasiController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// -------------------------------------------------------------------------
// Guest routes (hanya bisa diakses jika belum login)
// -------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

// -------------------------------------------------------------------------
// Authenticated routes
// -------------------------------------------------------------------------
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin routes
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
            Route::resource('periode-pendaftaran', PeriodePendaftaranController::class);
            Route::patch('periode-pendaftaran/{periodePendaftaran}/status', [PeriodePendaftaranController::class, 'ubahStatus'])
                ->name('periode-pendaftaran.ubah-status');
            
            Route::get('calon-agen', [CalonAgenController::class, 'index'])
                ->name('calon-agen.index');
            Route::get('calon-agen/create', [CalonAgenController::class, 'create'])
                ->name('calon-agen.create');
            Route::post('calon-agen', [CalonAgenController::class, 'store'])
                ->name('calon-agen.store');
            Route::get('calon-agen/{calonAgen}/edit', [CalonAgenController::class, 'edit'])
                ->name('calon-agen.edit');
            Route::put('calon-agen/{calonAgen}', [CalonAgenController::class, 'update'])
                ->name('calon-agen.update');
            Route::get('calon-agen/{calonAgen}', [CalonAgenController::class, 'show'])
                ->name('calon-agen.show');
            Route::patch('calon-agen/{calonAgen}/status', [CalonAgenController::class, 'ubahStatus'])
                ->name('calon-agen.ubah-status');
            Route::delete('calon-agen/{calonAgen}', [CalonAgenController::class, 'destroy'])   
                ->name('calon-agen.destroy');

            Route::resource('kriteria', KriteriaController::class)
                ->parameters(['kriteria' => 'kriteria']);;
            Route::get('sub-kriteria', [SubKriteriaController::class, 'all'])
                ->name('sub-kriteria.all');
            Route::resource('kriteria.sub-kriteria', SubKriteriaController::class)
                ->parameters(['kriteria' => 'kriteria', 'sub-kriteria' => 'subKriteria'])
                ->except(['show'])
                ->shallow();
            
            Route::resource('user', UserController::class)
                ->except(['show']);
            Route::patch('user/{user}/reset-password', [UserController::class, 'resetPassword'])
                ->name('user.reset-password');
            
            Route::prefix('penilaian')->name('penilaian.')->group(function () {
                Route::get('/', [PenilaianController::class, 'index'])->name('index');
 
                Route::get('/{periode}', [PenilaianController::class, 'daftarCalonAgen'])->name('calon-agen');
 
                Route::get('/{periode}/{calonAgen}', [PenilaianController::class, 'form'])->name('form');
 
                Route::post('/{periode}/{calonAgen}', [PenilaianController::class, 'store'])->name('store');
            });

            Route::prefix('smart')->name('smart.')->group(function () {
                Route::get('/',[SmartController::class, 'index'])
                    ->name('index');
                Route::get('/{periode}',[SmartController::class, 'show'])
                    ->name('show');
                Route::post('/{periode}/hitung',[SmartController::class, 'hitung'])
                    ->name('hitung');
                Route::get('/{periode}/langkah',[SmartController::class, 'langkah'])
                    ->name('langkah');
            });

            Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
            Route::get('laporan/rekap-periode', [LaporanController::class, 'rekapPeriode'])->name('laporan.rekap-periode');
            Route::get('laporan/calon-agen', [LaporanController::class, 'calonAgen'])->name('laporan.calon-agen');
            Route::get('laporan/penilaian', [LaporanController::class, 'penilaian'])->name('laporan.penilaian');
            Route::get('laporan/hasil-seleksi', [LaporanController::class, 'hasilSeleksi'])->name('laporan.hasil-seleksi');
        });

    // Calon Agen routes
    Route::middleware('role:calon_agen')
        ->prefix('calon-agen')
        ->name('calon-agen.')
        ->group(function () {
            Route::get('/dashboard', [CalonAgenDashboard::class, 'index'])->name('dashboard');
            Route::patch('/dokumen', [CalonAgenDashboard::class, 'updateDokumen'])->name('dokumen.update');
            
            Route::get('notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
            Route::get('notifikasi/{notifikasi}/read', [NotifikasiController::class, 'read'])->name('notifikasi.read');
        });

});

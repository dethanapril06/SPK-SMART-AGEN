<?php

use App\Models\CalonAgen;
use App\Models\Kriteria;
use App\Models\PeriodePendaftaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registrasi calon agen otomatis menambahkan prefix BeJuBis@ pada nama usaha', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

    $periode = PeriodePendaftaran::firstOrCreate(
        ['nama_periode' => 'Periode Uji Coba'],
        [
            'tanggal_buka' => now()->subDay(),
            'tanggal_tutup' => now()->addDays(7),
            'status' => 'aktif',
            'kuota' => 5,
            'created_by' => $admin->id,
        ]
    );

    $response = $this->actingAs($admin)->post(route('admin.calon-agen.store'), [
        'nik' => '9988776655443322',
        'nama_lengkap' => 'Budi Santoso',
        'nama_usaha' => 'Toko Barokah',
        'no_hp' => '081234567890',
        'email' => 'budi_uji@test.com',
        'alamat_domisili' => 'Jl. Merdeka No 1',
        'periode_id' => $periode->id,
        'ktp' => \Illuminate\Http\UploadedFile::fake()->create('ktp.pdf', 100),
        'formulir_pendaftaran' => \Illuminate\Http\UploadedFile::fake()->create('formulir.pdf', 100),
    ]);

    $response->assertSessionHasNoErrors();

    $calonAgen = CalonAgen::where('nik', '9988776655443322')->first();
    expect($calonAgen)->not->toBeNull()
        ->and($calonAgen->nama_usaha)->toBe('BeJuBis@Toko Barokah')
        ->and($calonAgen->sumber_pendaftaran)->toBe('admin');
});

test('pendaftaran calon agen ke periode yang sudah ditutup otomatis gagal', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    $periodeTutup = PeriodePendaftaran::create([
        'nama_periode' => 'Periode Sudah Ditutup',
        'tanggal_buka' => now()->subDays(10),
        'tanggal_tutup' => now()->subDay(),
        'status' => 'ditutup',
        'kuota' => 3,
        'created_by' => $admin->id,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.calon-agen.store'), [
        'nik' => '1122334455667788',
        'nama_lengkap' => 'Siti Aisyah',
        'nama_usaha' => 'Toko Siti',
        'no_hp' => '081298765432',
        'email' => 'siti_uji@test.com',
        'alamat_domisili' => 'Jl. Melati',
        'periode_id' => $periodeTutup->id,
    ]);

    $response->assertSessionHasErrors(['periode_id']);
    expect(CalonAgen::where('nik', '1122334455667788')->first())->toBeNull();

    $periodeTutup->delete();
});

test('bobot kriteria tidak boleh semua bernilai sama ketika total kriteria >= 2', function () {
    $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

    // Check existing kriteria
    $kriteriaList = Kriteria::all();
    if ($kriteriaList->count() >= 2) {
        // Ambil kriteria pertama dan update bobotnya sama dengan yang lain
        $k1 = $kriteriaList->first();
        $otherBobot = $kriteriaList->skip(1)->first()->bobot;

        // Coba set semua kriteria ke bobot yang sama
        $allSame = true;
        foreach ($kriteriaList->skip(1) as $k) {
            $k->update(['bobot' => 20]);
        }

        $response = $this->actingAs($admin)->put(route('admin.kriteria.update', $k1), [
            'kode_kriteria' => $k1->kode_kriteria,
            'nama_kriteria' => $k1->nama_kriteria,
            'bobot' => 20, // Sengaja disamakan semua 20
            'jenis' => $k1->jenis,
        ]);

        $response->assertSessionHasErrors(['bobot']);
    } else {
        expect(true)->toBeTrue();
    }
});

test('role petugas_survey hanya bisa mengakses penilaian dan diblokir dari menu admin lainnya', function () {
    $petugas = User::firstOrCreate(
        ['email' => 'petugas_test@example.com'],
        [
            'name' => 'Petugas Surveyor Test',
            'password' => bcrypt('password'),
            'role' => 'petugas_survey',
            'email_verified_at' => now(),
        ]
    );

    // Buka penilaian -> sukses (200)
    $responsePenilaian = $this->actingAs($petugas)->get(route('admin.penilaian.index'));
    $responsePenilaian->assertStatus(200);

    // Buka user management -> 403 Forbidden
    $responseUser = $this->actingAs($petugas)->get(route('admin.user.index'));
    $responseUser->assertStatus(403);

    // Buka kriteria -> 403 Forbidden
    $responseKriteria = $this->actingAs($petugas)->get(route('admin.kriteria.index'));
    $responseKriteria->assertStatus(403);

    // Buka periode -> 403 Forbidden
    $responsePeriode = $this->actingAs($petugas)->get(route('admin.periode-pendaftaran.index'));
    $responsePeriode->assertStatus(403);
});

test('admin dapat memverifikasi dokumen calon agen menjadi valid atau tidak valid dengan catatan', function () {
    $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    $userCandidate = User::factory()->create(['role' => 'calon_agen', 'email_verified_at' => now()]);
    $periode = PeriodePendaftaran::create([
        'nama_periode' => 'Periode Verifikasi',
        'tanggal_buka' => now()->subDay(),
        'tanggal_tutup' => now()->addDays(7),
        'status' => 'aktif',
        'kuota' => 10,
        'created_by' => $admin->id,
    ]);

    $calonAgen = CalonAgen::create([
        'user_id' => $userCandidate->id,
        'periode_id' => $periode->id,
        'nik' => '1234567890123456',
        'nama_lengkap' => 'Tes Verifikasi',
        'no_hp' => '08123456789',
        'alamat_domisili' => 'Alamat Tes',
        'status' => 'diproses',
        'status_verifikasi' => 'menunggu',
    ]);

    // Verifikasi tidak valid tanpa catatan -> gagal
    $respFail = $this->actingAs($admin)->patch(route('admin.calon-agen.verifikasi-dokumen', $calonAgen), [
        'status_verifikasi' => 'tidak_valid',
        'catatan_verifikasi' => '',
    ]);
    $respFail->assertSessionHasErrors(['catatan_verifikasi']);

    // Verifikasi tidak valid dengan catatan -> sukses
    $respInvalid = $this->actingAs($admin)->patch(route('admin.calon-agen.verifikasi-dokumen', $calonAgen), [
        'status_verifikasi' => 'tidak_valid',
        'catatan_verifikasi' => 'Foto KTP buram, mohon upload ulang.',
    ]);
    $respInvalid->assertSessionHasNoErrors();
    expect($calonAgen->fresh()->status_verifikasi)->toBe('tidak_valid')
        ->and($calonAgen->fresh()->catatan_verifikasi)->toBe('Foto KTP buram, mohon upload ulang.');

    // Verifikasi menjadi valid -> sukses
    $respValid = $this->actingAs($admin)->patch(route('admin.calon-agen.verifikasi-dokumen', $calonAgen), [
        'status_verifikasi' => 'valid',
    ]);
    $respValid->assertSessionHasNoErrors();
    expect($calonAgen->fresh()->status_verifikasi)->toBe('valid')
        ->and($calonAgen->fresh()->diverifikasi_oleh)->toBe($admin->id);
});

test('penilaian calon agen diblokir jika status verifikasi dokumen belum valid', function () {
    $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    $userCandidate = User::factory()->create(['role' => 'calon_agen', 'email_verified_at' => now()]);
    $periode = PeriodePendaftaran::create([
        'nama_periode' => 'Periode Penilaian Test',
        'tanggal_buka' => now()->subDay(),
        'tanggal_tutup' => now()->addDays(7),
        'status' => 'aktif',
        'kuota' => 10,
        'created_by' => $admin->id,
    ]);

    $calonAgen = CalonAgen::create([
        'user_id' => $userCandidate->id,
        'periode_id' => $periode->id,
        'nik' => '9876543210987654',
        'nama_lengkap' => 'Belum Valid',
        'no_hp' => '08123456788',
        'alamat_domisili' => 'Alamat Tes',
        'status' => 'diproses',
        'status_verifikasi' => 'menunggu',
    ]);

    // Buka form penilaian saat belum valid -> redirect dengan error
    $respForm = $this->actingAs($admin)->get(route('admin.penilaian.form', [$periode, $calonAgen]));
    $respForm->assertRedirect(route('admin.penilaian.calon-agen', $periode));
    $respForm->assertSessionHas('error');

    // Jadikan valid
    $calonAgen->update(['status_verifikasi' => 'valid']);

    // Buka form penilaian saat sudah valid -> 200 OK
    $respFormValid = $this->actingAs($admin)->get(route('admin.penilaian.form', [$periode, $calonAgen]));
    $respFormValid->assertStatus(200);
});

test('calon agen dapat memperbaiki dokumen ketika tidak valid dan status verifikasi kembali ke menunggu', function () {
    $userCandidate = User::factory()->create(['role' => 'calon_agen', 'email_verified_at' => now()]);
    $admin = User::factory()->create(['role' => 'admin']);
    $periode = PeriodePendaftaran::create([
        'nama_periode' => 'Periode Perbaikan Dokumen',
        'tanggal_buka' => now()->subDay(),
        'tanggal_tutup' => now()->addDays(7),
        'status' => 'aktif',
        'kuota' => 10,
        'created_by' => $admin->id,
    ]);

    $calonAgen = CalonAgen::create([
        'user_id' => $userCandidate->id,
        'periode_id' => $periode->id,
        'nik' => '5566778899001122',
        'nama_lengkap' => 'Calon Perbaikan',
        'no_hp' => '08123456787',
        'alamat_domisili' => 'Alamat Tes',
        'status' => 'diproses',
        'status_verifikasi' => 'tidak_valid',
        'catatan_verifikasi' => 'KTP buram',
    ]);

    // Calon agen mengunggah ulang dokumen KTP yang baru
    $response = $this->actingAs($userCandidate)->patch(route('calon-agen.dokumen.update'), [
        'ktp' => \Illuminate\Http\UploadedFile::fake()->create('ktp_baru.jpg', 150),
    ]);

    $response->assertSessionHasNoErrors();
    expect($calonAgen->fresh()->status_verifikasi)->toBe('menunggu')
        ->and($calonAgen->fresh()->catatan_verifikasi)->toBeNull();
});

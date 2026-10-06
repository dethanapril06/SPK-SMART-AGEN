@extends('layouts.calon-agen')

@section('title', 'Dashboard')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Dashboard</h3>
                    <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">

            @if (!$calonAgen)
                {{-- Belum ada data calon agen (kemungkinan bug data) --}}
                <div class="alert alert-light-warning color-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Data pendaftaran Anda tidak ditemukan. Silakan hubungi admin.
                </div>
            @else

                {{-- Alert Status SPK --}}
                @php
                    $alertClass = match($calonAgen->status) {
                        'diproses' => 'warning',
                        'disurvey' => 'info',
                        'direkomendasi' => 'success',
                        'belumdirekomendasi'  => 'danger',
                    };
                    $alertIcon = match($calonAgen->status) {
                        'diproses' => 'hourglass-split',
                        'disurvey' => 'clipboard2-pulse',
                        'direkomendasi' => 'patch-check-fill',
                        'belumdirekomendasi'  => 'x-circle-fill',
                    };
                    $alertPesan = match($calonAgen->status) {
                        'diproses' => 'Pendaftaran Anda sedang dalam proses verifikasi dan seleksi oleh tim.',
                        'disurvey' => 'Pendaftaran Anda sedang dalam tahap survey lapangan oleh petugas.',
                        'direkomendasi' => 'Selamat! Anda dinyatakan direkomendasi menjadi Agen.',
                        'belumdirekomendasi'  => 'Mohon maaf, pendaftaran Anda belum direkomendasi pada periode ini.',
                    };
                @endphp
                <div class="alert alert-light-{{ $alertClass }} color-{{ $alertClass }} mb-4">
                    <i class="bi bi-{{ $alertIcon }} me-2"></i>
                    {{ $alertPesan }}
                </div>

                @if (session('success'))
                    <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @error('dokumen')
                    <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @enderror

                {{-- Kartu Statistik --}}
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon blue mb-2">
                                            <i class="bi bi-person-badge-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Status Pendaftaran</h6>
                                        <h6 class="font-extrabold mb-0">{{ ucfirst($calonAgen->status) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon purple mb-2">
                                            <i class="bi bi-calendar2-range-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Periode &amp; Kuota</h6>
                                        <h6 class="font-extrabold mb-0" style="font-size: 0.9rem">
                                            {{ $calonAgen->periode->nama_periode ?? '-' }}
                                            @if ($calonAgen->periode && $calonAgen->periode->kuota)
                                                ({{ $calonAgen->periode->kuota }} Kuota)
                                            @endif
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon green mb-2">
                                            <i class="bi bi-clipboard2-check-fill"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Kriteria Dinilai</h6>
                                        <h6 class="font-extrabold mb-0">{{ $penilaian->count() }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon {{ $hasilSmart ? 'green' : 'red' }} mb-2">
                                            <i class="bi bi-graph-up-arrow"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Hasil SMART</h6>
                                        <h6 class="font-extrabold mb-0">
                                            {{ $hasilSmart ? number_format($hasilSmart->skor_akhir, 4) : 'Belum ada' }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">

                    {{-- Detail Pendaftaran --}}
                    <div class="col-12 col-xl-5">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title mb-0">Data Pendaftaran</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <th width="42%">NIK</th>
                                        <td>: {{ $calonAgen->nik }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Pemilik</th>
                                        <td>: {{ $calonAgen->nama_lengkap }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Usaha</th>
                                        <td>: {{ $calonAgen->nama_usaha ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>No HP</th>
                                        <td>: {{ $calonAgen->no_hp }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat Domisili</th>
                                        <td>: {{ $calonAgen->alamat_domisili }}</td>
                                    </tr>
                                    <tr>
                                        <th>Alamat Usaha</th>
                                        <td>: {{ $calonAgen->alamat_usaha ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Periode</th>
                                        <td>: {{ $calonAgen->periode->nama_periode ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Sumber Pendaftaran</th>
                                        <td>: 
                                            @if ($calonAgen->sumber_pendaftaran === 'admin')
                                                <span class="badge bg-light-info text-info border">
                                                    <i class="bi bi-person-gear me-1"></i> Didaftarkan Admin
                                                </span>
                                            @else
                                                <span class="badge bg-light-primary text-primary border">
                                                    <i class="bi bi-person me-1"></i> Mendaftar Mandiri
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tanggal Daftar</th>
                                        <td>: {{ $calonAgen->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status SPK</th>
                                        <td>:
                                            @php
                                                $badge = match($calonAgen->status) {
                                                    'direkomendasi' => 'success',
                                                    'disurvey' => 'info',
                                                    'diproses' => 'warning',
                                                    'belumdirekomendasi'  => 'danger',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badge }}">
                                                {{ ucfirst($calonAgen->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Dokumen Pendaftaran</h4>
                                @if ($calonAgen->status_verifikasi === 'valid')
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Valid</span>
                                @elseif ($calonAgen->status_verifikasi === 'tidak_valid')
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Perlu Perbaikan</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Menunggu Verifikasi</span>
                                @endif
                            </div>
                            <div class="card-body">
                                {{-- Status Verifikasi Alert --}}
                                @if ($calonAgen->status_verifikasi === 'valid')
                                    <div class="alert alert-light-success border border-success color-success py-2 px-3 mb-3 small">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        <strong>Dokumen Administrasi Valid:</strong> Semua berkas Anda telah diverifikasi valid oleh admin.
                                    </div>
                                @elseif ($calonAgen->status_verifikasi === 'tidak_valid')
                                    <div class="alert alert-light-danger border border-danger color-danger py-2 px-3 mb-3 small">
                                        <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                        <strong>Perlu Perbaikan Dokumen:</strong>
                                        <div class="mt-1">{{ $calonAgen->catatan_verifikasi ?? 'Silakan upload ulang berkas dokumen yang belum sesuai.' }}</div>
                                    </div>
                                @else
                                    <div class="alert alert-light-warning border border-warning color-warning py-2 px-3 mb-3 small">
                                        <i class="bi bi-hourglass-split me-1"></i>
                                        <strong>Menunggu Verifikasi:</strong> Berkas dokumen sedang dalam antrean verifikasi admin. Anda masih dapat memperbaiki dokumen sebelum disetujui.
                                    </div>
                                @endif

                                @php
                                    $dokumenList = [
                                        'KTP (Wajib)'                  => $calonAgen->ktp_path,
                                        'Formulir Pendaftaran (Wajib)' => $calonAgen->formulir_pendaftaran_path,
                                        'NIB (Opsional)'               => $calonAgen->nib_path,
                                        'NPWP (Opsional)'              => $calonAgen->npwp_path,
                                    ];
                                @endphp
                                <div class="list-group mb-4">
                                    @foreach ($dokumenList as $label => $path)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{ $label }}</span>
                                            @if ($path)
                                                <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-file-earmark-text"></i> Lihat Berkas
                                                </a>
                                            @else
                                                <span class="badge bg-light-secondary">Belum ada</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                @if ($calonAgen->status_verifikasi !== 'valid')
                                    <div class="card bg-light border shadow-none">
                                        <div class="card-body p-3">
                                            <h6 class="fw-semibold mb-2"><i class="bi bi-pencil-square me-1"></i> Perbarui / Unggah Berkas Dokumen</h6>
                                            <small class="text-muted d-block mb-3">Pilih berkas baru yang ingin Anda perbaiki. Berkas lama tetap tersimpan jika tidak diubah.</small>

                                            <form method="POST" action="{{ route('calon-agen.dokumen.update') }}" enctype="multipart/form-data">
                                                @csrf
                                                @method('PATCH')

                                                <p class="text-primary fw-semibold small mb-2"><i class="bi bi-check2-circle me-1"></i> Dokumen Wajib</p>
                                                <div class="mb-3">
                                                    <label for="ktp" class="form-label small fw-semibold">Unggah Ulang KTP</label>
                                                    <input type="file" name="ktp" id="ktp"
                                                        class="form-control form-control-sm @error('ktp') is-invalid @enderror"
                                                        accept=".pdf,.jpg,.jpeg,.png">
                                                    @error('ktp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="mb-3">
                                                    <label for="formulir_pendaftaran" class="form-label small fw-semibold">Unggah Ulang Formulir Pendaftaran</label>
                                                    <input type="file" name="formulir_pendaftaran" id="formulir_pendaftaran"
                                                        class="form-control form-control-sm @error('formulir_pendaftaran') is-invalid @enderror"
                                                        accept=".pdf,.jpg,.jpeg,.png">
                                                    @error('formulir_pendaftaran')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <hr class="my-3">

                                                <p class="text-secondary fw-semibold small mb-2"><i class="bi bi-files me-1"></i> Dokumen Opsional</p>
                                                <div class="mb-3">
                                                    <label for="nib" class="form-label small fw-semibold">Unggah / Perbarui NIB</label>
                                                    <input type="file" name="nib" id="nib"
                                                        class="form-control form-control-sm @error('nib') is-invalid @enderror"
                                                        accept=".pdf,.jpg,.jpeg,.png">
                                                    @error('nib')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="npwp" class="form-label small fw-semibold">Unggah / Perbarui NPWP</label>
                                                    <input type="file" name="npwp" id="npwp"
                                                        class="form-control form-control-sm @error('npwp') is-invalid @enderror"
                                                        accept=".pdf,.jpg,.jpeg,.png">
                                                    @error('npwp')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                                    <i class="bi bi-cloud-arrow-up me-1"></i> Simpan Perubahan Dokumen
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-2 text-success small">
                                        <i class="bi bi-lock-fill me-1"></i> Dokumen sudah terverifikasi valid dan terkunci.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Hasil SMART & Penilaian --}}
                    <div class="col-12 col-xl-7">
                        <div class="row g-3 h-100">

                            {{-- Hasil SMART --}}
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Hasil Seleksi SMART</h4>
                                    </div>
                                    <div class="card-body">
                                        @if ($hasilSmart)
                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <h3 class="fw-bold text-primary mb-0">
                                                        {{ number_format($hasilSmart->skor_akhir, 4) }}
                                                    </h3>
                                                    <small class="text-muted">Skor Akhir</small>
                                                </div>
                                                <div class="col-4">
                                                    <h3 class="fw-bold text-info mb-0">
                                                        #{{ $hasilSmart->peringkat }}
                                                    </h3>
                                                    <small class="text-muted">Peringkat</small>
                                                </div>
                                                <div class="col-4">
                                                    <span class="badge bg-{{ $hasilSmart->keputusan === 'direkomendasi' ? 'success' : 'danger' }} fs-6">
                                                        {{ ucfirst($hasilSmart->keputusan) }}
                                                    </span>
                                                    <div><small class="text-muted">Keputusan</small></div>
                                                </div>
                                            </div>
                                            <div class="text-center mt-3">
                                                <small class="text-muted">
                                                    Dihitung pada:
                                                    {{ \Carbon\Carbon::parse($hasilSmart->dihitung_at)->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        @else
                                            <div class="text-center py-3 text-muted">
                                                <i class="bi bi-hourglass fs-1 d-block mb-2"></i>
                                                Hasil seleksi SMART belum tersedia.
                                                <br>Tunggu hingga proses penilaian selesai.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Penilaian per Kriteria --}}
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Penilaian Per Kriteria</h4>
                                    </div>
                                    <div class="card-body">
                                        @if ($penilaian->isNotEmpty())
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover mb-0 align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Kriteria</th>
                                                            <th class="text-center">Nilai</th>
                                                            <th>Catatan Surveyor</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($penilaian as $item)
                                                            <tr>
                                                                <td>
                                                                    <span class="badge bg-secondary me-1">
                                                                        {{ $item->kriteria->kode_kriteria ?? '-' }}
                                                                    </span>
                                                                    <span class="fw-medium">{{ $item->kriteria->nama_kriteria ?? '-' }}</span>
                                                                    @if ($item->subKriteria)
                                                                        <br><small class="text-muted">{{ $item->subKriteria->nama_sub }}</small>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-primary-light text-primary border fw-bold">
                                                                        {{ $item->nilai_input }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    @if ($item->catatan)
                                                                        <div class="d-flex align-items-start gap-1">
                                                                            <i class="bi bi-chat-left-quote text-primary mt-1 flex-shrink-0" style="font-size:0.75rem;"></i>
                                                                            <small class="fst-italic">{{ $item->catatan }}</small>
                                                                        </div>
                                                                    @else
                                                                        <small class="text-muted">—</small>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-3 text-muted">
                                                <i class="bi bi-clipboard2 fs-1 d-block mb-2"></i>
                                                Belum ada penilaian dari surveyor.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Notifikasi Terbaru --}}
                @if ($notifikasiTerbaru && $notifikasiTerbaru->isNotEmpty())
                    <div class="row g-3 mt-1">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0">Notifikasi Terbaru</h4>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($notifikasiTerbaru as $notif)
                                            <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-4 {{ !$notif->is_read ? 'bg-light' : '' }}">
                                                <div class="me-3">
                                                    @php
                                                        $notifBadge = match($notif->tipe) {
                                                            'direkomendasi' => 'success',
                                                            'belumdirekomendasi'  => 'danger',
                                                            default    => 'info',
                                                        };
                                                        $notifIcon = match($notif->tipe) {
                                                            'direkomendasi' => 'patch-check-fill',
                                                            'belumdirekomendasi'  => 'x-circle-fill',
                                                            default    => 'info-circle-fill',
                                                        };
                                                    @endphp
                                                    <i class="bi bi-{{ $notifIcon }} text-{{ $notifBadge }} me-2"></i>
                                                    <span class="fw-semibold">{{ $notif->judul }}</span>
                                                    <div class="text-muted small mt-1">{{ $notif->pesan }}</div>
                                                </div>
                                                <div class="text-end text-nowrap">
                                                    <small class="text-muted">
                                                        {{ $notif->created_at->diffForHumans() }}
                                                    </small>
                                                    @if (!$notif->is_read)
                                                        <div><span class="badge bg-primary">Baru</span></div>
                                                    @endif
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            @endif

        </section>
    </div>
@endsection

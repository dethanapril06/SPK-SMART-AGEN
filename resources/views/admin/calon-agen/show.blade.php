@extends('layouts.admin')

@section('title', 'Detail Calon Agen')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .detail-map {
            height: 200px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #dce7f1;
            z-index: 0;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Calon Agen</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.calon-agen.index') }}">Calon Agen</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">

                {{-- Info Utama --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Informasi Calon Agen</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.calon-agen.edit', $calonAgen) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="{{ route('admin.calon-agen.index') }}" class="btn btn-sm btn-light-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Nama Pemilik</th>
                                            <td>: {{ $calonAgen->nama_lengkap }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Usaha</th>
                                            <td>: {{ $calonAgen->nama_usaha ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>NIK</th>
                                            <td>: {{ $calonAgen->nik }}</td>
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
                                                        <i class="bi bi-person-gear me-1"></i> Didaftarkan Admin ({{ $calonAgen->didaftarkanOleh->name ?? 'Admin' }})
                                                    </span>
                                                @else
                                                    <span class="badge bg-light-primary text-primary border">
                                                        <i class="bi bi-person me-1"></i> Mendaftar Mandiri
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Akun Email</th>
                                            <td>: {{ $calonAgen->user->email ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Verifikasi Email</th>
                                            <td>: 
                                                @if ($calonAgen->user && $calonAgen->user->hasVerifiedEmail())
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-patch-check me-1"></i> Terverifikasi ({{ $calonAgen->user->email_verified_at->format('d/m/Y H:i') }})
                                                    </span>
                                                @elseif ($calonAgen->user)
                                                    <span class="badge bg-warning text-dark me-2">
                                                        <i class="bi bi-clock-history me-1"></i> Belum Verifikasi
                                                    </span>
                                                    <form action="{{ route('admin.users.resend-verification', $calonAgen->user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;">
                                                            <i class="bi bi-envelope me-1"></i> Kirim Ulang Link
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">-</span>
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
                                                    $badge = match ($calonAgen->status) {
                                                        'direkomendasi' => 'success',
                                                        'disurvey' => 'info',
                                                        'diproses' => 'warning',
                                                        'belumdirekomendasi' => 'danger',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badge }}">
                                                    {{ ucfirst($calonAgen->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6 col-12">
                                    {{-- Verifikasi Dokumen Administrasi --}}
                                    <div class="card bg-light shadow-none mb-3 border">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="fw-semibold mb-0"><i class="bi bi-clipboard-check me-1"></i> Verifikasi Dokumen</h6>
                                                @if ($calonAgen->status_verifikasi === 'valid')
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Valid</span>
                                                @elseif ($calonAgen->status_verifikasi === 'tidak_valid')
                                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Tidak Valid</span>
                                                @else
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi</span>
                                                @endif
                                            </div>

                                            @if ($calonAgen->diverifikasi_at)
                                                <div class="text-muted small mb-2">
                                                    Diverifikasi oleh: <strong>{{ $calonAgen->diverifikasiOleh->name ?? 'Admin' }}</strong>
                                                    pada {{ $calonAgen->diverifikasi_at->format('d/m/Y H:i') }}
                                                </div>
                                            @endif

                                            @if ($calonAgen->catatan_verifikasi)
                                                <div class="alert alert-light-danger small mb-3">
                                                    <strong>Catatan:</strong> {{ $calonAgen->catatan_verifikasi }}
                                                </div>
                                            @endif

                                            <form action="{{ route('admin.calon-agen.verifikasi-dokumen', $calonAgen) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="form-group mb-2">
                                                    <label class="form-label small fw-semibold">Ubah Status Verifikasi:</label>
                                                    <select name="status_verifikasi" class="form-select form-select-sm" id="select-verifikasi">
                                                        <option value="valid" {{ $calonAgen->status_verifikasi === 'valid' ? 'selected' : '' }}>
                                                            Valid (Memenuhi Syarat Administrasi)
                                                        </option>
                                                        <option value="tidak_valid" {{ $calonAgen->status_verifikasi === 'tidak_valid' ? 'selected' : '' }}>
                                                            Tidak Valid (Perlu Perbaikan Dokumen)
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="form-group mb-2" id="group-catatan-verifikasi" style="{{ $calonAgen->status_verifikasi === 'tidak_valid' ? '' : 'display: none;' }}">
                                                    <label class="form-label small fw-semibold">Catatan Perbaikan Dokumen:</label>
                                                    <textarea name="catatan_verifikasi" class="form-control form-control-sm" rows="2" placeholder="Tuliskan dokumen apa yang salah/kurang jelas...">{{ old('catatan_verifikasi', $calonAgen->catatan_verifikasi) }}</textarea>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-primary mt-1">
                                                    <i class="bi bi-save me-1"></i> Simpan Hasil Verifikasi
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="card bg-light shadow-none mb-3">
                                        <div class="card-body">
                                            <h6 class="fw-semibold mb-3">Berkas Dokumen</h6>
                                            @php
                                                $dokumenList = [
                                                    'KTP (Wajib)'                  => $calonAgen->ktp_path,
                                                    'Formulir Pendaftaran (Wajib)' => $calonAgen->formulir_pendaftaran_path,
                                                    'NIB (Opsional)'               => $calonAgen->nib_path,
                                                    'NPWP (Opsional)'              => $calonAgen->npwp_path,
                                                    'Form Screening Surveyor'     => $calonAgen->form_screening_path,
                                                ];
                                            @endphp
                                            <div class="list-group">
                                                @foreach ($dokumenList as $label => $path)
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ $label }}</span>
                                                        @if ($path)
                                                            <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-file-earmark-text"></i> Lihat
                                                            </a>
                                                        @else
                                                            <span class="badge bg-light-secondary">Belum ada</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Ubah Status --}}
                                    <div class="card bg-light shadow-none">
                                        <div class="card-body">
                                            <h6 class="fw-semibold mb-3">Ubah Status SPK</h6>
                                            <form action="{{ route('admin.calon-agen.ubah-status', $calonAgen) }}"
                                                method="POST" id="form-ubah-status">
                                                @csrf
                                                @method('PATCH')
                                                <div class="form-group mb-3">
                                                    <select name="status" class="form-select" id="select-status">
                                                        <option value="diproses"
                                                            {{ $calonAgen->status === 'diproses' ? 'selected' : '' }}>
                                                            Diproses</option>
                                                        <option value="disurvey"
                                                            {{ $calonAgen->status === 'disurvey' ? 'selected' : '' }}>
                                                            Disurvey</option>
                                                        <option value="direkomendasi"
                                                            {{ $calonAgen->status === 'direkomendasi' ? 'selected' : '' }}>
                                                            Direkomendasi</option>
                                                        <option value="belumdirekomendasi"
                                                            {{ $calonAgen->status === 'belumdirekomendasi' ? 'selected' : '' }}>
                                                            Belum Direkomendasi</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-check-circle"></i> Simpan Status
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Peta Lokasi --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><i class="bi bi-map me-2"></i>Peta Lokasi</h4>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <p class="fw-semibold mb-2"><i class="bi bi-house-door me-1"></i> Alamat Domisili Pemilik</p>
                                    <div id="map-domisili" class="detail-map"></div>
                                    <small class="text-muted d-block mt-1">{{ $calonAgen->alamat_domisili }}</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <p class="fw-semibold mb-2"><i class="bi bi-building me-1"></i> Alamat Usaha</p>
                                    @if ($calonAgen->alamat_usaha)
                                        <div id="map-usaha" class="detail-map"></div>
                                        <small class="text-muted d-block mt-1">{{ $calonAgen->alamat_usaha }}</small>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height:200px;">
                                            <span class="text-muted"><i class="bi bi-building me-1"></i>Alamat usaha tidak diisi</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectVerifikasi = document.getElementById('select-verifikasi');
                const groupCatatan = document.getElementById('group-catatan-verifikasi');
                if (selectVerifikasi && groupCatatan) {
                    selectVerifikasi.addEventListener('change', function() {
                        groupCatatan.style.display = this.value === 'tidak_valid' ? 'block' : 'none';
                    });
                }

                document.getElementById('form-ubah-status').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const status = document.getElementById('select-status').value;

                    Swal.fire({
                        title: 'Ubah status calon agen?',
                        text: `Status akan diubah menjadi "${status}".`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#435ebe',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, ubah',
                        cancelButtonText: 'Batal'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            document.getElementById('form-ubah-status').submit();
                        }
                    });
                });
            });
        </script>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ESRI   = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
        const ESRI_A = '&copy; <a href="https://www.esri.com/">Esri</a>, Earthstar Geographics';

        function initDetailMap(mapId, lat, lng, label) {
            const hasCoords = lat !== null && lng !== null;
            const center    = hasCoords ? [lat, lng] : [-2.5, 118.0];
            const zoom      = hasCoords ? 15 : 5;

            const map = L.map(mapId).setView(center, zoom);
            L.tileLayer(ESRI, { attribution: ESRI_A, maxZoom: 19 }).addTo(map);

            if (hasCoords) {
                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup(label || 'Lokasi')
                    .openPopup();
            }
        }

        // Map Domisili — gunakan koordinat tersimpan
        initDetailMap(
            'map-domisili',
            @json($calonAgen->lat_domisili),
            @json($calonAgen->lng_domisili),
            @json($calonAgen->alamat_domisili)
        );

        // Map Usaha — hanya jika alamat usaha ada
        @if ($calonAgen->alamat_usaha)
            initDetailMap(
                'map-usaha',
                @json($calonAgen->lat_usaha),
                @json($calonAgen->lng_usaha),
                @json($calonAgen->alamat_usaha)
            );
        @endif
    });
    </script>
    @endpush
@endsection

@extends('layouts.admin')

@section('title', 'Tambah Calon Agen')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .map-container {
            height: 220px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #dce7f1;
            margin-bottom: 0.5rem;
            z-index: 0;
        }
        .map-search-wrap { position: relative; margin-bottom: 0.5rem; }
        .map-search-wrap input { padding-right: 40px; }
        .map-search-icon {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%); color: #6c757d; pointer-events: none;
        }
    </style>
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Calon Agen</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.calon-agen.index') }}">Calon Agen</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Form Calon Agen</h4>
                    <a href="{{ route('admin.calon-agen.index') }}" class="btn btn-sm btn-light-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.calon-agen.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="alert alert-light-info color-info">
                            Akun calon agen akan dibuat secara otomatis. Password login default: <strong>password</strong>. Tautan verifikasi email akan otomatis dikirimkan ke alamat email calon agen.
                        </div>

                        <h6 class="text-muted mb-3 mt-2">Data Calon Agen</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="periode_id" class="form-label">Periode Pendaftaran <span class="text-danger">*</span></label>
                                <select name="periode_id" id="periode_id"
                                    class="form-select @error('periode_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Periode --</option>
                                    @foreach ($periodes as $periode)
                                        <option value="{{ $periode->id }}"
                                            {{ old('periode_id') == $periode->id ? 'selected' : '' }}
                                            {{ $periode->isDitutup() ? 'disabled' : '' }}>
                                            {{ $periode->nama_periode }}
                                            @if ($periode->isDitutup())
                                                (Ditutup - Tidak dapat dipilih)
                                            @elseif ($periode->isAktif())
                                                (Aktif - Kuota: {{ $periode->kuota ?? 0 }})
                                            @else
                                                (Draft)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('periode_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Calon Agen <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="Contoh: agen@gmail.com"
                                    value="{{ old('email') }}" required>
                                <small class="text-muted">Digunakan untuk login dan menerima link verifikasi email.</small>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" name="nik" id="nik"
                                    class="form-control @error('nik') is-invalid @enderror"
                                    value="{{ old('nik') }}" maxlength="16" placeholder="16 digit NIK" required>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Pemilik <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap"
                                    class="form-control @error('nama_lengkap') is-invalid @enderror"
                                    placeholder="Nama lengkap sesuai KTP"
                                    value="{{ old('nama_lengkap') }}" required>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nama_usaha" class="form-label">Nama Usaha</label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold text-primary bg-light">BeJuBis@</span>
                                    <input type="text" name="nama_usaha" id="nama_usaha"
                                        class="form-control @error('nama_usaha') is-invalid @enderror"
                                        placeholder="Tuliskan nama usaha..."
                                        value="{{ old('nama_usaha') }}">
                                </div>
                                <small class="text-muted">Awalan <strong>BeJuBis@</strong> otomatis ditambahkan sistem.</small>
                                @error('nama_usaha')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="no_hp" class="form-label">No HP / WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" name="no_hp" id="no_hp"
                                    class="form-control @error('no_hp') is-invalid @enderror"
                                    value="{{ old('no_hp') }}" maxlength="20" placeholder="08xxxxxxxxxx" required>
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-muted mb-3">Alamat Domisili & Lokasi Usaha</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="alamat_domisili" class="form-label">Alamat Domisili <span class="text-danger">*</span></label>
                                <div class="map-search-wrap">
                                    <input type="text" class="form-control form-control-sm" id="search-domisili"
                                        placeholder="Cari lokasi domisili di peta..." autocomplete="off">
                                    <i class="bi bi-search map-search-icon"></i>
                                </div>
                                <div id="map-domisili" class="map-container"></div>
                                <small class="text-muted d-block mb-1"><i class="bi bi-info-circle me-1"></i>Geser marker merah untuk menentukan titik koordinat domisili.</small>
                                <textarea name="alamat_domisili" id="alamat_domisili" rows="3"
                                    class="form-control @error('alamat_domisili') is-invalid @enderror" required>{{ old('alamat_domisili') }}</textarea>
                                @error('alamat_domisili')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <input type="hidden" name="lat_domisili" id="lat_domisili" value="{{ old('lat_domisili') }}">
                                <input type="hidden" name="lng_domisili" id="lng_domisili" value="{{ old('lng_domisili') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="alamat_usaha" class="form-label">Alamat Usaha</label>
                                <div class="map-search-wrap">
                                    <input type="text" class="form-control form-control-sm" id="search-usaha"
                                        placeholder="Cari lokasi usaha di peta..." autocomplete="off">
                                    <i class="bi bi-search map-search-icon"></i>
                                </div>
                                <div id="map-usaha" class="map-container"></div>
                                <small class="text-muted d-block mb-1"><i class="bi bi-info-circle me-1"></i>Geser marker biru untuk menentukan titik koordinat usaha.</small>
                                <textarea name="alamat_usaha" id="alamat_usaha" rows="3"
                                    class="form-control @error('alamat_usaha') is-invalid @enderror">{{ old('alamat_usaha') }}</textarea>
                                @error('alamat_usaha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <input type="hidden" name="lat_usaha" id="lat_usaha" value="{{ old('lat_usaha') }}">
                                <input type="hidden" name="lng_usaha" id="lng_usaha" value="{{ old('lng_usaha') }}">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-primary mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Dokumen Wajib (Harus Diunggah)</h6>
                        <p class="text-muted small mb-3">Format file yang diterima: <strong>PDF, JPG, JPEG, PNG</strong> (Maksimal 2MB per file).</p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ktp" class="form-label">KTP <span class="text-danger">*</span></label>
                                <input type="file" name="ktp" id="ktp"
                                    class="form-control @error('ktp') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png" {{ session('admin_calon_agen_drafts.ktp') ? '' : 'required' }}>
                                @if (session('admin_calon_agen_drafts.ktp'))
                                    <div class="alert alert-light-success py-1 px-2 mt-1 small">
                                        <i class="bi bi-check-circle me-1"></i> File tersimpan: <strong>{{ session('admin_calon_agen_drafts.ktp.name') }}</strong> (Pilih file baru jika ingin mengganti)
                                    </div>
                                @endif
                                <small class="text-muted mt-1 d-block">Format: PDF, JPG, JPEG, PNG. Maks. 2MB</small>
                                @error('ktp')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="formulir_pendaftaran" class="form-label">Formulir Pendaftaran <span class="text-danger">*</span></label>
                                <input type="file" name="formulir_pendaftaran" id="formulir_pendaftaran"
                                    class="form-control @error('formulir_pendaftaran') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png" {{ session('admin_calon_agen_drafts.formulir_pendaftaran') ? '' : 'required' }}>
                                @if (session('admin_calon_agen_drafts.formulir_pendaftaran'))
                                    <div class="alert alert-light-success py-1 px-2 mt-1 small">
                                        <i class="bi bi-check-circle me-1"></i> File tersimpan: <strong>{{ session('admin_calon_agen_drafts.formulir_pendaftaran.name') }}</strong> (Pilih file baru jika ingin mengganti)
                                    </div>
                                @endif
                                <small class="text-muted mt-1 d-block">Format: PDF, JPG, JPEG, PNG. Maks. 2MB</small>
                                @error('formulir_pendaftaran')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="text-secondary mt-3 mb-2"><i class="bi bi-file-earmark-check me-1"></i> Dokumen Opsional (Jika Ada)</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nib" class="form-label">NIB (Nomor Induk Berusaha)</label>
                                <input type="file" name="nib" id="nib"
                                    class="form-control @error('nib') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                @if (session('admin_calon_agen_drafts.nib'))
                                    <div class="alert alert-light-success py-1 px-2 mt-1 small">
                                        <i class="bi bi-check-circle me-1"></i> File tersimpan: <strong>{{ session('admin_calon_agen_drafts.nib.name') }}</strong> (Pilih file baru jika ingin mengganti)
                                    </div>
                                @endif
                                <small class="text-muted mt-1 d-block">Format: PDF, JPG, JPEG, PNG. Maks. 2MB</small>
                                @error('nib')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="npwp" class="form-label">NPWP</label>
                                <input type="file" name="npwp" id="npwp"
                                    class="form-control @error('npwp') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                @if (session('admin_calon_agen_drafts.npwp'))
                                    <div class="alert alert-light-success py-1 px-2 mt-1 small">
                                        <i class="bi bi-check-circle me-1"></i> File tersimpan: <strong>{{ session('admin_calon_agen_drafts.npwp.name') }}</strong> (Pilih file baru jika ingin mengganti)
                                    </div>
                                @endif
                                <small class="text-muted mt-1 d-block">Format: PDF, JPG, JPEG, PNG. Maks. 2MB</small>
                                @error('npwp')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Calon Agen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ESRI   = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
    const ESRI_A = '&copy; <a href="https://www.esri.com/">Esri</a>, Earthstar Geographics';
    const fallback = [-2.5, 118.0];

    function initMap(mapId, searchId, textareaId, latId, lngId) {
        const map  = L.map(mapId).setView(fallback, 5);
        const area = document.getElementById(textareaId);
        const src  = document.getElementById(searchId);
        let marker = null;

        L.tileLayer(ESRI, { attribution: ESRI_A, maxZoom: 19 }).addTo(map);

        map.on('click', function (e) {
            const lat = e.latlng.lat, lng = e.latlng.lng;
            if (marker) marker.setLatLng(e.latlng);
            else marker = addMarker(e.latlng, map, area, latId, lngId);
            setCoords(latId, lngId, lat, lng);
            geocodeReverse(lat, lng, area);
        });

        let t;
        src.addEventListener('input', function () {
            clearTimeout(t);
            const q = this.value.trim();
            if (q.length < 3) return;
            t = setTimeout(function () {
                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=1&countrycodes=id')
                    .then(r => r.json()).then(d => {
                        if (!d || !d.length) return;
                        const ll = L.latLng(+d[0].lat, +d[0].lon);
                        map.setView(ll, 17);
                        if (marker) marker.setLatLng(ll);
                        else marker = addMarker(ll, map, area, latId, lngId);
                        setCoords(latId, lngId, ll.lat, ll.lng);
                        area.value = d[0].display_name;
                    }).catch(() => {});
            }, 500);
        });

        src.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });

        return map;
    }

    function addMarker(latlng, map, area, latId, lngId) {
        const m = L.marker(latlng, { draggable: true }).addTo(map);
        m.on('dragend', ev => {
            const p = ev.target.getLatLng();
            setCoords(latId, lngId, p.lat, p.lng);
            geocodeReverse(p.lat, p.lng, area);
        });
        return m;
    }

    function setCoords(latId, lngId, lat, lng) {
        const latEl = document.getElementById(latId);
        const lngEl = document.getElementById(lngId);
        if (latEl) latEl.value = lat;
        if (lngEl) lngEl.value = lng;
    }

    function geocodeReverse(lat, lng, area) {
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18')
            .then(r => r.json()).then(d => { if (d && d.display_name) area.value = d.display_name; })
            .catch(() => {});
    }

    const mapD = initMap('map-domisili', 'search-domisili', 'alamat_domisili', 'lat_domisili', 'lng_domisili');
    const mapU = initMap('map-usaha',    'search-usaha',    'alamat_usaha',    'lat_usaha',    'lng_usaha');

    // Geolocation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            pos => {
                const ll = L.latLng(pos.coords.latitude, pos.coords.longitude);
                mapD.setView(ll, 13);
                mapU.setView(ll, 13);
            },
            () => {},
            { timeout: 8000, maximumAge: 60000 }
        );
    }
});
</script>
@endpush

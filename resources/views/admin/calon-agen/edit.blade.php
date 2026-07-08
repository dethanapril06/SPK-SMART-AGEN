@extends('layouts.admin')

@section('title', 'Edit Calon Agen')

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
                    <h3>Edit Calon Agen</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.calon-agen.index') }}">Calon Agen</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Form Edit Calon Agen</h4>
                    <a href="{{ route('admin.calon-agen.show', $calonAgen) }}" class="btn btn-sm btn-light-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.calon-agen.update', $calonAgen) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h6 class="text-muted mb-3 mt-2">Data Calon Agen</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="periode_id" class="form-label">Periode</label>
                                <select name="periode_id" id="periode_id"
                                    class="form-select @error('periode_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Periode --</option>
                                    @foreach ($periodes as $periode)
                                        <option value="{{ $periode->id }}"
                                            {{ old('periode_id', $calonAgen->periode_id) == $periode->id ? 'selected' : '' }}>
                                            {{ $periode->nama_periode }} ({{ ucfirst($periode->status) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('periode_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" name="nik" id="nik"
                                    class="form-control @error('nik') is-invalid @enderror"
                                    value="{{ old('nik', $calonAgen->nik) }}" maxlength="16" required>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap"
                                    class="form-control @error('nama_lengkap') is-invalid @enderror"
                                    value="{{ old('nama_lengkap', $calonAgen->nama_lengkap) }}" required>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_usaha" class="form-label">Nama Usaha</label>
                                <input type="text" name="nama_usaha" id="nama_usaha"
                                    class="form-control @error('nama_usaha') is-invalid @enderror"
                                    value="{{ old('nama_usaha', $calonAgen->nama_usaha) }}">
                                @error('nama_usaha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="text" name="no_hp" id="no_hp"
                                    class="form-control @error('no_hp') is-invalid @enderror"
                                    value="{{ old('no_hp', $calonAgen->no_hp) }}" maxlength="20" required>
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ── Alamat Domisili + Map ── --}}
                            <div class="col-12 mb-3">
                                <label for="alamat_domisili" class="form-label fw-semibold">
                                    <i class="bi bi-house-door me-1"></i> Alamat Domisili Pemilik <span class="text-danger">*</span>
                                </label>
                                <div class="map-search-wrap">
                                    <input type="text" id="search-domisili" class="form-control form-control-sm"
                                        placeholder="Cari lokasi domisili...">
                                    <i class="bi bi-search map-search-icon"></i>
                                </div>
                                <div id="map-domisili" class="map-container"></div>
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-info-circle me-1"></i>Klik peta atau cari lokasi untuk memperbarui pin
                                </small>
                                <textarea name="alamat_domisili" id="alamat_domisili" rows="2"
                                    class="form-control @error('alamat_domisili') is-invalid @enderror"
                                    placeholder="Alamat domisili" required>{{ old('alamat_domisili', $calonAgen->alamat_domisili) }}</textarea>
                                <input type="hidden" name="lat_domisili" id="lat_domisili"
                                    value="{{ old('lat_domisili', $calonAgen->lat_domisili) }}">
                                <input type="hidden" name="lng_domisili" id="lng_domisili"
                                    value="{{ old('lng_domisili', $calonAgen->lng_domisili) }}">
                                @error('alamat_domisili')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ── Alamat Usaha + Map ── --}}
                            <div class="col-12 mb-3">
                                <label for="alamat_usaha" class="form-label fw-semibold">
                                    <i class="bi bi-building me-1"></i> Alamat Usaha
                                </label>
                                <div class="map-search-wrap">
                                    <input type="text" id="search-usaha" class="form-control form-control-sm"
                                        placeholder="Cari lokasi usaha...">
                                    <i class="bi bi-search map-search-icon"></i>
                                </div>
                                <div id="map-usaha" class="map-container"></div>
                                <small class="text-muted d-block mb-1">
                                    <i class="bi bi-info-circle me-1"></i>Klik peta atau cari lokasi untuk memperbarui pin
                                </small>
                                <textarea name="alamat_usaha" id="alamat_usaha" rows="2"
                                    class="form-control @error('alamat_usaha') is-invalid @enderror"
                                    placeholder="Alamat usaha">{{ old('alamat_usaha', $calonAgen->alamat_usaha) }}</textarea>
                                <input type="hidden" name="lat_usaha" id="lat_usaha"
                                    value="{{ old('lat_usaha', $calonAgen->lat_usaha) }}">
                                <input type="hidden" name="lng_usaha" id="lng_usaha"
                                    value="{{ old('lng_usaha', $calonAgen->lng_usaha) }}">
                                @error('alamat_usaha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="text-muted mb-3 mt-2">Dokumen Administratif</h6>
                        <div class="alert alert-light-info color-info py-2 px-3 mb-3" style="font-size: 0.85rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Format file yang diterima: <strong>PDF, JPG, JPEG, PNG</strong>. Ukuran maksimal: <strong>2MB</strong> per file.
                        </div>
                        @php
                            $dokumenList = [
                                'KTP' => $calonAgen->ktp_path,
                                'NIB' => $calonAgen->nib_path,
                                'NPWP' => $calonAgen->npwp_path,
                                'Formulir Pendaftaran' => $calonAgen->formulir_pendaftaran_path,
                            ];
                        @endphp
                        <div class="list-group mb-3">
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

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ktp" class="form-label">
                                    Ganti KTP @if (!$calonAgen->ktp_path)<span class="text-danger">*</span>@endif
                                </label>
                                <input type="file" name="ktp" id="ktp"
                                    class="form-control @error('ktp') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png" {{ !$calonAgen->ktp_path ? 'required' : '' }}>
                                @error('ktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="formulir_pendaftaran" class="form-label">
                                    Ganti Formulir Pendaftaran @if (!$calonAgen->formulir_pendaftaran_path)<span class="text-danger">*</span>@endif
                                </label>
                                <input type="file" name="formulir_pendaftaran" id="formulir_pendaftaran"
                                    class="form-control @error('formulir_pendaftaran') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png" {{ !$calonAgen->formulir_pendaftaran_path ? 'required' : '' }}>
                                @error('formulir_pendaftaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nib" class="form-label">Ganti NIB</label>
                                <input type="file" name="nib" id="nib"
                                    class="form-control @error('nib') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                @error('nib')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="npwp" class="form-label">Ganti NPWP</label>
                                <input type="file" name="npwp" id="npwp"
                                    class="form-control @error('npwp') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                @error('npwp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
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

    // Koordinat existing dari database
    const existingDomisili = {
        lat: @json($calonAgen->lat_domisili),
        lng: @json($calonAgen->lng_domisili),
    };
    const existingUsaha = {
        lat: @json($calonAgen->lat_usaha),
        lng: @json($calonAgen->lng_usaha),
    };

    function setCoords(latId, lngId, lat, lng) {
        const latEl = document.getElementById(latId);
        const lngEl = document.getElementById(lngId);
        if (latEl) latEl.value = lat;
        if (lngEl) lngEl.value = lng;
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

    function initMap(mapId, searchId, textareaId, latId, lngId, existingLat, existingLng) {
        const hasExisting = existingLat !== null && existingLng !== null;
        const initCenter  = hasExisting ? [existingLat, existingLng] : fallback;
        const initZoom    = hasExisting ? 15 : 5;

        const map  = L.map(mapId).setView(initCenter, initZoom);
        const area = document.getElementById(textareaId);
        const src  = document.getElementById(searchId);
        let marker = null;

        L.tileLayer(ESRI, { attribution: ESRI_A, maxZoom: 19 }).addTo(map);

        // Tampilkan marker existing jika ada koordinat tersimpan
        if (hasExisting) {
            marker = addMarker(L.latLng(existingLat, existingLng), map, area, latId, lngId);
        }

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
                fetch('https://nominatim.openstreetmap.org/search?format=json&q='
                    + encodeURIComponent(q) + '&limit=1&countrycodes=id')
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

    function geocodeReverse(lat, lng, area) {
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18')
            .then(r => r.json()).then(d => { if (d && d.display_name) area.value = d.display_name; })
            .catch(() => {});
    }

    initMap('map-domisili', 'search-domisili', 'alamat_domisili', 'lat_domisili', 'lng_domisili',
        existingDomisili.lat, existingDomisili.lng);

    initMap('map-usaha', 'search-usaha', 'alamat_usaha', 'lat_usaha', 'lng_usaha',
        existingUsaha.lat, existingUsaha.lng);
});
</script>
@endpush

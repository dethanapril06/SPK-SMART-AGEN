@extends('layouts.auth')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .leaflet-map-container {
            height: 220px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #dce7f1;
            margin-bottom: 0.5rem;
            z-index: 0;
        }
        .map-search-wrapper {
            position: relative;
            margin-bottom: 0.5rem;
        }
        .map-search-wrapper input {
            padding-right: 40px;
        }
        .map-search-wrapper .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            pointer-events: none;
        }
        .map-helper-text {
            font-size: 0.78rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }
        .dokumen-info {
            font-size: 0.78rem;
            color: #6c757d;
        }
        .wajib-info {
            font-size: 0.78rem;
            color: #dc3545;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="row h-100">
        <div class="col-lg-6 col-12">
            <div id="auth-left">
                <div class="auth-logo">
                    <a href="{{ route('login') }}">
                        <span class="fw-bold fs-1 text-primary">SPK SMART Agen</span>
                    </a>
                </div>
                <h1 class="auth-title">Daftar Calon Agen</h1>
                <p class="auth-subtitle mb-4">Isi data diri Anda untuk mendaftar sebagai calon agen.</p>

                @if ($errors->has('periode'))
                    <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first('periode') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- ── STEP TABS ── --}}
                    <div class="d-flex gap-2 mb-4" id="register-tabs">
                        <button type="button" class="btn btn-primary flex-fill" id="register-data-tab">
                            <i class="bi bi-person-badge"></i> Calon Agen
                        </button>
                        <button type="button" class="btn btn-light-secondary flex-fill" id="register-alamat-tab">
                            <i class="bi bi-geo-alt"></i> Alamat
                        </button>
                        <button type="button" class="btn btn-light-secondary flex-fill" id="register-dokumen-tab">
                            <i class="bi bi-file-earmark-arrow-up"></i> Dokumen
                        </button>
                        <button type="button" class="btn btn-light-secondary flex-fill" id="register-akun-tab">
                            <i class="bi bi-shield-lock"></i> Akun
                        </button>
                    </div>

                    {{-- ── STEP 1: CALON AGEN ── --}}
                    <div id="register-data-step">
                        <p class="text-muted fw-semibold mb-3">Data Calon Agen</p>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="nik" id="nik"
                                class="form-control form-control-xl @error('nik') is-invalid @enderror"
                                placeholder="NIK (16 digit)" value="{{ old('nik') }}" maxlength="16" required>
                            <div class="form-control-icon"><i class="bi bi-card-text"></i></div>
                            @error('nik')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="nama_lengkap" id="nama_lengkap"
                                class="form-control form-control-xl @error('nama_lengkap') is-invalid @enderror"
                                placeholder="Nama Pemilik" value="{{ old('nama_lengkap') }}" required>
                            <div class="form-control-icon"><i class="bi bi-person-badge"></i></div>
                            @error('nama_lengkap')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="nama_usaha" id="nama_usaha"
                                class="form-control form-control-xl @error('nama_usaha') is-invalid @enderror"
                                placeholder="Nama Usaha" value="{{ old('nama_usaha') }}">
                            <div class="form-control-icon"><i class="bi bi-shop"></i></div>
                            @error('nama_usaha')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="no_hp" id="no_hp"
                                class="form-control form-control-xl @error('no_hp') is-invalid @enderror"
                                placeholder="Nomor HP" value="{{ old('no_hp') }}" maxlength="20" required>
                            <div class="form-control-icon"><i class="bi bi-telephone"></i></div>
                            @error('no_hp')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="button" class="btn btn-primary btn-block btn-lg shadow-lg mt-3" id="btn-data-next">
                            Lanjut <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>

                    {{-- ── STEP 2: ALAMAT ── --}}
                    <div id="register-alamat-step" class="d-none">
                        <p class="text-muted fw-semibold mb-3">Alamat Pemilik &amp; Usaha</p>

                        {{-- Alamat Domisili Pemilik --}}
                        <div class="form-group mb-4">
                            <label for="alamat_domisili" class="form-label fw-semibold">
                                <i class="bi bi-house-door me-1"></i> Alamat Domisili Pemilik <span class="text-danger">*</span>
                            </label>
                            <div class="map-search-wrapper">
                                <input type="text" id="search-domisili" class="form-control" placeholder="Cari lokasi domisili...">
                                <i class="bi bi-search search-icon"></i>
                            </div>
                            <div id="map-domisili" class="leaflet-map-container"></div>
                            <p class="map-helper-text"><i class="bi bi-info-circle me-1"></i>Klik pada peta atau cari lokasi untuk menentukan alamat domisili</p>
                            <textarea name="alamat_domisili" id="alamat_domisili" rows="2"
                                class="form-control @error('alamat_domisili') is-invalid @enderror"
                                placeholder="Alamat domisili lengkap akan terisi otomatis dari peta, atau isi manual" required>{{ old('alamat_domisili') }}</textarea>
                            <input type="hidden" name="lat_domisili" id="lat_domisili" value="{{ old('lat_domisili') }}">
                            <input type="hidden" name="lng_domisili" id="lng_domisili" value="{{ old('lng_domisili') }}">
                            @error('alamat_domisili')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Alamat Usaha --}}
                        <div class="form-group mb-4">
                            <label for="alamat_usaha" class="form-label fw-semibold">
                                <i class="bi bi-building me-1"></i> Alamat Usaha
                            </label>
                            <div class="map-search-wrapper">
                                <input type="text" id="search-usaha" class="form-control" placeholder="Cari lokasi usaha...">
                                <i class="bi bi-search search-icon"></i>
                            </div>
                            <div id="map-usaha" class="leaflet-map-container"></div>
                            <p class="map-helper-text"><i class="bi bi-info-circle me-1"></i>Klik pada peta atau cari lokasi untuk menentukan alamat usaha</p>
                            <textarea name="alamat_usaha" id="alamat_usaha" rows="2"
                                class="form-control @error('alamat_usaha') is-invalid @enderror"
                                placeholder="Alamat usaha lengkap akan terisi otomatis dari peta, atau isi manual">{{ old('alamat_usaha') }}</textarea>
                            <input type="hidden" name="lat_usaha" id="lat_usaha" value="{{ old('lat_usaha') }}">
                            <input type="hidden" name="lng_usaha" id="lng_usaha" value="{{ old('lng_usaha') }}">
                            @error('alamat_usaha')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-light-secondary btn-lg flex-fill" id="btn-alamat-back">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-lg shadow-lg flex-fill" id="btn-alamat-next">
                                Lanjut <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ── STEP 3: DOKUMEN ── --}}
                    <div id="register-dokumen-step" class="d-none">
                        <p class="text-muted fw-semibold mb-2 mt-1">Dokumen Administratif</p>
                        <div class="alert alert-light-info color-info py-2 px-3 mb-3" style="font-size: 0.85rem;">
                            <i class="bi bi-info-circle me-1"></i>
                            Format file yang diterima: <strong>PDF, JPG, JPEG, PNG</strong>. Ukuran maksimal: <strong>2MB</strong> per file.
                        </div>

                        <div class="form-group mb-4">
                            <label for="ktp" class="form-label">KTP <span class="text-danger">*</span></label>
                            <small class="wajib-info d-block mb-1"><i class="bi bi-exclamation-circle me-1"></i>Wajib diunggah</small>
                            <input type="file" name="ktp" id="ktp"
                                class="form-control form-control-xl @error('ktp') is-invalid @enderror"
                                accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="dokumen-info mt-1 d-block">Format: PDF, JPG, JPEG, PNG. Maks. 2MB</small>
                            @error('ktp')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="nib" class="form-label">NIB</label>
                            <input type="file" name="nib" id="nib"
                                class="form-control form-control-xl @error('nib') is-invalid @enderror"
                                accept=".pdf,.jpg,.jpeg,.png">
                            <small class="dokumen-info mt-1 d-block">Format: PDF, JPG, JPEG, PNG. Maks. 2MB</small>
                            @error('nib')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="npwp" class="form-label">NPWP</label>
                            <input type="file" name="npwp" id="npwp"
                                class="form-control form-control-xl @error('npwp') is-invalid @enderror"
                                accept=".pdf,.jpg,.jpeg,.png">
                            <small class="dokumen-info mt-1 d-block">Format: PDF, JPG, JPEG, PNG. Maks. 2MB</small>
                            @error('npwp')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-4">
                            <label for="formulir_pendaftaran" class="form-label">
                                Formulir Pendaftaran <span class="text-danger">*</span>
                            </label>
                            <small class="wajib-info d-block mb-1"><i class="bi bi-exclamation-circle me-1"></i>Wajib diunggah</small>
                            <input type="file" name="formulir_pendaftaran" id="formulir_pendaftaran"
                                class="form-control form-control-xl @error('formulir_pendaftaran') is-invalid @enderror"
                                accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="dokumen-info mt-1 d-block">Format: PDF, JPG, JPEG, PNG. Maks. 2MB</small>
                            @error('formulir_pendaftaran')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-light-secondary btn-lg flex-fill" id="btn-dokumen-back">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-lg shadow-lg flex-fill" id="btn-dokumen-next">
                                Lanjut <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>

                    {{-- ── STEP 4: AKUN ── --}}
                    <div id="register-akun-step" class="d-none">
                        <p class="text-muted fw-semibold mb-2 mt-1">Data Akun</p>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" name="name" id="name"
                                class="form-control form-control-xl @error('name') is-invalid @enderror"
                                placeholder="Nama Pengguna" value="{{ old('name') }}" required autofocus>
                            <div class="form-control-icon"><i class="bi bi-person"></i></div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" name="email" id="email"
                                class="form-control form-control-xl @error('email') is-invalid @enderror"
                                placeholder="Email" value="{{ old('email') }}" required>
                            <div class="form-control-icon"><i class="bi bi-envelope"></i></div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="password" id="password"
                                class="form-control form-control-xl @error('password') is-invalid @enderror"
                                placeholder="Password" required>
                            <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control form-control-xl @error('password_confirmation') is-invalid @enderror"
                                placeholder="Konfirmasi Password" required>
                            <div class="form-control-icon"><i class="bi bi-shield-lock"></i></div>
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-light-secondary btn-lg flex-fill" id="btn-akun-back">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg shadow-lg flex-fill">
                                <i class="bi bi-send me-1"></i> Daftar Sekarang
                            </button>
                        </div>
                    </div>
                </form>

                <div class="text-center mt-4 text-lg fs-4">
                    <p class="text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="fw-bold">Masuk</a>.</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6 d-none d-lg-block">
            <div id="auth-right"
                style="
                display: flex !important;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                height: 100%;
                padding: 3rem;
                background: linear-gradient(135deg, #435ebe 0%, #2d4aa8 100%);
            ">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="rgba(255,255,255,0.9)"
                        viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                        <path
                            d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z" />
                    </svg>
                </div>
                <h1 class="text-white text-center fw-bold mb-3"
                    style="font-size: 2.2rem; line-height: 1.4; letter-spacing: 1px;">
                    SISTEM PENDUKUNG KEPUTUSAN
                </h1>
                <h2 class="text-white text-center fw-bold mb-4" style="font-size: 1.6rem; line-height: 1.5;">
                    PENERIMAAN AGEN BEJUBIS@LAKUPANDAI
                </h2>
                <div class="mb-4">
                    <span style="
                        display: inline-block;
                        background: rgba(255,255,255,0.2);
                        color: #fff;
                        padding: 0.5rem 1.5rem;
                        border-radius: 50px;
                        font-size: 1rem;
                        font-weight: 600;
                        letter-spacing: 2px;
                        border: 2px solid rgba(255,255,255,0.5);
                    ">
                        MENGGUNAKAN METODE SMART
                    </span>
                </div>
                <div style="width: 60px; height: 4px; background: rgba(255,255,255,0.5); border-radius: 2px; margin-bottom: 1.5rem;"></div>
                <p class="text-center mb-0"
                    style="color: rgba(255,255,255,0.8); font-size: 0.95rem; max-width: 400px; line-height: 1.7;">
                    Simple Multi Attribute Rating Technique (SMART) digunakan untuk membantu proses
                    seleksi dan penerimaan agen secara objektif dan terukur.
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ── Step elements ──
            const dataStep    = document.getElementById('register-data-step');
            const alamatStep  = document.getElementById('register-alamat-step');
            const dokumenStep = document.getElementById('register-dokumen-step');
            const akunStep    = document.getElementById('register-akun-step');

            // ── Tab buttons ──
            const dataTab    = document.getElementById('register-data-tab');
            const alamatTab  = document.getElementById('register-alamat-tab');
            const dokumenTab = document.getElementById('register-dokumen-tab');
            const akunTab    = document.getElementById('register-akun-tab');

            const allSteps = [dataStep, alamatStep, dokumenStep, akunStep];
            const allTabs  = [dataTab, alamatTab, dokumenTab, akunTab];
            const stepKeys = ['data', 'alamat', 'dokumen', 'akun'];

            // ── Show step helper ──
            const showStep = function (step) {
                const idx = stepKeys.indexOf(step);
                allSteps.forEach(function (el, i) {
                    el.classList.toggle('d-none', i !== idx);
                });
                allTabs.forEach(function (btn, i) {
                    btn.classList.toggle('btn-primary', i === idx);
                    btn.classList.toggle('btn-light-secondary', i !== idx);
                });

                // Invalidate Leaflet map size when alamat step becomes visible
                if (step === 'alamat') {
                    setTimeout(function () {
                        if (mapDomisili) mapDomisili.invalidateSize();
                        if (mapUsaha)    mapUsaha.invalidateSize();
                    }, 150);
                }
            };

            // ── Validation helpers ──
            const validateStep = function (stepEl) {
                const fields = stepEl.querySelectorAll('input[required], textarea[required], select[required]');
                for (const f of fields) {
                    if (!f.checkValidity()) { f.reportValidity(); return false; }
                }
                return true;
            };

            // ── Navigation buttons ──
            document.getElementById('btn-data-next').addEventListener('click', function () {
                if (validateStep(dataStep)) showStep('alamat');
            });

            document.getElementById('btn-alamat-back').addEventListener('click', function () {
                showStep('data');
            });
            document.getElementById('btn-alamat-next').addEventListener('click', function () {
                if (validateStep(alamatStep)) showStep('dokumen');
            });

            document.getElementById('btn-dokumen-back').addEventListener('click', function () {
                showStep('alamat');
            });
            document.getElementById('btn-dokumen-next').addEventListener('click', function () {
                if (validateStep(dokumenStep)) showStep('akun');
            });

            document.getElementById('btn-akun-back').addEventListener('click', function () {
                showStep('dokumen');
            });

            // ── Tab clicks (with progressive validation) ──
            dataTab.addEventListener('click', function () { showStep('data'); });

            alamatTab.addEventListener('click', function () {
                if (validateStep(dataStep)) showStep('alamat');
            });

            dokumenTab.addEventListener('click', function () {
                if (!validateStep(dataStep)) return;
                showStep('alamat');
                if (validateStep(alamatStep)) showStep('dokumen');
            });

            akunTab.addEventListener('click', function () {
                if (!validateStep(dataStep)) return;
                showStep('alamat');
                if (!validateStep(alamatStep)) return;
                showStep('dokumen');
                if (validateStep(dokumenStep)) showStep('akun');
            });

            // ── Redirect to correct step on validation error after submit ──
            @if ($errors->has('ktp') || $errors->has('nib') || $errors->has('npwp') || $errors->has('formulir_pendaftaran'))
                showStep('dokumen');
            @elseif ($errors->has('alamat_domisili') || $errors->has('alamat_usaha'))
                showStep('alamat');
            @elseif ($errors->has('name') || $errors->has('email') || $errors->has('password') || $errors->has('password_confirmation'))
                showStep('akun');
            @endif

            // ── Leaflet Maps with ESRI Satellite ──
            const esriSatellite  = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
            const esriAttribution = '&copy; <a href="https://www.esri.com/">Esri</a>, Earthstar Geographics';
            const fallbackCenter  = [-2.5, 118.0];
            const defaultZoom     = 13;

            function createMarker(latlng, map, textarea, latId, lngId) {
                const m = L.marker(latlng, { draggable: true }).addTo(map);
                m.on('dragend', function (ev) {
                    const p = ev.target.getLatLng();
                    setCoords(latId, lngId, p.lat, p.lng);
                    reverseGeocode(p.lat, p.lng, textarea);
                });
                return m;
            }

            function setCoords(latInputId, lngInputId, lat, lng) {
                const latEl = document.getElementById(latInputId);
                const lngEl = document.getElementById(lngInputId);
                if (latEl) latEl.value = lat;
                if (lngEl) lngEl.value = lng;
            }

            function initMap(mapId, searchId, textareaId, latInputId, lngInputId) {
                const map      = L.map(mapId).setView(fallbackCenter, 5);
                const textarea = document.getElementById(textareaId);
                const searchEl = document.getElementById(searchId);
                let marker     = null;

                L.tileLayer(esriSatellite, { attribution: esriAttribution, maxZoom: 19 }).addTo(map);

                map.on('click', function (e) {
                    const lat = e.latlng.lat, lng = e.latlng.lng;
                    if (marker) { marker.setLatLng(e.latlng); }
                    else        { marker = createMarker(e.latlng, map, textarea, latInputId, lngInputId); }
                    setCoords(latInputId, lngInputId, lat, lng);
                    reverseGeocode(lat, lng, textarea);
                });

                let searchTimeout;
                searchEl.addEventListener('input', function () {
                    clearTimeout(searchTimeout);
                    const q = this.value.trim();
                    if (q.length < 3) return;
                    searchTimeout = setTimeout(function () {
                        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=1&countrycodes=id')
                            .then(function (r) { return r.json(); })
                            .then(function (data) {
                                if (!data || !data.length) return;
                                const ll = L.latLng(parseFloat(data[0].lat), parseFloat(data[0].lon));
                                map.setView(ll, 17);
                                if (marker) { marker.setLatLng(ll); }
                                else        { marker = createMarker(ll, map, textarea, latInputId, lngInputId); }
                                setCoords(latInputId, lngInputId, ll.lat, ll.lng);
                                textarea.value = data[0].display_name;
                            })
                            .catch(function () {});
                    }, 500);
                });

                searchEl.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') e.preventDefault();
                });

                return map;
            }

            function reverseGeocode(lat, lng, textarea) {
                fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1')
                    .then(function (r) { return r.json(); })
                    .then(function (data) { if (data && data.display_name) textarea.value = data.display_name; })
                    .catch(function () {});
            }

            const mapDomisili = initMap('map-domisili', 'search-domisili', 'alamat_domisili', 'lat_domisili', 'lng_domisili');
            const mapUsaha    = initMap('map-usaha',    'search-usaha',    'alamat_usaha',    'lat_usaha',    'lng_usaha');

            // ── Geolocation: pusatkan peta ke lokasi terkini user ──
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        const ll = L.latLng(pos.coords.latitude, pos.coords.longitude);
                        mapDomisili.setView(ll, defaultZoom);
                        mapUsaha.setView(ll, defaultZoom);
                    },
                    function () {},
                    { timeout: 8000, maximumAge: 60000 }
                );
            }
        });
    </script>
    @endpush
@endsection

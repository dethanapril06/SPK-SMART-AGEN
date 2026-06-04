@extends('layouts.auth')

@section('content')
    <div class="row h-100">
        <div class="col-lg-5 col-12">
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

                    <div class="d-flex gap-2 mb-4">
                        <button type="button" class="btn btn-primary flex-fill" id="register-data-tab">
                            <i class="bi bi-person-badge"></i> Calon Agen
                        </button>
                        <button type="button" class="btn btn-light-secondary flex-fill" id="register-dokumen-tab">
                            <i class="bi bi-file-earmark-arrow-up"></i> Dokumen
                        </button>
                        <button type="button" class="btn btn-light-secondary flex-fill" id="register-akun-tab">
                            <i class="bi bi-shield-lock"></i> Akun
                        </button>
                    </div>

                    <div id="register-data-step">

                    {{-- ── DATA AKUN ── --}}
                    <div id="register-akun-fields" class="d-none">
                    <p class="text-muted fw-semibold mb-2">Data Akun</p>

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
                            class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Email"
                            value="{{ old('email') }}" required>
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

                    {{-- ── DATA CALON AGEN ── --}}
                    </div>

                    <p class="text-muted fw-semibold mb-2 mt-3">Data Calon Agen</p>

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
                            placeholder="Nama Lengkap" value="{{ old('nama_lengkap') }}" required>
                        <div class="form-control-icon"><i class="bi bi-person-badge"></i></div>
                        @error('nama_lengkap')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="text" name="no_hp" id="no_hp"
                            class="form-control form-control-xl @error('no_hp') is-invalid @enderror" placeholder="Nomor HP"
                            value="{{ old('no_hp') }}" maxlength="20" required>
                        <div class="form-control-icon"><i class="bi bi-telephone"></i></div>
                        @error('no_hp')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group position-relative has-icon-left mb-4">
                        <textarea name="alamat" id="alamat" rows="3"
                            class="form-control form-control-xl @error('alamat') is-invalid @enderror" placeholder="Alamat Lengkap" required>{{ old('alamat') }}</textarea>
                        <div class="form-control-icon"><i class="bi bi-geo-alt"></i></div>
                        @error('alamat')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                        <button type="button" class="btn btn-primary btn-block btn-lg shadow-lg mt-3" id="register-next-button">
                            Next
                        </button>
                    </div>

                    <div id="register-dokumen-step" class="d-none">
                    <p class="text-muted fw-semibold mb-2 mt-3">Dokumen Administratif</p>

                    <div class="form-group mb-4">
                        <label for="ktp" class="form-label">KTP <span class="text-danger">*</span></label>
                        <input type="file" name="ktp" id="ktp"
                            class="form-control form-control-xl @error('ktp') is-invalid @enderror"
                            accept=".pdf,.jpg,.jpeg,.png" required>
                        @error('ktp')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="nib" class="form-label">NIB</label>
                        <input type="file" name="nib" id="nib"
                            class="form-control form-control-xl @error('nib') is-invalid @enderror"
                            accept=".pdf,.jpg,.jpeg,.png">
                        @error('nib')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="npwp" class="form-label">NPWP</label>
                        <input type="file" name="npwp" id="npwp"
                            class="form-control form-control-xl @error('npwp') is-invalid @enderror"
                            accept=".pdf,.jpg,.jpeg,.png">
                        @error('npwp')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="formulir_pendaftaran" class="form-label">Formulir Pendaftaran <span class="text-danger">*</span></label>
                        <input type="file" name="formulir_pendaftaran" id="formulir_pendaftaran"
                            class="form-control form-control-xl @error('formulir_pendaftaran') is-invalid @enderror"
                            accept=".pdf,.jpg,.jpeg,.png" required>
                        @error('formulir_pendaftaran')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-light-secondary btn-lg flex-fill" id="register-back-button">
                                Kembali
                            </button>
                            <button type="button" class="btn btn-primary btn-lg shadow-lg flex-fill" id="register-dokumen-next-button">
                                Next
                            </button>
                        </div>
                    </div>

                    <div id="register-akun-step" class="d-none">
                        <div id="register-akun-target"></div>
                        <div class="d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-light-secondary btn-lg flex-fill" id="register-akun-back-button">
                                Kembali
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg shadow-lg flex-fill">
                                Daftar Sekarang
                            </button>
                        </div>
                    </div>
                </form>

                <div class="text-center mt-4 text-lg fs-4">
                    <p class="text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="fw-bold">Masuk</a>.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
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
                {{-- Logo / Icon --}}
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="rgba(255,255,255,0.9)"
                        viewBox="0 0 16 16">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                        <path
                            d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z" />
                    </svg>
                </div>

                {{-- Judul Sistem --}}
                <h1 class="text-white text-center fw-bold mb-3"
                    style="font-size: 2.2rem; line-height: 1.4; letter-spacing: 1px;">
                    SISTEM PENDUKUNG KEPUTUSAN
                </h1>
                <h2 class="text-white text-center fw-bold mb-4" style="font-size: 1.6rem; line-height: 1.5;">
                    PENERIMAAN AGEN BEJUBIS@LAKUPANDAI
                </h2>
                <div class="mb-4">
                    <span
                        style="
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

                {{-- Garis pemisah --}}
                <div
                    style="width: 60px; height: 4px; background: rgba(255,255,255,0.5); border-radius: 2px; margin-bottom: 1.5rem;">
                </div>

                {{-- Deskripsi singkat --}}
                <p class="text-center mb-0"
                    style="color: rgba(255,255,255,0.8); font-size: 0.95rem; max-width: 400px; line-height: 1.7;">
                    Simple Multi Attribute Rating Technique (SMART) digunakan untuk membantu proses
                    seleksi dan penerimaan agen secara objektif dan terukur.
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dataStep = document.getElementById('register-data-step');
            const dokumenStep = document.getElementById('register-dokumen-step');
            const akunStep = document.getElementById('register-akun-step');
            const dataTab = document.getElementById('register-data-tab');
            const dokumenTab = document.getElementById('register-dokumen-tab');
            const akunTab = document.getElementById('register-akun-tab');
            const nextButton = document.getElementById('register-next-button');
            const backButton = document.getElementById('register-back-button');
            const dokumenNextButton = document.getElementById('register-dokumen-next-button');
            const akunBackButton = document.getElementById('register-akun-back-button');
            const akunFields = document.getElementById('register-akun-fields');
            const akunTarget = document.getElementById('register-akun-target');

            akunTarget.appendChild(akunFields);
            akunFields.classList.remove('d-none');

            const showStep = function(step) {
                const isDokumen = step === 'dokumen';
                const isAkun = step === 'akun';

                dataStep.classList.toggle('d-none', isDokumen || isAkun);
                dokumenStep.classList.toggle('d-none', !isDokumen);
                akunStep.classList.toggle('d-none', !isAkun);
                dataTab.classList.toggle('btn-primary', !isDokumen && !isAkun);
                dataTab.classList.toggle('btn-light-secondary', isDokumen || isAkun);
                dokumenTab.classList.toggle('btn-primary', isDokumen);
                dokumenTab.classList.toggle('btn-light-secondary', !isDokumen);
                akunTab.classList.toggle('btn-primary', isAkun);
                akunTab.classList.toggle('btn-light-secondary', !isAkun);
            };

            const validateDataStep = function() {
                const fields = dataStep.querySelectorAll('input[required], textarea[required], select[required]');

                for (const field of fields) {
                    if (!field.checkValidity()) {
                        field.reportValidity();
                        return false;
                    }
                }

                return true;
            };

            const validateDokumenStep = function() {
                const fields = dokumenStep.querySelectorAll('input[required], textarea[required], select[required]');

                for (const field of fields) {
                    if (!field.checkValidity()) {
                        field.reportValidity();
                        return false;
                    }
                }

                return true;
            };

            nextButton.addEventListener('click', function() {
                if (validateDataStep()) {
                    showStep('dokumen');
                }
            });

            backButton.addEventListener('click', function() {
                showStep('data');
            });

            dokumenNextButton.addEventListener('click', function() {
                if (validateDokumenStep()) {
                    showStep('akun');
                }
            });

            akunBackButton.addEventListener('click', function() {
                showStep('dokumen');
            });

            dataTab.addEventListener('click', function() {
                showStep('data');
            });

            dokumenTab.addEventListener('click', function() {
                if (validateDataStep()) {
                    showStep('dokumen');
                }
            });

            akunTab.addEventListener('click', function() {
                if (!validateDataStep()) {
                    return;
                }

                showStep('dokumen');

                if (validateDokumenStep()) {
                    showStep('akun');
                }
            });

            @if ($errors->has('ktp') || $errors->has('nib') || $errors->has('npwp') || $errors->has('formulir_pendaftaran'))
                showStep('dokumen');
            @elseif ($errors->has('name') || $errors->has('email') || $errors->has('password') || $errors->has('password_confirmation'))
                showStep('akun');
            @endif
        });
    </script>
@endsection

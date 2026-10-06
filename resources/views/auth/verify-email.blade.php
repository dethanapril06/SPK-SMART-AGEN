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
                <h1 class="auth-title fs-2">Verifikasi Email Anda</h1>
                <p class="auth-subtitle mb-4">
                    Terima kasih telah mendaftar! Sebelum melanjutkan, mohon periksa kotak masuk email Anda dan klik link verifikasi yang telah kami kirimkan ke:
                    <strong class="d-block text-dark mt-1">{{ auth()->user()->email }}</strong>
                </p>

                @if (session('success'))
                    <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <p class="small text-muted mb-0">
                            <i class="bi bi-info-circle me-1 text-primary"></i>
                            Tidak menerima email verifikasi? Cek juga folder <strong>Spam / Junk</strong> Anda, atau klik tombol di bawah untuk mengirim ulang tautan verifikasi.
                        </p>
                    </div>
                </div>

                <div class="d-flex flex-column gap-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg">
                            <i class="bi bi-envelope-paper me-1"></i> Kirim Ulang Link Verifikasi
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-light-secondary btn-block btn-lg">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7 d-none d-lg-block">
            <div id="auth-right" style="
                display: flex !important;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 100vh;
                background: linear-gradient(135deg, #435ebe 0%, #25396f 100%);
                padding: 3rem;
                color: #fff;
            ">
                <div class="text-center mb-4">
                    <i class="bi bi-shield-check text-white" style="font-size: 5rem;"></i>
                    <h2 class="text-white fw-bold mt-3">Verifikasi Akun Agen</h2>
                    <p class="text-white-50">Langkah keamanan untuk memastikan keabsahan data pendaftar.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

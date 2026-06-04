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
                <h1 class="auth-title">Log in.</h1>
                <p class="auth-subtitle mb-5">Masukkan data akun Anda untuk masuk ke sistem.</p>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="email" name="email" id="email"
                            class="form-control form-control-xl @error('email') is-invalid @enderror" placeholder="Email"
                            value="{{ old('email') }}" required autofocus autocomplete="username">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group position-relative has-icon-left mb-4">
                        <input type="password" name="password" id="password"
                            class="form-control form-control-xl @error('password') is-invalid @enderror"
                            placeholder="Password" required autocomplete="current-password">
                        <div class="form-control-icon">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-check form-check-lg d-flex align-items-end">
                        <input class="form-check-input me-2" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-muted" for="remember">
                            Ingat saya
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Masuk</button>
                </form>
                <div class="text-center mt-5 text-lg fs-4">
                    <p class="text-muted">Belum punya akun? <a href="{{ route('register') }}" class="fw-bold">Daftar</a>.
                    </p>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan --}}
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
@endsection

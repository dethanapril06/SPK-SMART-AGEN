@php
    $role = auth()->user()->role;
    $layout = match ($role) {
        'admin' => 'layouts.admin',
        'calon_agen' => 'layouts.calon-agen',
        default => 'layouts.guest',
    };
    $dashboardRoute = match ($role) {
        'admin' => 'admin.dashboard',
        'calon_agen' => 'calon-agen.dashboard',
        default => 'login',
    };
@endphp

@extends($layout)

@section('title', 'Profil Saya')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Profil Saya</h3>
                    <p class="text-subtitle text-muted">Kelola informasi akun Anda</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route($dashboardRoute) }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Profil</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">

                {{-- Sidebar Profile Card --}}
                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            @php
                                $name = auth()->user()->name;
                                $initials = collect(explode(' ', $name))
                                    ->map(fn($w) => strtoupper($w[0]))
                                    ->take(2)
                                    ->implode('');
                                $avatarUrl =
                                    'https://ui-avatars.com/api/?name=' .
                                    urlencode($name) .
                                    '&background=435ebe&color=fff&size=128&bold=true&font-size=0.4';
                            @endphp
                            <div class="avatar avatar-xl mb-3 mx-auto">
                                <img src="{{ $avatarUrl }}" alt="Avatar {{ $name }}" class="rounded-circle"
                                    style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                            <h5 class="mb-1 fw-bold">{{ $name }}</h5>
                            <p class="text-muted mb-1">{{ auth()->user()->email }}</p>
                            <span class="badge bg-primary">
                                {{ match ($role) {
                                    'admin' => 'Administrator',
                                    'calon_agen' => 'Calon Agen',
                                    default => ucfirst($role),
                                } }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Forms Column --}}
                <div class="col-md-8 col-12">

                    {{-- Update Profile Information --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="bi bi-person-fill me-2 text-primary"></i>
                                Informasi Profil
                            </h4>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    {{-- Update Password --}}
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="bi bi-shield-lock-fill me-2 text-warning"></i>
                                Ubah Password
                            </h4>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    {{-- Delete Account --}}
                    <div class="card border border-danger mb-4">
                        <div class="card-header bg-danger bg-opacity-10">
                            <h4 class="card-title text-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                Hapus Akun
                            </h4>
                        </div>
                        <div class="card-body">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </div>
@endsection

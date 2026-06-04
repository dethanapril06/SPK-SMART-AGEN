@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Dashboard Admin</h3>
                    <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan sistem SPK SMART
                        Agen.</p>
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

            {{-- Info Periode Aktif --}}
            @if ($periodeAktif)
                <div class="alert alert-light-success color-success mb-4">
                    <i class="bi bi-calendar-check me-2"></i>
                    Periode pendaftaran aktif: <strong>{{ $periodeAktif->nama_periode }}</strong>
                    ({{ $periodeAktif->tanggal_buka->format('d/m/Y') }} –
                    {{ $periodeAktif->tanggal_tutup->format('d/m/Y') }})
                </div>
            @else
                <div class="alert alert-light-warning color-warning mb-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Tidak ada periode pendaftaran yang sedang aktif.
                    <a href="{{ route('admin.periode-pendaftaran.create') }}" class="fw-bold ms-1">Buat periode baru</a>
                </div>
            @endif

            {{-- Baris 1: Kartu Statistik Utama --}}
            <div class="row g-3">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-people-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Calon Agen</h6>
                                    <h6 class="font-extrabold mb-0">{{ $totalCalonAgen }}</h6>
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
                                        <i class="bi bi-diagram-3-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Kriteria</h6>
                                    <h6 class="font-extrabold mb-0">{{ $totalKriteria }}</h6>
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
                                        <i class="bi bi-list-check"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Sub Kriteria</h6>
                                    <h6 class="font-extrabold mb-0">{{ $totalSubKriteria }}</h6>
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
                                    <div class="stats-icon red mb-2">
                                        <i class="bi bi-calendar2-range-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Total Periode</h6>
                                    <h6 class="font-extrabold mb-0">{{ $totalPeriode }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Baris 2: Status Calon Agen --}}
            <div class="row g-3 mt-1">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                    <div class="stats-icon yellow mb-2">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Diproses</h6>
                                    <h6 class="font-extrabold mb-0">{{ $totalDiproses }}</h6>
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
                                    <div class="stats-icon blue mb-2">
                                        <i class="bi bi-clipboard2-pulse-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Disurvey</h6>
                                    <h6 class="font-extrabold mb-0">{{ $totalDisurvey }}</h6>
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
                                        <i class="bi bi-person-check-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Direkomendasikan</h6>
                                    <h6 class="font-extrabold mb-0">{{ $totalDirekomendasi }}</h6>
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
                                    <div class="stats-icon red mb-2">
                                        <i class="bi bi-person-x-fill"></i>
                                    </div>
                                </div>
                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                    <h6 class="text-muted font-semibold">Belum Direkomendasi</h6>
                                    <h6 class="font-extrabold mb-0">{{ $totalBelumDirekomendasi }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Baris 3: Tabel + Progress --}}
            <div class="row g-3 mt-1">

                {{-- Calon Agen Terbaru --}}
                <div class="col-12 col-xl-7">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-0">Calon Agen Terbaru</h4>
                                <small class="text-muted">5 pendaftar terakhir</small>
                            </div>
                            <a href="{{ route('admin.calon-agen.index') }}" class="btn btn-sm btn-light-secondary">Lihat
                                Semua</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Periode</th>
                                            <th>Status</th>
                                            <th>Tanggal Daftar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($calonAgenTerbaru as $item)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $item->nama_lengkap }}</div>
                                                    <small class="text-muted">{{ $item->nik }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ $item->periode->nama_periode ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $badge = match ($item->status) {
                                                            'direkomendasi' => 'success',
                                                            'disurvey' => 'info',
                                                            'diproses' => 'warning',
                                                            'belumdirekomendasi' => 'danger',
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $badge }}">
                                                        {{ ucfirst($item->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ $item->created_at->format('d/m/Y') }}</small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    Belum ada calon agen terdaftar.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Progress & Ringkasan --}}
                <div class="col-12 col-xl-5">
                    <div class="card h-100">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Distribusi Status Calon Agen</h4>
                            <small class="text-muted">Gambaran progres seleksi</small>
                        </div>
                        <div class="card-body">
                            @php $total = max(1, $totalCalonAgen); @endphp

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Diproses</span>
                                    <span>{{ $totalDiproses }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ round(($totalDiproses / $total) * 100, 1) }}%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Disurvey</span>
                                    <span>{{ $totalDisurvey }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ round(($totalDisurvey / $total) * 100, 1) }}%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Direkomendasi</span>
                                    <span>{{ $totalDirekomendasi }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ round(($totalDirekomendasi / $total) * 100, 1) }}%"></div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Belum Direkomendasi</span>
                                    <span>{{ $totalBelumDirekomendasi }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-danger" role="progressbar"
                                        style="width: {{ round(($totalBelumDirekomendasi / $total) * 100, 1) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Baris 4: Hasil SMART Terbaru --}}
            @if ($hasilSmartTerbaru->isNotEmpty())
                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="card-title mb-0">Hasil SMART Terbaru</h4>
                                    <small class="text-muted">Hasil perhitungan SMART terkini</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>Peringkat</th>
                                                <th>Calon Agen</th>
                                                <th>Periode</th>
                                                <th>Skor Akhir</th>
                                                <th>Keputusan</th>
                                                <th>Dihitung Pada</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($hasilSmartTerbaru as $hasil)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-primary">#{{ $hasil->peringkat }}</span>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold">
                                                            {{ $hasil->calonAgen->nama_lengkap ?? '-' }}
                                                        </div>
                                                        <small class="text-muted">
                                                            {{ $hasil->calonAgen->nik ?? '-' }}
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <small>{{ $hasil->periode->nama_periode ?? '-' }}</small>
                                                    </td>
                                                    <td>
                                                        <strong>{{ number_format($hasil->skor_akhir, 4) }}</strong>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $hasil->keputusan === 'direkomendasi' ? 'success' : 'danger' }}">
                                                            {{ ucfirst($hasil->keputusan) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small>{{ \Carbon\Carbon::parse($hasil->dihitung_at)->format('d/m/Y H:i') }}</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </section>
    </div>
@endsection

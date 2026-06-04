@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Laporan</h3>
                    <p class="text-muted">Cetak laporan dalam format PDF</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Laporan</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row g-3">

                {{-- Laporan 1: Rekap Periode --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-calendar2-range text-primary me-2"></i>
                                Rekap Periode
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Menampilkan rekap semua periode pendaftaran beserta jumlah pendaftar,
                                direkomendasi, belumdirekomendasi, dan status.</p>
                            <a href="{{ route('admin.laporan.rekap-periode') }}" target="_blank" class="btn btn-primary">
                                <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Laporan 2: Calon Agen --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people text-success me-2"></i>
                                Calon Agen
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Daftar calon agen dengan filter periode dan status pendaftaran.</p>
                            <form action="{{ route('admin.laporan.calon-agen') }}" target="_blank" method="GET"
                                class="row g-2">
                                <div class="col-12">
                                    <select name="periode_id" class="form-select form-select-sm">
                                        <option value="">-- Semua Periode --</option>
                                        @foreach ($periodes as $periode)
                                            <option value="{{ $periode->id }}">{{ $periode->nama_periode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="">-- Semua Status --</option>
                                        <option value="diproses">Diproses</option>
                                        <option value="disurvey">Disurvey</option>
                                        <option value="direkomendasi">Direkomendasi</option>
                                        <option value="belumdirekomendasi">Belum Direkomendasi</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Laporan 3: Penilaian --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-clipboard2-data text-warning me-2"></i>
                                Penilaian Per Kriteria
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Laporan nilai setiap calon agen berdasarkan setiap kriteria dalam satu
                                periode.</p>
                            <form action="{{ route('admin.laporan.penilaian') }}" target="_blank" method="GET"
                                class="row g-2">
                                <div class="col-12">
                                    <select name="periode_id" class="form-select form-select-sm" required>
                                        <option value="" disabled selected>-- Pilih Periode --</option>
                                        @foreach ($periodes as $periode)
                                            <option value="{{ $periode->id }}">{{ $periode->nama_periode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-warning btn-sm text-white">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Laporan 4: Hasil Seleksi --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-trophy text-danger me-2"></i>
                                Hasil Seleksi SMART
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Laporan ranking dan hasil keputusan seleksi SMART per periode pendaftaran.
                            </p>
                            <form action="{{ route('admin.laporan.hasil-seleksi') }}" target="_blank" method="GET"
                                class="row g-2">
                                <div class="col-12">
                                    <select name="periode_id" class="form-select form-select-sm" required>
                                        <option value="" disabled selected>-- Pilih Periode --</option>
                                        @foreach ($periodes as $periode)
                                            <option value="{{ $periode->id }}">{{ $periode->nama_periode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

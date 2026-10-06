@extends('layouts.admin')

@section('title', 'Daftar Calon Agen - ' . $periode->nama_periode)

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Calon Agen</h3>
                    <p class="text-subtitle text-muted">{{ $periode->nama_periode }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.penilaian.index') }}">Penilaian</a></li>
                            <li class="breadcrumb-item active">{{ $periode->nama_periode }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="page-content">

            @if (session('error'))
                <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Info periode --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-light border d-flex align-items-center justify-content-between mb-0">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                            <div>
                                Periode: <strong>{{ $periode->nama_periode }}</strong> &nbsp;|&nbsp;
                                {{ $periode->tanggal_buka->format('d M Y') }} &mdash;
                                {{ $periode->tanggal_tutup->format('d M Y') }}
                                &nbsp;|&nbsp; Kuota: <strong>{{ $periode->kuota ?? '-' }} Agen</strong>
                                &nbsp;|&nbsp; Total kriteria: <strong>{{ $kriteriaCount }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Daftar Calon Agen</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="table-calon-agen">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Calon Agen</th>
                                            <th>NIK</th>
                                            <th>No. HP</th>
                                            <th>Verifikasi Dokumen</th>
                                            <th>Status SPK</th>
                                            <th>Status Penilaian</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($calonAgens as $i => $ca)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>
                                                    <div class="fw-bold">{{ $ca->nama_usaha ?? '-' }}</div>
                                                    <small class="text-muted">{{ $ca->nama_lengkap }}</small>
                                                </td>
                                                <td><code>{{ $ca->nik }}</code></td>
                                                <td>{{ $ca->no_hp }}</td>
                                                <td>
                                                    @if ($ca->status_verifikasi === 'valid')
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle me-1"></i>Valid
                                                        </span>
                                                    @elseif ($ca->status_verifikasi === 'tidak_valid')
                                                        <span class="badge bg-danger" title="{{ $ca->catatan_verifikasi }}">
                                                            <i class="bi bi-x-circle me-1"></i>Tidak Valid
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="bi bi-clock me-1"></i>Menunggu
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClass = match ($ca->status) {
                                                            'diproses' => 'bg-warning',
                                                            'disurvey' => 'bg-info',
                                                            'direkomendasi' => 'bg-success',
                                                            'belumdirekomendasi' => 'bg-danger',
                                                            default => 'bg-secondary',
                                                        };
                                                        $statusLabel = match ($ca->status) {
                                                            'diproses' => 'Diproses',
                                                            'disurvey' => 'Disurvey',
                                                            'direkomendasi' => 'Direkomendasi',
                                                            'belumdirekomendasi' => 'Belum Direkomendasi',
                                                            default => ucfirst($ca->status),
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                                </td>
                                                <td>
                                                    @if ($ca->sudah_lengkap)
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle me-1"></i>Sudah Dinilai
                                                        </span>
                                                    @else
                                                        <span class="badge bg-light text-dark border">
                                                            <i class="bi bi-dash-circle me-1"></i>
                                                            {{ $ca->sudah_dinilai_count }}/{{ $kriteriaCount }} kriteria
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($ca->isVerifikasiValid())
                                                        <a href="{{ route('admin.penilaian.form', [$periode, $ca]) }}"
                                                            class="btn btn-sm {{ $ca->sudah_lengkap ? 'btn-outline-primary' : 'btn-primary' }}">
                                                            <i
                                                                class="bi bi-{{ $ca->sudah_lengkap ? 'pencil-square' : 'clipboard2-plus' }} me-1"></i>
                                                            {{ $ca->sudah_lengkap ? 'Edit Penilaian' : 'Nilai Sekarang' }}
                                                        </a>
                                                    @else
                                                        <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                                                            title="Dokumen calon agen belum diverifikasi valid oleh admin.">
                                                            <button class="btn btn-sm btn-secondary" disabled>
                                                                <i class="bi bi-lock me-1"></i>Dokumen Belum Valid
                                                            </button>
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">
                                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                                    Belum ada calon agen di periode ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

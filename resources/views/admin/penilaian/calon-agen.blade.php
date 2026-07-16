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

            {{-- Info periode --}}
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-light border d-flex align-items-center gap-3 mb-0">
                        <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                        <div>
                            Periode: <strong>{{ $periode->nama_periode }}</strong> &nbsp;|&nbsp;
                            {{ $periode->tanggal_buka->format('d M Y') }} &mdash;
                            {{ $periode->tanggal_tutup->format('d M Y') }}
                            &nbsp;|&nbsp; Total kriteria: <strong>{{ $kriteriaCount }}</strong>
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
                                <table class="table table-hover" id="table-calon-agen">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Calon Agen</th>
                                            <th>NIK</th>
                                            <th>No. HP</th>
                                            <th>Status Pendaftaran</th>
                                            <th>Status Penilaian</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($calonAgens as $i => $ca)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $ca->nama_usaha ?? '-' }}</td>
                                                <td><code>{{ $ca->nik }}</code></td>
                                                <td>{{ $ca->no_hp }}</td>
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
                                                    <a href="{{ route('admin.penilaian.form', [$periode, $ca]) }}"
                                                        class="btn btn-sm {{ $ca->sudah_lengkap ? 'btn-outline-primary' : 'btn-primary' }}">
                                                        <i
                                                            class="bi bi-{{ $ca->sudah_lengkap ? 'pencil-square' : 'clipboard2-plus' }} me-1"></i>
                                                        {{ $ca->sudah_lengkap ? 'Edit Penilaian' : 'Nilai Sekarang' }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
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

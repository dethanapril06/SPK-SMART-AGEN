@extends('layouts.admin')

@section('title', 'Detail Periode Pendaftaran')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Periode Pendaftaran</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.periode-pendaftaran.index') }}">Periode Pendaftaran</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detail Periode</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">

                {{-- Card Info Utama --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Informasi Periode</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.periode-pendaftaran.edit', $periodePendaftaran) }}"
                                    class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="{{ route('admin.periode-pendaftaran.index') }}"
                                    class="btn btn-sm btn-light-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Nama Periode</th>
                                            <td>: {{ $periodePendaftaran->nama_periode }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Buka</th>
                                            <td>: {{ $periodePendaftaran->tanggal_buka->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Tutup</th>
                                            <td>: {{ $periodePendaftaran->tanggal_tutup->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>:
                                                @php
                                                    $badge = match ($periodePendaftaran->status) {
                                                        'aktif' => 'success',
                                                        'draft' => 'warning',
                                                        'ditutup' => 'danger',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badge }}">
                                                    {{ ucfirst($periodePendaftaran->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Dibuat Oleh</th>
                                            <td>: {{ $periodePendaftaran->pembuat->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Dibuat Pada</th>
                                            <td>: {{ $periodePendaftaran->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Diperbarui Pada</th>
                                            <td>: {{ $periodePendaftaran->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <div class="card bg-light-primary shadow-none">
                                                <div class="card-body py-3">
                                                    <h3 class="mb-0">{{ $periodePendaftaran->calonAgen->count() }}</h3>
                                                    <small class="text-muted">Calon Agen</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card bg-light-success shadow-none">
                                                <div class="card-body py-3">
                                                    <h3 class="mb-0">{{ $periodePendaftaran->penilaian->count() }}</h3>
                                                    <small class="text-muted">Penilaian</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Daftar Calon Agen --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Daftar Calon Agen</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Status</th>
                                            <th>Tanggal Daftar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($periodePendaftaran->calonAgen as $index => $calon)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $calon->nama_lengkap ?? '-' }}</td>
                                                <td>{{ $calon->status ?? '-' }}</td>
                                                <td>{{ $calon->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Belum ada calon agen pada periode
                                                    ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
@endsection

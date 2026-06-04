@extends('layouts.admin')

@section('title', 'Detail Kriteria')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Kriteria</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.kriteria.index') }}">Kriteria</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">

                {{-- Info Utama --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Informasi Kriteria</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.kriteria.edit', $kriteria) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="{{ route('admin.kriteria.index') }}" class="btn btn-sm btn-light-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless" style="max-width: 500px">
                                <tr>
                                    <th width="40%">Kode Kriteria</th>
                                    <td>: <span class="badge bg-secondary">{{ $kriteria->kode_kriteria }}</span></td>
                                </tr>
                                <tr>
                                    <th>Nama Kriteria</th>
                                    <td>: {{ $kriteria->nama_kriteria }}</td>
                                </tr>
                                <tr>
                                    <th>Bobot</th>
                                    <td>: {{ $kriteria->bobot }}%</td>
                                </tr>
                                <tr>
                                    <th>Tipe</th>
                                    <td>:
                                        <span class="badge bg-{{ $kriteria->isBenefit() ? 'success' : 'danger' }}">
                                            {{ ucfirst($kriteria->tipe) }}
                                        </span>
                                        <small class="text-muted ms-1">
                                            ({{ $kriteria->isBenefit() ? 'semakin tinggi semakin baik' : 'semakin rendah semakin baik' }})
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dibuat Oleh</th>
                                    <td>: {{ $kriteria->pembuat->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat Pada</th>
                                    <td>: {{ $kriteria->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Daftar Sub Kriteria --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                Daftar Sub Kriteria
                                <span class="badge bg-primary ms-1">{{ $kriteria->subKriteria->count() }}</span>
                            </h5>
                            <a href="{{ route('admin.kriteria.sub-kriteria.index', $kriteria) }}"
                                class="btn btn-sm btn-primary">
                                <i class="bi bi-list-ul"></i> Kelola Sub Kriteria
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Sub Kriteria</th>
                                            <th>Nilai</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($kriteria->subKriteria as $index => $sub)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $sub->nama_sub }}</td>
                                                <td>{{ $sub->nilai }}</td>
                                                <td>{{ $sub->keterangan ?: '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Belum ada sub kriteria.</td>
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

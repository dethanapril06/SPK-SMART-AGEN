@extends('layouts.admin')

@section('title', 'Edit Sub Kriteria')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Sub Kriteria</h3>
                    <p class="text-muted">
                        Kriteria: <span class="badge bg-secondary">{{ $kriteria->kode_kriteria }}</span>
                        {{ $kriteria->nama_kriteria }}
                    </p>
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
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.kriteria.sub-kriteria.index', $kriteria) }}">Sub Kriteria</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Sub Kriteria</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form class="form form-vertical" method="POST"
                            action="{{ route('admin.sub-kriteria.update', $subKriteria) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">

                                    <div class="col-md-8 col-12">
                                        <div class="form-group">
                                            <label for="nama_sub">Nama Sub Kriteria</label>
                                            <input type="text"
                                                class="form-control @error('nama_sub') is-invalid @enderror"
                                                placeholder="Contoh: Sangat Berpengalaman" id="nama_sub" name="nama_sub"
                                                value="{{ old('nama_sub', $subKriteria->nama_sub) }}" required>
                                            @error('nama_sub')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="nilai">Nilai</label>
                                            <input type="number" class="form-control @error('nilai') is-invalid @enderror"
                                                placeholder="Contoh: 5" id="nilai" name="nilai"
                                                value="{{ old('nilai', $subKriteria->nilai) }}" min="0"
                                                step="0.01" required>
                                            @error('nilai')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="keterangan">Keterangan <span
                                                    class="text-muted">(opsional)</span></label>
                                            <input type="text"
                                                class="form-control @error('keterangan') is-invalid @enderror"
                                                placeholder="Contoh: Pengalaman lebih dari 5 tahun" id="keterangan"
                                                name="keterangan"
                                                value="{{ old('keterangan', $subKriteria->keterangan) }}">
                                            @error('keterangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('admin.kriteria.sub-kriteria.index', $kriteria) }}"
                                            class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Perbarui</button>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

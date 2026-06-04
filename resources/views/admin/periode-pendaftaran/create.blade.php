@extends('layouts.admin')

@section('title', 'Tambah Periode Pendaftaran')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Periode Pendaftaran</h3>
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
                            <li class="breadcrumb-item active" aria-current="page">Form Tambah Periode</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Tambah Periode Pendaftaran</h4>
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
                            action="{{ route('admin.periode-pendaftaran.store') }}">
                            @csrf
                            <div class="form-body">
                                <div class="row">

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="nama_periode">Nama Periode</label>
                                            <input type="text"
                                                class="form-control @error('nama_periode') is-invalid @enderror"
                                                placeholder="Contoh: Periode Pendaftaran 2025" id="nama_periode"
                                                name="nama_periode" value="{{ old('nama_periode') }}" required>
                                            @error('nama_periode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="tanggal_buka">Tanggal Buka</label>
                                            <input type="date"
                                                class="form-control @error('tanggal_buka') is-invalid @enderror"
                                                id="tanggal_buka" name="tanggal_buka" value="{{ old('tanggal_buka') }}"
                                                required>
                                            @error('tanggal_buka')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="tanggal_tutup">Tanggal Tutup</label>
                                            <input type="date"
                                                class="form-control @error('tanggal_tutup') is-invalid @enderror"
                                                id="tanggal_tutup" name="tanggal_tutup" value="{{ old('tanggal_tutup') }}"
                                                required>
                                            @error('tanggal_tutup')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status"
                                                name="status" required>
                                                <option value="" disabled selected>-- Pilih Status --</option>
                                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>
                                                    Draft</option>
                                                <option value="aktif" {{ old('status') === 'aktif' ? 'selected' : '' }}>
                                                    Aktif</option>
                                                <option value="ditutup"
                                                    {{ old('status') === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle"></i>
                                                Jika status diubah ke <strong>Aktif</strong>, periode lain yang sedang
                                                aktif akan otomatis ditutup.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('admin.periode-pendaftaran.index') }}"
                                            class="btn btn-light-secondary me-1 mb-1">Batal</a>
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Simpan</button>
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

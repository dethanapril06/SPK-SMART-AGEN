@extends('layouts.admin')

@section('title', 'Tambah Calon Agen')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Tambah Calon Agen</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.calon-agen.index') }}">Calon Agen</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Form Calon Agen</h4>
                    <a href="{{ route('admin.calon-agen.index') }}" class="btn btn-sm btn-light-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.calon-agen.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="alert alert-light-info color-info">
                            Akun calon agen akan dibuat otomatis. Email login dibuat dari nama lengkap, password default:
                            <strong>password</strong>.
                        </div>

                        <h6 class="text-muted mb-3 mt-2">Data Calon Agen</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="periode_id" class="form-label">Periode</label>
                                <select name="periode_id" id="periode_id"
                                    class="form-select @error('periode_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Periode --</option>
                                    @foreach ($periodes as $periode)
                                        <option value="{{ $periode->id }}"
                                            {{ old('periode_id') == $periode->id ? 'selected' : '' }}>
                                            {{ $periode->nama_periode }} ({{ ucfirst($periode->status) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('periode_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" name="nik" id="nik"
                                    class="form-control @error('nik') is-invalid @enderror"
                                    value="{{ old('nik') }}" maxlength="16" required>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap"
                                    class="form-control @error('nama_lengkap') is-invalid @enderror"
                                    value="{{ old('nama_lengkap') }}" required>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="text" name="no_hp" id="no_hp"
                                    class="form-control @error('no_hp') is-invalid @enderror"
                                    value="{{ old('no_hp') }}" maxlength="20" required>
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea name="alamat" id="alamat" rows="3"
                                    class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="text-muted mb-3 mt-2">Dokumen Administratif</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ktp" class="form-label">KTP <span class="text-danger">*</span></label>
                                <input type="file" name="ktp" id="ktp"
                                    class="form-control @error('ktp') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png" required>
                                @error('ktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="formulir_pendaftaran" class="form-label">Formulir Pendaftaran <span class="text-danger">*</span></label>
                                <input type="file" name="formulir_pendaftaran" id="formulir_pendaftaran"
                                    class="form-control @error('formulir_pendaftaran') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png" required>
                                @error('formulir_pendaftaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nib" class="form-label">NIB</label>
                                <input type="file" name="nib" id="nib"
                                    class="form-control @error('nib') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                @error('nib')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="npwp" class="form-label">NPWP</label>
                                <input type="file" name="npwp" id="npwp"
                                    class="form-control @error('npwp') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                @error('npwp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

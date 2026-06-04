@extends('layouts.admin')

@section('title', 'Edit Calon Agen')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Calon Agen</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.calon-agen.index') }}">Calon Agen</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Form Edit Calon Agen</h4>
                    <a href="{{ route('admin.calon-agen.show', $calonAgen) }}" class="btn btn-sm btn-light-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.calon-agen.update', $calonAgen) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <h6 class="text-muted mb-3 mt-2">Data Calon Agen</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="periode_id" class="form-label">Periode</label>
                                <select name="periode_id" id="periode_id"
                                    class="form-select @error('periode_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Periode --</option>
                                    @foreach ($periodes as $periode)
                                        <option value="{{ $periode->id }}"
                                            {{ old('periode_id', $calonAgen->periode_id) == $periode->id ? 'selected' : '' }}>
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
                                    value="{{ old('nik', $calonAgen->nik) }}" maxlength="16" required>
                                @error('nik')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" id="nama_lengkap"
                                    class="form-control @error('nama_lengkap') is-invalid @enderror"
                                    value="{{ old('nama_lengkap', $calonAgen->nama_lengkap) }}" required>
                                @error('nama_lengkap')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_hp" class="form-label">No HP</label>
                                <input type="text" name="no_hp" id="no_hp"
                                    class="form-control @error('no_hp') is-invalid @enderror"
                                    value="{{ old('no_hp', $calonAgen->no_hp) }}" maxlength="20" required>
                                @error('no_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea name="alamat" id="alamat" rows="3"
                                    class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $calonAgen->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h6 class="text-muted mb-3 mt-2">Dokumen Administratif</h6>
                        @php
                            $dokumenList = [
                                'KTP' => $calonAgen->ktp_path,
                                'NIB' => $calonAgen->nib_path,
                                'NPWP' => $calonAgen->npwp_path,
                                'Formulir Pendaftaran' => $calonAgen->formulir_pendaftaran_path,
                            ];
                        @endphp
                        <div class="list-group mb-3">
                            @foreach ($dokumenList as $label => $path)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $label }}</span>
                                    @if ($path)
                                        <a href="{{ asset('storage/' . $path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-earmark-text"></i> Lihat
                                        </a>
                                    @else
                                        <span class="badge bg-light-secondary">Belum ada</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ktp" class="form-label">
                                    Ganti KTP @if (!$calonAgen->ktp_path)<span class="text-danger">*</span>@endif
                                </label>
                                <input type="file" name="ktp" id="ktp"
                                    class="form-control @error('ktp') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png" {{ !$calonAgen->ktp_path ? 'required' : '' }}>
                                @error('ktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="formulir_pendaftaran" class="form-label">
                                    Ganti Formulir Pendaftaran @if (!$calonAgen->formulir_pendaftaran_path)<span class="text-danger">*</span>@endif
                                </label>
                                <input type="file" name="formulir_pendaftaran" id="formulir_pendaftaran"
                                    class="form-control @error('formulir_pendaftaran') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png" {{ !$calonAgen->formulir_pendaftaran_path ? 'required' : '' }}>
                                @error('formulir_pendaftaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nib" class="form-label">Ganti NIB</label>
                                <input type="file" name="nib" id="nib"
                                    class="form-control @error('nib') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                @error('nib')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="npwp" class="form-label">Ganti NPWP</label>
                                <input type="file" name="npwp" id="npwp"
                                    class="form-control @error('npwp') is-invalid @enderror"
                                    accept=".pdf,.jpg,.jpeg,.png">
                                @error('npwp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

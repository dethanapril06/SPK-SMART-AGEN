@extends('layouts.admin')

@section('title', 'Edit Kriteria')

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Kriteria</h3>
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
                            <li class="breadcrumb-item active" aria-current="page">Form Edit Kriteria</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Kriteria</h4>
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
                            action="{{ route('admin.kriteria.update', $kriteria) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">

                                    <div class="col-md-4 col-12">
                                        <div class="form-group">
                                            <label for="kode_kriteria">Kode Kriteria</label>
                                            <input type="text"
                                                class="form-control @error('kode_kriteria') is-invalid @enderror"
                                                placeholder="Contoh: C1" id="kode_kriteria" name="kode_kriteria"
                                                value="{{ old('kode_kriteria', $kriteria->kode_kriteria) }}" maxlength="10"
                                                required>
                                            @error('kode_kriteria')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-8 col-12">
                                        <div class="form-group">
                                            <label for="nama_kriteria">Nama Kriteria</label>
                                            <input type="text"
                                                class="form-control @error('nama_kriteria') is-invalid @enderror"
                                                placeholder="Contoh: Pengalaman Kerja" id="nama_kriteria"
                                                name="nama_kriteria"
                                                value="{{ old('nama_kriteria', $kriteria->nama_kriteria) }}" required>
                                            @error('nama_kriteria')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="bobot">Bobot (Skala 1-10)</label>
                                            <input type="number" class="form-control @error('bobot') is-invalid @enderror"
                                                placeholder="Contoh: 8" id="bobot" name="bobot"
                                                value="{{ old('bobot', $kriteria->bobot) }}" min="1" max="10"
                                                step="0.01" required>
                                            @error('bobot')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="tipe">Tipe Kriteria</label>
                                            <select class="form-select @error('tipe') is-invalid @enderror" id="tipe"
                                                name="tipe" required>
                                                <option value="benefit"
                                                    {{ old('tipe', $kriteria->tipe) === 'benefit' ? 'selected' : '' }}>
                                                    Benefit (semakin tinggi semakin baik)
                                                </option>
                                                <option value="cost"
                                                    {{ old('tipe', $kriteria->tipe) === 'cost' ? 'selected' : '' }}>
                                                    Cost (semakin rendah semakin baik)
                                                </option>
                                            </select>
                                            @error('tipe')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 d-flex justify-content-end">
                                        <a href="{{ route('admin.kriteria.index') }}"
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

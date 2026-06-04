@extends('layouts.admin')

@section('title', 'Detail Calon Agen')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Calon Agen</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.calon-agen.index') }}">Calon Agen</a>
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
                            <h4 class="card-title mb-0">Informasi Calon Agen</h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.calon-agen.edit', $calonAgen) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <a href="{{ route('admin.calon-agen.index') }}" class="btn btn-sm btn-light-secondary">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Nama Lengkap</th>
                                            <td>: {{ $calonAgen->nama_lengkap }}</td>
                                        </tr>
                                        <tr>
                                            <th>NIK</th>
                                            <td>: {{ $calonAgen->nik }}</td>
                                        </tr>
                                        <tr>
                                            <th>No HP</th>
                                            <td>: {{ $calonAgen->no_hp }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>: {{ $calonAgen->alamat }}</td>
                                        </tr>
                                        <tr>
                                            <th>Periode</th>
                                            <td>: {{ $calonAgen->periode->nama_periode ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Akun Email</th>
                                            <td>: {{ $calonAgen->user->email ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Daftar</th>
                                            <td>: {{ $calonAgen->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>:
                                                @php
                                                    $badge = match ($calonAgen->status) {
                                                        'direkomendasi' => 'success',
                                                        'disurvey' => 'info',
                                                        'diproses' => 'warning',
                                                        'belumdirekomendasi' => 'danger',
                                                    };
                                                @endphp
                                                <span class="badge bg-{{ $badge }}">
                                                    {{ ucfirst($calonAgen->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6 col-12">
                                    <div class="card bg-light shadow-none mb-3">
                                        <div class="card-body">
                                            <h6 class="fw-semibold mb-3">Dokumen Pendaftaran</h6>
                                            @php
                                                $dokumenList = [
                                                    'KTP' => $calonAgen->ktp_path,
                                                    'NIB' => $calonAgen->nib_path,
                                                    'NPWP' => $calonAgen->npwp_path,
                                                    'Formulir Pendaftaran' => $calonAgen->formulir_pendaftaran_path,
                                                ];
                                            @endphp
                                            <div class="list-group">
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
                                        </div>
                                    </div>

                                    {{-- Ubah Status --}}
                                    <div class="card bg-light shadow-none">
                                        <div class="card-body">
                                            <h6 class="fw-semibold mb-3">Ubah Status</h6>
                                            <form action="{{ route('admin.calon-agen.ubah-status', $calonAgen) }}"
                                                method="POST" id="form-ubah-status">
                                                @csrf
                                                @method('PATCH')
                                                <div class="form-group mb-3">
                                                    <select name="status" class="form-select" id="select-status">
                                                        <option value="diproses"
                                                            {{ $calonAgen->status === 'diproses' ? 'selected' : '' }}>
                                                            Diproses</option>
                                                        <option value="disurvey"
                                                            {{ $calonAgen->status === 'disurvey' ? 'selected' : '' }}>
                                                            Disurvey</option>
                                                        <option value="direkomendasi"
                                                            {{ $calonAgen->status === 'direkomendasi' ? 'selected' : '' }}>
                                                            Direkomendasi</option>
                                                        <option value="belumdirekomendasi"
                                                            {{ $calonAgen->status === 'belumdirekomendasi' ? 'selected' : '' }}>
                                                            Belum Direkomendasi</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-check-circle"></i> Simpan Status
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('form-ubah-status').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const status = document.getElementById('select-status').value;

                    Swal.fire({
                        title: 'Ubah status calon agen?',
                        text: `Status akan diubah menjadi "${status}".`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#435ebe',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, ubah',
                        cancelButtonText: 'Batal'
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            document.getElementById('form-ubah-status').submit();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection

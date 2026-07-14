@extends('layouts.admin')

@section('title', 'Daftar Calon Agen')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Calon Agen</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Calon Agen</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Data Calon Agen</h5>
                        <a href="{{ route('admin.calon-agen.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Calon Agen
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Info: calon agen direkomendasi tidak bisa dihapus --}}
                    @if ($calonAgens->contains('status', 'direkomendasi'))
                        <div class="alert alert-info d-flex align-items-center" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div>Calon agen yang sudah <strong>direkomendasi</strong> tidak dapat dihapus.</div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Filter --}}
                    <form method="GET" action="{{ route('admin.calon-agen.index') }}" class="row g-2 mb-4">
                        <div class="col-md-4 col-12">
                            <select name="periode_id" class="form-select">
                                <option value="">-- Semua Periode --</option>
                                @foreach ($periodes as $periode)
                                    <option value="{{ $periode->id }}"
                                        {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
                                        {{ $periode->nama_periode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-12">
                            <select name="status" class="form-select">
                                <option value="">-- Semua Status --</option>
                                <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses
                                </option>
                                <option value="disurvey" {{ request('status') === 'disurvey' ? 'selected' : '' }}>Disurvey
                                </option>
                                <option value="direkomendasi" {{ request('status') === 'direkomendasi' ? 'selected' : '' }}>Direkomendasi
                                </option>
                                <option value="belumdirekomendasi" {{ request('status') === 'belumdirekomendasi' ? 'selected' : '' }}>Belum Direkomendasi
                                </option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <a href="{{ route('admin.calon-agen.index') }}" class="btn btn-light-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Usaha</th>
                                    <th>Nama Pemilik</th>
                                    <th>NIK</th>
                                    <th>No HP</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($calonAgens as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->nama_usaha ?? '-' }}</td>
                                        <td>{{ $item->nama_lengkap }}</td>
                                        <td>{{ $item->nik }}</td>
                                        <td>{{ $item->no_hp }}</td>
                                        <td>{{ $item->periode->nama_periode ?? '-' }}</td>
                                        <td>
                                            @php
                                                $badge = match ($item->status) {
                                                    'direkomendasi' => 'success',
                                                    'disurvey' => 'info',
                                                    'diproses' => 'warning',
                                                    'belumdirekomendasi' => 'danger',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badge }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.calon-agen.show', $item) }}"
                                                    class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.calon-agen.edit', $item) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <form action="{{ route('admin.calon-agen.destroy', $item) }}"
                                                    method="POST" class="delete-calon-agen-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    @if ($item->isDirekomendasi())
                                                        <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip"
                                                            title="Calon agen yang sudah direkomendasi tidak dapat dihapus.">
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                title="Hapus" disabled style="pointer-events: none;">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </span>
                                                    @else
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    @endif
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada data calon agen.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script src="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.js') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.delete-calon-agen-form').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        Swal.fire({
                            title: 'Yakin ingin menghapus data ini?',
                            text: 'Data yang dihapus tidak bisa dikembalikan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus',
                            cancelButtonText: 'Batal'
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                // Initialize Bootstrap tooltips for disabled delete buttons
                try {
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltipTriggerList.forEach(function(el) {
                        new bootstrap.Tooltip(el);
                    });
                } catch (e) {
                    // ignore if bootstrap not available
                }
            });
        </script>
    @endpush
@endsection

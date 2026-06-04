@extends('layouts.admin')

@section('title', 'Daftar Sub Kriteria')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Sub Kriteria</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Sub Kriteria</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Sub Kriteria</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-light-success color-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-light-danger color-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Filter per Kriteria --}}
                    <form method="GET" action="{{ route('admin.sub-kriteria.all') }}" class="row g-2 mb-4">
                        <div class="col-md-4 col-12">
                            <select name="kriteria_id" class="form-select">
                                <option value="">-- Semua Kriteria --</option>
                                @foreach ($kriterias as $kriteria)
                                    <option value="{{ $kriteria->id }}"
                                        {{ request('kriteria_id') == $kriteria->id ? 'selected' : '' }}>
                                        [{{ $kriteria->kode_kriteria }}] {{ $kriteria->nama_kriteria }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <a href="{{ route('admin.sub-kriteria.all') }}" class="btn btn-light-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kriteria</th>
                                    <th>Nama Sub Kriteria</th>
                                    <th>Nilai</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subKriterias as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $item->kriteria->kode_kriteria }}</span>
                                            {{ $item->kriteria->nama_kriteria }}
                                        </td>
                                        <td>{{ $item->nama_sub }}</td>
                                        <td>{{ $item->nilai }}</td>
                                        <td>{{ $item->keterangan ?: '-' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.sub-kriteria.edit', $item) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('admin.sub-kriteria.destroy', $item) }}"
                                                    method="POST" class="delete-sub-kriteria-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data sub kriteria.</td>
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
                document.querySelectorAll('.delete-sub-kriteria-form').forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        Swal.fire({
                            title: 'Yakin ingin menghapus sub kriteria ini?',
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
            });
        </script>
    @endpush
@endsection

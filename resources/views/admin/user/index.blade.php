@extends('layouts.admin')

@section('title', 'Kelola Akun')

@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/extensions/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Kelola Akun</h3>
                    <a href="{{ route('admin.user.create') }}" class="btn btn-sm btn-primary mb-2">
                        <i class="bi bi-plus-lg"></i> Tambah Akun
                    </a>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Kelola Akun</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Akun</h5>
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

                    {{-- Filter & Search --}}
                    <form method="GET" action="{{ route('admin.user.index') }}" class="row g-2 mb-4">
                        <div class="col-md-3 col-12">
                            <select name="role" class="form-select">
                                <option value="">-- Semua Role --</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="calon_agen" {{ request('role') === 'calon_agen' ? 'selected' : '' }}>Calon
                                    Agen</option>
                            </select>
                        </div>
                        <div class="col-md-4 col-12">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Cari
                            </button>
                            <a href="{{ route('admin.user.index') }}" class="btn btn-light-secondary">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset
                            </a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped" id="table1">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Dibuat Pada</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $item->name }}
                                            @if ($item->id === 1)
                                                <span class="badge bg-dark ms-1">Default</span>
                                            @endif
                                            @if ($item->id === auth()->id())
                                                <span class="badge bg-info ms-1">Anda</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $item->role === 'admin' ? 'primary' : 'secondary' }}">
                                                {{ $item->role === 'admin' ? 'Admin' : 'Calon Agen' }}
                                            </span>
                                        </td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.user.edit', $item) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                {{-- Reset Password --}}
                                                <form action="{{ route('admin.user.reset-password', $item) }}"
                                                    method="POST" class="reset-password-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-info"
                                                        title="Reset Password" {{ $item->id === 1 ? 'disabled' : '' }}>
                                                        <i class="bi bi-key"></i>
                                                    </button>
                                                </form>

                                                {{-- Hapus --}}
                                                <form action="{{ route('admin.user.destroy', $item) }}" method="POST"
                                                    class="delete-user-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Hapus"
                                                        {{ $item->id === 1 || $item->id === auth()->id() ? 'disabled' : '' }}>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data akun.</td>
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

                document.querySelectorAll('.reset-password-form').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Reset password akun ini?',
                            text: 'Password akan direset ke "password" (default).',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#0dcaf0',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, reset',
                            cancelButtonText: 'Batal'
                        }).then(function(result) {
                            if (result.isConfirmed) form.submit();
                        });
                    });
                });

                document.querySelectorAll('.delete-user-form').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Yakin ingin menghapus akun ini?',
                            text: 'Data yang dihapus tidak bisa dikembalikan.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, hapus',
                            cancelButtonText: 'Batal'
                        }).then(function(result) {
                            if (result.isConfirmed) form.submit();
                        });
                    });
                });

            });
        </script>
    @endpush
@endsection

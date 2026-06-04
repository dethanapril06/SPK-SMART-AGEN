@extends('layouts.admin')

@section('title', 'Penilaian Survey')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Penilaian Survey</h3>
                    <p class="text-subtitle text-muted">Pilih periode untuk melihat daftar calon agen yang akan dinilai.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Penilaian</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="page-content">
            <div class="row">
                @forelse ($periodes as $periode)
                    <div class="col-md-4 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-lg bg-primary-light rounded me-3">
                                        <i class="bi bi-calendar-range-fill text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $periode->nama_periode }}</h6>
                                        <small class="text-muted">
                                            {{ $periode->tanggal_buka->format('d M Y') }} &mdash;
                                            {{ $periode->tanggal_tutup->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small">Calon Agen</span>
                                    <span class="badge bg-primary">{{ $periode->calon_agen_count }} orang</span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small">Status</span>
                                    @php
                                        $badgeClass = match ($periode->status) {
                                            'aktif' => 'bg-success',
                                            'draft' => 'bg-secondary',
                                            'ditutup' => 'bg-danger',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($periode->status) }}</span>
                                </div>

                                <a href="{{ route('admin.penilaian.calon-agen', $periode) }}"
                                    class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-arrow-right-circle me-1"></i> Lihat Calon Agen
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Belum ada periode pendaftaran. Silakan buat periode terlebih dahulu.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

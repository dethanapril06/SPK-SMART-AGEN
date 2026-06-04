@extends('layouts.admin')

@section('title', 'Perhitungan SMART')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Perhitungan SMART</h3>
                    <p class="text-subtitle text-muted">Pilih periode untuk menjalankan perhitungan metode SMART.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Perhitungan SMART</li>
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
                                        <i class="bi bi-calculator-fill text-primary fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $periode->nama_periode }}</h6>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($periode->tanggal_buka)->format('d M Y') }} &mdash;
                                            {{ \Carbon\Carbon::parse($periode->tanggal_tutup)->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Calon Agen</span>
                                    <span class="badge bg-primary">{{ $periode->calon_agen_count }} orang</span>
                                </div>

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small">Status Perhitungan</span>
                                    @if ($periode->hasil_smart_count > 0)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Sudah Dihitung
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock me-1"></i>Belum Dihitung
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('admin.smart.show', $periode) }}"
                                    class="btn btn-sm w-100 {{ $periode->hasil_smart_count > 0 ? 'btn-outline-primary' : 'btn-primary' }}">
                                    <i class="bi bi-{{ $periode->hasil_smart_count > 0 ? 'eye' : 'play-circle' }} me-1"></i>
                                    {{ $periode->hasil_smart_count > 0 ? 'Lihat Hasil' : 'Hitung Sekarang' }}
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Belum ada periode pendaftaran.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

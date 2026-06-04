@extends('layouts.calon-agen')

@section('title', 'Notifikasi')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Notifikasi</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('calon-agen.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Notifikasi</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Semua Notifikasi</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($notifikasis as $notif)
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3 px-4">
                                <div class="d-flex align-items-start">
                                    <div class="notification-icon bg-{{ match ($notif->tipe) {'direkomendasi' => 'success','belumdirekomendasi' => 'danger',default => 'primary'} }} me-3"
                                        style="width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        <i
                                            class="bi bi-{{ match ($notif->tipe) {'direkomendasi' => 'patch-check','belumdirekomendasi' => 'x-circle',default => 'info-circle'} }} text-white"></i>
                                    </div>
                                    <div>
                                        <p class="fw-semibold mb-1">{{ $notif->judul }}</p>
                                        <p class="text-muted mb-1">{{ $notif->pesan }}</p>
                                        <small class="text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                <div class="text-end text-nowrap ms-3">
                                    <span
                                        class="badge bg-{{ match ($notif->tipe) {'direkomendasi' => 'success','belumdirekomendasi' => 'danger',default => 'info'} }}">
                                        {{ ucfirst($notif->tipe) }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-5">
                                <i class="bi bi-bell-slash fs-1 d-block mb-2"></i>
                                Belum ada notifikasi.
                            </li>
                        @endforelse
                    </ul>
                </div>
                @if ($notifikasis->hasPages())
                    <div class="card-footer">
                        {{ $notifikasis->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Penilaian - ' . $calonAgen->nama_lengkap)

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Form Penilaian</h3>
                    <p class="text-subtitle text-muted">{{ $calonAgen->nama_lengkap }} &mdash; {{ $periode->nama_periode }}
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.penilaian.index') }}">Penilaian</a></li>
                            <li class="breadcrumb-item">
                                <a
                                    href="{{ route('admin.penilaian.calon-agen', $periode) }}">{{ $periode->nama_periode }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $calonAgen->nama_lengkap }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="page-content">
            <div class="row">

                {{-- Info Calon Agen --}}
                <div class="col-12 mb-3">
                    <div class="card border-0 bg-primary text-white">
                        <div class="card-body py-3">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar avatar-lg">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($calonAgen->nama_lengkap) }}&background=ffffff&color=435ebe&size=64&bold=true&font-size=0.4"
                                            alt="{{ $calonAgen->nama_lengkap }}" class="rounded-circle">
                                    </div>
                                </div>
                                <div class="col">
                                    <h5 class="mb-0 text-white">{{ $calonAgen->nama_lengkap }}</h5>
                                    <small class="opacity-75">NIK: {{ $calonAgen->nik }} &nbsp;|&nbsp;
                                        {{ $calonAgen->no_hp }}</small>
                                </div>
                                <div class="col-auto">
                                    <span class="badge bg-white text-primary fw-semibold">
                                        {{ $kriterias->count() }} Kriteria
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Error validasi --}}
                @if ($errors->any())
                    <div class="col-12 mb-3">
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Penilaian belum lengkap!</strong> Semua kriteria wajib dinilai.
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- Form Penilaian --}}
                <div class="col-12">
                    <form action="{{ route('admin.penilaian.store', [$periode, $calonAgen]) }}" method="POST">
                        @csrf

                        @foreach ($kriterias as $index => $kriteria)
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-pill">{{ $kriteria->kode_kriteria }}</span>
                                        <h6 class="mb-0">{{ $kriteria->nama_kriteria }}</h6>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span
                                            class="badge {{ $kriteria->tipe === 'benefit' ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }} border">
                                            {{ ucfirst($kriteria->tipe) }}
                                        </span>
                                        <span class="badge bg-light text-dark border">Bobot: {{ $kriteria->bobot }}</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if ($errors->has("penilaian.{$kriteria->id}"))
                                        <div class="alert alert-danger py-2 mb-3">
                                            <i class="bi bi-exclamation-circle me-1"></i>
                                            Kriteria ini wajib dipilih.
                                        </div>
                                    @endif

                                    <div class="row g-2">
                                        @foreach ($kriteria->subKriteria->sortBy('nilai') as $sub)
                                            @php
                                                $isSelected =
                                                    isset($existingPenilaian[$kriteria->id]) &&
                                                    $existingPenilaian[$kriteria->id] == $sub->id;
                                                $isOldSelected = old("penilaian.{$kriteria->id}") == $sub->id;
                                                $checked = $isOldSelected || (!old('penilaian') && $isSelected);
                                            @endphp
                                            <div class="col-12">
                                                <label class="penilaian-option w-100 {{ $checked ? 'selected' : '' }}"
                                                    for="sub_{{ $kriteria->id }}_{{ $sub->id }}">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <input type="radio" class="form-check-input mt-0 flex-shrink-0"
                                                            name="penilaian[{{ $kriteria->id }}]"
                                                            id="sub_{{ $kriteria->id }}_{{ $sub->id }}"
                                                            value="{{ $sub->id }}" {{ $checked ? 'checked' : '' }}>
                                                        <div class="flex-grow-1">
                                                            <span class="fw-medium">{{ $sub->nama_sub }}</span>
                                                            @if ($sub->keterangan)
                                                                <small
                                                                    class="d-block text-muted">{{ $sub->keterangan }}</small>
                                                            @endif
                                                        </div>
                                                        <span
                                                            class="badge bg-primary-light text-primary border fw-bold flex-shrink-0">
                                                            Nilai: {{ $sub->nilai }}
                                                        </span>
                                                    </div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Tombol aksi --}}
                        <div class="d-flex justify-content-between mt-2 mb-5">
                            <a href="{{ route('admin.penilaian.calon-agen', $periode) }}"
                                class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan Penilaian
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .penilaian-option {
            display: block;
            padding: 12px 16px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            background: #fff;
            margin-bottom: 0;
        }

        .penilaian-option:hover {
            border-color: #435ebe;
            background: #f0f3ff;
        }

        .penilaian-option.selected {
            border-color: #435ebe;
            background: #eef1fd;
        }

        [data-bs-theme="dark"] .penilaian-option {
            background: #2b2b40;
            border-color: #3d3d5c;
            color: #e0e0e0;
        }

        [data-bs-theme="dark"] .penilaian-option:hover,
        [data-bs-theme="dark"] .penilaian-option.selected {
            border-color: #435ebe;
            background: #313158;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Highlight pilihan yang aktif
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    // Reset semua option dalam group yang sama
                    const name = this.getAttribute('name');
                    document.querySelectorAll(`input[name="${name}"]`).forEach(function(r) {
                        r.closest('.penilaian-option').classList.remove('selected');
                    });
                    // Tandai yang dipilih
                    this.closest('.penilaian-option').classList.add('selected');
                });
            });
        });
    </script>
@endpush

@extends('layouts.admin')

@section('title', 'Langkah SMART - ' . $periode->nama_periode)

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Langkah Perhitungan SMART</h3>
                    <p class="text-subtitle text-muted">{{ $periode->nama_periode }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.smart.index') }}">SMART</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.smart.show', $periode) }}">{{ $periode->nama_periode }}</a></li>
                            <li class="breadcrumb-item active">Langkah SMART</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="page-content">

            @php
                $kriterias = $langkah['kriterias'];
                $calonAgens = $langkah['calon_agens'];
                $totalBobot = $kriterias->sum('bobot');
                $bobotNormal = $kriterias->mapWithKeys(fn($k) => [$k->id => round($k->bobot / $totalBobot, 4)]);
            @endphp

            {{-- ================================================================ --}}
            {{-- LANGKAH 1: Bobot Kriteria                                        --}}
            {{-- ================================================================ --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                    <span class="badge bg-white text-primary fw-bold fs-6">1</span>
                    <h5 class="mb-0 text-white">Bobot Kriteria & Normalisasi Bobot</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Normalisasi bobot dihitung dengan membagi bobot tiap kriteria dengan total bobot seluruh kriteria.
                        <br><strong>Rumus:</strong> W<sub>j</sub> = Bobot<sub>j</sub> / &Sigma;Bobot
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Kriteria</th>
                                    <th>Tipe</th>
                                    <th>Bobot (1-10)</th>
                                    <th>Total Bobot</th>
                                    <th>Bobot Normal (W<sub>j</sub>)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($kriterias as $k)
                                    <tr>
                                        <td class="text-start">
                                            <span class="badge bg-primary me-1">{{ $k->kode_kriteria }}</span>
                                            {{ $k->nama_kriteria }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $k->tipe === 'benefit' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($k->tipe) }}
                                            </span>
                                        </td>
                                        <td>{{ $k->bobot }}</td>
                                        <td>{{ $totalBobot }}</td>
                                        <td><strong>{{ $bobotNormal[$k->id] }}</strong></td>
                                    </tr>
                                @endforeach
                                <tr class="table-light fw-bold">
                                    <td class="text-end" colspan="2">Total</td>
                                    <td>{{ $totalBobot }}</td>
                                    <td>-</td>
                                    <td>{{ $bobotNormal->sum() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================================================================ --}}
            {{-- LANGKAH 2: Matriks Nilai Asli                                   --}}
            {{-- ================================================================ --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                    <span class="badge bg-white text-primary fw-bold fs-6">2</span>
                    <h5 class="mb-0 text-white">Matriks Nilai Alternatif</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Nilai mentah hasil penilaian survey untuk setiap calon agen pada setiap kriteria.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Calon Agen</th>
                                    @foreach ($kriterias as $k)
                                        <th>{{ $k->kode_kriteria }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($calonAgens as $ca)
                                    <tr>
                                        <td class="text-start fw-medium">{{ $ca['nama'] }}</td>
                                        @foreach ($kriterias as $k)
                                            <td>{{ $ca['nilai_asli'][$k->id] ?? '-' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                <tr class="table-warning fw-bold">
                                    <td class="text-start">Nilai Maks</td>
                                    @foreach ($kriterias as $k)
                                        <td>{{ $langkah['nilai_maks'][$k->id] }}</td>
                                    @endforeach
                                </tr>
                                <tr class="table-info fw-bold">
                                    <td class="text-start">Nilai Min</td>
                                    @foreach ($kriterias as $k)
                                        <td>{{ $langkah['nilai_min'][$k->id] }}</td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================================================================ --}}
            {{-- LANGKAH 3: Normalisasi Nilai                                     --}}
            {{-- ================================================================ --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                    <span class="badge bg-white text-primary fw-bold fs-6">3</span>
                    <h5 class="mb-0 text-white">Nilai Utility</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Normalisasi nilai dilakukan berdasarkan tipe kriteria:<br>
                        &bull; <strong>Benefit</strong>: R<sub>ij</sub> = X<sub>ij</sub> / X<sub>maks</sub><br>
                        &bull; <strong>Cost</strong>: R<sub>ij</sub> = X<sub>min</sub> / X<sub>ij</sub>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Calon Agen</th>
                                    @foreach ($kriterias as $k)
                                        <th>
                                            {{ $k->kode_kriteria }}
                                            <br><small class="text-muted">({{ ucfirst($k->tipe) }})</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($calonAgens as $ca)
                                    <tr>
                                        <td class="text-start fw-medium">{{ $ca['nama'] }}</td>
                                        @foreach ($kriterias as $k)
                                            <td>{{ number_format($ca['nilai_normal'][$k->id] ?? 0, 4) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================================================================ --}}
            {{-- LANGKAH 4: Pembobotan (Wj x Rij)                                --}}
            {{-- ================================================================ --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                    <span class="badge bg-white text-primary fw-bold fs-6">4</span>
                    <h5 class="mb-0 text-white">Nilai Akhir</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Setiap nilai ternormalisasi dikalikan dengan bobot normal masing-masing kriteria.
                        <br><strong>Rumus:</strong> V<sub>ij</sub> = W<sub>j</sub> &times; R<sub>ij</sub>
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Calon Agen</th>
                                    @foreach ($kriterias as $k)
                                        <th>
                                            {{ $k->kode_kriteria }}
                                            <br><small class="text-muted">W={{ $bobotNormal[$k->id] }}</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($calonAgens as $ca)
                                    <tr>
                                        <td class="text-start fw-medium">{{ $ca['nama'] }}</td>
                                        @foreach ($kriterias as $k)
                                            @php
                                                $vij = round(
                                                    $bobotNormal[$k->id] * ($ca['nilai_normal'][$k->id] ?? 0),
                                                    6,
                                                );
                                            @endphp
                                            <td>{{ number_format($vij, 4) }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ================================================================ --}}
            {{-- LANGKAH 5: Skor Akhir & Peringkat                               --}}
            {{-- ================================================================ --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white d-flex align-items-center gap-2">
                    <span class="badge bg-white text-success fw-bold fs-6">5</span>
                    <h5 class="mb-0 text-white">Pemeringkatan</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Skor akhir diperoleh dengan menjumlahkan seluruh hasil pembobotan tiap kriteria.
                        <br><strong>Rumus:</strong> V<sub>i</sub> = &Sigma; (W<sub>j</sub> &times; R<sub>ij</sub>)
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Peringkat</th>
                                    <th class="text-start">Calon Agen</th>
                                    @foreach ($kriterias as $k)
                                        <th>{{ $k->kode_kriteria }}</th>
                                    @endforeach
                                    <th>Skor Akhir (V<sub>i</sub>)</th>
                                    <th>Keputusan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($calonAgens as $ca)
                                    <tr class="{{ $ca['keputusan'] === 'direkomendasi' ? 'table-success' : '' }}">
                                        <td>
                                            @if ($ca['peringkat'] <= 3)
                                                <span
                                                    class="badge bg-{{ ['warning', 'secondary', 'danger'][$ca['peringkat'] - 1] }} rounded-pill">
                                                    #{{ $ca['peringkat'] }}
                                                </span>
                                            @else
                                                <span class="text-muted">#{{ $ca['peringkat'] }}</span>
                                            @endif
                                        </td>
                                        <td class="text-start fw-medium">{{ $ca['nama'] }}</td>
                                        @foreach ($kriterias as $k)
                                            @php $vij = round($bobotNormal[$k->id] * ($ca['nilai_normal'][$k->id] ?? 0), 6) @endphp
                                            <td>{{ number_format($vij, 4) }}</td>
                                        @endforeach
                                        <td><strong class="text-primary">{{ number_format($ca['skor_akhir'], 4) }}</strong>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $ca['keputusan'] === 'direkomendasi' ? 'bg-success' : 'bg-danger' }}">
                                                <i
                                                    class="bi bi-{{ $ca['keputusan'] === 'direkomendasi' ? 'check-circle' : 'x-circle' }} me-1"></i>
                                                {{ ucfirst($ca['keputusan']) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-start mb-5">
                <a href="{{ route('admin.smart.show', $periode) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Hasil
                </a>
            </div>

        </div>
    </div>
@endsection

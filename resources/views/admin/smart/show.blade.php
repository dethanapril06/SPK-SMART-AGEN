@extends('layouts.admin')

@section('title', 'Hasil SMART - ' . $periode->nama_periode)

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Perhitungan SMART</h3>
                    <p class="text-subtitle text-muted">{{ $periode->nama_periode }}</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.smart.index') }}">SMART</a></li>
                            <li class="breadcrumb-item active">{{ $periode->nama_periode }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="page-content">

            {{-- Form Hitung --}}
            <div class="row mb-4">
                <div class="col-md-5">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-calculator me-2"></i>
                                {{ $sudahDihitung ? 'Hitung Ulang SMART' : 'Jalankan Perhitungan SMART' }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @if ($sudahDihitung)
                                <div class="alert alert-warning py-2 my-3">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Perhitungan sebelumnya akan di-<strong>overwrite</strong> dan notifikasi akan dikirim
                                    ulang.
                                </div>
                            @endif

                            <form action="{{ route('admin.smart.hitung', $periode) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="top_n" class="form-label fw-semibold">
                                        Jumlah Kuota Calon Agen Diterima Pada Periode Ini ({{ $periode->nama_periode }})
                                    </label>
                                    <input type="number" class="form-control @error('top_n') is-invalid @enderror"
                                        id="top_n" name="top_n" value="{{ old('top_n', 3) }}" min="1"
                                        placeholder="Contoh: 3">
                                    <div class="form-text">Calon agen dengan peringkat 1 s/d N akan dinyatakan
                                        direkomendasi.
                                    </div>
                                    @error('top_n')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-play-circle me-1"></i>
                                    {{ $sudahDihitung ? 'Hitung Ulang' : 'Hitung Sekarang' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabel Hasil --}}
            @if ($sudahDihitung && !empty($detail))
                @php
                    $kriterias = $detail['kriterias'];
                    $hasil = $detail['hasil'];
                    $penilaian = $detail['penilaian'];
                @endphp

                {{-- Ringkasan --}}
                <div class="row mb-3">
                    @foreach ([['label' => 'Total Peserta', 'value' => $hasil->count(), 'icon' => 'people-fill', 'color' => 'primary'], ['label' => 'Direkomendasi', 'value' => $hasil->where('keputusan', 'direkomendasi')->count(), 'icon' => 'check-circle-fill', 'color' => 'success'], ['label' => 'Belum Direkomendasi', 'value' => $hasil->where('keputusan', 'belumdirekomendasi')->count(), 'icon' => 'x-circle-fill', 'color' => 'danger'], ['label' => 'Skor Tertinggi', 'value' => number_format($hasil->first()->skor_akhir, 4), 'icon' => 'trophy-fill', 'color' => 'warning']] as $stat)
                        <div class="col-md-3 col-sm-6">
                            <div class="card">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="avatar bg-{{ $stat['color'] }}-light rounded">
                                        <i class="bi bi-{{ $stat['icon'] }} text-{{ $stat['color'] }} fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold fs-5">{{ $stat['value'] }}</div>
                                        <div class="text-muted small">{{ $stat['label'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Tabel Ranking --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>Hasil Peringkat SMART
                        </h5>
                        <a href="{{ route('admin.smart.langkah', $periode) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-list-ol me-1"></i> Lihat Langkah SMART
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="table-hasil-smart">
                                <thead>
                                    <tr>
                                        <th>Peringkat</th>
                                        <th>Nama Usaha</th>
                                        @foreach ($kriterias as $k)
                                            <th class="text-center">
                                                {{ $k->kode_kriteria }}
                                                <br>
                                                <small class="text-muted fw-normal">{{ $k->bobot }}</small>
                                            </th>
                                        @endforeach
                                        <th class="text-center">Skor Akhir</th>
                                        <th class="text-center">Keputusan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($hasil as $h)
                                        <tr class="{{ $h->keputusan === 'direkomendasi' ? 'table-success' : '' }}">
                                            <td>
                                                @if ($h->peringkat <= 3)
                                                    <span
                                                        class="badge bg-{{ ['warning', 'secondary', 'danger'][$h->peringkat - 1] }} rounded-pill">
                                                        #{{ $h->peringkat }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">#{{ $h->peringkat }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $h->calonAgen->nama_usaha ?? '-' }}</td>
                                            @foreach ($kriterias as $k)
                                                @php
                                                    $nilaiCa =
                                                        $penilaian[$h->calon_agen_id]?->firstWhere(
                                                            'kriteria_id',
                                                            $k->id,
                                                        )?->nilai_input ?? '-';
                                                @endphp
                                                <td class="text-center">{{ $nilaiCa }}</td>
                                            @endforeach
                                            <td class="text-center">
                                                <strong
                                                    class="text-primary">{{ number_format($h->skor_akhir, 4) }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $h->keputusan === 'direkomendasi' ? 'bg-success' : 'bg-danger' }}">
                                                    <i
                                                        class="bi bi-{{ $h->keputusan === 'direkomendasi' ? 'check-circle' : 'x-circle' }} me-1"></i>
                                                    {{ ucfirst($h->keputusan) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        Dihitung pada: {{ $hasil->first()->dihitung_at->format('d M Y, H:i') }} WIB
                    </div>
                </div>

                {{-- Info Kriteria --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Kriteria
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Kriteria</th>
                                        <th class="text-center">Tipe</th>
                                        <th class="text-center">Bobot (1-5)</th>
                                        <th class="text-center">Bobot Normal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalBobot = $kriterias->sum('bobot') @endphp
                                    @foreach ($kriterias as $k)
                                        <tr>
                                            <td><span class="badge bg-primary">{{ $k->kode_kriteria }}</span></td>
                                            <td>{{ $k->nama_kriteria }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $k->tipe === 'benefit' ? 'bg-success' : 'bg-danger' }}-light text-{{ $k->tipe === 'benefit' ? 'success' : 'danger' }} border">
                                                    {{ ucfirst($k->tipe) }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $k->bobot }}</td>
                                            <td class="text-center">{{ number_format($k->bobot / $totalBobot, 4) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="fw-bold">
                                        <td colspan="3" class="text-end">Total</td>
                                        <td class="text-center">{{ $kriterias->sum('bobot') }}</td>
                                        <td class="text-center">1.0000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection

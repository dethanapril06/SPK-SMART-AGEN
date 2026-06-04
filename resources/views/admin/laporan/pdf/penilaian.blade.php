@extends('admin.laporan.pdf.layout')
@section('content')
    @php $title = 'Laporan Penilaian Per Kriteria'; @endphp

    <div class="info-box">
        Periode: <span>{{ $periode->nama_periode }}</span>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Total Calon Agen Dinilai: <span>{{ $penilaians->count() }}</span>
    </div>

    @forelse ($penilaians as $calonAgenId => $nilais)
        @php $calon = $nilais->first()->calonAgen; @endphp
        <p style="font-weight:bold; margin: 10px 0 4px;">
            {{ $calon->nama_lengkap }} — <span style="color:#666; font-weight:normal;">NIK: {{ $calon->nik }}</span>
        </p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Kriteria</th>
                    <th>Sub Kriteria</th>
                    <th class="text-center">Nilai Input</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($nilais as $j => $item)
                    <tr>
                        <td>{{ $j + 1 }}</td>
                        <td><span class="badge badge-secondary">{{ $item->kriteria->kode_kriteria ?? '-' }}</span></td>
                        <td>{{ $item->kriteria->nama_kriteria ?? '-' }}</td>
                        <td>{{ $item->subKriteria->nama_sub ?? '-' }}</td>
                        <td class="text-center">{{ $item->nilai_input }}</td>
                        <td>{{ $item->catatan ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    @empty
        <p class="text-center">Tidak ada data penilaian.</p>
    @endforelse
@endsection

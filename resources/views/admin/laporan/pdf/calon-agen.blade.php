@extends('admin.laporan.pdf.layout')
@section('content')
    @php $title = 'Laporan Calon Agen'; @endphp

    <div class="info-box">
        Periode: <span>{{ $periode->nama_periode ?? 'Semua Periode' }}</span>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Status: <span>{{ $request->status ? ucfirst($request->status) : 'Semua Status' }}</span>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Total Data: <span>{{ $calonAgens->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pemilik</th>
                <th>NIK</th>
                <th>No HP</th>
                <th>Alamat</th>
                <th>Periode</th>
                <th>Status</th>
                <th>Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($calonAgens as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->nama_lengkap }}</td>
                    <td>{{ $item->nik }}</td>
                    <td>{{ $item->no_hp }}</td>
                    <td>{{ $item->alamat }}</td>
                    <td>{{ $item->periode->nama_periode ?? '-' }}</td>
                    <td>
                        @php $badgeMap = ['direkomendasi' => 'success', 'belumdirekomendasi' => 'danger', 'disurvey' => 'info', 'diproses' => 'warning']; @endphp
                        <span class="badge badge-{{ $badgeMap[$item->status] ?? 'secondary' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection

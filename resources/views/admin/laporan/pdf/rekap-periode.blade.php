@extends('admin.laporan.pdf.layout')
@section('content')
    @php $title = 'Laporan Rekap Periode Pendaftaran'; @endphp

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Periode</th>
                <th>Tanggal Buka</th>
                <th>Tanggal Tutup</th>
                <th>Status</th>
                <th class="text-center">Total Pendaftar</th>
                <th class="text-center">Diproses</th>
                <th class="text-center">Disurvey</th>
                <th class="text-center">Direkomendasi</th>
                <th class="text-center">Belum Direkomendasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($periodes as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->nama_periode }}</td>
                    <td>{{ $item->tanggal_buka->format('d/m/Y') }}</td>
                    <td>{{ $item->tanggal_tutup->format('d/m/Y') }}</td>
                    <td>
                        @php $badgeMap = ['aktif' => 'success', 'draft' => 'warning', 'ditutup' => 'danger']; @endphp
                        <span class="badge badge-{{ $badgeMap[$item->status] ?? 'secondary' }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td class="text-center">{{ $item->calon_agen_count }}</td>
                    <td class="text-center">{{ $item->total_diproses }}</td>
                    <td class="text-center">{{ $item->total_disurvey }}</td>
                    <td class="text-center">{{ $item->total_direkomendasi }}</td>
                    <td class="text-center">{{ $item->total_belumdirekomendasi }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection

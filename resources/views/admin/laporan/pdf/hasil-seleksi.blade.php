@extends('admin.laporan.pdf.layout')
@section('content')
    @php $title = 'Laporan Hasil Seleksi SMART'; @endphp

    <div class="info-box">
        Periode: <span>{{ $periode->nama_periode }}</span>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Tanggal: <span>{{ $periode->tanggal_buka->format('d/m/Y') }} – {{ $periode->tanggal_tutup->format('d/m/Y') }}</span>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Total Peserta: <span>{{ $hasilSmarts->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">Peringkat</th>
                <th>Nama Lengkap</th>
                <th>NIK</th>
                <th class="text-center">Skor Akhir</th>
                <th class="text-center">Keputusan</th>
                <th>Dihitung Pada</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hasilSmarts as $item)
                <tr>
                    <td class="text-center">
                        <span class="badge badge-info">#{{ $item->peringkat }}</span>
                    </td>
                    <td>{{ $item->calonAgen->nama_lengkap ?? '-' }}</td>
                    <td>{{ $item->calonAgen->nik ?? '-' }}</td>
                    <td class="text-center"><strong>{{ number_format($item->skor_akhir, 4) }}</strong></td>
                    <td class="text-center">
                        <span class="badge badge-{{ $item->keputusan === 'direkomendasi' ? 'success' : 'danger' }}">
                            {{ ucfirst($item->keputusan) }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->dihitung_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data hasil seleksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan --}}
    @if ($hasilSmarts->isNotEmpty())
        <div
            style="margin-top: 15px; padding: 8px 12px; background: #f8f9ff; border: 1px solid #e2e8f0; border-radius: 4px; font-size: 10px;">
            <strong>Ringkasan:</strong>
            &nbsp; Direkomendasi: <strong
                style="color: #065f46;">{{ $hasilSmarts->where('keputusan', 'direkomendasi')->count() }}</strong>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            Belum Direkomendasi: <strong style="color: #991b1b;">{{ $hasilSmarts->where('keputusan', 'belumdirekomendasi')->count() }}</strong>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            Skor Tertinggi: <strong
                style="color: #435ebe;">{{ number_format($hasilSmarts->max('skor_akhir'), 4) }}</strong>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            Skor Terendah: <strong style="color: #435ebe;">{{ number_format($hasilSmarts->min('skor_akhir'), 4) }}</strong>
        </div>
    @endif
@endsection

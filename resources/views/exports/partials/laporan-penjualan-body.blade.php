{{-- Ringkasan --}}
<div class="summary-section">
    <div class="section-title">Ringkasan Penjualan</div>
    <div class="cards-grid">
        <div class="card">
            <div class="card-label">Total Unit Terjual</div>
            <div class="card-value">{{ number_format($laporan['ringkasan']['total_unit_terjual']) }}</div>
        </div>
        <div class="card">
            <div class="card-label">Total Nilai Penjualan</div>
            <div class="card-value green">Rp {{ number_format($laporan['ringkasan']['total_nilai_penjualan'], 0, ',', '.') }}
            </div>
        </div>
        <div class="card">
            <div class="card-label">Total Booking</div>
            <div class="card-value orange">{{ number_format($laporan['ringkasan']['total_booking']) }}</div>
        </div>
        <div class="card">
            <div class="card-label">Rata-rata Harga</div>
            <div class="card-value purple">Rp {{ number_format($laporan['ringkasan']['rata_rata_harga'], 0, ',', '.') }}</div>
        </div>
    </div>
</div>

{{-- Detail Transaksi --}}
<div class="table-section">
    <div class="section-title">Detail Transaksi Penjualan</div>
    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Konsumen</th>
                <th>Unit</th>
                <th>Kategori</th>
                <th class="text-right">Nilai</th>
                <th>Marketing</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan['data'] as $i => $row)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="nowrap">{{ \Carbon\Carbon::parse($row->tanggal_perubahan)->format('d/m/Y') }}</td>
                    <td>{{ $row->nama_konsumen }}</td>
                    <td>{{ $row->kode_unit }}</td>
                    <td>
                        <span class="badge {{ $row->kategori === 'subsidi' ? 'badge-subsidi' : 'badge-non-subsidi' }}">
                            {{ $row->kategori === 'subsidi' ? 'Subsidi' : 'Non-Subsidi' }}
                        </span>
                    </td>
                    <td class="text-right nowrap">Rp {{ number_format((float) $row->harga_jual, 0, ',', '.') }}</td>
                    <td>{{ $row->nama_marketing }}</td>
                    <td>
                        <span class="badge {{ $row->status_saat_ini === 'akad' ? 'badge-akad' : 'badge-serah-terima' }}">
                            {{ strtoupper(str_replace('_', ' ', $row->status_saat_ini)) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 16px; color: #6b7280;">
                        Tidak ada data penjualan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

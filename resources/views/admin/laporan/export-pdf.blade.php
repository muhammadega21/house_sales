<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan – Admin</title>
    <style>
        /* ================================================================
           RESET & BASE
        ================================================================ */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.5;
        }

        /* ================================================================
           HEADER / KOP SURAT
        ================================================================ */
        .header {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            color: #ffffff;
            padding: 20px 24px 18px;
            border-radius: 0 0 8px 8px;
            margin-bottom: 18px;
        }

        .header-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .company-name {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .company-subtitle {
            font-size: 9px;
            opacity: 0.85;
            margin-top: 2px;
        }

        .report-badge {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.35);
            border-radius: 4px;
            padding: 6px 12px;
            text-align: right;
        }

        .report-badge .title {
            font-size: 11px;
            font-weight: 700;
        }

        .report-badge .subtitle {
            font-size: 8px;
            opacity: 0.85;
        }

        .header-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.3);
            margin: 12px 0 10px;
        }

        .header-meta {
            display: flex;
            gap: 24px;
            font-size: 8.5px;
            opacity: 0.9;
        }

        .header-meta span strong {
            opacity: 1;
        }

        /* ================================================================
           SUMMARY CARDS
        ================================================================ */
        .summary-section {
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 700;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-left: 3px solid #1d4ed8;
            padding-left: 8px;
            margin-bottom: 10px;
        }

        .cards-grid {
            display: flex;
            gap: 10px;
        }

        .card {
            flex: 1;
            background: #f0f4ff;
            border: 1px solid #c7d2fe;
            border-radius: 6px;
            padding: 10px 12px;
        }

        .card-label {
            font-size: 7.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #4b5563;
            margin-bottom: 4px;
        }

        .card-value {
            font-size: 14px;
            font-weight: 700;
            color: #1d4ed8;
        }

        .card-value.green { color: #15803d; }
        .card-value.orange { color: #b45309; }
        .card-value.purple { color: #7c3aed; }

        /* ================================================================
           KATEGORI BREAKDOWN
        ================================================================ */
        .breakdown-grid {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
        }

        .breakdown-card {
            flex: 1;
            border-radius: 6px;
            padding: 12px;
            border: 1px solid;
        }

        .breakdown-card.subsidi {
            background: #f0fdf4;
            border-color: #86efac;
        }

        .breakdown-card.non-subsidi {
            background: #fff7ed;
            border-color: #fed7aa;
        }

        .breakdown-card .label {
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 6px;
        }

        .breakdown-card.subsidi .label { color: #15803d; }
        .breakdown-card.non-subsidi .label { color: #b45309; }

        .breakdown-row {
            display: flex;
            justify-content: space-between;
            font-size: 8.5px;
            margin-bottom: 2px;
        }

        .breakdown-row span:last-child {
            font-weight: 600;
        }

        /* ================================================================
           TABLE
        ================================================================ */
        .table-section {
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }

        thead tr {
            background: #1d4ed8;
            color: #ffffff;
        }

        thead th {
            padding: 7px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        thead th.text-right { text-align: right; }
        thead th.text-center { text-align: center; }

        tbody tr:nth-child(even) {
            background: #f0f4ff;
        }

        tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        tbody tr:hover {
            background: #e0e7ff;
        }

        tbody td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        tbody td.text-right { text-align: right; }
        tbody td.text-center { text-align: center; }
        tbody td.nowrap { white-space: nowrap; }

        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 20px;
            font-size: 7px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-akad {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-serah-terima {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-subsidi {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-non-subsidi {
            background: #ffedd5;
            color: #9a3412;
        }

        /* ================================================================
           BREAKDOWN PER BULAN TABLE
        ================================================================ */
        .bulan-table {
            margin-bottom: 18px;
        }

        /* ================================================================
           FOOTER
        ================================================================ */
        .footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            color: #6b7280;
        }

        .footer-left {
            font-style: italic;
        }

        .footer-right {
            text-align: right;
        }

        .page-number::after {
            content: counter(page) ' / ' counter(pages);
        }

        /* DomPDF page break */
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

{{-- ================================================================
     HEADER
================================================================ --}}
<div class="header">
    <div class="header-top">
        <div>
            <div class="company-name">Sistem Manajemen Penjualan Rumah</div>
            <div class="company-subtitle">Laporan Resmi – Divisi Admin</div>
        </div>
        <div class="report-badge">
            <div class="title">LAPORAN PENJUALAN</div>
            <div class="subtitle">Dicetak: {{ $generated_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>
    <hr class="header-divider">
    <div class="header-meta">
        <span>Periode: <strong>{{ \Carbon\Carbon::parse($laporan['filters']['periode_mulai'])->format('d/m/Y') }} – {{ \Carbon\Carbon::parse($laporan['filters']['periode_selesai'])->format('d/m/Y') }}</strong></span>
        @if(!empty($filters['id_perumahan']))
            <span>Perumahan: <strong>{{ \App\Models\Perumahan::find($filters['id_perumahan'])?->nama_perumahan ?? '-' }}</strong></span>
        @endif
        @if(!empty($filters['kategori']))
            <span>Kategori: <strong>{{ strtoupper(str_replace('_', ' ', $filters['kategori'])) }}</strong></span>
        @endif
        @if(!empty($filters['id_marketing']))
            <span>Marketing: <strong>{{ \App\Models\User::find($filters['id_marketing'])?->nama_lengkap ?? '-' }}</strong></span>
        @endif
    </div>
</div>

{{-- ================================================================
     RINGKASAN CARDS
================================================================ --}}
<div class="summary-section">
    <div class="section-title">Ringkasan Penjualan</div>
    <div class="cards-grid">
        <div class="card">
            <div class="card-label">Total Unit Terjual</div>
            <div class="card-value">{{ number_format($laporan['total_unit_terjual']) }}</div>
        </div>
        <div class="card">
            <div class="card-label">Total Nilai Penjualan</div>
            <div class="card-value green">Rp {{ number_format($laporan['total_nilai_penjualan'], 0, ',', '.') }}</div>
        </div>
        <div class="card">
            <div class="card-label">Total Booking</div>
            <div class="card-value orange">{{ number_format($laporan['total_booking']) }}</div>
        </div>
        <div class="card">
            <div class="card-label">Rata-rata Harga</div>
            <div class="card-value purple">Rp {{ number_format($laporan['rata_rata_harga'], 0, ',', '.') }}</div>
        </div>
    </div>
</div>

{{-- ================================================================
     BREAKDOWN KATEGORI
================================================================ --}}
<div class="section-title">Breakdown Kategori</div>
<div class="breakdown-grid">
    <div class="breakdown-card subsidi">
        <div class="label">🟢 Subsidi</div>
        <div class="breakdown-row">
            <span>Total Unit:</span>
            <span>{{ number_format($laporan['breakdown_kategori']['subsidi']['total_unit']) }} unit</span>
        </div>
        <div class="breakdown-row">
            <span>Total Nilai:</span>
            <span>Rp {{ number_format($laporan['breakdown_kategori']['subsidi']['total_nilai'], 0, ',', '.') }}</span>
        </div>
    </div>
    <div class="breakdown-card non-subsidi">
        <div class="label">🟠 Non-Subsidi</div>
        <div class="breakdown-row">
            <span>Total Unit:</span>
            <span>{{ number_format($laporan['breakdown_kategori']['non_subsidi']['total_unit']) }} unit</span>
        </div>
        <div class="breakdown-row">
            <span>Total Nilai:</span>
            <span>Rp {{ number_format($laporan['breakdown_kategori']['non_subsidi']['total_nilai'], 0, ',', '.') }}</span>
        </div>
    </div>
</div>

{{-- ================================================================
     BREAKDOWN PER BULAN
================================================================ --}}
@if(!empty($laporan['breakdown_bulan']))
<div class="bulan-table">
    <div class="section-title">Penjualan per Bulan</div>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-center">Total Unit</th>
                <th class="text-right">Total Nilai (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan['breakdown_bulan'] as $bulan)
            <tr>
                <td>{{ $bulan['label'] }}</td>
                <td class="text-center">{{ number_format($bulan['total_unit']) }}</td>
                <td class="text-right nowrap">{{ number_format($bulan['total_nilai'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ================================================================
     TABEL DETAIL PENJUALAN
================================================================ --}}
<div class="table-section">
    <div class="section-title">Detail Transaksi Penjualan</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Booking</th>
                <th>Tgl Booking</th>
                <th>Status</th>
                <th>Kode Unit</th>
                <th>Tipe</th>
                <th>Kategori</th>
                <th>Perumahan</th>
                <th class="text-right">Harga Jual</th>
                <th>Konsumen</th>
                <th>Marketing</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan['data'] as $i => $row)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="nowrap">{{ $row->kode_booking }}</td>
                <td class="nowrap">{{ \Carbon\Carbon::parse($row->tanggal_booking)->format('d/m/Y') }}</td>
                <td>
                    <span class="badge {{ $row->status_saat_ini === 'akad' ? 'badge-akad' : 'badge-serah-terima' }}">
                        {{ strtoupper(str_replace('_', ' ', $row->status_saat_ini)) }}
                    </span>
                </td>
                <td>{{ $row->kode_unit }}</td>
                <td>{{ $row->tipe_rumah }}</td>
                <td>
                    <span class="badge {{ $row->kategori === 'subsidi' ? 'badge-subsidi' : 'badge-non-subsidi' }}">
                        {{ $row->kategori === 'subsidi' ? 'Subsidi' : 'Non-Subsidi' }}
                    </span>
                </td>
                <td>{{ $row->nama_perumahan }}</td>
                <td class="text-right nowrap">{{ number_format((float)$row->harga_jual, 0, ',', '.') }}</td>
                <td>{{ $row->nama_konsumen }}</td>
                <td>{{ $row->nama_marketing }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center" style="padding: 16px; color: #6b7280;">
                    Tidak ada data penjualan pada periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ================================================================
     FOOTER
================================================================ --}}
<div class="footer">
    <div class="footer-left">
        Laporan ini digenerate secara otomatis oleh sistem. Harap diverifikasi sebelum digunakan sebagai dokumen resmi.
    </div>
    <div class="footer-right">
        Halaman <span class="page-number"></span>
    </div>
</div>

</body>
</html>

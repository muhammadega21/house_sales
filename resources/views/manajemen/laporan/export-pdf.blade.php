@extends('exports.layouts.pdf')

@section('docTitle', 'LAPORAN PENJUALAN PERUSAHAAN')

@push('styles')
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
        background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
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
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 4px;
        padding: 6px 12px;
        text-align: right;
    }

    .report-badge .title {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
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

    /* ================================================================
       KPI SECTION (Executive Summary)
    ================================================================ */
    .kpi-section {
        margin-bottom: 18px;
    }

    .section-title {
        font-size: 11px;
        font-weight: 700;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-left: 3px solid #0f172a;
        padding-left: 8px;
        margin-bottom: 10px;
    }

    .kpi-grid {
        display: flex;
        gap: 8px;
    }

    .kpi-card {
        flex: 1;
        border-radius: 6px;
        padding: 10px 12px;
        border: 1px solid;
    }

    .kpi-card.primary {
        background: #0f172a;
        border-color: #0f172a;
        color: #ffffff;
    }

    .kpi-card.success {
        background: #f0fdf4;
        border-color: #86efac;
    }

    .kpi-card.warning {
        background: #fffbeb;
        border-color: #fde68a;
    }

    .kpi-card.info {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .kpi-label {
        font-size: 7.5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        margin-bottom: 4px;
        opacity: 0.75;
    }

    .kpi-card.primary .kpi-label { color: #cbd5e1; opacity: 1; }
    .kpi-card.success .kpi-label { color: #15803d; }
    .kpi-card.warning .kpi-label { color: #b45309; }
    .kpi-card.info .kpi-label { color: #1d4ed8; }

    .kpi-value {
        font-size: 14px;
        font-weight: 700;
    }

    .kpi-card.primary .kpi-value { color: #ffffff; }
    .kpi-card.success .kpi-value { color: #15803d; }
    .kpi-card.warning .kpi-value { color: #b45309; }
    .kpi-card.info .kpi-value { color: #1d4ed8; }

    /* ================================================================
       BREAKDOWN KATEGORI (Eksekutif style)
    ================================================================ */
    .breakdown-executive {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
    }

    .breakdown-half {
        flex: 1;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .breakdown-header {
        padding: 6px 12px;
        font-size: 8.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .breakdown-header.subsidi {
        background: #15803d;
        color: #ffffff;
    }

    .breakdown-header.non-subsidi {
        background: #b45309;
        color: #ffffff;
    }

    .breakdown-body {
        padding: 8px 12px;
        background: #ffffff;
    }

    .breakdown-stat {
        display: flex;
        justify-content: space-between;
        font-size: 8.5px;
        padding: 2px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .breakdown-stat:last-child {
        border-bottom: none;
    }

    .breakdown-stat .val {
        font-weight: 700;
    }

    /* ================================================================
       TREN BULANAN TABLE
    ================================================================ */
    .tren-section {
        margin-bottom: 18px;
    }

    /* ================================================================
       TABLE
    ================================================================ */
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.5px;
    }

    thead tr {
        background: #0f172a;
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
        background: #f8fafc;
    }

    tbody tr:nth-child(odd) {
        background: #ffffff;
    }

    tbody td {
        padding: 6px 8px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
    }

    tbody td.text-right { text-align: right; }
    tbody td.text-center { text-align: center; }
    tbody td.nowrap { white-space: nowrap; }

    tfoot tr {
        background: #1e3a5f;
        color: #ffffff;
        font-weight: 700;
    }

    tfoot td {
        padding: 7px 8px;
    }

    tfoot td.text-right { text-align: right; }

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
       SIGNATURE BOX
    ================================================================ */
    .signature-section {
        margin-top: 24px;
        display: flex;
        justify-content: flex-end;
    }

    .signature-box {
        width: 200px;
        text-align: center;
    }

    .signature-title {
        font-size: 8.5px;
        color: #374151;
        margin-bottom: 48px;
    }

    .signature-line {
        border-top: 1px solid #374151;
        padding-top: 4px;
        font-size: 8.5px;
        font-weight: 600;
        color: #0f172a;
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

    .page-break {
        page-break-before: always;
    }

    /* Confidential watermark */
    .confidential-stamp {
        display: inline-block;
        border: 2px solid #ef4444;
        color: #ef4444;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 2px 8px;
        border-radius: 3px;
        opacity: 0.7;
        transform: rotate(-3deg);
    }
</style>
@endpush

@section('content')
{{-- ================================================================
     HEADER
=============================================================== --}}
<div class="header">
    <div class="header-top">
        <div>
            <div class="company-name">Sistem Manajemen Penjualan Rumah</div>
            <div class="company-subtitle">Laporan Eksekutif – Divisi Manajemen</div>
        </div>
        <div class="report-badge">
            <div class="title">LAPORAN PENJUALAN</div>
            <div class="subtitle">
                Dicetak: {{ $generated_at->format('d/m/Y H:i') }}<br>
                <span class="confidential-stamp">Rahasia</span>
            </div>
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
     KPI EXECUTIVE SUMMARY
=============================================================== --}}
<div class="kpi-section">
    <div class="section-title">Executive Summary</div>
    <div class="kpi-grid">
        <div class="kpi-card primary">
            <div class="kpi-label">Total Unit Terjual</div>
            <div class="kpi-value">{{ number_format($laporan['ringkasan']['total_unit_terjual']) }} Unit</div>
        </div>
        <div class="kpi-card success">
            <div class="kpi-label">Total Nilai Penjualan</div>
            <div class="kpi-value">Rp {{ number_format($laporan['ringkasan']['total_nilai_penjualan'], 0, ',', '.') }}</div>
        </div>
        <div class="kpi-card warning">
            <div class="kpi-label">Rata-rata Harga/Unit</div>
            <div class="kpi-value">Rp {{ number_format($laporan['ringkasan']['rata_rata_harga'], 0, ',', '.') }}</div>
        </div>
        <div class="kpi-card info">
            <div class="kpi-label">Total Booking</div>
            <div class="kpi-value">{{ number_format($laporan['ringkasan']['total_booking']) }}</div>
        </div>
    </div>
</div>

{{-- ================================================================
     BREAKDOWN KATEGORI
=============================================================== --}}
<div class="section-title">Perbandingan Kategori</div>
<div class="breakdown-executive">
    <div class="breakdown-half">
        <div class="breakdown-header subsidi">Subsidi</div>
        <div class="breakdown-body">
            <div class="breakdown-stat">
                <span>Jumlah Unit Terjual</span>
                <span class="val">{{ number_format($laporan['per_kategori']['subsidi']['total_unit']) }} unit</span>
            </div>
            <div class="breakdown-stat">
                <span>Total Nilai Penjualan</span>
                <span class="val">Rp {{ number_format($laporan['per_kategori']['subsidi']['total_nilai'], 0, ',', '.') }}</span>
            </div>
            @php
                $pctSubsidi = $laporan['ringkasan']['total_unit_terjual'] > 0
                    ? round($laporan['per_kategori']['subsidi']['total_unit'] / $laporan['ringkasan']['total_unit_terjual'] * 100, 1)
                    : 0;
            @endphp
            <div class="breakdown-stat">
                <span>Kontribusi Unit</span>
                <span class="val">{{ $pctSubsidi }}%</span>
            </div>
        </div>
    </div>
    <div class="breakdown-half">
        <div class="breakdown-header non-subsidi">Non-Subsidi</div>
        <div class="breakdown-body">
            <div class="breakdown-stat">
                <span>Jumlah Unit Terjual</span>
                <span class="val">{{ number_format($laporan['per_kategori']['non_subsidi']['total_unit']) }} unit</span>
            </div>
            <div class="breakdown-stat">
                <span>Total Nilai Penjualan</span>
                <span class="val">Rp {{ number_format($laporan['per_kategori']['non_subsidi']['total_nilai'], 0, ',', '.') }}</span>
            </div>
            @php
                $pctNonSubsidi = $laporan['ringkasan']['total_unit_terjual'] > 0
                    ? round($laporan['per_kategori']['non_subsidi']['total_unit'] / $laporan['ringkasan']['total_unit_terjual'] * 100, 1)
                    : 0;
            @endphp
            <div class="breakdown-stat">
                <span>Kontribusi Unit</span>
                <span class="val">{{ $pctNonSubsidi }}%</span>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================
     TREN PENJUALAN PER BULAN
=============================================================== --}}
@if(!empty($laporan['per_bulan']))
<div class="tren-section">
    <div class="section-title">Tren Penjualan per Bulan</div>
    <table>
        <thead>
            <tr>
                <th>Bulan</th>
                <th class="text-center">Total Unit</th>
                <th class="text-right">Total Nilai (Rp)</th>
                <th class="text-right">Rata-rata Harga (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan['per_bulan'] as $bulan)
            <tr>
                <td>{{ $bulan['label'] }}</td>
                <td class="text-center">{{ number_format($bulan['total_unit']) }}</td>
                <td class="text-right nowrap">{{ number_format($bulan['total_nilai'], 0, ',', '.') }}</td>
                <td class="text-right nowrap">
                    {{ $bulan['total_unit'] > 0 ? number_format($bulan['total_nilai'] / $bulan['total_unit'], 0, ',', '.') : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ number_format($laporan['ringkasan']['total_unit_terjual']) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($laporan['ringkasan']['total_nilai_penjualan'], 0, ',', '.') }}</strong></td>
                <td class="text-right"><strong>{{ number_format($laporan['ringkasan']['rata_rata_harga'], 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</div>
@endif

{{-- ================================================================
     DETAIL PENJUALAN
=============================================================== --}}
<div class="page-break"></div>
<div class="section-title" style="margin-top: 16px;">Detail Transaksi Penjualan</div>
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Booking</th>
            <th>Tgl Booking</th>
            <th>Tgl Status</th>
            <th>Status</th>
            <th>Unit</th>
            <th>Kategori</th>
            <th>Perumahan</th>
            <th class="text-right">Harga Jual (Rp)</th>
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
            <td class="nowrap">{{ \Carbon\Carbon::parse($row->tanggal_perubahan)->format('d/m/Y') }}</td>
            <td>
                <span class="badge {{ $row->status_saat_ini === 'akad' ? 'badge-akad' : 'badge-serah-terima' }}">
                    {{ strtoupper(str_replace('_', ' ', $row->status_saat_ini)) }}
                </span>
            </td>
            <td>{{ $row->kode_unit }} <small style="color:#6b7280;">({{ $row->tipe_rumah }})</small></td>
            <td>
                <span class="badge {{ $row->kategori === 'subsidi' ? 'badge-subsidi' : 'badge-non-subsidi' }}">
                    {{ $row->kategori === 'subsidi' ? 'Subsidi' : 'Non-Sub' }}
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
    @if(count($laporan['data']) > 0)
    <tfoot>
        <tr>
            <td colspan="8"><strong>TOTAL</strong></td>
            <td class="text-right"><strong>{{ number_format($laporan['ringkasan']['total_nilai_penjualan'], 0, ',', '.') }}</strong></td>
            <td colspan="2"></td>
        </tr>
    </tfoot>
    @endif
</table>
@endsection

@section('after')
{{-- ================================================================
     TANDA TANGAN
=============================================================== --}}
<div class="signature-section">
    <div class="signature-box">
        <div class="signature-title">Disahkan oleh,</div>
        <div class="signature-line">Direktur / Manajemen</div>
    </div>
</div>
@endsection

@section('footerLeft')
RAHASIA – Dokumen ini hanya untuk kalangan internal manajemen. Dilarang mendistribusikan tanpa izin.
@endsection

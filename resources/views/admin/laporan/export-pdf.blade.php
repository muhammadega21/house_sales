@extends('exports.layouts.pdf')

@section('docTitle', 'LAPORAN PENJUALAN')

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
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.35);
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
            border-top: 1px solid rgba(255, 255, 255, 0.3);
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

        .card-value.green {
            color: #15803d;
        }

        .card-value.orange {
            color: #b45309;
        }

        .card-value.purple {
            color: #7c3aed;
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

        thead th.text-right {
            text-align: right;
        }

        thead th.text-center {
            text-align: center;
        }

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

        tbody td.text-right {
            text-align: right;
        }

        tbody td.text-center {
            text-align: center;
        }

        tbody td.nowrap {
            white-space: nowrap;
        }

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
           FOOTER
        ================================================================ */
    </style>
@endpush

@section('content')
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
            <span>Periode: <strong>{{ \Carbon\Carbon::parse($laporan['filters']['periode_mulai'])->format('d/m/Y') }} –
                    {{ \Carbon\Carbon::parse($laporan['filters']['periode_selesai'])->format('d/m/Y') }}</strong></span>
            @if (!empty($filters['id_perumahan']))
                <span>Perumahan:
                    <strong>{{ \App\Models\Perumahan::find($filters['id_perumahan'])?->nama_perumahan ?? '-' }}</strong></span>
            @endif
            @if (!empty($filters['kategori']))
                <span>Kategori: <strong>{{ strtoupper(str_replace('_', ' ', $filters['kategori'])) }}</strong></span>
            @endif
            @if (!empty($filters['id_marketing']))
                <span>Marketing:
                    <strong>{{ \App\Models\User::find($filters['id_marketing'])?->nama_lengkap ?? '-' }}</strong></span>
            @endif
        </div>
    </div>

    @include('exports.partials.laporan-penjualan-body')
@endsection

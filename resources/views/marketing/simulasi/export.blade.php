<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Export PDF - Perbandingan Simulasi Pembayaran</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 24px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #4b5563;
            margin-bottom: 24px;
        }

        .section {
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .card {
            border: 1px solid #d1d5db;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .grid {
            display: grid;
            gap: 16px;
        }

        .grid-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .table th,
        .table td {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
            font-size: 13px;
        }

        .table th {
            background: #f8fafc;
            font-weight: 700;
        }

        .badge {
            display: inline-block;
            background: #d1fae5;
            color: #065f46;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
        }

        .highlight {
            background: #ecfdf5;
            border-color: #34d399;
        }
    </style>
</head>

<body>
    <div class="section">
        <div class="page-title">Perbandingan Simulasi Pembayaran</div>
        <div class="subtitle">Dokumen ini menampilkan ringkasan perbandingan metode pembayaran berdasarkan kondisi unit
            dan parameter yang Anda pilih.</div>
    </div>

    <div class="section grid grid-2">
        <div class="card">
            <div class="section-title">Informasi Unit</div>
            <table class="table">
                <tr>
                    <th>Kode Unit</th>
                    <td>{{ $unit->kode_unit }}</td>
                </tr>
                <tr>
                    <th>Tipe Rumah</th>
                    <td>{{ $unit->tipe_rumah }}</td>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <td>{{ $unit->kategori->value }}</td>
                </tr>
                <tr>
                    <th>Harga Jual</th>
                    <td>Rp {{ number_format($unit->harga_jual, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Perumahan</th>
                    <td>{{ $unit->perumahan->nama_perumahan ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="card">
            <div class="section-title">Parameter Simulasi</div>
            <table class="table">
                <tr>
                    <th>Konsumen</th>
                    <td>{{ $konsumen?->nama_lengkap ?? 'Tidak dipilih' }}</td>
                </tr>
                <tr>
                    <th>DP Persen</th>
                    <td>{{ $hasilKpr['dp_persen'] ?? ($hasilCashBertahap['dp_persen'] ?? 0) }}%</td>
                </tr>
                <tr>
                    <th>Tenor (tahun)</th>
                    <td>{{ $hasilKpr['tenor_tahun'] ?? ($hasilCashBertahap['tenor_tahun'] ?? 0) }}</td>
                </tr>
                <tr>
                    <th>Suku Bunga</th>
                    <td>{{ $hasilKpr['suku_bunga'] ?? 0 }}%</td>
                </tr>
                <tr>
                    <th>Diskon Cash Keras</th>
                    <td>{{ $hasilCashKeras['diskon_persen'] ?? 0 }}%</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="section card">
        <div class="section-title">Ringkasan Perbandingan</div>
        <div class="grid grid-2">
            <div class="card highlight">
                <strong>KPR</strong>
                <table class="table">
                    <tr>
                        <th>Total Pembayaran</th>
                        <td>Rp {{ number_format($hasilKpr['total_pembayaran'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Cicilan Bulanan</th>
                        <td>Rp {{ number_format($hasilKpr['cicilan_bulanan'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total Bunga</th>
                        <td>Rp {{ number_format($hasilKpr['total_bunga'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>DP</th>
                        <td>{{ $hasilKpr['dp_persen'] }}% (Rp
                            {{ number_format($hasilKpr['dp_nominal'], 0, ',', '.') }})</td>
                    </tr>
                </table>
            </div>
            <div class="card">
                <strong>Cash Bertahap</strong>
                <table class="table">
                    <tr>
                        <th>Total Pembayaran</th>
                        <td>Rp {{ number_format($hasilCashBertahap['total_pembayaran'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Cicilan Bulanan</th>
                        <td>Rp {{ number_format($hasilCashBertahap['cicilan_bulanan'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>DP</th>
                        <td>{{ $hasilCashBertahap['dp_persen'] }}% (Rp
                            {{ number_format($hasilCashBertahap['dp_nominal'], 0, ',', '.') }})</td>
                    </tr>
                </table>
            </div>
            <div class="card">
                <strong>Cash Keras</strong>
                <table class="table">
                    <tr>
                        <th>Total Pembayaran</th>
                        <td>Rp {{ number_format($hasilCashKeras['total_pembayaran'], 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Diskon</th>
                        <td>{{ $hasilCashKeras['diskon_persen'] ?? 0 }}%</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Nilai Metode Terbaik</div>
        <p>Metode terbaik dipilih berdasarkan total pembayaran terendah.</p>
        @php
            $totals = [
                'KPR' => $hasilKpr['total_pembayaran'],
                'Cash Bertahap' => $hasilCashBertahap['total_pembayaran'],
                'Cash Keras' => $hasilCashKeras['total_pembayaran'],
            ];
            asort($totals);
            $bestMethod = array_key_first($totals);
        @endphp
        <div class="card">
            <strong>{{ $bestMethod }}</strong>
        </div>
    </div>
</body>

</html>

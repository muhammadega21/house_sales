@props([
    'plafon' => 0,
    'tenor' => 0,
    'bunga' => 0,
])

@php
    $plafon = (float) $plafon;
    $tenorTahun = (int) $tenor;
    $bunga = (float) $bunga;

    $jumlahBulan = $tenorTahun * 12;

    if ($jumlahBulan > 0 && $plafon > 0) {
        if ($bunga > 0) {
            $bungaBulanan = $bunga / 100 / 12;
            $cicilanBulanan = $plafon * $bungaBulanan * pow(1 + $bungaBulanan, $jumlahBulan) / (pow(1 + $bungaBulanan, $jumlahBulan) - 1);
            $totalPembayaran = $cicilanBulanan * $jumlahBulan;
            $totalBunga = $totalPembayaran - $plafon;
        } else {
            $cicilanBulanan = $plafon / $jumlahBulan;
            $totalPembayaran = $plafon;
            $totalBunga = 0;
        }
    } else {
        $cicilanBulanan = 0;
        $totalPembayaran = 0;
        $totalBunga = 0;
    }
@endphp

<div class="space-y-4">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center">
            <p class="text-sm font-semibold text-gray-600">Cicilan per Bulan</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($cicilanBulanan, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center">
            <p class="text-sm font-semibold text-gray-600">Total Pembayaran</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($totalPembayaran, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center">
            <p class="text-sm font-semibold text-gray-600">Total Bunga</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($totalBunga, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="text-sm text-gray-500">
        <span class="font-medium text-gray-700">Plafon:</span> Rp {{ number_format($plafon, 0, ',', '.') }}
        <span class="mx-2 text-gray-300">|</span>
        <span class="font-medium text-gray-700">Tenor:</span> {{ $tenorTahun }} tahun ({{ $jumlahBulan }} bulan)
        <span class="mx-2 text-gray-300">|</span>
        <span class="font-medium text-gray-700">Bunga:</span> {{ number_format($bunga, 2) }}%/tahun
    </div>
</div>

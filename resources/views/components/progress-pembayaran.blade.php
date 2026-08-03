@props([
    'totalTerbayar' => 0,
    'hargaUnit' => 0,
])

@php
    $persentase = $hargaUnit > 0 ? round(($totalTerbayar / $hargaUnit) * 100) : 0;
    $warna = $persentase >= 50 ? 'emerald' : 'amber';
    $persentaseDisplay = min($persentase, 100);
@endphp

<div class="space-y-2">
    <div class="flex items-center justify-between text-sm">
        <span class="font-medium text-gray-700">Progress Pembayaran</span>
        <span class="font-semibold text-{{ $warna }}-600">{{ $persentaseDisplay }}%</span>
    </div>
    <div class="h-3 w-full overflow-hidden rounded-full bg-gray-200">
        <div class="h-full rounded-full bg-{{ $warna }}-500 transition-all duration-500"
             style="width: {{ $persentaseDisplay }}%"></div>
    </div>
    <div class="flex justify-between text-xs text-gray-500">
        <span>Terverifikasi: Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</span>
        <span>Harga Unit: Rp {{ number_format($hargaUnit, 0, ',', '.') }}</span>
    </div>
</div>
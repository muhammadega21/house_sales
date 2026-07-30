@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Admin</h1>
            <p class="text-sm text-gray-500">Selamat datang kembali, {{ auth()->user()->nama_lengkap }}</p>
        </div>
    </div>

    <!-- Summary Cards Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Pengguna</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">4</p>
                </div>
                <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Unit Tersedia</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">0</p>
                </div>
                <div class="rounded-lg bg-green-50 p-3 text-green-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Booking</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">0</p>
                </div>
                <div class="rounded-lg bg-yellow-50 p-3 text-yellow-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Omset Penjualan</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">Rp 0</p>
                </div>
                <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Prospek Perusahaan Section -->
    <div class="space-y-6">
        <h2 class="text-xl font-bold text-gray-800">Prospek Perusahaan</h2>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Prospek (Bulan Ini)</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalProspekBulanIni }}</p>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Conversion Rate Perusahaan</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $conversionRatePerusahaan }}%</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-3 text-green-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Marketing Aktif</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ \App\Models\User::marketing()->aktif()->count() }}</p>
                    </div>
                    <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Bar Chart: Prospek per Marketing -->
        <x-card>
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Prospek per Marketing (Bulan Ini)</h2>
            <div class="flex items-center justify-center" style="height: 300px;">
                <canvas id="prospekPerMarketingChart"></canvas>
            </div>
        </x-card>

        <!-- Top Marketing Tables -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Top by Prospek -->
            <x-card>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Top Marketing by Prospek</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">#</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Marketing</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Prospek</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Konversi</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Rate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($topMarketingByProspek as $i => $m)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-gray-500">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-gray-800">{{ $m['nama_lengkap'] }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $m['total_prospek'] }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $m['konversi'] }}</td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $m['conversion_rate'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Top by Conversion Rate -->
            <x-card>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Top Marketing by Conversion Rate</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">#</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Marketing</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Prospek</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Konversi</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Rate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($topMarketingByConversion as $i => $m)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-gray-500">{{ $i + 1 }}</td>
                                    <td class="px-3 py-2 font-medium text-gray-800">{{ $m['nama_lengkap'] }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $m['total_prospek'] }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $m['konversi'] }}</td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $m['conversion_rate'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('prospekPerMarketingChart');
    if (!ctx) return;

    const data = @json($prospekPerMarketing);
    const labels = data.map(d => d.nama_lengkap);
    const prospekData = data.map(d => d.total_prospek);
    const konversiData = data.map(d => d.konversi);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Prospek',
                    data: prospekData,
                    backgroundColor: '#2563EB',
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Konversi',
                    data: konversiData,
                    backgroundColor: '#10B981',
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { size: 11 } },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    ticks: { font: { size: 10 }, maxRotation: 45 },
                    grid: { display: false }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10, font: { size: 11 } }
                }
            }
        }
    });
});
</script>
@endpush
@endsection

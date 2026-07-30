@extends('layouts.app')

@section('title', 'Dashboard Marketing')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Marketing</h1>
            <p class="text-sm text-gray-500">Selamat datang kembali, {{ auth()->user()->nama_lengkap }}</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Prospek</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalProspekBulanIni }}</p>
                    <p class="text-xs text-gray-400">Bulan ini</p>
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
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Prospek Baru</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $baruBulanIni }}</p>
                    <p class="text-xs text-gray-400">Belum dihubungi</p>
                </div>
                <div class="rounded-lg bg-gray-100 p-3 text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Prospek Berminat</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $berminatBulanIni }}</p>
                    <p class="text-xs text-gray-400">Siap konversi</p>
                </div>
                <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Konversi</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $konversiBulanIni }}</p>
                    <p class="text-xs text-gray-400">Menjadi konsumen</p>
                </div>
                <div class="rounded-lg bg-green-50 p-3 text-green-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Conversion Rate</p>
                    <p class="mt-1 text-2xl font-bold text-gray-800">{{ $conversionRate }}%</p>
                    <p class="text-xs text-gray-400">Prospek → Konsumen</p>
                </div>
                <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Pipeline + Charts Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
        <!-- Pipeline/Funnel -->
        <div class="lg:col-span-2">
            <x-card>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Pipeline Prospek</h2>
                <div class="space-y-3">
                        @php
                            $pipelineData = [
                                ['label' => 'Baru', 'count' => $stats['baru'] ?? 0, 'color' => 'bg-gray-400', 'href' => route('marketing.prospek.index', ['status' => 'baru'])],
                                ['label' => 'Dihubungi', 'count' => $stats['dihubungi'] ?? 0, 'color' => 'bg-blue-400', 'href' => route('marketing.prospek.index', ['status' => 'dihubungi'])],
                                ['label' => 'Berminat', 'count' => $stats['berminat'] ?? 0, 'color' => 'bg-primary', 'href' => route('marketing.prospek.index', ['status' => 'berminat'])],
                                ['label' => 'Tidak Berminat', 'count' => $stats['tidak_berminat'] ?? 0, 'color' => 'bg-red-400', 'href' => route('marketing.prospek.index', ['status' => 'tidak_berminat'])],
                                ['label' => 'Jadi Konsumen', 'count' => $stats['jadi_konsumen'] ?? 0, 'color' => 'bg-green-500', 'href' => route('marketing.konsumen.index')],
                            ];
                            $maxCount = max(array_column($pipelineData, 'count')) ?: 1;
                        @endphp
                    @foreach($pipelineData as $item)
                        <a href="{{ $item['href'] }}" class="block group">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $item['label'] }}</span>
                                <span class="text-sm font-bold text-gray-800">{{ $item['count'] }}</span>
                            </div>
                            <div class="h-6 w-full rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full {{ $item['color'] }} transition-all duration-300 group-hover:opacity-80" style="width: {{ max(($item['count'] / $maxCount) * 100, $item['count'] > 0 ? 8 : 0) }}%"></div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </x-card>
        </div>

        <!-- Charts Column -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Pie Chart: Prospek per Sumber -->
            <x-card>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Prospek per Sumber</h2>
                <div class="flex items-center justify-center" style="height: 220px;">
                    <canvas id="prospekPerSumberChart"></canvas>
                </div>
            </x-card>

            <!-- Line Chart: Prospek per Bulan -->
            <x-card>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Tren Prospek & Konversi (6 Bulan)</h2>
                <div class="flex items-center justify-center" style="height: 220px;">
                    <canvas id="prospekPerBulanChart"></canvas>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Prospek Terbaru -->
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Prospek Terbaru</h2>
                <a href="{{ route('marketing.prospek.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Nama</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Sumber</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($prospekTerbaru as $prospek)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-800">
                                    <a href="{{ route('marketing.prospek.show', $prospek->id) }}" class="hover:text-blue-600">{{ $prospek->nama_prospek }}</a>
                                </td>
                                <td class="px-3 py-2 text-gray-600">{{ $prospek->sumber_prospek->label() }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $prospek->status_prospek->color() }}-100 text-{{ $prospek->status_prospek->color() }}-800">
                                        {{ $prospek->status_prospek->label() }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-500">{{ $prospek->tanggal_prospek->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada prospek</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Konsumen Terbaru -->
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Konsumen Terbaru</h2>
                <a href="{{ route('marketing.konsumen.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Lihat Semua →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Nama</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">NIK</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($konsumenTerbaru as $konsumen)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-800">
                                    <a href="{{ route('marketing.konsumen.show', $konsumen->id) }}" class="hover:text-blue-600">{{ $konsumen->nama_lengkap }}</a>
                                </td>
                                <td class="px-3 py-2 text-gray-600">{{ $konsumen->nik }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $konsumen->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada konsumen</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Pie Chart: Prospek per Sumber
    const sumberCtx = document.getElementById('prospekPerSumberChart');
    if (sumberCtx) {
        const sumberData = @json($prospekPerSumber);
        const sumberLabels = {
            'facebook': 'Facebook',
            'instagram': 'Instagram',
            'tiktok': 'TikTok',
            'walk_in': 'Walk-in',
            'referral': 'Referral',
            'lainnya': 'Lainnya'
        };
        const sumberColors = [
            '#2563EB', '#10B981', '#F59E0B', '#EF4444', '#6366F1', '#6B7280'
        ];
        const sumberEntries = Object.entries(sumberData);
        new Chart(sumberCtx, {
            type: 'doughnut',
            data: {
                labels: sumberEntries.map(([k]) => sumberLabels[k] || k),
                datasets: [{
                    data: sumberEntries.map(([, v]) => v),
                    backgroundColor: sumberColors.slice(0, sumberEntries.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10, font: { size: 11 } }
                    }
                }
            }
        });
    }

    // Line Chart: Prospek per Bulan
    const bulanCtx = document.getElementById('prospekPerBulanChart');
    if (bulanCtx) {
        const bulanData = @json($dataBulan);
        new Chart(bulanCtx, {
            type: 'line',
            data: {
                labels: bulanData.map(d => d.bulan),
                datasets: [
                    {
                        label: 'Prospek Baru',
                        data: bulanData.map(d => d.prospek),
                        borderColor: '#2563EB',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Konversi',
                        data: bulanData.map(d => d.konversi),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointHoverRadius: 6
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
                        ticks: { font: { size: 11 } },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10, font: { size: 11 } }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
});
</script>
@endpush
@endsection

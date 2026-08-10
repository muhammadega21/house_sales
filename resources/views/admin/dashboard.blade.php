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

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-card>
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Pengguna</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalUsers }}</p>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M17 8a3 3 0 11-6 0 3 3 0 016 0zM7 8a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Unit Tersedia / Total Unit
                        </p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalUnitsAvailable }} / {{ $totalUnits }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-3 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Booking Bulan Ini</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalBooking }}</p>
                    </div>
                    <div class="rounded-lg bg-amber-50 p-3 text-amber-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Omset Bulan Ini</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">Rp
                            {{ number_format($totalOmsetBulanIni, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 text-slate-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Widgets -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-card title="Conversion Rate Perusahaan" subtitle="Prospek menjadi closing bulan ini">
                <div class="flex items-center justify-between">
                    <p class="text-4xl font-bold text-gray-900">{{ $conversionRatePerusahaan }}%</p>
                    <div class="rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-800">Baik</div>
                </div>
            </x-card>

            <x-card title="Rata-rata Waktu Closing" subtitle="Rata-rata hari dari booking hingga akad">
                <div class="flex items-center justify-between">
                    <p class="text-4xl font-bold text-gray-900">{{ $averageClosingTime }} hari</p>
                    <div class="rounded-full bg-blue-100 px-4 py-2 text-sm font-semibold text-blue-800">Stabil</div>
                </div>
            </x-card>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <x-card title="Penjualan per Bulan">
                <div class="h-72">
                    <canvas id="penjualanPerBulanChart"></canvas>
                </div>
            </x-card>

            <x-card title="Subsidi vs Non-Subsidi">
                <div class="h-72">
                    <canvas id="kategoriBreakdownChart"></canvas>
                </div>
            </x-card>

            <x-card title="Tren Prospek vs Closing">
                <div class="h-72">
                    <canvas id="trenBulananChart"></canvas>
                </div>
            </x-card>
        </div>

        <!-- Tables -->
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <x-card title="Top 5 Marketing">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">#</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Marketing</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Closing</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Nilai</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Komisi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($topMarketing as $index => $marketing)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-3 py-3 font-medium text-gray-800">{{ $marketing->nama_lengkap }}</td>
                                    <td class="px-3 py-3 text-gray-600">{{ $marketing->total_closing }}</td>
                                    <td class="px-3 py-3 text-gray-600">Rp
                                        {{ number_format($marketing->total_nilai, 0, ',', '.') }}</td>
                                    <td class="px-3 py-3 text-gray-600">Rp
                                        {{ number_format($marketing->total_komisi, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada data
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card title="Booking Terbaru">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Kode</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Konsumen</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tanggal
                                    Booking</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($latestBookings as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 text-gray-600">{{ $booking->kode_booking }}</td>
                                    <td class="px-3 py-3 text-gray-800">{{ $booking->nama_konsumen ?? '-' }}</td>
                                    <td class="px-3 py-3 text-gray-600">
                                        {{ optional($booking->tanggal_booking)->format('d M Y') ?? '-' }}</td>
                                    <td class="px-3 py-3">
                                        <x-badge :status="$booking->status_penjualan ?? 'booking'" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada
                                        booking terbaru</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <x-card title="Unit Tersedia per Perumahan">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Perumahan
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Tersedia</th>
                                <th class="px-3 py-3 text-left text-xs font-semibold uppercase text-gray-500">Total Unit
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($unitsAvailableByPerumahan as $unit)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-3 text-gray-800">{{ $unit->nama_perumahan }}</td>
                                    <td class="px-3 py-3 text-gray-600">{{ $unit->tersedia }}</td>
                                    <td class="px-3 py-3 text-gray-600">{{ $unit->total_unit }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada data
                                        unit</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <!-- Prospek Summary Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Prospek Bulan Ini</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalProspekBulanIni }}</p>
                        <p class="text-xs text-gray-400">Bulan ini</p>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Prospek Baru</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $prospekStats['baru'] ?? 0 }}</p>
                        <p class="text-xs text-gray-400">Belum dihubungi</p>
                    </div>
                    <div class="rounded-lg bg-gray-100 p-3 text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Prospek Berminat</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $prospekStats['berminat'] ?? 0 }}</p>
                        <p class="text-xs text-gray-400">Siap konversi</p>
                    </div>
                    <div class="rounded-lg bg-blue-50 p-3 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Konversi Bulan Ini</p>
                        <p class="mt-1 text-2xl font-bold text-gray-800">{{ $konversiBulanIni }}</p>
                        <p class="text-xs text-gray-400">Menjadi konsumen</p>
                    </div>
                    <div class="rounded-lg bg-green-50 p-3 text-green-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Pipeline + Target Row -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Pipeline/Funnel -->
            <div class="lg:col-span-2">
                <x-card>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Pipeline Prospek</h2>
                    <div class="space-y-3">
                        @php
                            $pipelineData = [
                                [
                                    'label' => 'Baru',
                                    'count' => $prospekStats['baru'] ?? 0,
                                    'bg' => '#9CA3AF',
                                ],
                                [
                                    'label' => 'Dihubungi',
                                    'count' => $prospekStats['dihubungi'] ?? 0,
                                    'bg' => '#60A5FA',
                                ],
                                [
                                    'label' => 'Berminat',
                                    'count' => $prospekStats['berminat'] ?? 0,
                                    'bg' => '#2563EB',
                                ],
                                [
                                    'label' => 'Tidak Berminat',
                                    'count' => $prospekStats['tidak_berminat'] ?? 0,
                                    'bg' => '#EF4444',
                                ],
                                [
                                    'label' => 'Jadi Konsumen',
                                    'count' => $prospekStats['jadi_konsumen'] ?? 0,
                                    'bg' => '#10B981',
                                ],
                            ];
                            $maxCount = max(array_column($pipelineData, 'count')) ?: 1;
                        @endphp
                        @foreach ($pipelineData as $item)
                            <div class="block group">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $item['label'] }}</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $item['count'] }}</span>
                                </div>
                                <div class="h-6 w-full rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-300 group-hover:opacity-80"
                                        style="width: {{ max(($item['count'] / $maxCount) * 100, $item['count'] > 0 ? 8 : 0) }}%; background-color: {{ $item['bg'] }};">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>

            <!-- Conversion Rate -->
            <div class="lg:col-span-1">
                <x-card title="Conversion Rate" subtitle="Prospek menjadi konsumen bulan ini">
                    <div class="flex items-center justify-between">
                        <p class="text-4xl font-bold text-gray-900">{{ $conversionRate }}%</p>
                        <div class="rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-800">Baik</div>
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
                                        {{ $prospek->nama_prospek }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-600">{{ $prospek->sumber_prospek->label() }}</td>
                                    <td class="px-3 py-2">
                                        <x-badge :status="$prospek->status_prospek" />
                                    </td>
                                    <td class="px-3 py-2 text-gray-500">{{ \Carbon\Carbon::parse($prospek->tanggal_prospek)->format('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada
                                        prospek</td>
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
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Nama</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">NIK</th>
                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-500">Tanggal
                                    Daftar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($konsumenTerbaru as $konsumen)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 font-medium text-gray-800">
                                        {{ $konsumen->nama_lengkap }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-600">{{ $konsumen->nik }}</td>
                                    <td class="px-3 py-2 text-gray-500">{{ \Carbon\Carbon::parse($konsumen->created_at)->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-3 py-4 text-center text-sm text-gray-400">Tidak ada
                                        konsumen</td>
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
            document.addEventListener('DOMContentLoaded', function() {
                const penjualanPerBulan = @json($penjualanPerBulan);
                const kategoriBreakdown = @json($kategoriBreakdown);
                const trenBulanan = @json($trenBulanan);

                const barCtx = document.getElementById('penjualanPerBulanChart');
                if (barCtx) {
                    new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: penjualanPerBulan.map(item => item.label),
                            datasets: [{
                                label: 'Total Omset',
                                data: penjualanPerBulan.map(item => item.total),
                                backgroundColor: '#2563EB',
                                borderRadius: 12,
                                maxBarThickness: 32,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: value => new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            maximumFractionDigits: 0
                                        }).format(value),
                                        color: '#4B5563',
                                    },
                                    grid: {
                                        color: 'rgba(15, 23, 42, 0.05)',
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#374151',
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }

                const pieCtx = document.getElementById('kategoriBreakdownChart');
                if (pieCtx) {
                    new Chart(pieCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Subsidi', 'Non-Subsidi'],
                            datasets: [{
                                data: [kategoriBreakdown.subsidi.total_nilai, kategoriBreakdown
                                    .non_subsidi.total_nilai
                                ],
                                backgroundColor: ['#10B981', '#2563EB'],
                                borderWidth: 0,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#374151'
                                    }
                                }
                            }
                        }
                    });
                }

                const lineCtx = document.getElementById('trenBulananChart');
                if (lineCtx) {
                    new Chart(lineCtx, {
                        type: 'line',
                        data: {
                            labels: trenBulanan.map(item => item.label),
                            datasets: [{
                                    label: 'Prospek',
                                    data: trenBulanan.map(item => item.prospek),
                                    borderColor: '#2563EB',
                                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                                    fill: true,
                                    tension: 0.3,
                                },
                                {
                                    label: 'Closing',
                                    data: trenBulanan.map(item => item.closing),
                                    borderColor: '#10B981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.15)',
                                    fill: true,
                                    tension: 0.3,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: '#374151'
                                    },
                                    grid: {
                                        color: 'rgba(15, 23, 42, 0.05)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#374151'
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: '#374151'
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
